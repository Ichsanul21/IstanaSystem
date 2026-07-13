<?php

namespace Database\Factories;

use App\Models\InventoryBatch;
use App\Models\InventoryTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryTransactionFactory extends Factory
{
    protected $model = InventoryTransaction::class;

    public function definition(): array
    {
        $batch = InventoryBatch::factory()->create();

        return [
            'inventory_batch_id' => $batch->id,
            'type' => fake()->randomElement(['in', 'out']),
            'quantity' => fake()->numberBetween(1, 50),
            'unit_cost' => $batch->unit_cost,
            'reference' => fake()->optional()->bothify('REF-####'),
            'created_by' => User::factory(),
        ];
    }
}
