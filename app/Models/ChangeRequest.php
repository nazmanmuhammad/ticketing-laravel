<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ChangeRequest extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'request_number', 'requester_id', 'title', 'description',
        'change_type', 'system_id', 'impact', 'risk', 'rollback_plan',
        'scheduled_at', 'implemented_at', 'status', 'post_review_notes',
        'related_ticket_id', 'assigned_to', 'assigned_team_id',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'implemented_at' => 'datetime',
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
        return sprintf('CHG-%d-%04d', $year, $last);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function relatedTicket()
    {
        return $this->belongsTo(Ticket::class, 'related_ticket_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'assigned_team_id');
    }

    public function approvals()
    {
        return $this->hasMany(ChangeRequestApproval::class)->orderBy('level');
    }

    public function attachments()
    {
        return $this->hasMany(ChangeRequestAttachment::class);
    }

    public function comments()
    {
        return $this->hasMany(ChangeRequestComment::class)->orderBy('created_at', 'asc');
    }

    public function activities()
    {
        return $this->hasMany(ChangeRequestActivity::class)->orderBy('created_at', 'desc');
    }
}
