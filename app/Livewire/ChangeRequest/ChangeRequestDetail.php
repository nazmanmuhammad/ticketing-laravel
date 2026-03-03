<?php

namespace App\Livewire\ChangeRequest;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\ChangeRequest;
use App\Models\ChangeRequestActivity;
use App\Models\ChangeRequestCommentAttachment;
use App\Models\CannedResponse;
use App\Actions\ChangeRequest\ApproveChangeRequestAction;
use App\Services\ChangeRequestService;
use App\Mail\ApprovalRequestMail;
use App\Mail\FeedbackReviewMail;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.master')]
class ChangeRequestDetail extends Component
{
    use WithFileUploads;

    public ChangeRequest $changeRequest;
    public string $approvalNotes = '';
    public string $infoResponse = '';
    public string $scheduledAt = '';
    public string $postReviewNotes = '';
    public string $failReason = '';
    public string $commentBody = '';
    public bool $isInternal = false;
    public $commentAttachments = [];
    public ?int $replyToId = null;
    public ?string $replyToUser = null;
    public ?string $replyToBody = null;

    public function mount(ChangeRequest $changeRequest): void
    {
        $this->changeRequest = $changeRequest->load([
            'requester', 'system', 'relatedTicket', 'assignee', 'team',
            'approvals.approver', 'attachments',
            'comments.user', 'comments.attachments', 'comments.parent.user',
            'activities.user',
        ]);
        $this->scheduledAt = $changeRequest->scheduled_at?->format('Y-m-d\TH:i') ?? '';
    }

    // ── Comments ──────────────────────────────────────

