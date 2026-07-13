<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'branch_id' => currentBranchId(),
                'loggable_type' => get_class($model),
                'loggable_id' => $model->id,
                'event' => 'created',
                'old_values' => null,
                'new_values' => json_encode($model->getAttributes()),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        });

        static::updated(function ($model) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'branch_id' => currentBranchId(),
                'loggable_type' => get_class($model),
                'loggable_id' => $model->id,
                'event' => 'updated',
                'old_values' => json_encode($model->getOriginal()),
                'new_values' => json_encode($model->getAttributes()),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        });

        static::deleted(function ($model) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'branch_id' => currentBranchId(),
                'loggable_type' => get_class($model),
                'loggable_id' => $model->id,
                'event' => 'deleted',
                'old_values' => json_encode($model->getOriginal()),
                'new_values' => null,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        });
    }
}
