<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('SVC-###')),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'unit' => fake()->randomElement(['kg', 'pcs', 'm2']),
            'is_active' => true,
        ];
    }
}
