<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerApiTest extends TestCase
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

    public function test_index_returns_paginated_customers(): void
    {
        Customer::factory(3)->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->getJson('/api/v1/customers')
            ->assertOk()
            ->assertJsonStructure(['success', 'data', 'meta' => ['current_page', 'last_page', 'per_page', 'total']]);
    }

    public function test_show_returns_customer(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->getJson("/api/v1/customers/{$customer->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $customer->id);
    }

    public function test_store_creates_customer(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/customers', [
                'name' => 'John Doe',
                'phone' => '08123456789',
                'email' => 'john@example.com',
                'address' => 'Test Address',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'John Doe');
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/v1/customers', [])
            ->assertStatus(422);
    }

    public function test_update_customer(): void
    {
        $customer = Customer::factory()->create([
            'branch_id' => $this->branch->id,
            'name' => 'Old Name',
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/v1/customers/{$customer->id}", [
                'name' => 'New Name',
                'phone' => '08123456789',
                'email' => 'john@example.com',
            ])
            ->assertOk();

        $this->assertEquals('New Name', $customer->fresh()->name);
    }

    public function test_search_by_name(): void
    {
        Customer::factory()->create([
            'branch_id' => $this->branch->id,
            'name' => 'Ahmad Santoso',
        ]);
        Customer::factory()->create([
            'branch_id' => $this->branch->id,
            'name' => 'Budi Santoso',
        ]);

        $this->actingAs($this->user)
            ->getJson('/api/customers/search?q=Ahmad')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_search_by_phone(): void
    {
        Customer::factory()->create([
            'branch_id' => $this->branch->id,
            'phone' => '08111111111',
        ]);

        $this->actingAs($this->user)
            ->getJson('/api/customers/search?q=08111111111')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_unauthenticated_access_is_blocked(): void
    {
        $this->getJson('/api/v1/customers')
            ->assertUnauthorized();
    }
}
