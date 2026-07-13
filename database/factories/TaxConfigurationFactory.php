<?php

namespace Database\Factories;

use App\Models\TaxConfiguration;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxConfigurationFactory extends Factory
{
    protected $model = TaxConfiguration::class;

    public function definition(): array
    {
        return [
            'regime' => fake()->randomElement(['none', 'pp23', 'pkp']),
            'rate' => fn(array $attrs) => match($attrs['regime']) { 'pp23' => 0.5, 'pkp' => 11.0, default => 0 },
            'effective_date' => now()->startOfMonth(),
            'is_active' => true,
        ];
    }

    public function pp23(): static
    {
        return $this->state(fn() => ['regime' => 'pp23', 'rate' => 0.5]);
    }

    public function pkp(): static
    {
        return $this->state(fn() => ['regime' => 'pkp', 'rate' => 11.0]);
    }
}
