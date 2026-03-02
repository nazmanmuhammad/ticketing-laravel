<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeRequestApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'change_request_id', 'approver_id', 'level', 'status', 'notes', 'acted_at',
    ];

    protected function casts(): array
    {
        return ['acted_at' => 'datetime'];
    }

    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
