<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessRequestApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'access_request_id', 'approver_id', 'level', 'status', 'notes', 'acted_at',
    ];

    protected function casts(): array
    {
        return ['acted_at' => 'datetime'];
    }

    public function accessRequest()
    {
        return $this->belongsTo(AccessRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
