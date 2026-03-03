<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCommentAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_comment_id', 'file_path', 'file_name', 'file_size'];

    public function comment()
    {
        return $this->belongsTo(TicketComment::class, 'ticket_comment_id');
    }
}
