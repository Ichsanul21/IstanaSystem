<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Promotion;
use App\Models\PromotionBranch;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionBranchFactory extends Factory
{
    protected $model = PromotionBranch::class;

    public function definition(): array
    {
        return [
            'promotion_id' => Promotion::factory(),
            'branch_id' => Branch::factory(),
            'is_active' => true,
        ];
    }
}
