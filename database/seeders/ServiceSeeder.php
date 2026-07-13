<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['code' => 'CK', 'name' => 'Cuci Kering', 'unit' => 'kg', 'is_active' => true],
            ['code' => 'CB', 'name' => 'Cuci Basah', 'unit' => 'kg', 'is_active' => true],
            ['code' => 'ST', 'name' => 'Setrika', 'unit' => 'kg', 'is_active' => true],
            ['code' => 'CK+ST', 'name' => 'Cuci Kering + Setrika', 'unit' => 'kg', 'is_active' => true],
            ['code' => 'EXP', 'name' => 'Express', 'unit' => 'kg', 'is_active' => true],
            ['code' => 'SL', 'name' => 'Selimut', 'unit' => 'pcs', 'is_active' => true],
            ['code' => 'LP', 'name' => 'Lipat', 'unit' => 'kg', 'is_active' => true],
            ['code' => 'KP', 'name' => 'Karpet', 'unit' => 'm2', 'is_active' => true],
            ['code' => 'SF', 'name' => 'Sofa', 'unit' => 'pcs', 'is_active' => true],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['code' => $service['code']],
                $service
            );
        }
    }
}
