<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'TERIMA', 'name' => 'Terima', 'sequence' => 1, 'color' => '#FF6B00', 'description' => 'Order diterima dari customer'],
            ['code' => 'PILAH', 'name' => 'Pilah', 'sequence' => 2, 'color' => '#FF8C38', 'description' => 'Pakaian dipilah berdasarkan jenis dan warna'],
            ['code' => 'CUCI', 'name' => 'Cuci', 'sequence' => 3, 'color' => '#FFA863', 'description' => 'Proses pencucian'],
            ['code' => 'KERING', 'name' => 'Kering', 'sequence' => 4, 'color' => '#FFC592', 'description' => 'Proses pengeringan'],
            ['code' => 'LIPAT', 'name' => 'Lipat', 'sequence' => 5, 'color' => '#FFC107', 'description' => 'Pakaian dilipat dan di-packing'],
            ['code' => 'CEK', 'name' => 'Cek', 'sequence' => 6, 'color' => '#4CAF50', 'description' => 'Pengecekan kualitas akhir'],
            ['code' => 'SIAP', 'name' => 'Siap', 'sequence' => 7, 'color' => '#2196F3', 'description' => 'Siap diambil customer'],
            ['code' => 'DIAMBIL', 'name' => 'Diambil', 'sequence' => 8, 'color' => '#9E9E9E', 'description' => 'Sudah diambil customer'],
        ];

        foreach ($statuses as $status) {
            DB::table('production_statuses')->updateOrInsert(
                ['code' => $status['code']],
                $status
            );
        }
    }
};
