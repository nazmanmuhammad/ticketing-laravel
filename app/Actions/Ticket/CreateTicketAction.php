<?php

namespace App\Actions\Ticket;

use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketAttachment;
use App\Models\SlaSetting;
use Illuminate\Support\Facades\DB;

class CreateTicketAction
{
    public function execute(array $data, array $attachments = []): Ticket
    {
        return DB::transaction(function () use ($data, $attachments) {
            $data['ticket_number'] = Ticket::generateNumber();
            $data['status'] = 'open';

            $sla = SlaSetting::getForPriority($data['priority'] ?? 'medium');
            if ($sla) {
                $data['sla_due_at'] = now()->addHours($sla->resolution_hours);
            }

            $ticket = Ticket::create($data);

            foreach ($attachments as $file) {
                $path = $file->store('tickets/' . $ticket->id, 'public');
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $data['requester_id'],
                'action' => 'created',
                'description' => 'Ticket created',
            ]);

            return $ticket;
        });
    }
}
