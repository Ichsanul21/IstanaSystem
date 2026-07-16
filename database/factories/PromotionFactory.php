<?php

namespace Database\Factories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->bothify('PRM-####')),
            'name' => fake()->words(3, true),
            'type' => fake()->randomElement(['percentage', 'fixed', 'buy_get']),
            'value' => fake()->numberBetween(5, 50),
            'min_order_amount' => fake()->numberBetween(0, 50000),
            'max_discount_amount' => fake()->optional()->numberBetween(5000, 20000),
            'total_usage_limit' => fake()->numberBetween(10, 100),
            'total_used' => 0,
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(20),
            'is_active' => true,
        ];
    }
}
