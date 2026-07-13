<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\MembershipTier;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'code' => 'CUS-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'pin' => substr(fake()->phoneNumber(), -2),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'branch_id' => Branch::factory(),
            'membership_tier_id' => MembershipTier::factory(),
            'total_points' => 0,
            'is_active' => true,
        ];
    }
}
