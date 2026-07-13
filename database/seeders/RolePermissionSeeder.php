<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'branch.create', 'branch.read', 'branch.update', 'branch.delete',
            'customer.create', 'customer.read', 'customer.update', 'customer.delete',
            'order.create', 'order.read', 'order.update', 'order.delete',
            'payment.create', 'payment.read', 'payment.refund',
            'promotion.create', 'promotion.read', 'promotion.update', 'promotion.delete',
            'inventory.create', 'inventory.read', 'inventory.update', 'inventory.delete',
            'workshop.scan', 'workshop.update_status', 'workshop.read',
            'report.read', 'report.export',
            'settings.read', 'settings.update',
            'user.create', 'user.read', 'user.update', 'user.delete',
            'finance.read', 'finance.journal',
            'backup.create',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $developer = Role::firstOrCreate(['name' => 'Developer']);
        $developer->givePermissionTo(Permission::all());

        $owner = Role::firstOrCreate(['name' => 'Owner']);
        $owner->givePermissionTo([
            'branch.read',
            'customer.read',
            'order.read',
            'payment.read',
            'promotion.read',
            'inventory.read',
            'workshop.read',
            'report.read', 'report.export',
            'finance.read',
            'settings.read',
            'user.read',
        ]);

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo([
            'branch.create', 'branch.read', 'branch.update', 'branch.delete',
            'customer.create', 'customer.read', 'customer.update', 'customer.delete',
            'order.create', 'order.read', 'order.update', 'order.delete',
            'payment.create', 'payment.read', 'payment.refund',
            'promotion.create', 'promotion.read', 'promotion.update', 'promotion.delete',
            'inventory.create', 'inventory.read', 'inventory.update', 'inventory.delete',
            'workshop.scan', 'workshop.update_status', 'workshop.read',
            'report.read', 'report.export',
            'settings.read', 'settings.update',
            'user.create', 'user.read', 'user.update', 'user.delete',
            'finance.read', 'finance.journal',
        ]);

        $branchAdmin = Role::firstOrCreate(['name' => 'Branch Admin']);
        $branchAdmin->givePermissionTo([
            'branch.read', 'branch.update',
            'customer.create', 'customer.read', 'customer.update', 'customer.delete',
            'order.create', 'order.read', 'order.update', 'order.delete',
            'payment.create', 'payment.read', 'payment.refund',
            'promotion.read',
            'inventory.create', 'inventory.read', 'inventory.update', 'inventory.delete',
            'workshop.scan', 'workshop.read',
            'report.read',
            'settings.read',
            'user.read',
            'finance.read',
        ]);

        $workshopAdmin = Role::firstOrCreate(['name' => 'Workshop Admin']);
        $workshopAdmin->givePermissionTo([
            'workshop.scan', 'workshop.update_status', 'workshop.read',
            'order.read', 'order.update',
            'inventory.read', 'inventory.update',
            'report.read',
        ]);

        $cs = Role::firstOrCreate(['name' => 'CS']);
        $cs->givePermissionTo([
            'customer.create', 'customer.read', 'customer.update',
            'order.create', 'order.read', 'order.update',
            'payment.read',
            'promotion.read',
            'workshop.scan', 'workshop.read',
            'report.read',
        ]);

        $cashier = Role::firstOrCreate(['name' => 'Cashier']);
        $cashier->givePermissionTo([
            'order.create', 'order.read',
            'payment.create', 'payment.read',
            'customer.read',
            'promotion.read',
        ]);

        $workshopStaff = Role::firstOrCreate(['name' => 'Workshop Staff']);
        $workshopStaff->givePermissionTo([
            'workshop.scan', 'workshop.read',
            'order.read',
        ]);
    }
}
