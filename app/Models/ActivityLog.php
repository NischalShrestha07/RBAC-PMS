<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'log_uid',
        'user_id',
        'ip_address',
        'user_agent',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'changes',
        'description',
        'route',
        'method',
        'request_data',
        'status',
        'performed_at'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changes' => 'array',
        'request_data' => 'array',
        'performed_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->log_uid = Str::uuid();
            $model->performed_at = now();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    // Helper methods
    public function getFormattedActionAttribute()
    {
        $actions = [
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'failed_login' => 'Failed Login Attempt',
            'export' => 'Exported',
            'import' => 'Imported',
            'payment' => 'Payment Made',
            'followup' => 'Follow-up Added',
            'status_change' => 'Status Changed',
            'failed' => 'Marked as Failed',
        ];

        return $actions[$this->action] ?? ucfirst($this->action);
    }

    public function getChangesSummaryAttribute()
    {
        if (empty($this->changes)) {
            return 'No changes detected';
        }

        $changes = [];
        foreach ($this->changes as $field => $change) {
            $changes[] = "$field: '{$change['old']}' → '{$change['new']}'";
        }

        return implode(', ', $changes);
    }

    public function getActionColorAttribute()
    {
        $colors = [
            'create' => 'success',
            'update' => 'info',
            'delete' => 'danger',
            'login' => 'primary',
            'logout' => 'secondary',
            'failed_login' => 'warning',
            'payment' => 'success',
            'followup' => 'info',
            'status_change' => 'warning',
            'failed' => 'danger',
            'export' => 'primary',
            'import' => 'primary',
        ];

        return $colors[$this->action] ?? 'secondary';
    }
}
