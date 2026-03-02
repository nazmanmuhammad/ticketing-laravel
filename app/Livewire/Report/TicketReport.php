<?php

namespace App\Livewire\Report;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;

#[Layout('layouts.master')]
class TicketReport extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $groupBy = 'status';

    public function mount(): void
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = Ticket::query()
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo));

        $groupedData = match ($this->groupBy) {
            'status' => (clone $query)->selectRaw('status as label, count(*) as total')->groupBy('status')->pluck('total', 'label')->toArray(),
            'priority' => (clone $query)->selectRaw('priority as label, count(*) as total')->groupBy('priority')->pluck('total', 'label')->toArray(),
            'category' => (clone $query)->join('categories', 'tickets.category_id', '=', 'categories.id')
                ->selectRaw('categories.name as label, count(*) as total')->groupBy('categories.name')->pluck('total', 'label')->toArray(),
            'agent' => (clone $query)->join('users', 'tickets.assigned_to', '=', 'users.id')
                ->selectRaw('users.name as label, count(*) as total')->groupBy('users.name')->pluck('total', 'label')->toArray(),
            default => [],
        };

        $totalTickets = (clone $query)->count();

        $trend = [];
        $start = Carbon::parse($this->dateFrom);
        $end = Carbon::parse($this->dateTo);
        $diff = $start->diffInDays($end);
        for ($i = 0; $i <= min($diff, 60); $i++) {
            $date = $start->copy()->addDays($i);
            $trend[] = [
                'date' => $date->format('M d'),
                'count' => Ticket::whereDate('created_at', $date->format('Y-m-d'))->count(),
            ];
        }

        return view('livewire.report.ticket-report', compact('groupedData', 'totalTickets', 'trend'));
    }
}
