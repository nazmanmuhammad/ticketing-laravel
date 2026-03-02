<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessRequestAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['access_request_id', 'file_path', 'file_name'];

    public function accessRequest()
    {
        return $this->belongsTo(AccessRequest::class);
    }
}
