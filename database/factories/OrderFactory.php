<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => strtoupper(fake()->bothify('CAB-####-?????')),
            'branch_id' => Branch::factory(),
            'customer_id' => Customer::factory(),
            'created_by' => User::factory(),
            'status' => 'pending',
            'total_amount' => fake()->numberBetween(10000, 100000),
            'discount_amount' => 0,
            'grand_total' => fn(array $attrs) => $attrs['total_amount'],
            'payment_status' => 'unpaid',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn(array $attrs) => [
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn(array $attrs) => [
            'status' => 'completed',
            'payment_status' => 'paid',
            'finished_at' => now(),
        ]);
    }
}
