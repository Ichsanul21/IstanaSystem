<?php

namespace Tests\Feature\Middleware;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetBranchContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_sets_branch_id_from_user_when_authenticated(): void
    {
        $branch = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $branch->id]);
        $user->assignRole('Developer');

        $this->actingAs($user)
            ->get(route('admin.dashboard'));

        $this->assertEquals($branch->id, session('current_branch_id'));
    }

    public function test_does_not_set_branch_id_for_guest(): void
    {
        $this->get(route('login'));

        $this->assertNull(session('current_branch_id'));
    }

    public function test_developer_can_set_branch_id_via_input(): void
    {
        $branch = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => null]);
        $user->assignRole('Developer');

        $this->actingAs($user)
            ->get(route('admin.dashboard', ['branch_id' => $branch->id]));

        $this->assertEquals($branch->id, session('current_branch_id'));
    }

    public function test_super_admin_can_set_branch_id_via_session(): void
    {
        $branch = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => null]);
        $user->assignRole('Super Admin');

        session(['current_branch_id' => $branch->id]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'));

        $this->assertEquals($branch->id, session('current_branch_id'));
    }
}
