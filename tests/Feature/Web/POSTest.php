<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\Service;
use App\Models\ServicePricing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class POSTest extends TestCase
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
        $service = Service::factory()->create();
        ServicePricing::factory()->create([
            'service_id' => $service->id,
            'branch_id' => $this->branch->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->user)
            ->get(route('admin.pos.index'))
            ->assertOk();
    }

    public function test_index_requires_auth(): void
    {
        $this->get(route('admin.pos.index'))
            ->assertRedirect(route('login'));
    }
}
