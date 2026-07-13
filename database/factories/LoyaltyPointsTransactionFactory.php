<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\LoyaltyPointsTransaction;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoyaltyPointsTransactionFactory extends Factory
{
    protected $model = LoyaltyPointsTransaction::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'order_id' => Order::factory(),
            'points' => fake()->numberBetween(10, 500),
            'type' => fake()->randomElement(['earn', 'redeem', 'expire', 'adjust']),
            'reference' => fake()->optional()->bothify('REF-####'),
            'expires_at' => now()->addDays(90),
            'expired_at' => null,
        ];
    }
}
