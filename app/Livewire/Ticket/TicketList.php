<?php

namespace App\Livewire\Ticket;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;

#[Layout('layouts.master')]
class TicketList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $priorityFilter = '';
    public string $categoryFilter = '';
    public string $assignedFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'priorityFilter', 'categoryFilter', 'assignedFilter', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $teamIds = $user->teams()->pluck('teams.id')->toArray();

        $tickets = Ticket::with(['requester', 'assignee', 'category'])
            ->when(!$user->canAny(['tickets.assign', 'tickets.edit', 'tickets.delete']), function ($q) use ($user, $teamIds) {
                $q->where(function ($q2) use ($user, $teamIds) {
                    $q2->where('requester_id', $user->id)
                        ->orWhere('assigned_to', $user->id);
                    if (!empty($teamIds)) {
                        $q2->orWhereIn('assigned_team_id', $teamIds);
                    }
                });
            })
            ->when($this->search, fn ($q) => $q->where(fn ($q2) => $q2->where('title', 'like', "%{$this->search}%")->orWhere('ticket_number', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->priorityFilter, fn ($q) => $q->where('priority', $this->priorityFilter))
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->assignedFilter, fn ($q) => $q->where('assigned_to', $this->assignedFilter))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        $categories = Category::parents()->get();
        $agents = User::role(['Agent', 'Admin', 'Super Admin'])->get();

        return view('livewire.ticket.ticket-list', compact('tickets', 'categories', 'agents'));
    }
}
