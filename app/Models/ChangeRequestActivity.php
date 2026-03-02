<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeRequestActivity extends Model
{
    use HasFactory;

    protected $fillable = ['change_request_id', 'user_id', 'action', 'description'];

    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
