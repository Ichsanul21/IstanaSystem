<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'workshop_id',
        'address',
        'phone',
        'opening_time',
        'closing_time',
        'daily_capacity',
        'is_active',
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function servicePricings()
    {
        return $this->hasMany(ServicePricing::class);
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_branches');
    }

    public function dailyCashFlows()
    {
        return $this->hasMany(DailyCashFlow::class);
    }
}
