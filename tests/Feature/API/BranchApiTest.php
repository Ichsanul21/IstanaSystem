<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchApiTest extends TestCase
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

    public function test_index_returns_branches(): void
    {
        Branch::factory(3)->create();

        $this->actingAs($this->user)
            ->getJson('/api/v1/branches')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_index_filters_by_search(): void
    {
        Branch::factory()->create(['name' => 'Cabang Utama']);
        Branch::factory()->create(['name' => 'Cabang Selatan']);

        $this->actingAs($this->user)
            ->getJson('/api/v1/branches?search=Utama')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_show_returns_single_branch(): void
    {
        $this->actingAs($this->user)
            ->getJson("/api/v1/branches/{$this->branch->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $this->branch->id)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'code', 'name', 'address', 'phone', 'is_active'],
            ]);
    }

    public function test_show_returns_404_for_nonexistent_branch(): void
    {
        $this->actingAs($this->user)
            ->getJson('/api/v1/branches/99999')
            ->assertStatus(404);
    }

    public function test_unauthenticated_access_is_blocked(): void
    {
        $this->getJson('/api/v1/branches')
            ->assertUnauthorized();
    }
}
