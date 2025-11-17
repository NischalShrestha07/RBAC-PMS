<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
