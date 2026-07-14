<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackupAuthTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->session(['current_branch_id' => $this->branch->id]);
    }

    public function test_backup_index_requires_run_backup(): void
    {
        $this->user->givePermissionTo('run_backup');
        $response = $this->actingAs($this->user)->get(route('admin.backup.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('run_backup');
        $this->actingAs($this->user)->get(route('admin.backup.index'))->assertForbidden();
    }

    public function test_backup_index_requires_view_system_info(): void
    {
        $this->user->givePermissionTo('view_system_info');
        $response = $this->actingAs($this->user)->get(route('admin.backup.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('view_system_info');
        $this->actingAs($this->user)->get(route('admin.backup.index'))->assertForbidden();
    }

    public function test_backup_create_requires_run_backup(): void
    {
        $this->user->givePermissionTo('run_backup');
        $response = $this->actingAs($this->user)->post(route('admin.backup.create'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('run_backup');
        $this->actingAs($this->user)->post(route('admin.backup.create'))->assertForbidden();
    }

    public function test_backup_forbidden_when_no_permission(): void
    {
        $this->actingAs($this->user)->get(route('admin.backup.index'))->assertForbidden();
        $this->actingAs($this->user)->post(route('admin.backup.create'))->assertForbidden();
    }
}
