<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\MembershipTier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->user->assignRole('Developer');
        session(['current_branch_id' => $this->branch->id]);
    }

    public function test_index(): void
    {
        Customer::factory(3)->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->get(route('admin.customers.index'))
            ->assertOk();
    }

    public function test_index_filters_by_search(): void
    {
        Customer::factory()->create(['branch_id' => $this->branch->id, 'name' => 'Ahmad']);
        Customer::factory()->create(['branch_id' => $this->branch->id, 'name' => 'Budi']);

        $this->actingAs($this->user)
            ->get(route('admin.customers.index', ['search' => 'Ahmad']))
            ->assertOk();
    }

    public function test_create(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.customers.create'))
            ->assertOk();
    }

    public function test_store(): void
    {
        $tier = MembershipTier::where('is_active', true)->first();

        $this->actingAs($this->user)
            ->post(route('admin.customers.store'), [
                'name' => 'John Doe',
                'phone' => '08123456789',
                'email' => 'john@example.com',
                'address' => 'Jl. Test No.1',
                'membership_tier_id' => $tier->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('customers', ['phone' => '08123456789']);
    }

    public function test_show(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->get(route('admin.customers.show', $customer))
            ->assertOk();
    }

    public function test_edit(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->get(route('admin.customers.edit', $customer))
            ->assertOk();
    }

    public function test_update(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id, 'name' => 'Old Name']);

        $tier = MembershipTier::where('is_active', true)->first();

        $this->actingAs($this->user)
            ->put(route('admin.customers.update', $customer), [
                'name' => 'New Name',
                'phone' => $customer->phone,
                'email' => $customer->email,
                'address' => $customer->address,
                'membership_tier_id' => $tier->id,
            ])
            ->assertRedirect(route('admin.customers.show', $customer));

        $this->assertEquals('New Name', $customer->fresh()->name);
    }

    public function test_add_points(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id, 'total_points' => 100]);

        $this->actingAs($this->user)
            ->post(route('admin.customers.points', $customer), [
                'points' => 50,
                'reason' => 'Bonus membership',
            ])
            ->assertRedirect(route('admin.customers.show', $customer));

        $this->assertEquals(150, $customer->fresh()->total_points);
    }

    public function test_add_negative_points_redeems(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id, 'total_points' => 100]);

        $this->actingAs($this->user)
            ->post(route('admin.customers.points', $customer), [
                'points' => -30,
                'reason' => 'Redeem voucher',
            ])
            ->assertRedirect(route('admin.customers.show', $customer));

        $this->assertEquals(70, $customer->fresh()->total_points);
    }

    public function test_get_by_phone(): void
    {
        $customer = Customer::factory()->create([
            'branch_id' => $this->branch->id,
            'phone' => '08111111111',
        ]);

        $this->actingAs($this->user)
            ->get(route('admin.customers.by-phone', '08111111111'))
            ->assertOk()
            ->assertJsonPath('id', $customer->id);
    }

    public function test_destroy(): void
    {
        $customer = Customer::factory()->create(['branch_id' => $this->branch->id]);

        $this->actingAs($this->user)
            ->delete(route('admin.customers.destroy', $customer))
            ->assertRedirect(route('admin.customers.index'));

        $this->assertSoftDeleted($customer);
    }
}
