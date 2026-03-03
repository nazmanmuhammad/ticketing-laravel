<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 'approver_id', 'level', 'status', 'notes', 'acted_at',
    ];

    protected function casts(): array
    {
        return ['acted_at' => 'datetime'];
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
