<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasBranchScope;

class Customer extends Model
{
    use HasFactory, SoftDeletes, HasBranchScope;

    protected $fillable = [
        'code',
        'name',
        'phone',
        'pin',
        'email',
        'address',
        'id_card_number',
        'birth_date',
        'gender',
        'is_member',
        'membership_tier_id',
        'total_points',
        'total_purchase',
        'total_orders',
        'last_order_at',
        'join_date',
        'branch_id',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'total_purchase' => 'decimal:2',
        'total_orders' => 'integer',
        'birth_date' => 'date',
        'last_order_at' => 'datetime',
        'join_date' => 'date',
        'is_member' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function membershipTier()
    {
        return $this->belongsTo(MembershipTier::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function loyaltyPointsTransactions()
    {
        return $this->hasMany(LoyaltyPointsTransaction::class);
    }
}
