<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipTier extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'level',
        'min_points',
        'discount_percent',
        'discount_per_order',
        'free_delivery',
        'priority_service',
        'birthday_voucher',
        'benefits',
        'color',
        'is_active',
    ];

    protected $casts = [
        'min_points' => 'integer',
        'discount_percent' => 'decimal:2',
        'discount_per_order' => 'decimal:2',
        'free_delivery' => 'boolean',
        'priority_service' => 'boolean',
        'birthday_voucher' => 'decimal:2',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
