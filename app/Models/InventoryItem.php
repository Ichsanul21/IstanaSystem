<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'unit',
        'min_stock',
        'is_active',
    ];

    public function batches()
    {
        return $this->hasMany(InventoryBatch::class, 'inventory_item_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_inventory_item')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
