<?php

namespace App\Livewire\Report;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\AccessRequest;

#[Layout('layouts.master')]
class AccessReport extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(): void
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = AccessRequest::query()
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo));

        $byStatus = (clone $query)->selectRaw('status as label, count(*) as total')->groupBy('status')->pluck('total', 'label')->toArray();
        $bySystem = (clone $query)->join('systems', 'access_requests.system_id', '=', 'systems.id')
            ->selectRaw('systems.name as label, count(*) as total')->groupBy('systems.name')->pluck('total', 'label')->toArray();
        $byType = (clone $query)->selectRaw('access_type as label, count(*) as total')->groupBy('access_type')->pluck('total', 'label')->toArray();
        $total = (clone $query)->count();

        return view('livewire.report.access-report', compact('byStatus', 'bySystem', 'byType', 'total'));
    }
}
