<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['code' => '1-1000', 'name' => 'Kas', 'category' => 'asset', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '1-1001', 'name' => 'Bank BCA', 'category' => 'asset', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '1-2000', 'name' => 'Piutang Usaha', 'category' => 'asset', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '1-3000', 'name' => 'Persediaan / Inventory', 'category' => 'asset', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '1-4000', 'name' => 'Peralatan', 'category' => 'asset', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '2-1000', 'name' => 'Utang Usaha', 'category' => 'liability', 'normal_balance' => 'credit', 'is_active' => true],
            ['code' => '2-2000', 'name' => 'Utang Pajak', 'category' => 'liability', 'normal_balance' => 'credit', 'is_active' => true],
            ['code' => '2-3000', 'name' => 'Utang Gaji', 'category' => 'liability', 'normal_balance' => 'credit', 'is_active' => true],
            ['code' => '3-1000', 'name' => 'Modal', 'category' => 'equity', 'normal_balance' => 'credit', 'is_active' => true],
            ['code' => '3-2000', 'name' => 'Laba Ditahan', 'category' => 'equity', 'normal_balance' => 'credit', 'is_active' => true],
            ['code' => '4-1000', 'name' => 'Pendapatan Laundry', 'category' => 'revenue', 'normal_balance' => 'credit', 'is_active' => true],
            ['code' => '4-2000', 'name' => 'Pendapatan Express', 'category' => 'revenue', 'normal_balance' => 'credit', 'is_active' => true],
            ['code' => '4-3000', 'name' => 'Diskon Diberikan', 'category' => 'revenue', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '5-1000', 'name' => 'Beban Gaji', 'category' => 'expense', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '5-2000', 'name' => 'Beban Sewa', 'category' => 'expense', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '5-3000', 'name' => 'Beban Inventory', 'category' => 'expense', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '5-4000', 'name' => 'Beban Perlengkapan', 'category' => 'expense', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '5-5000', 'name' => 'Beban Penyusutan', 'category' => 'expense', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '5-6000', 'name' => 'Beban Lain-lain', 'category' => 'expense', 'normal_balance' => 'debit', 'is_active' => true],
            ['code' => '5-7000', 'name' => 'Beban Listrik & Air', 'category' => 'expense', 'normal_balance' => 'debit', 'is_active' => true],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::firstOrCreate(
                ['code' => $account['code']],
                $account
            );
        }
    }
};
