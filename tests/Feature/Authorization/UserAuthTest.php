<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        session(['current_branch_id' => $this->branch->id]);
    }

    public function test_users_index_requires_user_read(): void
    {
        $this->user->givePermissionTo('user.read');
        $this->actingAs($this->user)->get(route('admin.users.index'))->assertOk();

        $this->user->revokePermissionTo('user.read');
        $this->actingAs($this->user)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_users_create_requires_user_create(): void
    {
        $this->user->givePermissionTo('user.create');
        $this->actingAs($this->user)->get(route('admin.users.create'))->assertOk();

        $this->user->revokePermissionTo('user.create');
        $this->actingAs($this->user)->get(route('admin.users.create'))->assertForbidden();
    }

    public function test_users_store_requires_user_create(): void
    {
        $this->user->givePermissionTo('user.create');
        $this->actingAs($this->user)->post(route('admin.users.store'), [
            'name' => 'Test User', 'email' => 'test@example.com', 'password' => 'password123',
            'role' => 'Developer', 'branch_id' => $this->branch->id,
        ])->assertRedirect();

        $this->user->revokePermissionTo('user.create');
        $this->actingAs($this->user)->post(route('admin.users.store'), [
            'name' => 'Test User', 'email' => 'test2@example.com', 'password' => 'password123',
            'role' => 'Developer', 'branch_id' => $this->branch->id,
        ])->assertForbidden();
    }

    public function test_users_edit_requires_user_update(): void
    {
        $target = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->givePermissionTo('user.update');
        $this->actingAs($this->user)->get(route('admin.users.edit', $target))->assertOk();

        $this->user->revokePermissionTo('user.update');
        $this->actingAs($this->user)->get(route('admin.users.edit', $target))->assertForbidden();
    }

    public function test_users_destroy_requires_user_delete(): void
    {
        $target = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->givePermissionTo('user.delete');
        $this->actingAs($this->user)->delete(route('admin.users.destroy', $target))->assertRedirect();

        $freshTarget = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->revokePermissionTo('user.delete');
        $this->actingAs($this->user)->delete(route('admin.users.destroy', $freshTarget))->assertForbidden();
    }
}
