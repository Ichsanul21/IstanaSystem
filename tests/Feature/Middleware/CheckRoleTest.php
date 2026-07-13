<?php

namespace Tests\Feature\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_role_can_access(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Developer');

        $this->actingAs($user)
            ->get(route('admin.branches.index'))
            ->assertOk();
    }

    public function test_user_without_role_is_forbidden(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Cashier');

        $this->actingAs($user)
            ->get(route('admin.branches.index'))
            ->assertForbidden();
    }

    public function test_guest_is_forbidden(): void
    {
        $this->get(route('admin.branches.index'))
            ->assertRedirect(route('login'));
    }

    public function test_user_with_any_of_multiple_roles_can_access(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Branch Admin');

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertOk();
    }
}
