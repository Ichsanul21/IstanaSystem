<?php

namespace Database\Seeders;

use App\Models\MembershipTier;
use Illuminate\Database\Seeder;

class MembershipTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['name' => 'Bronze', 'level' => 1, 'min_points' => 0, 'discount_percent' => 0, 'discount_per_order' => 0, 'free_delivery' => false, 'priority_service' => false, 'birthday_voucher' => 0, 'benefits' => null, 'color' => '#CD7F32', 'is_active' => true],
            ['name' => 'Silver', 'level' => 2, 'min_points' => 100, 'discount_percent' => 5, 'discount_per_order' => 5000, 'free_delivery' => false, 'priority_service' => false, 'birthday_voucher' => 20000, 'benefits' => 'Diskon 5%, voucher ulang tahun Rp20.000', 'color' => '#C0C0C0', 'is_active' => true],
            ['name' => 'Gold', 'level' => 3, 'min_points' => 300, 'discount_percent' => 10, 'discount_per_order' => 10000, 'free_delivery' => true, 'priority_service' => false, 'birthday_voucher' => 50000, 'benefits' => 'Diskon 10%, gratis antar, voucher ulang tahun Rp50.000', 'color' => '#FFD700', 'is_active' => true],
            ['name' => 'Platinum', 'level' => 4, 'min_points' => 500, 'discount_percent' => 15, 'discount_per_order' => 15000, 'free_delivery' => true, 'priority_service' => true, 'birthday_voucher' => 100000, 'benefits' => 'Diskon 15%, gratis antar, prioritas, voucher ulang tahun Rp100.000', 'color' => '#E5E4E2', 'is_active' => true],
        ];

        foreach ($tiers as $tier) {
            MembershipTier::firstOrCreate(['name' => $tier['name']], $tier);
        }
    }
};
