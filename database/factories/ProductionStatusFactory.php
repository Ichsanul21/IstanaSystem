<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\ProductionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductionStatusFactory extends Factory
{
    protected $model = ProductionStatus::class;

    public function definition(): array
    {
        return [
            'order_item_id' => OrderItem::factory(),
            'from_status' => fake()->randomElement(['received', 'washed', 'dried', 'ironed']),
            'to_status' => fake()->randomElement(['washed', 'dried', 'ironed', 'packed']),
            'user_id' => User::factory(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
