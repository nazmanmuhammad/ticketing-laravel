<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeRequestComment extends Model
{
    use HasFactory;

    protected $fillable = ['change_request_id', 'user_id', 'body', 'is_internal', 'parent_id'];

    protected function casts(): array
    {
        return ['is_internal' => 'boolean'];
    }

    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ChangeRequestComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ChangeRequestComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function attachments()
    {
        return $this->hasMany(ChangeRequestCommentAttachment::class, 'comment_id');
    }
}
