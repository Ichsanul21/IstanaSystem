<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchAuthTest extends TestCase
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

    public function test_branches_index_requires_branch_read(): void
    {
        $this->user->givePermissionTo('branch.read');
        $this->actingAs($this->user)->get(route('admin.branches.index'))->assertOk();

        $this->user->revokePermissionTo('branch.read');
        $this->actingAs($this->user)->get(route('admin.branches.index'))->assertForbidden();
    }

    public function test_branches_create_requires_branch_create(): void
    {
        $this->user->givePermissionTo('branch.create');
        $this->actingAs($this->user)->get(route('admin.branches.create'))->assertOk();

        $this->user->revokePermissionTo('branch.create');
        $this->actingAs($this->user)->get(route('admin.branches.create'))->assertForbidden();
    }

    public function test_branches_store_requires_branch_create(): void
    {
        $this->user->givePermissionTo('branch.create');
        $response = $this->actingAs($this->user)->post(route('admin.branches.store'), [
            'name' => 'Test Branch', 'code' => 'TST', 'address' => 'Addr', 'phone' => '0812',
            'opening_time' => '08:00', 'closing_time' => '17:00',
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('branch.create');
        $this->actingAs($this->user)->post(route('admin.branches.store'), [
            'name' => 'Test Branch', 'code' => 'TST', 'address' => 'Addr', 'phone' => '0812',
            'opening_time' => '08:00', 'closing_time' => '17:00',
        ])->assertForbidden();
    }

    public function test_branches_edit_requires_branch_update(): void
    {
        $b = Branch::factory()->create();
        $this->user->givePermissionTo('branch.update');
        $this->actingAs($this->user)->get(route('admin.branches.edit', $b))->assertOk();

        $this->user->revokePermissionTo('branch.update');
        $this->actingAs($this->user)->get(route('admin.branches.edit', $b))->assertForbidden();
    }

    public function test_branches_destroy_requires_branch_delete(): void
    {
        $b = Branch::factory()->create();
        $this->user->givePermissionTo('branch.delete');
        $this->actingAs($this->user)->delete(route('admin.branches.destroy', $b))->assertRedirect();

        $freshBranch = Branch::factory()->create();
        $this->user->revokePermissionTo('branch.delete');
        $this->actingAs($this->user)->delete(route('admin.branches.destroy', $freshBranch))->assertForbidden();
    }
}
