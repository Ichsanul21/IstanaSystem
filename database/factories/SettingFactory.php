<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'group' => fake()->randomElement(['general', 'tax', 'loyalty', 'gateway']),
            'key' => fake()->unique()->bothify('setting.##'),
            'value' => fake()->word(),
            'is_public' => fake()->boolean(),
        ];
    }
}
