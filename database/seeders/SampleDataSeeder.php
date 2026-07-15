<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\MembershipTier;
use App\Models\Service;
use App\Models\ServicePricing;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $workshop = Workshop::firstOrCreate(
            ['code' => 'WKS01'],
            ['name' => 'Workshop Pusat', 'address' => 'Jl. Laundry No. 1', 'phone' => '021-12345678', 'is_active' => true]
        );

        $branchMargonda = Branch::firstOrCreate(
            ['code' => 'BRG01'],
            ['name' => 'Cabang Margonda', 'workshop_id' => $workshop->id, 'address' => 'Jl. Margonda Raya No. 10', 'phone' => '021-23456789', 'opening_time' => '08:00', 'closing_time' => '21:00', 'daily_capacity' => 100, 'is_active' => true]
        );

        $branchDepok = Branch::firstOrCreate(
            ['code' => 'BRG02'],
            ['name' => 'Cabang Depok', 'workshop_id' => $workshop->id, 'address' => 'Jl. Depok Baru No. 5', 'phone' => '021-34567890', 'opening_time' => '08:00', 'closing_time' => '21:00', 'daily_capacity' => 80, 'is_active' => true]
        );

        $dev = User::firstOrCreate(
            ['email' => 'dev@istanalaundry.com'],
            ['name' => 'Developer', 'password' => Hash::make('password'), 'branch_id' => $branchMargonda->id, 'is_protected' => true]
        );
        if (!$dev->hasRole('Developer')) $dev->assignRole('Developer');

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@istanalaundry.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('password'), 'branch_id' => $branchMargonda->id, 'is_protected' => true]
        );
        if (!$superAdmin->hasRole('Super Admin')) $superAdmin->assignRole('Super Admin');

        foreach ([
            ['email' => 'branchadmin@istanalaundry.com', 'name' => 'Branch Admin Margonda', 'role' => 'Branch Admin'],
            ['email' => 'cs@istanalaundry.com', 'name' => 'CS Margonda', 'role' => 'CS'],
            ['email' => 'cashier@istanalaundry.com', 'name' => 'Cashier Margonda', 'role' => 'Cashier'],
        ] as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => Hash::make('password'), 'branch_id' => $branchMargonda->id, 'is_protected' => false]
            );
            if (!$user->hasRole($u['role'])) $user->assignRole($u['role']);
        }

        $serviceCuciSetrika = Service::firstOrCreate(
            ['code' => 'CKS'],
            ['name' => 'Cuci Kering + Setrika', 'description' => 'Cuci kering dan setrika dengan harga per kg', 'unit' => 'kg', 'is_active' => true]
        );

        $serviceCuci = Service::firstOrCreate(
            ['code' => 'CK'],
            ['name' => 'Cuci Kering', 'description' => 'Cuci kering tanpa setrika dengan harga per kg', 'unit' => 'kg', 'is_active' => true]
        );

        $serviceSetrika = Service::firstOrCreate(
            ['code' => 'SA'],
            ['name' => 'Setrika Aja', 'description' => 'Setrika saja dengan harga per kg', 'unit' => 'kg', 'is_active' => true]
        );

        $pricings = [
            ['service' => $serviceCuciSetrika, 'branch' => $branchMargonda, 'price' => 8000],
            ['service' => $serviceCuciSetrika, 'branch' => $branchDepok, 'price' => 8000],
            ['service' => $serviceCuci, 'branch' => $branchMargonda, 'price' => 5000],
            ['service' => $serviceCuci, 'branch' => $branchDepok, 'price' => 5000],
            ['service' => $serviceSetrika, 'branch' => $branchMargonda, 'price' => 4000],
            ['service' => $serviceSetrika, 'branch' => $branchDepok, 'price' => 4000],
        ];

        foreach ($pricings as $p) {
            ServicePricing::firstOrCreate(
                ['service_id' => $p['service']->id, 'branch_id' => $p['branch']->id],
                ['price' => $p['price'], 'min_weight' => 1, 'max_weight' => null, 'estimated_days' => 2, 'is_active' => true]
            );
        }

        $bronze = MembershipTier::where('name', 'Bronze')->first();

        Customer::firstOrCreate(
            ['code' => 'CUST001'],
            ['name' => 'Budi', 'phone' => '081234567890', 'email' => 'budi@email.com', 'address' => 'Jl. Budi No. 1', 'membership_tier_id' => $bronze?->id, 'total_points' => 0, 'total_purchase' => 0, 'branch_id' => $branchMargonda->id, 'is_active' => true]
        );

        Customer::firstOrCreate(
            ['code' => 'CUST002'],
            ['name' => 'Siti', 'phone' => '081234567891', 'email' => 'siti@email.com', 'address' => 'Jl. Siti No. 2', 'membership_tier_id' => $bronze?->id, 'total_points' => 0, 'total_purchase' => 0, 'branch_id' => $branchDepok->id, 'is_active' => true]
        );

        $coas = [
            ['code' => '1-1000', 'name' => 'Kas', 'category' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1-1100', 'name' => 'Piutang Usaha', 'category' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '4-1000', 'name' => 'Pendapatan Jasa', 'category' => 'revenue', 'normal_balance' => 'credit'],
            ['code' => '5-1000', 'name' => 'Beban Operasional', 'category' => 'expense', 'normal_balance' => 'debit'],
        ];

        foreach ($coas as $c) {
            ChartOfAccount::firstOrCreate(['code' => $c['code']], $c + ['is_active' => true]);
        }
    }
}
