<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatewayPayment extends Model
{
    protected $fillable = [
        'order_id',
        'transaction_id',
        'gross_amount',
        'status',
        'payment_type',
        'va_number',
        'bill_key',
        'biller_code',
        'qr_url',
        'expiry_time',
        'raw_response',
        'paid_at',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'raw_response' => 'array',
        'paid_at' => 'datetime',
        'expiry_time' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
