<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryBatch extends Model
{
    use HasFactory;
    protected $fillable = [
        'inventory_item_id',
        'branch_id',
        'batch_code',
        'quantity',
        'unit_cost',
        'received_at',
        'expired_at',
        'notes',
    ];

    protected $casts = [
        'received_at' => 'date',
        'expired_at' => 'date',
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'inventory_batch_id');
    }
}
