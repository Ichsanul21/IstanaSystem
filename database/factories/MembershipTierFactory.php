<?php

namespace Database\Factories;

use App\Models\MembershipTier;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipTierFactory extends Factory
{
    protected $model = MembershipTier::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum']),
            'min_points' => fake()->randomElement([0, 500, 1500, 5000]),
            'discount_percent' => fake()->randomElement([0, 5, 10, 15]),
            'color' => fake()->hexColor(),
            'is_active' => true,
        ];
    }
}
