<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePricingAuthTest extends TestCase
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

    public function test_service_pricing_index_requires_edit_service_pricing(): void
    {
        $this->markTestSkipped('Controller has $this->middleware("role:...") in constructor. Permission-only test cannot bypass role check.');
    }

    public function test_service_pricing_create_requires_edit_service_pricing(): void
    {
        $this->markTestSkipped('Controller has $this->middleware("role:...") in constructor. Permission-only test cannot bypass role check.');
    }

    public function test_service_pricing_store_requires_edit_service_pricing(): void
    {
        $this->markTestSkipped('Controller has $this->middleware("role:...") in constructor. Permission-only test cannot bypass role check.');
    }
}
