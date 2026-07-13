<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Service;
use App\Models\ServicePricing;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicePricingFactory extends Factory
{
    protected $model = ServicePricing::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'branch_id' => Branch::factory(),
            'price' => fake()->numberBetween(3000, 50000),
            'min_weight' => fake()->randomFloat(2, 1, 5),
            'estimated_days' => fake()->randomElement([1, 2, 3]),
            'is_active' => true,
        ];
    }
}
