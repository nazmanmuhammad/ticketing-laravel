<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Ticket extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'ticket_number', 'title', 'description', 'category_id', 'sub_category_id',
        'priority', 'status', 'requester_id', 'assigned_to', 'assigned_team_id',
        'sla_due_at', 'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'sla_due_at' => 'datetime',
            'closed_at' => 'datetime',
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
        return sprintf('TKT-%d-%04d', $year, $last);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'assigned_team_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at', 'asc');
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function approvals()
    {
        return $this->hasMany(TicketApproval::class)->orderBy('level');
    }

    public function activities()
    {
        return $this->hasMany(TicketActivity::class)->orderBy('created_at', 'desc');
    }

    public function changeRequests()
    {
        return $this->hasMany(ChangeRequest::class, 'related_ticket_id');
    }

    public function isSlaBreached(): bool
    {
        return $this->sla_due_at && now()->greaterThan($this->sla_due_at) && !in_array($this->status, ['resolved', 'closed']);
    }

    public function slaPercentage(): float
    {
        if (!$this->sla_due_at) return 100;
        $total = $this->created_at->diffInMinutes($this->sla_due_at);
        if ($total <= 0) return 0;
        $remaining = now()->diffInMinutes($this->sla_due_at, false);
        return max(0, min(100, ($remaining / $total) * 100));
    }
}
