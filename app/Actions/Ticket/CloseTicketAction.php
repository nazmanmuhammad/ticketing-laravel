<?php

namespace App\Actions\Ticket;

use App\Models\Ticket;
use App\Models\TicketActivity;

class CloseTicketAction
{
    public function execute(Ticket $ticket, int $userId): Ticket
    {
        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'action' => 'closed',
            'description' => 'Ticket closed',
        ]);

        return $ticket;
    }
}
