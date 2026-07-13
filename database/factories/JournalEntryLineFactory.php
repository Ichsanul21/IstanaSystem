<?php

namespace Database\Factories;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalEntryLineFactory extends Factory
{
    protected $model = JournalEntryLine::class;

    public function definition(): array
    {
        return [
            'journal_entry_id' => JournalEntry::factory(),
            'account_id' => ChartOfAccount::factory(),
            'debit' => fn(array $attrs) => fake()->randomElement([fake()->numberBetween(1000, 100000), 0]),
            'credit' => fn(array $attrs) => $attrs['debit'] > 0 ? 0 : fake()->numberBetween(1000, 100000),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
