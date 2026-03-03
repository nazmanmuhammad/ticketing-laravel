<?php

namespace App\Livewire\Ticket;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Ticket;
use App\Models\CannedResponse;
use App\Models\User;
use App\Models\Team;
use App\Models\TicketApproval;
use App\Models\TicketActivity;
use App\Models\TicketCommentAttachment;
use App\Services\TicketService;
use App\Actions\Ticket\AssignTicketAction;
use App\Actions\Ticket\CloseTicketAction;
use App\Mail\ApprovalRequestMail;
use App\Mail\FeedbackReviewMail;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.master')]
class TicketDetail extends Component
{
    use WithFileUploads;

    public Ticket $ticket;
    public string $commentBody = '';
    public bool $isInternal = false;
    public ?int $assignToUser = null;
    public ?int $assignToTeam = null;
    public string $newStatus = '';
    public string $newPriority = '';
    public bool $showAssignModal = false;
    public string $approvalNotes = '';
    public string $infoResponse = '';
    public $commentAttachments = [];
    public ?int $replyToId = null;
    public ?string $replyToUser = null;
    public ?string $replyToBody = null;

    public function mount(Ticket $ticket): void
    {
        $this->ticket = $ticket->load(['requester', 'assignee', 'team', 'category', 'subCategory', 'comments.user', 'comments.attachments', 'comments.parent.user', 'attachments', 'activities.user', 'approvals.approver']);
        $this->newStatus = $ticket->status;
        $this->newPriority = $ticket->priority;
        $this->assignToUser = $ticket->assigned_to;
        $this->assignToTeam = $ticket->assigned_team_id;
    }

