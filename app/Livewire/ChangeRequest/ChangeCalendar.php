<?php

namespace App\Livewire\ChangeRequest;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\ChangeRequest;
use Carbon\Carbon;

#[Layout('layouts.master')]
class ChangeCalendar extends Component
{
    public string $month;
    public int $year;

    public function mount(): void
    {
        $this->month = now()->format('m');
        $this->year = now()->year;
    }

    public function previousMonth(): void
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->subMonth();
        $this->month = $date->format('m');
        $this->year = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->addMonth();
        $this->month = $date->format('m');
        $this->year = $date->year;
    }

    public function render()
    {
        $start = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $changes = ChangeRequest::whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [$start, $end])
            ->whereNotIn('status', ['draft', 'closed', 'failed'])
            ->with('system')
            ->get()
            ->groupBy(fn ($cr) => $cr->scheduled_at->format('Y-m-d'));

        $days = [];
        $current = $start->copy()->startOfWeek(Carbon::MONDAY);
        $endOfGrid = $end->copy()->endOfWeek(Carbon::SUNDAY);

        while ($current <= $endOfGrid) {
            $dateKey = $current->format('Y-m-d');
            $days[] = [
                'date' => $current->copy(),
                'isCurrentMonth' => $current->month == $this->month,
                'isToday' => $current->isToday(),
                'changes' => $changes->get($dateKey, collect()),
            ];
            $current->addDay();
        }

        $monthLabel = Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');

        return view('livewire.change-request.change-calendar', compact('days', 'monthLabel'));
    }
}
