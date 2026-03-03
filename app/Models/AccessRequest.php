<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AccessRequest extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'request_number', 'requester_id', 'system_id', 'access_type',
        'custom_access_type', 'reason', 'start_date', 'end_date',
        'status', 'current_approval_level', 'assigned_to', 'assigned_team_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public static function generateNumber(): string
    {
        $year = now()->year;
        $last = static::withTrashed()->whereYear('created_at', $year)->count() + 1;
        return sprintf('ACC-%d-%04d', $year, $last);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function approvals()
    {
        return $this->hasMany(AccessRequestApproval::class)->orderBy('level');
    }

    public function attachments()
    {
        return $this->hasMany(AccessRequestAttachment::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'assigned_team_id');
    }

    public function comments()
    {
        return $this->hasMany(AccessRequestComment::class)->orderBy('created_at', 'asc');
    }

    public function activities()
    {
        return $this->hasMany(AccessRequestActivity::class)->orderByDesc('created_at');
    }

    public function currentApproval()
    {
        return $this->hasOne(AccessRequestApproval::class)
            ->where('level', $this->current_approval_level)
            ->where('status', 'pending');
    }
}
