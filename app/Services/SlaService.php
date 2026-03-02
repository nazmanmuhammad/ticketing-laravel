<?php

namespace App\Services;

use App\Models\SlaSetting;
use App\Models\Ticket;
use Carbon\Carbon;

class SlaService
{
    public function calculateDueDate(string $priority): ?Carbon
    {
        $sla = SlaSetting::getForPriority($priority);
        return $sla ? now()->addHours($sla->resolution_hours) : null;
    }

    public function getSlaStatus(Ticket $ticket): string
    {
        if (!$ticket->sla_due_at) return 'none';
        if (in_array($ticket->status, ['resolved', 'closed'])) return 'met';

        $remaining = now()->diffInMinutes($ticket->sla_due_at, false);
        $total = $ticket->created_at->diffInMinutes($ticket->sla_due_at);

        if ($remaining <= 0) return 'breached';
        if ($total > 0 && ($remaining / $total) < 0.25) return 'warning';
        return 'on_track';
    }

    public function checkSlaBreaches()
    {
        return Ticket::whereNotNull('sla_due_at')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->where('sla_due_at', '<=', now())
            ->with(['assignee', 'category'])
            ->latest('sla_due_at')
            ->take(10)
            ->get();
    }

    public function getSlaColor(string $status): string
    {
        return match ($status) {
            'on_track' => 'text-green-600 bg-green-50',
            'warning' => 'text-yellow-600 bg-yellow-50',
            'breached' => 'text-red-600 bg-red-50 animate-pulse',
            'met' => 'text-blue-600 bg-blue-50',
            default => 'text-gray-500 bg-gray-50',
        };
    }
}
