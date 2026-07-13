<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasBranchScope;

class Order extends Model
{
    use HasFactory, SoftDeletes, HasBranchScope;

    protected $fillable = [
        'order_number',
        'branch_id',
        'customer_id',
        'created_by',
        'status',
        'total_amount',
        'discount_amount',
        'point_discount',
        'grand_total',
        'payment_status',
        'payment_method',
        'paid_amount',
        'change_amount',
        'paid_at',
        'notes',
        'finished_at',
        'qr_token',
        'tracking_token',
        'customer_name',
        'customer_phone',
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'point_discount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function promotionUsages()
    {
        return $this->hasMany(PromotionUsage::class);
    }

    public function loyaltyPointsTransactions()
    {
        return $this->hasMany(LoyaltyPointsTransaction::class);
    }

    public function gatewayPayment()
    {
        return $this->hasOne(GatewayPayment::class);
    }
}
