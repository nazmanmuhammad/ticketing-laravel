<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeRequestAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['change_request_id', 'file_path', 'file_name'];

    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class);
    }
}
