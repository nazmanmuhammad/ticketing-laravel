<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessRequestCommentAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['comment_id', 'file_path', 'file_name', 'file_size'];

    public function comment()
    {
        return $this->belongsTo(AccessRequestComment::class, 'comment_id');
    }
}
