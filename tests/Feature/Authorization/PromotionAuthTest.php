<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionAuthTest extends TestCase
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
        $this->user->givePermissionTo('promotion.read');

        $response = $this->actingAs($this->user)
            ->get(route('admin.promotions.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('promotion.read');

        $this->actingAs($this->user)
            ->get(route('admin.promotions.index'))
            ->assertForbidden();
    }

    public function test_create(): void
    {
        $this->user->givePermissionTo('promotion.create');

        $response = $this->actingAs($this->user)
            ->get(route('admin.promotions.create'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('promotion.create');

        $this->actingAs($this->user)
            ->get(route('admin.promotions.create'))
            ->assertForbidden();
    }

    public function test_store(): void
    {
        $this->user->givePermissionTo('promotion.create');

        $response = $this->actingAs($this->user)
            ->post(route('admin.promotions.store'), [
                'code' => 'DISKON10',
                'name' => 'Test',
                'type' => 'percentage',
                'value' => 10,
                'min_order_amount' => 0,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'is_active' => true,
            ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('promotion.create');

        $this->actingAs($this->user)
            ->post(route('admin.promotions.store'), [
                'code' => 'DISKON20',
                'name' => 'Test 2',
                'type' => 'percentage',
                'value' => 20,
                'min_order_amount' => 0,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'is_active' => true,
            ])
            ->assertForbidden();
    }

    public function test_edit(): void
    {
        $promotion = Promotion::factory()->create();

        $this->user->givePermissionTo('promotion.update');

        $response = $this->actingAs($this->user)
            ->get(route('admin.promotions.edit', $promotion));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('promotion.update');

        $this->actingAs($this->user)
            ->get(route('admin.promotions.edit', $promotion))
            ->assertForbidden();
    }

    public function test_destroy(): void
    {
        $promotion = Promotion::factory()->create();

        $this->user->givePermissionTo('promotion.delete');

        $response = $this->actingAs($this->user)
            ->delete(route('admin.promotions.destroy', $promotion));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('promotion.delete');

        $freshPromotion = Promotion::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('admin.promotions.destroy', $freshPromotion))
            ->assertForbidden();
    }

    public function test_toggle_branch(): void
    {
        $promotion = Promotion::factory()->create();

        $this->user->givePermissionTo('toggle_promotion_branch');

        $response = $this->actingAs($this->user)
            ->post(route('admin.promotions.toggle-branch', [$promotion, $this->branch]));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('toggle_promotion_branch');

        $this->actingAs($this->user)
            ->post(route('admin.promotions.toggle-branch', [$promotion, $this->branch]))
            ->assertForbidden();
    }
}
