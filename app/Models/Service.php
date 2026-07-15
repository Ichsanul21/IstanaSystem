<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'unit',
        'is_active',
    ];

    public function servicePricings()
    {
        return $this->hasMany(ServicePricing::class);
    }

    public function inventoryItems()
    {
        return $this->belongsToMany(InventoryItem::class, 'service_inventory_item')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
