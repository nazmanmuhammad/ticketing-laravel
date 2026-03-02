<?php

namespace App\Livewire\Report;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Ticket;
use App\Models\AccessRequest;
use App\Models\ChangeRequest;
use Carbon\Carbon;

#[Layout('layouts.master')]
class ReportDashboard extends Component
{
    public string $period = '30';

    public function render()
    {
        $from = now()->subDays((int) $this->period);

        $ticketStats = [
            'total' => Ticket::where('created_at', '>=', $from)->count(),
            'resolved' => Ticket::where('created_at', '>=', $from)->whereIn('status', ['resolved', 'closed'])->count(),
            'avg_resolution' => Ticket::where('created_at', '>=', $from)->whereNotNull('closed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, closed_at)) as avg_hours')
                ->value('avg_hours') ?? 0,
            'sla_met' => Ticket::where('created_at', '>=', $from)
                ->whereNotNull('sla_due_at')
                ->whereNotNull('closed_at')
                ->whereColumn('closed_at', '<=', 'sla_due_at')
                ->count(),
            'sla_total' => Ticket::where('created_at', '>=', $from)->whereNotNull('sla_due_at')->whereNotNull('closed_at')->count(),
        ];

        $accessStats = [
            'total' => AccessRequest::where('created_at', '>=', $from)->count(),
            'approved' => AccessRequest::where('created_at', '>=', $from)->where('status', 'approved')->count(),
            'rejected' => AccessRequest::where('created_at', '>=', $from)->where('status', 'rejected')->count(),
            'pending' => AccessRequest::where('created_at', '>=', $from)->where('status', 'pending_approval')->count(),
        ];

        $changeStats = [
            'total' => ChangeRequest::where('created_at', '>=', $from)->count(),
            'implemented' => ChangeRequest::where('created_at', '>=', $from)->whereIn('status', ['implemented', 'closed'])->count(),
            'failed' => ChangeRequest::where('created_at', '>=', $from)->where('status', 'failed')->count(),
        ];

        return view('livewire.report.report-dashboard', compact('ticketStats', 'accessStats', 'changeStats'));
    }
}
