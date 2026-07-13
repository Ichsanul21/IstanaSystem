<?php

namespace Database\Factories;

use App\Models\GatewayPayment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class GatewayPaymentFactory extends Factory
{
    protected $model = GatewayPayment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'transaction_id' => fake()->unique()->bothify('TRX-##########'),
            'gross_amount' => fake()->numberBetween(10000, 100000),
            'status' => fake()->randomElement(['pending', 'success', 'failed', 'expired', 'refund']),
            'payment_type' => fake()->randomElement(['bank_transfer', 'gopay', 'qris', 'credit_card']),
            'fraud_status' => fake()->optional()->randomElement(['accept', 'deny', 'challenge']),
            'raw_response' => ['dummy' => 'response'],
            'paid_at' => now(),
        ];
    }
}
