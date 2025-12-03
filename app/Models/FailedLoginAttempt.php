<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedLoginAttempt extends Model
{
    protected $table = 'failed_login_attempts';

    protected $fillable = [
        'email',
        'username',
        'ip_address',
        'user_agent',
        'reason',
        'attempted_at'
    ];

    protected $casts = [
        'attempted_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->attempted_at = now();
        });
    }
}
