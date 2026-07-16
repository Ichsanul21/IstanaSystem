<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    private array $allPermissions;

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->allPermissions = [
            // Branches
            'branch.create', 'branch.read', 'branch.update', 'branch.delete',
            // Users
            'user.create', 'user.read', 'user.update', 'user.delete',
            'assign_roles',
            // Customers
            'customer.create', 'customer.read', 'customer.update', 'customer.delete',
            // Orders
            'order.create', 'order.read', 'order.update', 'order.delete',
            'cancel_orders', 'assign_operator',
            // Payments & Refunds
            'payment.create', 'payment.read', 'payment.refund',
            'process_refund', 'approve_refund',
            // Workshop
            'workshop.read', 'workshop.scan', 'workshop.update_status', 'quality_check',
            'manage_workshops',
            // Promotions
            'promotion.create', 'promotion.read', 'promotion.update', 'promotion.delete',
            'toggle_promotion_branch',
            // Inventory
            'inventory.create', 'inventory.read', 'inventory.update', 'inventory.delete',
            'stock_in', 'stock_out', 'adjust_stock', 'manage_items',
            // Services
            'view_services', 'create_services', 'edit_services', 'edit_service_pricing',
            // Reports & Finance
            'report.read', 'report.export',
            'finance.read', 'view_financial_reports', 'view_journal_entries',
            'create_manual_journal', 'manage_accounting_periods', 'manage_expenses', 'manage_tax_config',
            // Settings
            'settings.read', 'settings.update',
            'edit_global_settings', 'edit_branch_settings', 'view_settings',
            // Gateway
            'manage_gateway_config',
            // CRM
            'manage_tiers', 'manage_loyalty_settings',
            'membership.create', 'membership.update',
            // Finance granular (for view-level @can checks)
            'finance.access', 'finance.coa', 'finance.journal', 'finance.period', 'finance.expense',
            // Notifications
            'send_wa_notification',
            // Audit & Backup
            'view_activity_logs', 'export_data', 'run_backup', 'view_system_info',
            // Branch
            'switch_branch',
        ];

        foreach ($this->allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->seedDeveloper();
        $this->seedOwner();
        $this->seedSuperAdmin();
        $this->seedBranchAdmin();
        $this->seedWorkshopAdmin();
        $this->seedCS();
        $this->seedCashier();
        $this->seedWorkshopStaff();
    }

    private function seedDeveloper(): void
    {
        $role = Role::firstOrCreate(['name' => 'Developer']);
        $role->givePermissionTo(Permission::all());
    }

    private function seedOwner(): void
    {
        $role = Role::firstOrCreate(['name' => 'Owner']);
        $role->givePermissionTo([
            'branch.read', 'switch_branch',
            'user.read',
            'customer.read',
            'order.read',
            'payment.read',
            'promotion.read',
            'inventory.read',
            'view_services',
            'workshop.read',
            'report.read', 'report.export',
            'finance.read', 'view_financial_reports', 'view_journal_entries',
            'finance.access', 'finance.coa', 'finance.journal',
            'settings.read',
            'manage_loyalty_settings', 'manage_tax_config',
            'view_activity_logs',
            'view_system_info',
            'export_data',
        ]);
    }

    private function seedSuperAdmin(): void
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin']);
        $role->givePermissionTo([
            'branch.create', 'branch.read', 'branch.update',
            'user.create', 'user.read', 'user.update',
            'assign_roles',
            'customer.create', 'customer.read', 'customer.update',
            'order.read',
            'payment.create', 'payment.read',
            'promotion.create', 'promotion.read', 'promotion.update',
            'inventory.create', 'inventory.read', 'inventory.update',
            'stock_in', 'stock_out', 'adjust_stock', 'manage_items',
            'workshop.scan', 'workshop.read',
            'report.read', 'report.export',
            'settings.read', 'view_settings',
            'edit_global_settings',
            'finance.read', 'view_financial_reports', 'view_journal_entries',
            'create_manual_journal', 'manage_accounting_periods', 'manage_tax_config',
            'view_services', 'create_services', 'edit_services',
            'manage_tiers', 'membership.create', 'membership.update',
            'finance.access', 'finance.coa', 'finance.journal', 'finance.period', 'finance.expense',
            'view_activity_logs',
            'export_data',
            'switch_branch',
            'run_backup',
        ]);
    }

    private function seedBranchAdmin(): void
    {
        $role = Role::firstOrCreate(['name' => 'Branch Admin']);
        $role->givePermissionTo([
            'branch.read', 'branch.update',
            'user.create', 'user.read', 'user.update', 'user.delete',
            'assign_roles',
            'customer.create', 'customer.read', 'customer.update',
            'order.read', 'order.update', 'cancel_orders',
            'payment.create', 'payment.read', 'payment.refund',
            'process_refund', 'approve_refund',
            'promotion.read', 'promotion.update',
            'toggle_promotion_branch',
            'inventory.create', 'inventory.read', 'inventory.update', 'inventory.delete',
            'stock_in', 'stock_out', 'adjust_stock',
            'workshop.scan', 'workshop.read',
            'report.read', 'report.export',
            'settings.read', 'view_settings',
            'finance.read', 'view_financial_reports', 'view_journal_entries',
            'finance.access', 'finance.journal', 'finance.expense',
            'manage_expenses',
            'view_services', 'edit_services', 'edit_service_pricing',
            'send_wa_notification',
            'edit_branch_settings',
            'export_data',
        ]);
    }

    private function seedWorkshopAdmin(): void
    {
        $role = Role::firstOrCreate(['name' => 'Workshop Admin']);
        $role->givePermissionTo([
            'workshop.scan', 'workshop.update_status', 'workshop.read',
            'assign_operator', 'quality_check',
            'order.read',
            'report.read',
            'customer.read',
            'export_data',
        ]);
    }

    private function seedCS(): void
    {
        $role = Role::firstOrCreate(['name' => 'CS']);
        $role->givePermissionTo([
            'customer.create', 'customer.read', 'customer.update',
            'order.read', 'order.update', 'cancel_orders',
            'payment.read',
            'promotion.read',
            'workshop.read',
            'view_services',
            'send_wa_notification',
            'export_data',
        ]);
    }

    private function seedCashier(): void
    {
        $role = Role::firstOrCreate(['name' => 'Cashier']);
        $role->givePermissionTo([
            'order.create', 'order.read',
            'payment.create', 'payment.read',
            'process_refund',
            'customer.read',
            'promotion.read',
            'view_services',
            'report.export',
            'export_data',
        ]);
    }

    private function seedWorkshopStaff(): void
    {
        $role = Role::firstOrCreate(['name' => 'Workshop Staff']);
        $role->givePermissionTo([
            'workshop.scan', 'workshop.read', 'workshop.update_status',
        ]);
    }
}
