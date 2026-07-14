<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\MembershipTier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipTierAuthTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        session(['current_branch_id' => $this->branch->id]);
    }

    public function test_index(): void
    {
        $this->user->givePermissionTo('manage_tiers');

        $response = $this->actingAs($this->user)
            ->get(route('admin.membership-tiers.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('manage_tiers');

        $this->actingAs($this->user)
            ->get(route('admin.membership-tiers.index'))
            ->assertForbidden();
    }

    public function test_edit(): void
    {
        $tier = MembershipTier::factory()->create();

        $this->user->givePermissionTo('manage_tiers');

        $response = $this->actingAs($this->user)
            ->get(route('admin.membership-tiers.edit', $tier));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('manage_tiers');

        $this->actingAs($this->user)
            ->get(route('admin.membership-tiers.edit', $tier))
            ->assertForbidden();
    }

    public function test_update(): void
    {
        $tier = MembershipTier::factory()->create(['name' => 'Bronze']);

        $this->user->givePermissionTo('manage_tiers');

        $response = $this->actingAs($this->user)
            ->put(route('admin.membership-tiers.update', $tier), [
                'name' => 'Silver',
                'level' => $tier->level,
                'min_points' => 100,
                'discount_percent' => 5,
                'discount_per_order' => 0,
                'free_delivery' => false,
                'priority_service' => false,
                'birthday_voucher' => 0,
                'color' => '#C0C0C0',
                'is_active' => true,
            ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('manage_tiers');

        $this->actingAs($this->user)
            ->put(route('admin.membership-tiers.update', $tier), [
                'name' => 'Gold',
                'level' => $tier->level,
                'min_points' => 200,
                'discount_percent' => 10,
                'discount_per_order' => 0,
                'free_delivery' => true,
                'priority_service' => true,
                'birthday_voucher' => 25000,
                'color' => '#FFD700',
                'is_active' => true,
            ])
            ->assertForbidden();
    }
}
