<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServicePricing extends Model
{
    use HasFactory, SoftDeletes, HasBranchScope;

    protected $fillable = [
        'service_id',
        'branch_id',
        'price',
        'min_weight',
        'max_weight',
        'estimated_days',
        'is_active',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
