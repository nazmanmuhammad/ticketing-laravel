<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaSetting extends Model
{
    use HasFactory;

    protected $fillable = ['priority', 'response_hours', 'resolution_hours'];

    public static function getForPriority(string $priority): ?self
    {
        return static::where('priority', $priority)->first();
    }
}
