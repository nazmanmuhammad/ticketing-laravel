<?php

namespace App\Livewire\AccessRequest;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\AccessRequest;
use App\Models\AccessRequestActivity;
use App\Models\AccessRequestCommentAttachment;
use App\Models\CannedResponse;
use App\Actions\AccessRequest\ApproveAccessRequestAction;
use App\Services\AccessRequestService;
use App\Mail\ApprovalRequestMail;
use App\Mail\FeedbackReviewMail;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.master')]
class AccessRequestDetail extends Component
{
    use WithFileUploads;

    public AccessRequest $accessRequest;
    public string $approvalNotes = '';
    public string $infoResponse = '';
    public string $commentBody = '';
    public bool $isInternal = false;
    public $commentAttachments = [];
    public ?int $replyToId = null;
    public ?string $replyToUser = null;
    public ?string $replyToBody = null;

    public function mount(AccessRequest $accessRequest): void
    {
        $this->accessRequest = $accessRequest->load([
            'requester', 'system', 'assignee', 'team',
            'approvals.approver', 'attachments',
            'comments.user', 'comments.attachments', 'comments.parent.user',
            'activities.user',
        ]);
    }

    // ── Comments ──────────────────────────────────────

    public function addComment(): void
    {
        $rules = ['commentBody' => 'required|string'];
        if (!empty($this->commentAttachments)) {
            $rules['commentAttachments.*'] = 'file|max:10240';
        }
        $this->validate($rules);

        $comment = $this->accessRequest->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->commentBody,
            'is_internal' => $this->isInternal,
            'parent_id' => $this->replyToId,
        ]);

        if (!empty($this->commentAttachments)) {
            foreach ($this->commentAttachments as $file) {
                $path = $file->store('ar-comment-attachments', 'public');
                AccessRequestCommentAttachment::create([
                    'comment_id' => $comment->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        AccessRequestActivity::create([
            'access_request_id' => $this->accessRequest->id,
            'user_id' => auth()->id(),
            'action' => 'comment',
            'description' => ($this->isInternal ? 'Added internal note' : 'Added comment'),
        ]);

        // Send feedback review email for non-internal comments
        if (!$this->isInternal) {
            $this->sendFeedbackEmail('access_request', $this->accessRequest, $comment);
        }

        $this->commentBody = '';
        $this->isInternal = false;
        $this->commentAttachments = [];
        $this->replyToId = null;
        $this->replyToUser = null;
        $this->replyToBody = null;
        $this->accessRequest->refresh()->load([
            'comments.user', 'comments.attachments', 'comments.parent.user', 'activities.user',
        ]);
        $this->dispatch('toast', type: 'success', message: 'Comment added');
    }

    public function setReplyTo(int $commentId): void
    {
        $comment = $this->accessRequest->comments->find($commentId);
        if ($comment) {
            $this->replyToId = $comment->id;
            $this->replyToUser = $comment->user->name;
            $this->replyToBody = \Illuminate\Support\Str::limit($comment->body, 100);
        }
    }

    public function cancelReply(): void
    {
        $this->replyToId = null;
        $this->replyToUser = null;
        $this->replyToBody = null;
    }

    public function removeAttachment(int $index): void
    {
        $attachments = collect($this->commentAttachments)->values()->toArray();
        unset($attachments[$index]);
        $this->commentAttachments = array_values($attachments);
    }

    public function applyCannedResponse(int $id): void
    {
        $canned = CannedResponse::find($id);
        if ($canned) {
            $this->commentBody = $canned->body;
        }
    }

    // ── Approval ──────────────────────────────────────

    public function approve(): void
    {
        $this->handleApproval('approved');
    }

    public function reject(): void
    {
        $this->validate(['approvalNotes' => 'required|string']);
        $this->handleApproval('rejected');
    }

    public function requestInfo(): void
    {
        $this->validate(['approvalNotes' => 'required|string']);
        $this->handleApproval('info_requested');
    }

    private function handleApproval(string $action): void
    {
        $user = auth()->user();
        $approval = $this->accessRequest->approvals()
            ->where('approver_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$approval) {
            // Check if user has permission to approve directly (ad-hoc)
            if ($user->can('access_requests.approve')) {
                $maxLevel = $this->accessRequest->approvals()->max('level') ?? 0;
                $approval = $this->accessRequest->approvals()->create([
                    'approver_id' => $user->id,
                    'level' => $maxLevel + 1,
                    'status' => 'pending',
                ]);
            } else {
                $this->dispatch('toast', type: 'error', message: 'You are not a pending approver');
                return;
            }
        }

        $approval->update([
            'status' => $action,
            'notes' => $this->approvalNotes ?: null,
            'acted_at' => now(),
        ]);

        AccessRequestActivity::create([
            'access_request_id' => $this->accessRequest->id,
            'user_id' => $user->id,
            'action' => $action,
            'description' => "Access request {$action}" . ($this->approvalNotes ? ": {$this->approvalNotes}" : ''),
        ]);

        // If info_requested, save notes as a comment
        if ($action === 'info_requested' && $this->approvalNotes) {
            $this->accessRequest->comments()->create([
                'user_id' => $user->id,
                'body' => "[Info Request] {$this->approvalNotes}",
                'is_internal' => false,
            ]);
        }

        if ($action === 'approved') {
            $pendingAtSameOrLower = $this->accessRequest->approvals()
                ->where('level', '<=', $approval->level)
                ->where('status', 'pending')
                ->count();

            if ($pendingAtSameOrLower === 0) {
                $nextLevelApprovals = $this->accessRequest->approvals()
                    ->where('level', '>', $approval->level)
                    ->where('status', 'pending')
                    ->get();

                if ($nextLevelApprovals->isEmpty()) {
                    $this->accessRequest->update(['status' => 'approved']);
                    AccessRequestActivity::create([
                        'access_request_id' => $this->accessRequest->id,
                        'user_id' => $user->id,
                        'action' => 'fully_approved',
                        'description' => 'All approvals completed, request is now approved',
                    ]);
                } else {
                    $nextMinLevel = $nextLevelApprovals->min('level');
                    $nextLevelOnly = $nextLevelApprovals->where('level', $nextMinLevel);
                    foreach ($nextLevelOnly as $nextApproval) {
                        $nextApproval->load('approver');
                        if ($nextApproval->approver) {
                            try {
                                Mail::to($nextApproval->approver->email)->send(new ApprovalRequestMail(
                                    $this->accessRequest, 'access_request', $nextApproval->approver->name, $nextMinLevel,
                                    route('access-requests.show', $this->accessRequest)
                                ));
                            } catch (\Throwable $e) {
                                \Log::warning('Failed to send AR next level approval email: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
            $this->dispatch('toast', type: 'success', message: 'Request approved');
        } elseif ($action === 'rejected') {
            $this->accessRequest->update(['status' => 'rejected']);
            $this->dispatch('toast', type: 'error', message: 'Request rejected');
        } elseif ($action === 'info_requested') {
            $this->accessRequest->update(['status' => 'info_requested']);
            $this->dispatch('toast', type: 'info', message: 'Additional info requested');
        }

        $this->approvalNotes = '';
        $this->accessRequest->refresh()->load(['approvals.approver', 'activities.user']);
    }

    public function respondToInfoRequest(): void
    {
        $this->validate(['infoResponse' => 'required|string']);

        $infoApproval = $this->accessRequest->approvals()
            ->where('status', 'info_requested')
            ->latest('acted_at')
            ->first();

        if (!$infoApproval) {
            $this->dispatch('toast', type: 'error', message: 'No pending info request found');
            return;
        }

        // Save response as a comment for history
        $approverName = $infoApproval->approver?->name ?? 'Approver';
        $this->accessRequest->comments()->create([
            'user_id' => auth()->id(),
            'body' => "[Info Response] Replying to {$approverName}'s request:\n\n{$this->infoResponse}",
            'is_internal' => false,
        ]);

        $infoApproval->update([
            'status' => 'pending',
            'notes' => $infoApproval->notes,
            'acted_at' => null,
        ]);

        $this->accessRequest->update(['status' => 'pending_approval']);

        AccessRequestActivity::create([
            'access_request_id' => $this->accessRequest->id,
            'user_id' => auth()->id(),
            'action' => 'info_responded',
            'description' => 'Responded to info request',
        ]);

        $this->infoResponse = '';
        $this->accessRequest->refresh()->load([
            'approvals.approver', 'activities.user',
            'comments.user', 'comments.attachments', 'comments.parent.user',
        ]);
        $this->dispatch('toast', type: 'success', message: 'Response submitted, approval resumed');
    }

    private function sendFeedbackEmail(string $type, $request, $comment): void
    {
        $commenter = auth()->user();
        $recipients = collect();

        $requester = $request->requester;
        if ($requester && $requester->id !== $commenter->id) {
            $recipients->push($requester->email);
        }

        if ($request->assignee && $request->assignee->id !== $commenter->id) {
            $recipients->push($request->assignee->email);
        }

        $recipients = $recipients->unique()->filter();
        if ($recipients->isEmpty()) {
            return;
        }

        $routeName = match ($type) {
            'ticket' => 'tickets.show',
            'access_request' => 'access-requests.show',
            'change_request' => 'change-requests.show',
        };

        try {
            foreach ($recipients as $email) {
                Mail::to($email)->send(new FeedbackReviewMail(
                    $request, $type, $commenter->name, $comment->body, route($routeName, $request)
                ));
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed to send feedback review email: ' . $e->getMessage());
        }
    }

    // ── Actions ───────────────────────────────────────

    public function markImplemented(): void
    {
        $service = app(AccessRequestService::class);
        $service->markImplemented($this->accessRequest);

        AccessRequestActivity::create([
            'access_request_id' => $this->accessRequest->id,
            'user_id' => auth()->id(),
            'action' => 'implemented',
            'description' => 'Marked as implemented',
        ]);

        $this->accessRequest->refresh()->load(['activities.user']);
        $this->dispatch('toast', type: 'success', message: 'Marked as implemented');
    }

    public function render()
    {
        $user = auth()->user();
        $cannedResponses = CannedResponse::all();

        $canApprove = false;
        if (in_array($this->accessRequest->status, ['pending_approval', 'submitted'])) {
            // Check if user already acted on an approval for this request
            $alreadyActed = $this->accessRequest->approvals()
                ->where('approver_id', $user->id)
                ->whereIn('status', ['approved', 'rejected', 'info_requested'])
                ->exists();

            if (!$alreadyActed) {
                $minPendingLevel = $this->accessRequest->approvals()->where('status', 'pending')->min('level');
                if ($minPendingLevel) {
                    $inWorkflow = $this->accessRequest->approvals()
                        ->where('approver_id', $user->id)
                        ->where('status', 'pending')
                        ->where('level', $minPendingLevel)
                        ->exists();

                    $hasPermission = $user->can('access_requests.approve');
                    $canApprove = $inWorkflow || $hasPermission;
                }
            }
        }

        $needsInfoResponse = false;
        $infoRequestDetail = null;
        if ($this->accessRequest->status === 'info_requested' && $this->accessRequest->requester_id === auth()->id()) {
            $infoRequestDetail = $this->accessRequest->approvals()
                ->where('status', 'info_requested')
                ->with('approver')
                ->latest('acted_at')
                ->first();
            $needsInfoResponse = $infoRequestDetail !== null;
        }

        return view('livewire.access-request.access-request-detail', compact(
            'cannedResponses', 'canApprove', 'needsInfoResponse', 'infoRequestDetail'
        ));
    }
}
