<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'code' => 'CAB-' . str_pad(fake()->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'name' => 'Cabang ' . fake()->city(),
            'workshop_id' => Workshop::factory(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'opening_time' => '08:00',
            'closing_time' => '21:00',
            'daily_capacity' => 100,
            'is_active' => true,
        ];
    }
}
