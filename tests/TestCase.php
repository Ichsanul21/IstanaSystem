<?php

namespace Tests;

use App\Models\Branch;
use App\Models\MembershipTier;
use App\Models\Service;
use App\Models\ServicePricing;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if ($this->shouldSeed()) {
            $this->seedEssentialData();
        }
    }

    private function shouldSeed(): bool
    {
        try {
            return \Schema::hasTable('roles');
        } catch (\Exception) {
            return false;
        }
    }

    protected function seedEssentialData(): void
    {
        if (Role::count() > 0) {
            return;
        }

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

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $dev = Role::firstOrCreate(['name' => 'Developer', 'guard_name' => 'web']);
        $dev->syncPermissions(Permission::all());

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions([
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

        $owner = Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'web']);
        $owner->syncPermissions([
            'branch.read', 'customer.read', 'order.read', 'payment.read',
            'promotion.read', 'inventory.read', 'workshop.read', 'report.read',
            'report.export', 'finance.read', 'settings.read', 'user.read',
        ]);

        $branchAdmin = Role::firstOrCreate(['name' => 'Branch Admin', 'guard_name' => 'web']);
        $branchAdmin->syncPermissions([
            'branch.read', 'branch.update',
            'customer.create', 'customer.read', 'customer.update', 'customer.delete',
            'order.create', 'order.read', 'order.update', 'order.delete',
            'payment.create', 'payment.read', 'payment.refund',
            'promotion.read',
            'inventory.create', 'inventory.read', 'inventory.update', 'inventory.delete',
            'workshop.scan', 'workshop.read',
            'report.read', 'settings.read', 'user.read', 'finance.read',
        ]);

        $workshopAdmin = Role::firstOrCreate(['name' => 'Workshop Admin', 'guard_name' => 'web']);
        $workshopAdmin->syncPermissions([
            'workshop.scan', 'workshop.update_status', 'workshop.read',
            'order.read', 'order.update', 'inventory.read', 'inventory.update', 'report.read',
        ]);

        $cs = Role::firstOrCreate(['name' => 'CS', 'guard_name' => 'web']);
        $cs->syncPermissions([
            'customer.create', 'customer.read', 'customer.update',
            'order.create', 'order.read', 'order.update',
            'payment.read', 'promotion.read', 'workshop.scan', 'workshop.read', 'report.read',
        ]);

        $cashier = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
        $cashier->syncPermissions([
            'order.create', 'order.read',
            'payment.create', 'payment.read',
            'customer.read', 'promotion.read',
        ]);

        $workshopStaff = Role::firstOrCreate(['name' => 'Workshop Staff', 'guard_name' => 'web']);
        $workshopStaff->syncPermissions([
            'workshop.scan', 'workshop.read', 'order.read',
        ]);

        if (MembershipTier::count() === 0) {
            MembershipTier::create(['name' => 'Bronze', 'min_points' => 0, 'discount_percent' => 0, 'color' => '#CD7F32', 'is_active' => true]);
            MembershipTier::create(['name' => 'Silver', 'min_points' => 500, 'discount_percent' => 5, 'color' => '#C0C0C0', 'is_active' => true]);
            MembershipTier::create(['name' => 'Gold', 'min_points' => 1500, 'discount_percent' => 10, 'color' => '#FFD700', 'is_active' => true]);
            MembershipTier::create(['name' => 'Platinum', 'min_points' => 5000, 'discount_percent' => 15, 'color' => '#E5E4E2', 'is_active' => true]);
        }

        if (Service::count() === 0) {
            $services = [
                ['code' => 'CK', 'name' => 'Cuci Kering', 'unit' => 'kg'],
                ['code' => 'CB', 'name' => 'Cuci Basah', 'unit' => 'kg'],
                ['code' => 'ST', 'name' => 'Setrika', 'unit' => 'kg'],
                ['code' => 'EXP', 'name' => 'Express', 'unit' => 'kg'],
                ['code' => 'SL', 'name' => 'Selimut', 'unit' => 'pcs'],
                ['code' => 'LP', 'name' => 'Lipat', 'unit' => 'kg'],
                ['code' => 'KP', 'name' => 'Karpet', 'unit' => 'm2'],
                ['code' => 'SF', 'name' => 'Sofa', 'unit' => 'pcs'],
            ];
            foreach ($services as $svc) {
                Service::create($svc);
            }
        }
    }

    protected function createUserWithRole(string $roleName, ?Branch $branch = null): User
    {
        $user = User::factory()->create([
            'branch_id' => $branch?->id,
        ]);
        $user->assignRole($roleName);

        return $user;
    }
}
