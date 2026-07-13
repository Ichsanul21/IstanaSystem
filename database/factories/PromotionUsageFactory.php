<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\PromotionUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionUsageFactory extends Factory
{
    protected $model = PromotionUsage::class;

    public function definition(): array
    {
        return [
            'promotion_id' => Promotion::factory(),
            'order_id' => Order::factory(),
            'customer_id' => Customer::factory(),
            'discount_amount' => fake()->numberBetween(1000, 10000),
        ];
    }
}
