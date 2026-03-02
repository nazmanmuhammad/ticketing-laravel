<?php

namespace App\Livewire\AccessRequest;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\AccessRequest;
use App\Actions\AccessRequest\ApproveAccessRequestAction;
use App\Services\AccessRequestService;

#[Layout('layouts.master')]
class AccessRequestDetail extends Component
{
    public AccessRequest $accessRequest;
    public string $approvalNotes = '';

    public function mount(AccessRequest $accessRequest): void
    {
        $this->accessRequest = $accessRequest->load(['requester', 'system', 'approvals.approver', 'attachments']);
    }

    public function approve(): void
    {
        $action = app(ApproveAccessRequestAction::class);
        $action->execute($this->accessRequest, auth()->id(), 'approved', $this->approvalNotes ?: null);
        $this->accessRequest->refresh()->load(['approvals.approver']);
        $this->approvalNotes = '';
        $this->dispatch('toast', type: 'success', message: 'Request approved');
    }

    public function reject(): void
    {
        $this->validate(['approvalNotes' => 'required|string']);
        $action = app(ApproveAccessRequestAction::class);
        $action->execute($this->accessRequest, auth()->id(), 'rejected', $this->approvalNotes);
        $this->accessRequest->refresh()->load(['approvals.approver']);
        $this->approvalNotes = '';
        $this->dispatch('toast', type: 'success', message: 'Request rejected');
    }

    public function requestInfo(): void
    {
        $this->validate(['approvalNotes' => 'required|string']);
        $action = app(ApproveAccessRequestAction::class);
        $action->execute($this->accessRequest, auth()->id(), 'info_requested', $this->approvalNotes);
        $this->accessRequest->refresh()->load(['approvals.approver']);
        $this->approvalNotes = '';
        $this->dispatch('toast', type: 'info', message: 'Additional info requested');
    }

    public function markImplemented(): void
    {
        $service = app(AccessRequestService::class);
        $service->markImplemented($this->accessRequest);
        $this->accessRequest->refresh();
        $this->dispatch('toast', type: 'success', message: 'Marked as implemented');
    }

    public function render()
    {
        $user = auth()->user();
        
        $canApprove = false;
        
        if ($this->accessRequest->status === 'pending_approval') {
            // Check if user is in approval workflow for current level
            $inWorkflow = $this->accessRequest->approvals()
                ->where('approver_id', $user->id)
                ->where('status', 'pending')
                ->exists();
            
            // Check if user has approve permission (can approve even without workflow entry)
            $hasPermission = $user->can('access_requests.approve');
            
            $canApprove = $inWorkflow || $hasPermission;
        }

        return view('livewire.access-request.access-request-detail', compact('canApprove'));
    }
}