    public function addComment(): void
    {
        $rules = ['commentBody' => 'required|string'];
        if (!empty($this->commentAttachments)) {
            $rules['commentAttachments.*'] = 'file|max:10240';
        }
        $this->validate($rules);

        $service = app(TicketService::class);
        $comment = $service->addComment($this->ticket, auth()->id(), $this->commentBody, $this->isInternal, $this->replyToId);

        // Handle attachments
        if (!empty($this->commentAttachments)) {
            foreach ($this->commentAttachments as $file) {
                $path = $file->store('comment-attachments', 'public');
                TicketCommentAttachment::create([
                    'ticket_comment_id' => $comment->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        // Send feedback review email for non-internal comments
        if (!$this->isInternal) {
            $this->sendFeedbackEmail('ticket', $this->ticket, $comment);
        }

        $this->commentBody = '';
        $this->isInternal = false;
        $this->commentAttachments = [];
        $this->replyToId = null;
        $this->replyToUser = null;
        $this->replyToBody = null;
        $this->ticket->refresh();
        $this->ticket->load(['comments.user', 'comments.attachments', 'comments.parent.user', 'activities.user']);
        $this->dispatch('toast', type: 'success', message: 'Comment added');
    }

    public function setReplyTo(int $commentId): void
    {
        $comment = $this->ticket->comments->find($commentId);
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

    public function updateStatus(): void
    {
        if ($this->newStatus === $this->ticket->status) return;
        if (!$this->isAssignedToMe()) {
            $this->newStatus = $this->ticket->status;
            $this->dispatch('toast', type: 'error', message: 'Only assigned agent/team member can update status');
            return;
        }

        $service = app(TicketService::class);
        $service->changeStatus($this->ticket, $this->newStatus, auth()->id());
        $this->ticket->refresh();
        $this->dispatch('toast', type: 'success', message: 'Status updated');
    }

    public function updatePriority(): void
    {
        if ($this->newPriority === $this->ticket->priority) return;
        if (!$this->isAssignedToMe()) {
            $this->newPriority = $this->ticket->priority;
            $this->dispatch('toast', type: 'error', message: 'Only assigned agent/team member can update priority');
            return;
        }

        $service = app(TicketService::class);
        $service->changePriority($this->ticket, $this->newPriority, auth()->id());
        $this->ticket->refresh();
        $this->dispatch('toast', type: 'success', message: 'Priority updated');
    }

    public function assignTicket(): void
    {
        if (!$this->isAssignedToMe()) {
            $this->dispatch('toast', type: 'error', message: 'Only assigned agent/team member can reassign');
            $this->showAssignModal = false;
            return;
        }

        $action = app(AssignTicketAction::class);
        $action->execute($this->ticket, $this->assignToUser, $this->assignToTeam, auth()->id());
        $this->ticket->refresh();
        $this->ticket->load(['assignee', 'team']);
        $this->showAssignModal = false;
        $this->dispatch('toast', type: 'success', message: 'Ticket assigned');
    }

    public function closeTicket(): void
    {
        // Check if ticket has approvals
        if ($this->ticket->approvals()->exists()) {
            // Check if all approvals are completed (approved or rejected)
            $pendingApprovals = $this->ticket->approvals()
                ->whereIn('status', ['pending', 'info_requested'])
                ->count();
            
            if ($pendingApprovals > 0) {
                $this->dispatch('toast', type: 'error', message: 'Cannot close ticket. All approvals must be completed first.');
                return;
            }
        }

        $action = app(CloseTicketAction::class);
        $action->execute($this->ticket, auth()->id());
        $this->ticket->refresh();
        $this->newStatus = 'closed';
        $this->dispatch('toast', type: 'success', message: 'Ticket closed');
    }

    public function approveTicket(): void
    {
        $this->handleTicketApproval('approved');
    }

    public function rejectTicket(): void
    {
        $this->validate(['approvalNotes' => 'required|string']);
        $this->handleTicketApproval('rejected');
    }

    public function requestTicketInfo(): void
    {
        $this->validate(['approvalNotes' => 'required|string']);
        $this->handleTicketApproval('info_requested');
    }

    private function handleTicketApproval(string $action): void
    {
        $user = auth()->user();
        $approval = $this->ticket->approvals()
            ->where('approver_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$approval) {
            $this->dispatch('toast', type: 'error', message: 'You are not a pending approver for this ticket');
            return;
        }

        $approval->update([
            'status' => $action,
            'notes' => $this->approvalNotes ?: null,
            'acted_at' => now(),
        ]);

        TicketActivity::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => $user->id,
            'action' => $action,
            'description' => "Ticket {$action}" . ($this->approvalNotes ? ": {$this->approvalNotes}" : ''),
        ]);

        if ($action === 'approved') {
            $pendingAtSameOrLower = $this->ticket->approvals()
                ->where('level', '<=', $approval->level)
                ->where('status', 'pending')
                ->count();

            if ($pendingAtSameOrLower === 0) {
                $nextLevelApprovals = $this->ticket->approvals()
                    ->where('level', '>', $approval->level)
                    ->where('status', 'pending')
                    ->get();

                if ($nextLevelApprovals->isEmpty()) {
                    $this->ticket->update(['status' => 'open']);
                    TicketActivity::create([
                        'ticket_id' => $this->ticket->id,
                        'user_id' => $user->id,
                        'action' => 'fully_approved',
                        'description' => 'All approvals completed, ticket is now open',
                    ]);
                } else {
                    // Send email to next level approvers
                    $nextMinLevel = $nextLevelApprovals->min('level');
                    $nextLevelOnly = $nextLevelApprovals->where('level', $nextMinLevel);
                    foreach ($nextLevelOnly as $nextApproval) {
                        $nextApproval->load('approver');
                        if ($nextApproval->approver) {
                            try {
                                Mail::to($nextApproval->approver->email)->send(new ApprovalRequestMail(
                                    $this->ticket, 'ticket', $nextApproval->approver->name, $nextMinLevel,
                                    route('tickets.show', $this->ticket)
                                ));
                            } catch (\Throwable $e) {
                                \Log::warning('Failed to send next level approval email: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
            $this->dispatch('toast', type: 'success', message: 'Ticket approved');
        } elseif ($action === 'rejected') {
            $this->ticket->update(['status' => 'rejected']);
            $this->dispatch('toast', type: 'error', message: 'Ticket rejected');
        } elseif ($action === 'info_requested') {
            $this->ticket->update(['status' => 'info_requested']);
            $this->dispatch('toast', type: 'info', message: 'Additional info requested');
        }

        $this->approvalNotes = '';
        $this->ticket->refresh();
        $this->ticket->load(['approvals.approver', 'activities.user']);
        $this->newStatus = $this->ticket->status;
    }

    public function respondToInfoRequest(): void
    {
        $this->validate(['infoResponse' => 'required|string']);

        $infoApproval = $this->ticket->approvals()
            ->where('status', 'info_requested')
            ->latest('acted_at')
            ->first();

        if (!$infoApproval) {
            $this->dispatch('toast', type: 'error', message: 'No pending info request found');
            return;
        }

        // Save response as a comment for history
        $approverName = $infoApproval->approver?->name ?? 'Approver';
        $commentBody = "[Info Response] Replying to {$approverName}'s request:\n\n{$this->infoResponse}";
        $service = app(TicketService::class);
        $service->addComment($this->ticket, auth()->id(), $commentBody, false);

        // Reset approval back to pending
        $infoApproval->update([
            'status' => 'pending',
            'notes' => $infoApproval->notes,
            'acted_at' => null,
        ]);

        // Set ticket status back to pending_approval
        $this->ticket->update(['status' => 'pending_approval']);

        TicketActivity::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => auth()->id(),
            'action' => 'info_responded',
            'description' => 'Responded to info request',
        ]);

        $this->infoResponse = '';
        $this->ticket->refresh();
        $this->ticket->load(['approvals.approver', 'activities.user', 'comments.user', 'comments.attachments', 'comments.parent.user']);
        $this->newStatus = $this->ticket->status;
        $this->dispatch('toast', type: 'success', message: 'Response submitted, approval resumed');
    }

    private function sendFeedbackEmail(string $type, $request, $comment): void
    {
        $commenter = auth()->user();
        $recipients = collect();

        // Send to requester if commenter is not the requester
        $requester = $request->requester;
        if ($requester && $requester->id !== $commenter->id) {
            $recipients->push($requester->email);
        }

        // Send to assignee if commenter is not the assignee
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
            \Log::warning("Failed to send feedback review email: " . $e->getMessage());
        }
    }

    private function isAssignedToMe(): bool
    {
        $user = auth()->user();
        $teamIds = $user->teams()->pluck('teams.id')->toArray();
        return $this->ticket->assigned_to === $user->id
            || ($this->ticket->assigned_team_id && in_array($this->ticket->assigned_team_id, $teamIds));
    }

    public function render()
    {
        $cannedResponses = CannedResponse::all();
        $agents = User::role(['Agent', 'Admin', 'Super Admin'])->get();
        $teams = Team::where('is_active', true)->get();
        
        $canManage = $this->isAssignedToMe();

        $canApprove = false;
        if ($this->ticket->status === 'pending_approval') {
            $minPendingLevel = $this->ticket->approvals()->where('status', 'pending')->min('level');
            if ($minPendingLevel) {
                $canApprove = $this->ticket->approvals()
                    ->where('approver_id', auth()->id())
                    ->where('status', 'pending')
                    ->where('level', $minPendingLevel)
                    ->exists();
            }
        }

        // Check if requester needs to respond to info request
        $needsInfoResponse = false;
        $infoRequestDetail = null;
        if ($this->ticket->status === 'info_requested' && $this->ticket->requester_id === auth()->id()) {
            $infoRequestDetail = $this->ticket->approvals()
                ->where('status', 'info_requested')
                ->with('approver')
                ->latest('acted_at')
                ->first();
            $needsInfoResponse = $infoRequestDetail !== null;
        }

        return view('livewire.ticket.ticket-detail', compact('cannedResponses', 'agents', 'teams', 'canManage', 'canApprove', 'needsInfoResponse', 'infoRequestDetail'));
    }
}
