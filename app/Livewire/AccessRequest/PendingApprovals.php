<?php

namespace App\Livewire\AccessRequest;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\AccessRequest;

#[Layout('layouts.master')]
class PendingApprovals extends Component
{
    use WithPagination;

    public function render()
    {
        $requests = AccessRequest::where('status', 'pending_approval')
            ->whereHas('approvals', function ($q) {
                $q->where('approver_id', auth()->id())->where('status', 'pending');
            })
            ->with(['requester', 'system'])
            ->latest()
            ->paginate(15);

        return view('livewire.access-request.pending-approvals', compact('requests'));
    }
}
