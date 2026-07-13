<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'service_id',
        'quantity',
        'price_per_unit',
        'subtotal',
        'qr_token',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderItemStatusLog::class);
    }
}
