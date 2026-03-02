<?php

namespace App\Livewire\Task;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Ticket;
use App\Models\AccessRequest;
use App\Models\ChangeRequest;

#[Layout('layouts.master')]
class TaskList extends Component
{
    use WithPagination;

    public string $tabFilter = 'all';

    public function updatingTabFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $userId = $user->id;
        $teamIds = $user->teams()->pluck('teams.id')->toArray();

        // Tickets assigned to me or my teams
        $assignedTickets = collect();
        if ($user->can('tickets.assign') || $user->can('tickets.view')) {
            $assignedTickets = Ticket::with(['requester', 'category'])
                ->whereNotIn('status', ['resolved', 'closed'])
                ->where(function ($q) use ($userId, $teamIds) {
                    $q->where('assigned_to', $userId);
                    if (!empty($teamIds)) {
                        $q->orWhereIn('assigned_team_id', $teamIds);
                    }
                })
                ->orderByDesc('created_at')
                ->get();
        }

        // Access requests pending my approval
        $pendingAccessApprovals = collect();
        if ($user->can('access_requests.approve')) {
            $pendingAccessApprovals = AccessRequest::with(['requester', 'system'])
                ->where('status', 'pending_approval')
                ->whereHas('approvals', function ($q) use ($userId) {
                    $q->where('approver_id', $userId)->where('status', 'pending');
                })
                ->orderByDesc('created_at')
                ->get();
        }

        // Change requests pending my approval
        $pendingChangeApprovals = collect();
        if ($user->can('change_requests.approve')) {
            $pendingChangeApprovals = ChangeRequest::with(['requester', 'system'])
                ->whereIn('status', ['under_review', 'submitted'])
                ->whereHas('approvals', function ($q) use ($userId) {
                    $q->where('approver_id', $userId)->where('status', 'pending');
                })
                ->orderByDesc('created_at')
                ->get();
        }

        return view('livewire.task.task-list', compact(
            'assignedTickets',
            'pendingAccessApprovals',
            'pendingChangeApprovals'
        ));
    }
}
