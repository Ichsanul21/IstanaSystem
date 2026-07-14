<?php

namespace Tests\Feature\Authorization;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefundAuthTest extends TestCase
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

    public function test_index(): void
    {
        $this->user->givePermissionTo('process_refund');

        $response = $this->actingAs($this->user)
            ->get(route('admin.refunds.index'));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('process_refund');

        $this->actingAs($this->user)
            ->get(route('admin.refunds.index'))
            ->assertForbidden();
    }

    public function test_store(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);

        $this->user->givePermissionTo('process_refund');

        $response = $this->actingAs($this->user)
            ->post(route('admin.refunds.store', $order), [
                'amount' => 10000,
                'reason' => 'Customer requested refund',
            ]);
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('process_refund');

        $this->actingAs($this->user)
            ->post(route('admin.refunds.store', $order), [
                'amount' => 10000,
                'reason' => 'Customer requested refund',
            ])
            ->assertForbidden();
    }

    public function test_approve(): void
    {
        $refund = Refund::factory()->create(['status' => 'requested']);

        $this->user->givePermissionTo('approve_refund');

        $response = $this->actingAs($this->user)
            ->post(route('admin.refunds.approve', $refund));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('approve_refund');

        $freshRefund = Refund::factory()->create(['status' => 'requested']);

        $this->actingAs($this->user)
            ->post(route('admin.refunds.approve', $freshRefund))
            ->assertForbidden();
    }

    public function test_reject(): void
    {
        $refund = Refund::factory()->create(['status' => 'requested']);

        $this->user->givePermissionTo('approve_refund');

        $response = $this->actingAs($this->user)
            ->post(route('admin.refunds.reject', $refund));
        $this->assertNotEquals(403, $response->getStatusCode());

        $this->user->revokePermissionTo('approve_refund');

        $freshRefund = Refund::factory()->create(['status' => 'requested']);

        $this->actingAs($this->user)
            ->post(route('admin.refunds.reject', $freshRefund))
            ->assertForbidden();
    }
}
