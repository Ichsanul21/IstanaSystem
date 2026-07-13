<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->admin = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->admin->assignRole('Developer');
        session(['current_branch_id' => $this->branch->id]);
    }

    public function test_index(): void
    {
        User::factory(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_create(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users.create'))
            ->assertOk();
    }

    public function test_store(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'Cashier',
                'branch_id' => $this->branch->id,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_show(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $user))
            ->assertOk();
    }

    public function test_edit(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.users.edit', $user))
            ->assertOk();
    }

    public function test_update(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Cashier');

        $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Updated Name',
                'email' => $user->email,
                'password' => '',
                'password_confirmation' => '',
                'role' => 'Cashier',
                'branch_id' => $this->branch->id,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertEquals('Updated Name', $user->fresh()->name);
    }

    public function test_destroy(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Cashier');

        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertModelMissing($user);
    }
}
