<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemStatusLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'order_item_id',
        'production_status_id',
        'note',
        'scanned_by',
        'scan_time',
    ];

    protected $casts = [
        'scan_time' => 'datetime',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function productionStatus()
    {
        return $this->belongsTo(ProductionStatus::class, 'production_status_id');
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
