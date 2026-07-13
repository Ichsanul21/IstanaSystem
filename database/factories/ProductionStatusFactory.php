<?php

namespace Database\Factories;

use App\Models\ProductionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductionStatusFactory extends Factory
{
    protected $model = ProductionStatus::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->randomElement(['TERIMA', 'PILAH', 'CUCI', 'KERING', 'LIPAT', 'CEK', 'SIAP', 'DIAMBIL']),
            'name' => fake()->word(),
            'sequence' => fake()->numberBetween(1, 8),
            'color' => fake()->hexColor(),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
