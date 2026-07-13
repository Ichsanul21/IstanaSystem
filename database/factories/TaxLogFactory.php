<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\TaxConfiguration;
use App\Models\TaxLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxLogFactory extends Factory
{
    protected $model = TaxLog::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'tax_config_id' => TaxConfiguration::factory(),
            'base_amount' => fake()->numberBetween(10000, 100000),
            'rate' => fake()->randomFloat(2, 0.5, 11),
            'tax_amount' => fn(array $attrs) => $attrs['base_amount'] * $attrs['rate'] / 100,
        ];
    }
}
