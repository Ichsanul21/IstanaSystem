<?php

namespace App\Traits;

trait GeneratesOrderNumber
{
    public static function bootGeneratesOrderNumber(): void
    {
        static::creating(function ($model) {
            if (empty($model->order_number) && $model->branch_id) {
                $branch = $model->branch;
                $model->order_number = generateOrderNumber($branch->code);
            }
        });
    }
}
