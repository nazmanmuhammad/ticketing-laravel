<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalWorkflow extends Model
{
    use HasFactory;

    protected $fillable = ['module', 'system_id', 'level', 'approver_id', 'approver_role', 'sla_hours'];

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
