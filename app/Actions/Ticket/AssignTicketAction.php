<?php

namespace App\Actions\Ticket;

use App\Models\Ticket;
use App\Models\TicketActivity;

class AssignTicketAction
{
    public function execute(Ticket $ticket, ?int $userId, ?int $teamId, int $assignedBy): Ticket
    {
        $changes = [];
        if ($userId && $ticket->assigned_to !== $userId) {
            $changes[] = 'assigned to user #' . $userId;
            $ticket->assigned_to = $userId;
        }
        if ($teamId && $ticket->assigned_team_id !== $teamId) {
            $changes[] = 'assigned to team #' . $teamId;
            $ticket->assigned_team_id = $teamId;
        }
        if ($ticket->status === 'open') {
            $ticket->status = 'in_progress';
        }
        $ticket->save();

        if (!empty($changes)) {
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $assignedBy,
                'action' => 'assigned',
                'description' => 'Ticket ' . implode(', ', $changes),
            ]);
        }

        return $ticket;
    }
}
