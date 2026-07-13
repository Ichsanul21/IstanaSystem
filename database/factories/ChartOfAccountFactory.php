<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChartOfAccountFactory extends Factory
{
    protected $model = ChartOfAccount::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->numerify('#-####'),
            'name' => fake()->randomElement(['Kas', 'Bank BCA', 'Piutang Usaha', 'Pendapatan Laundry', 'Beban Gaji', 'Beban Sewa', 'Hutang Pajak', 'Modal']),
            'category' => fake()->randomElement(['asset', 'liability', 'equity', 'revenue', 'expense']),
            'is_active' => true,
        ];
    }
}
