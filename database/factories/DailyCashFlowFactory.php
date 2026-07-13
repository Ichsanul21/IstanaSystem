<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\DailyCashFlow;
use Illuminate\Database\Eloquent\Factories\Factory;

class DailyCashFlowFactory extends Factory
{
    protected $model = DailyCashFlow::class;

    public function definition(): array
    {
        $opening = fake()->numberBetween(500000, 2000000);
        $in = fake()->numberBetween(1000000, 5000000);
        $out = fake()->numberBetween(500000, 3000000);

        return [
            'branch_id' => Branch::factory(),
            'date' => now()->subDays(fake()->numberBetween(0, 7)),
            'opening_balance' => $opening,
            'total_cash_in' => $in,
            'total_cash_out' => $out,
            'closing_balance' => $opening + $in - $out,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
