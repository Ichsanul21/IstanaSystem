<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'min_order_items',
        'max_discount_amount',
        'applicable_service_ids',
        'buy_quantity',
        'get_type',
        'get_value',
        'start_date',
        'end_date',
        'usage_limit_per_customer',
        'total_usage_limit',
        'total_used',
        'is_all_branches',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'type' => \App\Enums\PromotionType::class,
        'applicable_service_ids' => 'array',
        'is_all_branches' => 'boolean',
    ];

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'promotion_branches');
    }

    public function usages()
    {
        return $this->hasMany(PromotionUsage::class);
    }
}
