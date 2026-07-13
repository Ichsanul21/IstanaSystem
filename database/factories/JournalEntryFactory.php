<?php

namespace Database\Factories;

use App\Models\AccountingPeriod;
use App\Models\Branch;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    public function definition(): array
    {
        return [
            'entry_number' => 'JE-' . now()->format('Ymd') . '-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'description' => fake()->sentence(),
            'period_id' => AccountingPeriod::factory(),
            'branch_id' => Branch::factory(),
            'user_id' => User::factory(),
            'posted_at' => now(),
        ];
    }
}
