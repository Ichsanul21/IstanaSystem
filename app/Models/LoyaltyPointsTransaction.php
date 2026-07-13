<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPointsTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'order_id',
        'points',
        'balance_after',
        'type',
        'description',
        'expiry_date',
        'created_by',
    ];

    protected $casts = [
        'points' => 'integer',
        'balance_after' => 'integer',
        'expiry_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
