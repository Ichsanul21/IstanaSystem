<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditAuthTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        session(['current_branch_id' => $this->branch->id]);
    }

    public function test_index_requires_view_activity_logs_permission(): void
    {
        $this->user->givePermissionTo('view_activity_logs');
        $response = $this->actingAs($this->user)->get(route('admin.audit.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('view_activity_logs');
        $this->actingAs($this->user)->get(route('admin.audit.index'))->assertForbidden();
    }

    public function test_export_requires_view_activity_logs_permission(): void
    {
        $this->user->givePermissionTo('view_activity_logs');
        $response = $this->actingAs($this->user)->get(route('admin.audit.export'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('view_activity_logs');
        $this->actingAs($this->user)->get(route('admin.audit.export'))->assertForbidden();
    }
}
