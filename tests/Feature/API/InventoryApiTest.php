<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryApiTest extends TestCase
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

    public function test_items_index_returns_paginated_items(): void
    {
        InventoryItem::factory(3)->create();

        $this->actingAs($this->user)
            ->getJson('/api/v1/inventory/items')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_items_index_filters_by_category(): void
    {
        InventoryItem::factory()->create(['category' => 'deterjen']);
        InventoryItem::factory()->create(['category' => 'pewangi']);

        $this->actingAs($this->user)
            ->getJson('/api/v1/inventory/items?category=deterjen')
            ->assertOk();
    }

    public function test_items_store_creates_item(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/inventory/items', [
                'name' => 'Deterjen Bubuk',
                'unit' => 'kg',
                'min_stock' => 10,
            ]);

        $response->assertCreated()
            ->assertJsonPath('success', true);
    }

    public function test_items_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/v1/inventory/items', [
                'name' => '',
            ])
            ->assertStatus(422);
    }

    public function test_items_show_returns_single_item(): void
    {
        $item = InventoryItem::factory()->create();

        $this->actingAs($this->user)
            ->getJson("/api/v1/inventory/items/{$item->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $item->id);
    }

    public function test_items_show_returns_404_for_nonexistent(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/v1/inventory/items/99999')
            ->assertStatus(404);
    }

    public function test_items_update_modifies_item(): void
    {
        $item = InventoryItem::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->user)
            ->putJson("/api/v1/inventory/items/{$item->id}", [
                'name' => 'New Name',
            ])
            ->assertOk();

        $this->assertEquals('New Name', $item->fresh()->name);
    }

    public function test_unauthenticated_access_is_blocked(): void
    {
        $this->getJson('/api/v1/inventory/items')
            ->assertUnauthorized();
    }
}
