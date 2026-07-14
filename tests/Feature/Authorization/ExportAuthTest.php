<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportAuthTest extends TestCase
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

    public function test_revenue_requires_export_data_permission(): void
    {
        $this->user->givePermissionTo('export_data');
        $response = $this->actingAs($this->user)->get(route('admin.exports.revenue'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('export_data');
        $this->actingAs($this->user)->get(route('admin.exports.revenue'))->assertForbidden();
    }

    public function test_orders_requires_export_data_permission(): void
    {
        $this->user->givePermissionTo('export_data');
        $response = $this->actingAs($this->user)->get(route('admin.exports.orders'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('export_data');
        $this->actingAs($this->user)->get(route('admin.exports.orders'))->assertForbidden();
    }
}
