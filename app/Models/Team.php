<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'team_user')->withTimestamps();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_team_id');
    }
}
