<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->assignRole('Developer');
        session(['current_branch_id' => $this->branch->id]);
    }

    public function test_index(): void
    {
        Promotion::factory(3)->create();

        $this->actingAs($this->user)
            ->get(route('admin.promotions.index'))
            ->assertOk();
    }

    public function test_create(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.promotions.create'))
            ->assertOk();
    }

    public function test_store(): void
    {
        $this->actingAs($this->user)
            ->post(route('admin.promotions.store'), [
                'code' => 'DISKON50',
                'name' => 'Diskon 50%',
                'type' => 'percentage',
                'value' => 50,
                'min_order_amount' => 0,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'is_active' => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('promotions', ['code' => 'DISKON50']);
    }

    public function test_store_with_branches(): void
    {
        $this->actingAs($this->user)
            ->post(route('admin.promotions.store'), [
                'code' => 'DISKON30',
                'name' => 'Diskon 30%',
                'type' => 'percentage',
                'value' => 30,
                'min_order_amount' => 0,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'is_active' => true,
                'branches' => [$this->branch->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('promotion_branches', ['branch_id' => $this->branch->id]);
    }

    public function test_show(): void
    {
        $promotion = Promotion::factory()->create();

        $this->actingAs($this->user)
            ->get(route('admin.promotions.show', $promotion))
            ->assertOk();
    }

    public function test_edit(): void
    {
        $promotion = Promotion::factory()->create();

        $this->actingAs($this->user)
            ->get(route('admin.promotions.edit', $promotion))
            ->assertOk();
    }

    public function test_update(): void
    {
        $promotion = Promotion::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->user)
            ->put(route('admin.promotions.update', $promotion), [
                'code' => $promotion->code,
                'name' => 'New Name',
                'type' => 'percentage',
                'value' => 20,
                'min_order_amount' => 0,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.promotions.show', $promotion));

        $this->assertEquals('New Name', $promotion->fresh()->name);
    }

    public function test_destroy(): void
    {
        $promotion = Promotion::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('admin.promotions.destroy', $promotion))
            ->assertRedirect(route('admin.promotions.index'));

        $this->assertSoftDeleted($promotion);
    }

    public function test_toggle_branch(): void
    {
        $promotion = Promotion::factory()->create();

        $this->actingAs($this->user)
            ->post(route('admin.promotions.toggle-branch', [$promotion, $this->branch]))
            ->assertRedirect();

        $this->assertDatabaseHas('promotion_branches', [
            'promotion_id' => $promotion->id,
            'branch_id' => $this->branch->id,
        ]);
    }
}
