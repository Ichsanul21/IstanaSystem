<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAuthTest extends TestCase
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

    public function test_customers_index_requires_customer_read(): void
    {
        $this->user->givePermissionTo('customer.read');
        $response = $this->actingAs($this->user)->get(route('admin.customers.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('customer.read');
        $this->actingAs($this->user)->get(route('admin.customers.index'))->assertForbidden();
    }

    public function test_customers_create_requires_customer_create(): void
    {
        $this->user->givePermissionTo('customer.create');
        $response = $this->actingAs($this->user)->get(route('admin.customers.create'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('customer.create');
        $this->actingAs($this->user)->get(route('admin.customers.create'))->assertForbidden();
    }

    public function test_customers_store_requires_customer_create(): void
    {
        $this->user->givePermissionTo('customer.create');
        $response = $this->actingAs($this->user)->post(route('admin.customers.store'), [
            'name' => 'Test', 'phone' => '081111',
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('customer.create');
        $this->actingAs($this->user)->post(route('admin.customers.store'), [
            'name' => 'Test', 'phone' => '081112',
        ])->assertForbidden();
    }

    public function test_customers_show_requires_customer_read(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->givePermissionTo('customer.read');
        $response = $this->actingAs($this->user)->get(route('admin.customers.show', $customer));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('customer.read');
        $this->actingAs($this->user)->get(route('admin.customers.show', $customer))->assertForbidden();
    }

    public function test_customers_edit_requires_customer_update(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->givePermissionTo('customer.update');
        $response = $this->actingAs($this->user)->get(route('admin.customers.edit', $customer));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('customer.update');
        $this->actingAs($this->user)->get(route('admin.customers.edit', $customer))->assertForbidden();
    }

    public function test_customers_update_requires_customer_update(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->givePermissionTo('customer.update');
        $response = $this->actingAs($this->user)->put(route('admin.customers.update', $customer), [
            'name' => 'Updated Name', 'phone' => '089999',
        ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('customer.update');
        $this->actingAs($this->user)->put(route('admin.customers.update', $customer), [
            'name' => 'Updated Name', 'phone' => '089999',
        ])->assertForbidden();
    }
}
