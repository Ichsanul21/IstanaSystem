<?php

namespace Tests;

use App\Models\Branch;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
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

        $this->seed(RolePermissionSeeder::class);

        if (\App\Models\MembershipTier::count() === 0) {
            \App\Models\MembershipTier::create(['name' => 'Bronze', 'min_points' => 0, 'discount_percent' => 0, 'color' => '#CD7F32', 'is_active' => true]);
            \App\Models\MembershipTier::create(['name' => 'Silver', 'min_points' => 500, 'discount_percent' => 5, 'color' => '#C0C0C0', 'is_active' => true]);
            \App\Models\MembershipTier::create(['name' => 'Gold', 'min_points' => 1500, 'discount_percent' => 10, 'color' => '#FFD700', 'is_active' => true]);
            \App\Models\MembershipTier::create(['name' => 'Platinum', 'min_points' => 5000, 'discount_percent' => 15, 'color' => '#E5E4E2', 'is_active' => true]);
        }

        if (\App\Models\Service::count() === 0) {
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
                \App\Models\Service::create($svc);
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
