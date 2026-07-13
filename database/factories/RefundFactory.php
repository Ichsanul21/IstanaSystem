<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RefundFactory extends Factory
{
    protected $model = Refund::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'payment_id' => Payment::factory(),
            'amount' => fake()->numberBetween(5000, 50000),
            'reason' => fake()->sentence(),
            'status' => 'requested',
            'requested_by' => User::factory(),
            'approved_by' => null,
            'approved_at' => null,
        ];
    }
}
