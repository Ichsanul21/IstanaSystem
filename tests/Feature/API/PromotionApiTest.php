<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionApiTest extends TestCase
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

    public function test_index_returns_promotions(): void
    {
        Promotion::factory(3)->create(['is_active' => true]);

        $this->actingAs($this->user)
            ->getJson('/api/v1/promotions')
            ->assertSuccessful()
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_index_filters_by_active_status(): void
    {
        Promotion::factory()->create(['is_active' => true]);
        Promotion::factory()->create(['is_active' => false]);

        $this->actingAs($this->user)
            ->getJson('/api/v1/promotions?is_active=1')
            ->assertSuccessful();
    }

    public function test_show_returns_single_promotion(): void
    {
        $promotion = Promotion::factory()->create();

        $this->actingAs($this->user)
            ->getJson("/api/v1/promotions/{$promotion->id}")
            ->assertSuccessful()
            ->assertJsonPath('data.id', $promotion->id);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/v1/promotions', [
                'code' => '',
                'name' => '',
            ])
            ->assertStatus(422);
    }

    public function test_store_rejects_duplicate_code(): void
    {
        Promotion::factory()->create(['code' => 'EXISTING']);

        $this->actingAs($this->user)
            ->postJson('/api/v1/promotions', [
                'code' => 'EXISTING',
                'name' => 'Duplicate Code',
                'type' => 'percentage',
                'value' => 10,
                'starts_at' => now()->subDays(5)->toDateString(),
                'ends_at' => now()->addDays(30)->toDateString(),
                'is_active' => true,
            ])
            ->assertStatus(422);
    }

    public function test_eligible_returns_eligible_promotions(): void
    {
        Promotion::factory()->create(['is_active' => true]);
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'created_by' => $this->user->id,
            'total_amount' => 50000,
        ]);

        $this->actingAs($this->user)
            ->getJson("/api/v1/promotions/eligible/{$order->id}")
            ->assertSuccessful()
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_unauthenticated_access_is_blocked(): void
    {
        $this->getJson('/api/v1/promotions')
            ->assertUnauthorized();
    }
}
