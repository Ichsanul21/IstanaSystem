<?php

namespace Database\Factories;

use App\Models\AccountingPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountingPeriodFactory extends Factory
{
    protected $model = AccountingPeriod::class;

    public function definition(): array
    {
        return [
            'name' => fake()->monthName() . ' ' . fake()->year(),
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'is_closed' => false,
            'closed_at' => null,
        ];
    }
}
