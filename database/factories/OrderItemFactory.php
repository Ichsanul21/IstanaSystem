<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ServicePricing;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $pricing = ServicePricing::factory()->create();
        $qty = fake()->randomFloat(2, 1, 10);

        return [
            'order_id' => Order::factory(),
            'service_id' => $pricing->service_id,
            'quantity' => $qty,
            'price_per_unit' => $pricing->price,
            'subtotal' => $qty * $pricing->price,
        ];
    }
}
