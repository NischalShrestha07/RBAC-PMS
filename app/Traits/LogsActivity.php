<?php

namespace App\Traits;

use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function (Model $model) {
            app(ActivityLogger::class)->logCreate($model, $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $oldData = array_intersect_key($model->getOriginal(), $model->getDirty());
            $newData = $model->getDirty();

            app(ActivityLogger::class)->logUpdate($model, $oldData, $newData);
        });

        static::deleted(function (Model $model) {
            app(ActivityLogger::class)->logDelete($model);
        });
    }

    /**
     * Get the display name for logging
     */
    public function getDisplayName(): string
    {
        $modelName = class_basename($this);

        if (isset($this->name)) {
            return "{$modelName}: {$this->name}";
        }

        if (isset($this->email)) {
            return "{$modelName}: {$this->email}";
        }

        if (isset($this->id)) {
            return "{$modelName} #{$this->id}";
        }

        return $modelName;
    }
}
