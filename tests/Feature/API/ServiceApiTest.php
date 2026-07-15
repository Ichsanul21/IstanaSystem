<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Service;
use App\Models\ServicePricing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceApiTest extends TestCase
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

    public function test_index_returns_services(): void
    {
        Service::factory(3)->create(['is_active' => true]);

        $this->actingAs($this->user)
            ->getJson('/api/v1/services')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_index_includes_created_services(): void
    {
        $before = Service::where('is_active', true)->count();
        Service::factory()->create(['is_active' => true]);

        $this->actingAs($this->user)
            ->getJson('/api/v1/services')
            ->assertOk()
            ->assertJsonCount($before + 1, 'data');
    }

    public function test_show_returns_single_service(): void
    {
        $service = Service::factory()->create();

        $this->actingAs($this->user)
            ->getJson("/api/v1/services/{$service->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $service->id)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'code', 'name', 'unit', 'description', 'is_active'],
            ]);
    }

    public function test_show_returns_404_for_nonexistent_service(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/v1/services/99999')
            ->assertStatus(404);
    }

    public function test_pricings_returns_service_pricings(): void
    {
        ServicePricing::factory()->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->getJson('/api/v1/service-pricings')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_pricings_filters_by_branch(): void
    {
        ServicePricing::factory()->create(['branch_id' => $this->branch->id]);
        ServicePricing::factory()->create();

        $this->actingAs($this->user)
            ->getJson("/api/v1/service-pricings?branch_id={$this->branch->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_unauthenticated_access_is_blocked(): void
    {
        $this->getJson('/api/v1/services')
            ->assertUnauthorized();
    }
}
