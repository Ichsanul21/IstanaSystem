<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'app_name', 'value' => 'Istana Laundry', 'is_public' => true, 'type' => 'string', 'description' => 'Nama aplikasi'],
            ['group' => 'general', 'key' => 'app_url', 'value' => env('APP_URL', 'http://localhost'), 'is_public' => true, 'type' => 'string', 'description' => 'URL aplikasi'],
            ['group' => 'general', 'key' => 'logo', 'value' => '', 'is_public' => true, 'type' => 'string', 'description' => 'Logo aplikasi'],
            ['group' => 'branch_config', 'key' => 'default_opening', 'value' => '08:00', 'is_public' => false, 'type' => 'string', 'description' => 'Jam buka default cabang'],
            ['group' => 'branch_config', 'key' => 'default_closing', 'value' => '21:00', 'is_public' => false, 'type' => 'string', 'description' => 'Jam tutup default cabang'],
            ['group' => 'tax', 'key' => 'tax_regime', 'value' => 'pp23', 'is_public' => false, 'type' => 'string', 'description' => 'Rezim pajak (none/pp23/pkp)'],
            ['group' => 'tax', 'key' => 'pp23_rate', 'value' => 0.5, 'is_public' => false, 'type' => 'number', 'description' => 'Tarif PP23 (%)'],
            ['group' => 'tax', 'key' => 'ppn_rate', 'value' => 11, 'is_public' => false, 'type' => 'number', 'description' => 'Tarif PPN (%)'],
            ['group' => 'loyalty', 'key' => 'points_ratio', 'value' => 1000, 'is_public' => false, 'type' => 'number', 'description' => 'Rupiah per 1 poin'],
            ['group' => 'loyalty', 'key' => 'points_redeem_rate', 'value' => 100, 'is_public' => false, 'type' => 'number', 'description' => 'Nilai 1 poin saat redeem (rupiah)'],
            ['group' => 'loyalty', 'key' => 'points_expiry_days', 'value' => 90, 'is_public' => false, 'type' => 'number', 'description' => 'Masa berlaku poin (hari)'],
            ['group' => 'gateway', 'key' => 'midtrans_production', 'value' => false, 'is_public' => false, 'type' => 'boolean', 'description' => 'Mode produksi Midtrans'],
            ['group' => 'accounting', 'key' => 'revenue_account_code', 'value' => '4-1000', 'is_public' => false, 'type' => 'string', 'description' => 'Kode akun pendapatan'],
            ['group' => 'accounting', 'key' => 'kas_account_code', 'value' => '1-1000', 'is_public' => false, 'type' => 'string', 'description' => 'Kode akun kas'],
            ['group' => 'accounting', 'key' => 'promo_expense_code', 'value' => '5-1000', 'is_public' => false, 'type' => 'string', 'description' => 'Kode akun beban promosi'],
            ['group' => 'accounting', 'key' => 'inventory_asset_code', 'value' => '1-3000', 'is_public' => false, 'type' => 'string', 'description' => 'Kode akun persediaan'],
            ['group' => 'accounting', 'key' => 'inventory_expense_code', 'value' => '5-3000', 'is_public' => false, 'type' => 'string', 'description' => 'Kode akun beban persediaan'],
            ['group' => 'order', 'key' => 'auto_complete_days', 'value' => 7, 'is_public' => false, 'type' => 'number', 'description' => 'Hari auto-complete pesanan'],
            ['group' => 'notification', 'key' => 'wa_enabled', 'value' => true, 'is_public' => false, 'type' => 'boolean', 'description' => 'Aktifkan notifikasi WhatsApp'],
            ['group' => 'inventory', 'key' => 'min_stock_alert', 'value' => true, 'is_public' => false, 'type' => 'boolean', 'description' => 'Aktifkan alert stok minimum'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(
                ['group' => $s['group'], 'key' => $s['key']],
                $s
            );
        }
    }
};
