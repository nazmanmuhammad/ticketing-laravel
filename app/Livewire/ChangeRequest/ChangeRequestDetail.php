<?php

namespace App\Livewire\ChangeRequest;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ChangeRequest;
use App\Actions\ChangeRequest\ApproveChangeRequestAction;
use App\Services\ChangeRequestService;

#[Layout('layouts.master')]
class ChangeRequestDetail extends Component
{
    public ChangeRequest $changeRequest;
    public string $approvalNotes = '';
    public string $scheduledAt = '';
    public string $postReviewNotes = '';
    public string $failReason = '';

    public function mount(ChangeRequest $changeRequest): void
    {
        $this->changeRequest = $changeRequest->load(['requester', 'system', 'relatedTicket', 'approvals.approver', 'attachments', 'activities.user']);
        $this->scheduledAt = $changeRequest->scheduled_at?->format('Y-m-d\TH:i') ?? '';
    }

    public function approve(): void
    {
        $action = app(ApproveChangeRequestAction::class);
        $action->execute($this->changeRequest, auth()->id(), 'approved', $this->approvalNotes ?: null);
        $this->changeRequest->refresh()->load(['approvals.approver', 'activities.user']);
        $this->approvalNotes = '';
        $this->dispatch('toast', type: 'success', message: 'Change request approved');
    }

    public function reject(): void
    {
        $this->validate(['approvalNotes' => 'required|string']);
        $action = app(ApproveChangeRequestAction::class);
        $action->execute($this->changeRequest, auth()->id(), 'rejected', $this->approvalNotes);
        $this->changeRequest->refresh()->load(['approvals.approver', 'activities.user']);
        $this->approvalNotes = '';
        $this->dispatch('toast', type: 'success', message: 'Change request rejected');
    }

    public function requestInfo(): void
    {
        $this->validate(['approvalNotes' => 'required|string']);
        $action = app(ApproveChangeRequestAction::class);
        $action->execute($this->changeRequest, auth()->id(), 'info_requested', $this->approvalNotes);
        $this->changeRequest->refresh()->load(['approvals.approver', 'activities.user']);
        $this->approvalNotes = '';
        $this->dispatch('toast', type: 'info', message: 'Additional info requested');
    }

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
        
        $canApprove = false;
        
        if (in_array($this->changeRequest->status, ['under_review', 'submitted'])) {
            // Check if user is in approval workflow
            $inWorkflow = $this->changeRequest->approvals()
                ->where('approver_id', $user->id)
                ->where('status', 'pending')
                ->exists();
            
            // Check if user has approve permission (can approve even without workflow entry)
            $hasPermission = $user->can('change_requests.approve');
            
            $canApprove = $inWorkflow || $hasPermission;
        }

        return view('livewire.change-request.change-request-detail', compact('canApprove'));
    }
}
