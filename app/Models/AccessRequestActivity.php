<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessRequestActivity extends Model
{
    use HasFactory;

    protected $fillable = ['access_request_id', 'user_id', 'action', 'description'];

    public function accessRequest()
    {
        return $this->belongsTo(AccessRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
