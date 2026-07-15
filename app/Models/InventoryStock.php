<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasBranchScope;

class InventoryStock extends Model
{
    use HasFactory, HasBranchScope;

    protected $fillable = [
        'inventory_item_id',
        'branch_id',
        'quantity',
        'unit_price',
        'batch_number',
        'expired_at',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'expired_at' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
