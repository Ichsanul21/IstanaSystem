<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->user->assignRole('Developer');
    }

    public function test_index(): void
    {
        Branch::factory(3)->create();

        $this->actingAs($this->user)
            ->get(route('admin.branches.index'))
            ->assertOk();
    }

    public function test_create(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.branches.create'))
            ->assertOk();
    }

    public function test_store(): void
    {
        $this->actingAs($this->user)
            ->post(route('admin.branches.store'), [
                'code' => 'CAB-001',
                'name' => 'Cabang Pusat',
                'address' => 'Jl. Merdeka No.1',
                'phone' => '0211234567',
                'opening_time' => '08:00',
                'closing_time' => '21:00',
                'daily_capacity' => 100,
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.branches.index'));

        $this->assertDatabaseHas('branches', ['code' => 'CAB-001']);
    }

    public function test_show(): void
    {
        $branch = Branch::factory()->create();

        $this->actingAs($this->user)
            ->get(route('admin.branches.show', $branch))
            ->assertOk();
    }

    public function test_edit(): void
    {
        $branch = Branch::factory()->create();

        $this->actingAs($this->user)
            ->get(route('admin.branches.edit', $branch))
            ->assertOk();
    }

    public function test_update(): void
    {
        $branch = Branch::factory()->create();

        $this->actingAs($this->user)
            ->put(route('admin.branches.update', $branch), [
                'code' => $branch->code,
                'name' => 'Cabang Baru',
                'address' => $branch->address,
                'phone' => '0211234567',
                'opening_time' => '08:00',
                'closing_time' => '21:00',
                'daily_capacity' => 100,
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.branches.show', $branch));

        $this->assertEquals('Cabang Baru', $branch->fresh()->name);
    }

    public function test_destroy(): void
    {
        $branch = Branch::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('admin.branches.destroy', $branch))
            ->assertRedirect(route('admin.branches.index'));

        $this->assertSoftDeleted($branch);
    }

    public function test_switch(): void
    {
        $branch = Branch::factory()->create();
        $this->user->update(['branch_id' => $this->user->branch_id]);

        $this->actingAs($this->user)
            ->post(route('admin.branch.switch', $branch))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertEquals($branch->id, session('current_branch_id'));
    }
}
