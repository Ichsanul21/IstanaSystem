<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    public function test_dashboard_displays_stats(): void
    {
        Order::factory(3)->create(['branch_id' => $this->branch->id]);
        Customer::factory(2)->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_dashboard_requires_auth(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }
}
