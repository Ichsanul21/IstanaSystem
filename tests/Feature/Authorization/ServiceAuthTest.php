<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceAuthTest extends TestCase
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

    public function test_services_index_requires_view_services(): void
    {
        $this->user->givePermissionTo('view_services');
        $response = $this->actingAs($this->user)->get(route('admin.services.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('view_services');
        $this->actingAs($this->user)->get(route('admin.services.index'))->assertForbidden();
    }

    public function test_services_create_requires_create_services(): void
    {
        $this->user->givePermissionTo('create_services');
        $response = $this->actingAs($this->user)->get(route('admin.services.create'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('create_services');
        $this->actingAs($this->user)->get(route('admin.services.create'))->assertForbidden();
    }

    public function test_services_store_requires_create_services(): void
    {
        $this->user->givePermissionTo('create_services');
        $response = $this->actingAs($this->user)->post(route('admin.services.store'), [
            'code' => 'SVC-999', 'name' => 'Cuci Setrika', 'unit' => 'kg', 'category' => 'Cuci',
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('create_services');
        $this->actingAs($this->user)->post(route('admin.services.store'), [
            'code' => 'SVC-998', 'name' => 'Cuci Setrika 2', 'unit' => 'kg', 'category' => 'Cuci',
        ])->assertForbidden();
    }

    public function test_services_edit_requires_edit_services(): void
    {
        $service = Service::factory()->create();
        $this->user->givePermissionTo('edit_services');
        $response = $this->actingAs($this->user)->get(route('admin.services.edit', $service));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('edit_services');
        $this->actingAs($this->user)->get(route('admin.services.edit', $service))->assertForbidden();
    }

    public function test_services_update_requires_edit_services(): void
    {
        $service = Service::factory()->create();
        $this->user->givePermissionTo('edit_services');
        $response = $this->actingAs($this->user)->put(route('admin.services.update', $service), [
            'code' => $service->code, 'name' => 'Updated Service', 'unit' => 'kg', 'category' => 'Cuci',
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('edit_services');
        $this->actingAs($this->user)->put(route('admin.services.update', $service), [
            'code' => $service->code, 'name' => 'Updated Service 2', 'unit' => 'kg', 'category' => 'Cuci',
        ])->assertForbidden();
    }
}
