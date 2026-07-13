<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryBatchFactory extends Factory
{
    protected $model = InventoryBatch::class;

    public function definition(): array
    {
        return [
            'inventory_item_id' => InventoryItem::factory(),
            'branch_id' => Branch::factory(),
            'batch_code' => 'BCH-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -4)),
            'quantity' => fake()->numberBetween(10, 100),
            'unit_cost' => fake()->numberBetween(1000, 15000),
            'received_at' => now()->subDays(fake()->numberBetween(0, 30)),
            'expired_at' => fake()->optional()->dateTimeBetween('+30 days', '+365 days'),
        ];
    }
}
