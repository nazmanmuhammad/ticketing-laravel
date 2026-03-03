<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketComment;
use App\Models\SlaSetting;

class TicketService
{
    public function changeStatus(Ticket $ticket, string $status, int $userId): Ticket
    {
        $oldStatus = $ticket->status;
        $ticket->update(['status' => $status]);

        if ($status === 'closed') {
            $ticket->update(['closed_at' => now()]);
        }

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'action' => 'status_changed',
            'description' => "Status changed from {$oldStatus} to {$status}",
        ]);

        return $ticket;
    }

    public function changePriority(Ticket $ticket, string $priority, int $userId): Ticket
    {
        $oldPriority = $ticket->priority;
        $ticket->update(['priority' => $priority]);

        $sla = SlaSetting::getForPriority($priority);
        if ($sla) {
            $ticket->update(['sla_due_at' => now()->addHours($sla->resolution_hours)]);
        }

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'action' => 'priority_changed',
            'description' => "Priority changed from {$oldPriority} to {$priority}",
        ]);

        return $ticket;
    }

    public function addComment(Ticket $ticket, int $userId, string $body, bool $isInternal = false, ?int $parentId = null): TicketComment
    {
        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'body' => $body,
            'is_internal' => $isInternal,
            'parent_id' => $parentId,
        ]);

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'action' => $isInternal ? 'internal_note' : 'replied',
            'description' => $isInternal ? 'Added internal note' : ($parentId ? 'Replied to a comment' : 'Added public reply'),
        ]);

        return $comment;
    }

    public function autoCloseResolved(int $days): int
    {
        $tickets = Ticket::where('status', 'resolved')
            ->where('updated_at', '<=', now()->subDays($days))
            ->get();

        foreach ($tickets as $ticket) {
            $ticket->update(['status' => 'closed', 'closed_at' => now()]);
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->assigned_to ?? $ticket->requester_id,
                'action' => 'auto_closed',
                'description' => "Auto-closed after {$days} days of inactivity",
            ]);
        }

        return $tickets->count();
    }

    public function checkSlaBreaches(): \Illuminate\Support\Collection
    {
        return Ticket::whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', now())
            ->whereNotIn('status', ['resolved', 'closed'])
            ->get();
    }
}
