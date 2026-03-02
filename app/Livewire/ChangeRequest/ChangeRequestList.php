<?php

namespace App\Livewire\ChangeRequest;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\ChangeRequest;
use App\Models\System;

#[Layout('layouts.master')]
class ChangeRequestList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $typeFilter = '';
    public string $systemFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->sortField = $field;
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'typeFilter', 'systemFilter']);
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $requests = ChangeRequest::with(['requester', 'system'])
            ->when(!$user->canAny(['change_requests.approve', 'change_requests.schedule', 'change_requests.implement']), function ($q) use ($user) {
                $q->where('requester_id', $user->id);
            })
            ->when($this->search, fn ($q) => $q->where(fn ($q2) => $q2->where('request_number', 'like', "%{$this->search}%")->orWhere('title', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->typeFilter, fn ($q) => $q->where('change_type', $this->typeFilter))
            ->when($this->systemFilter, fn ($q) => $q->where('system_id', $this->systemFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        $systems = System::where('is_active', true)->get();

        return view('livewire.change-request.change-request-list', compact('requests', 'systems'));
    }
}