    public function addComment(): void
    {
        $rules = ['commentBody' => 'required|string'];
        if (!empty($this->commentAttachments)) {
            $rules['commentAttachments.*'] = 'file|max:10240';
        }
        $this->validate($rules);

        $comment = $this->changeRequest->comments()->create([
            'user_id' => auth()->id(),
            'body' => $this->commentBody,
            'is_internal' => $this->isInternal,
            'parent_id' => $this->replyToId,
        ]);

        if (!empty($this->commentAttachments)) {
            foreach ($this->commentAttachments as $file) {
                $path = $file->store('cr-comment-attachments', 'public');
                ChangeRequestCommentAttachment::create([
                    'comment_id' => $comment->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        ChangeRequestActivity::create([
            'change_request_id' => $this->changeRequest->id,
            'user_id' => auth()->id(),
            'action' => 'comment',
            'description' => ($this->isInternal ? 'Added internal note' : 'Added comment'),
        ]);

        // Send feedback review email for non-internal comments
        if (!$this->isInternal) {
            $this->sendFeedbackEmail('change_request', $this->changeRequest, $comment);
        }

        $this->commentBody = '';
        $this->isInternal = false;
        $this->commentAttachments = [];
        $this->replyToId = null;
        $this->replyToUser = null;
        $this->replyToBody = null;
        $this->changeRequest->refresh()->load([
            'comments.user', 'comments.attachments', 'comments.parent.user', 'activities.user',
        ]);
        $this->dispatch('toast', type: 'success', message: 'Comment added');
    }

    public function setReplyTo(int $commentId): void
    {
        $comment = $this->changeRequest->comments->find($commentId);
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
        $approval = $this->changeRequest->approvals()
            ->where('approver_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$approval) {
            if ($user->can('change_requests.approve')) {
                $maxLevel = $this->changeRequest->approvals()->max('level') ?? 0;
                $approval = $this->changeRequest->approvals()->create([
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

        ChangeRequestActivity::create([
            'change_request_id' => $this->changeRequest->id,
            'user_id' => $user->id,
            'action' => $action,
            'description' => "Change request {$action}" . ($this->approvalNotes ? ": {$this->approvalNotes}" : ''),
        ]);

        // If info_requested, save notes as a comment
        if ($action === 'info_requested' && $this->approvalNotes) {
            $this->changeRequest->comments()->create([
                'user_id' => $user->id,
                'body' => "[Info Request] {$this->approvalNotes}",
                'is_internal' => false,
            ]);
        }

        if ($action === 'approved') {
            $pendingAtSameOrLower = $this->changeRequest->approvals()
                ->where('level', '<=', $approval->level)
                ->where('status', 'pending')
                ->count();

            if ($pendingAtSameOrLower === 0) {
                $nextLevelApprovals = $this->changeRequest->approvals()
                    ->where('level', '>', $approval->level)
                    ->where('status', 'pending')
                    ->get();

                if ($nextLevelApprovals->isEmpty()) {
                    $this->changeRequest->update(['status' => 'approved']);
                    ChangeRequestActivity::create([
                        'change_request_id' => $this->changeRequest->id,
                        'user_id' => $user->id,
                        'action' => 'fully_approved',
                        'description' => 'All approvals completed, change request is now approved',
                    ]);
                } else {
                    $nextMinLevel = $nextLevelApprovals->min('level');
                    $nextLevelOnly = $nextLevelApprovals->where('level', $nextMinLevel);
                    foreach ($nextLevelOnly as $nextApproval) {
                        $nextApproval->load('approver');
                        if ($nextApproval->approver) {
                            try {
                                Mail::to($nextApproval->approver->email)->send(new ApprovalRequestMail(
                                    $this->changeRequest, 'change_request', $nextApproval->approver->name, $nextMinLevel,
                                    route('change-requests.show', $this->changeRequest)
                                ));
                            } catch (\Throwable $e) {
                                \Log::warning('Failed to send CR next level approval email: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
            $this->dispatch('toast', type: 'success', message: 'Change request approved');
        } elseif ($action === 'rejected') {
            $this->changeRequest->update(['status' => 'rejected']);
            $this->dispatch('toast', type: 'error', message: 'Change request rejected');
        } elseif ($action === 'info_requested') {
            $this->changeRequest->update(['status' => 'info_requested']);
            $this->dispatch('toast', type: 'info', message: 'Additional info requested');
        }

        $this->approvalNotes = '';
        $this->changeRequest->refresh()->load([
            'approvals.approver', 'activities.user',
            'comments.user', 'comments.attachments', 'comments.parent.user',
        ]);
    }

    public function respondToInfoRequest(): void
    {
        $this->validate(['infoResponse' => 'required|string']);

        $infoApproval = $this->changeRequest->approvals()
            ->where('status', 'info_requested')
            ->latest('acted_at')
            ->first();

        if (!$infoApproval) {
            $this->dispatch('toast', type: 'error', message: 'No pending info request found');
            return;
        }

        $approverName = $infoApproval->approver?->name ?? 'Approver';
        $this->changeRequest->comments()->create([
            'user_id' => auth()->id(),
            'body' => "[Info Response] Replying to {$approverName}'s request:\n\n{$this->infoResponse}",
            'is_internal' => false,
        ]);

        $infoApproval->update([
            'status' => 'pending',
            'notes' => $infoApproval->notes,
            'acted_at' => null,
        ]);

        $this->changeRequest->update(['status' => 'under_review']);

        ChangeRequestActivity::create([
            'change_request_id' => $this->changeRequest->id,
            'user_id' => auth()->id(),
            'action' => 'info_responded',
            'description' => 'Responded to info request',
        ]);

        $this->infoResponse = '';
        $this->changeRequest->refresh()->load([
            'approvals.approver', 'activities.user',
            'comments.user', 'comments.attachments', 'comments.parent.user',
        ]);
        $this->dispatch('toast', type: 'success', message: 'Response submitted, approval resumed');
    }

    // ── Actions ───────────────────────────────────────

    public function schedule(): void
    {
        $this->validate(['scheduledAt' => 'required|date|after:now']);
        $service = app(ChangeRequestService::class);
        $service->schedule($this->changeRequest, $this->scheduledAt, auth()->id());
        $this->changeRequest->refresh()->load(['activities.user']);
        $this->dispatch('toast', type: 'success', message: 'Change request scheduled');
    }

    public function markImplemented(): void
    {
        $service = app(ChangeRequestService::class);
        $service->markImplemented($this->changeRequest, auth()->id());
        $this->changeRequest->refresh()->load(['activities.user']);
        $this->dispatch('toast', type: 'success', message: 'Marked as implemented');
    }

    public function markFailed(): void
    {
        $this->validate(['failReason' => 'required|string']);
        $service = app(ChangeRequestService::class);
        $service->markFailed($this->changeRequest, auth()->id(), $this->failReason);
        $this->changeRequest->refresh()->load(['activities.user']);
        $this->failReason = '';
        $this->dispatch('toast', type: 'error', message: 'Marked as failed');
    }

    public function close(): void
    {
        $service = app(ChangeRequestService::class);
        $service->close($this->changeRequest, auth()->id(), $this->postReviewNotes ?: null);
        $this->changeRequest->refresh()->load(['activities.user']);
        $this->dispatch('toast', type: 'success', message: 'Change request closed');
    }

    public function render()
    {
        $user = auth()->user();
        $cannedResponses = CannedResponse::all();

        $canApprove = false;
        if (in_array($this->changeRequest->status, ['under_review', 'submitted', 'pending_approval'])) {
            $alreadyActed = $this->changeRequest->approvals()
                ->where('approver_id', $user->id)
                ->whereIn('status', ['approved', 'rejected', 'info_requested'])
                ->exists();

            if (!$alreadyActed) {
                $minPendingLevel = $this->changeRequest->approvals()->where('status', 'pending')->min('level');
                if ($minPendingLevel) {
                    $inWorkflow = $this->changeRequest->approvals()
                        ->where('approver_id', $user->id)
                        ->where('status', 'pending')
                        ->where('level', $minPendingLevel)
                        ->exists();

                    $hasPermission = $user->can('change_requests.approve');
                    $canApprove = $inWorkflow || $hasPermission;
                }
            }
        }

        $needsInfoResponse = false;
        $infoRequestDetail = null;
        if ($this->changeRequest->status === 'info_requested' && $this->changeRequest->requester_id === auth()->id()) {
            $infoRequestDetail = $this->changeRequest->approvals()
                ->where('status', 'info_requested')
                ->with('approver')
                ->latest('acted_at')
                ->first();
            $needsInfoResponse = $infoRequestDetail !== null;
        }

        return view('livewire.change-request.change-request-detail', compact(
            'cannedResponses', 'canApprove', 'needsInfoResponse', 'infoRequestDetail'
        ));
    }
}
