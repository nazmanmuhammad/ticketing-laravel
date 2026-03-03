<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessRequestComment extends Model
{
    use HasFactory;

    protected $fillable = ['access_request_id', 'user_id', 'body', 'is_internal', 'parent_id'];

    protected function casts(): array
    {
        return ['is_internal' => 'boolean'];
    }

    public function accessRequest()
    {
        return $this->belongsTo(AccessRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(AccessRequestComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(AccessRequestComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function attachments()
    {
        return $this->hasMany(AccessRequestCommentAttachment::class, 'comment_id');
    }
}
