<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Ticket;
use App\Models\AccessRequest;
use App\Models\ChangeRequest;
use App\Models\TicketActivity;
use App\Services\SlaService;
use Carbon\Carbon;

#[Layout('layouts.master')]
class Dashboard extends Component
{
    public function render()
    {
        $slaService = app(SlaService::class);

        $ticketStats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];

        $pendingApprovals = AccessRequest::where('status', 'pending_approval')->count()
            + ChangeRequest::whereIn('status', ['under_review', 'submitted'])->count();

        $slaBreaches = $slaService->checkSlaBreaches();

        $recentActivities = TicketActivity::with(['ticket', 'user'])
            ->latest()
            ->take(10)
            ->get();

        // Chart data: tickets last 30 days
        $ticketTrend = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $ticketTrend[] = [
                'date' => Carbon::now()->subDays($i)->format('M d'),
                'count' => Ticket::whereDate('created_at', $date)->count(),
            ];
        }

        $ticketsByPriority = [
            'low' => Ticket::where('priority', 'low')->whereNotIn('status', ['closed'])->count(),
            'medium' => Ticket::where('priority', 'medium')->whereNotIn('status', ['closed'])->count(),
            'high' => Ticket::where('priority', 'high')->whereNotIn('status', ['closed'])->count(),
            'critical' => Ticket::where('priority', 'critical')->whereNotIn('status', ['closed'])->count(),
        ];

        $ticketsByCategory = Ticket::selectRaw('categories.name, count(*) as total')
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->whereNull('categories.parent_id')
            ->groupBy('categories.name')
            ->pluck('total', 'name')
            ->toArray();

        return view('livewire.dashboard', compact(
            'ticketStats', 'pendingApprovals', 'slaBreaches',
            'recentActivities', 'ticketTrend', 'ticketsByPriority', 'ticketsByCategory'
        ));
    }
}
