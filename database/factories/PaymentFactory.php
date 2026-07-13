<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'amount' => fake()->numberBetween(10000, 100000),
            'method' => fake()->randomElement(['cash', 'transfer', 'qris', 'gateway']),
            'reference' => fake()->optional()->bothify('REF-####-????'),
            'paid_at' => now(),
            'created_by' => User::factory(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
