<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'user_id', 'body', 'is_internal', 'parent_id'];

    protected function casts(): array
    {
        return ['is_internal' => 'boolean'];
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(TicketComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(TicketComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function attachments()
    {
        return $this->hasMany(TicketCommentAttachment::class);
    }
}
