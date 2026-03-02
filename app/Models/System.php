<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class System extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function accessRequests()
    {
        return $this->hasMany(AccessRequest::class);
    }

    public function changeRequests()
    {
        return $this->hasMany(ChangeRequest::class);
    }
}
