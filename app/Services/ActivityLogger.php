<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityLogger
{
    protected $request;
    protected $user;

    public function __construct(Request $request = null)
    {
        $this->request = $request ?? request();
        $this->user = Auth::user();
    }

    /**
     * Log an activity
     */
    public function log(
        string $action,
        string $description,
        ?Model $model = null,
        array $oldData = [],
        array $newData = [],
        string $status = 'success'
    ): ActivityLog {
        $changes = [];

        if ($model && !empty($oldData) && !empty($newData)) {
            $changes = $this->calculateChanges($oldData, $newData);
        }

        return ActivityLog::create([
            'user_id' => $this->user?->id,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'old_values' => $oldData,
            'new_values' => $newData,
            'changes' => $changes,
            'description' => $description,
            'route' => $this->request->route()?->getName() ?? $this->request->path(),
            'method' => $this->request->method(),
            'request_data' => $this->getSanitizedRequestData(),
            'status' => $status,
        ]);
    }

    /**
     * Log creation of a model
     */
    public function logCreate(Model $model, array $data, string $description = null): ActivityLog
    {
        $description = $description ?? "Created {$this->getModelName($model)}";

        return $this->log('create', $description, $model, [], $data);
    }

    /**
     * Log update of a model
     */
    public function logUpdate(Model $model, array $oldData, array $newData, string $description = null): ActivityLog
    {
        $description = $description ?? "Updated {$this->getModelName($model)}";

        return $this->log('update', $description, $model, $oldData, $newData);
    }

    /**
     * Log deletion of a model
     */
    public function logDelete(Model $model, string $description = null): ActivityLog
    {
        $description = $description ?? "Deleted {$this->getModelName($model)}";

        return $this->log('delete', $description, $model, $model->getAttributes(), []);
    }

    /**
     * Log user login
     */
    public function logLogin(User $user): ActivityLog
    {
        return $this->log('login', "User {$user->email} logged in", $user, [], [], 'success');
    }

    /**
     * Log failed login attempt
     */
    public function logFailedLogin(string $email, string $reason): ActivityLog
    {
        \App\Models\FailedLoginAttempt::create([
            'email' => $email,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'reason' => $reason,
        ]);

        return $this->log('failed_login', "Failed login attempt for {$email}: {$reason}", null, [], [], 'failed');
    }

    /**
     * Log payment
     */
    public function logPayment(Model $model, float $amount, string $type, string $description = null): ActivityLog
    {
        $description = $description ?? "Payment of {$amount} recorded for {$this->getModelName($model)}";

        return $this->log('payment', $description, $model, [], [
            'amount' => $amount,
            'type' => $type,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Log follow-up
     */
    public function logFollowup(Model $model, string $comments, string $description = null): ActivityLog
    {
        $description = $description ?? "Follow-up added to {$this->getModelName($model)}";

        return $this->log('followup', $description, $model, [], [
            'comments' => $comments,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Log status change
     */
    public function logStatusChange(Model $model, string $oldStatus, string $newStatus, string $description = null): ActivityLog
    {
        $description = $description ?? "Status changed from {$oldStatus} to {$newStatus} for {$this->getModelName($model)}";

        return $this->log('status_change', $description, $model, [
            'status' => $oldStatus
        ], [
            'status' => $newStatus
        ]);
    }

    /**
     * Log export/import
     */
    public function logExport(string $type, array $data = [], string $description = null): ActivityLog
    {
        $description = $description ?? "Exported {$type} data";

        return $this->log('export', $description, null, [], $data);
    }

    /**
     * Calculate changes between old and new data
     */
    private function calculateChanges(array $oldData, array $newData): array
    {
        $changes = [];

        foreach ($newData as $key => $newValue) {
            $oldValue = $oldData[$key] ?? null;

            if ($oldValue != $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        return $changes;
    }

    /**
     * Get sanitized request data (exclude sensitive fields)
     */
    private function getSanitizedRequestData(): array
    {
        $data = $this->request->all();

        // Remove sensitive fields
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_token', 'secret', '_token'];

        foreach ($sensitiveFields as $field) {
            unset($data[$field]);
        }

        return $data;
    }

    /**
     * Get model name for logging
     */
    private function getModelName(Model $model): string
    {
        $modelName = class_basename($model);

        if (method_exists($model, 'getDisplayName')) {
            return $model->getDisplayName();
        }

        if (isset($model->name)) {
            return "{$modelName}: {$model->name}";
        }

        if (isset($model->email)) {
            return "{$modelName}: {$model->email}";
        }

        return $modelName;
    }
}
