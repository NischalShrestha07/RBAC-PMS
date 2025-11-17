<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function agents()
    {
        return $this->hasMany(Agent::class);
    }
    public function commitments()
    {
        return $this->hasMany(Commitment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
