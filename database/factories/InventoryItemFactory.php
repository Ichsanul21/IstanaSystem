<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryItemFactory extends Factory
{
    protected $model = InventoryItem::class;

    public function definition(): array
    {
        return [
            'code' => 'INV-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'name' => fake()->randomElement(['Plastik', 'Deterjen', 'Pewangi', 'Pemutih', 'Softener', 'Kertas Struk', 'Tinta Printer', 'Stapler', 'Hanger', 'Paper Bag']),
            'description' => fake()->sentence(),
            'unit' => fake()->randomElement(['pcs', 'liter', 'kg', 'roll', 'pack']),
            'min_stock' => 5,
            'is_active' => true,
        ];
    }
}
