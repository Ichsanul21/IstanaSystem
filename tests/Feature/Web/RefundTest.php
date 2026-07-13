<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefundTest extends TestCase
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
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        Refund::factory(3)->create(['order_id' => $order->id]);

        $this->actingAs($this->user)
            ->get(route('admin.refunds.index'))
            ->assertOk();
    }

    public function test_store(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('admin.refunds.store', $order), [
                'amount' => 10000,
                'reason' => 'Customer requested refund',
            ])
            ->assertRedirect(route('admin.refunds.index'));

        $this->assertDatabaseHas('refunds', [
            'order_id' => $order->id,
            'amount' => 10000,
        ]);
    }

    public function test_approve(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        $refund = Refund::factory()->create([
            'order_id' => $order->id,
            'status' => 'requested',
        ]);

        $this->actingAs($this->user)
            ->post(route('admin.refunds.approve', $refund))
            ->assertRedirect(route('admin.refunds.index'));

        $this->assertEquals('followed', $refund->fresh()->status);
    }

    public function test_reject(): void
    {
        $order = Order::factory()->create(['branch_id' => $this->branch->id]);
        $refund = Refund::factory()->create([
            'order_id' => $order->id,
            'status' => 'requested',
        ]);

        $this->actingAs($this->user)
            ->post(route('admin.refunds.reject', $refund))
            ->assertRedirect(route('admin.refunds.index'));

        $this->assertEquals('rejected', $refund->fresh()->status);
    }

    public function test_complete(): void
    {
        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'payment_status' => 'paid',
        ]);
        $refund = Refund::factory()->create([
            'order_id' => $order->id,
            'status' => 'followed',
            'amount' => 10000,
            'requested_by' => User::factory()->create()->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('admin.refunds.complete', $refund))
            ->assertRedirect(route('admin.refunds.index'));

        $this->assertEquals('completed', $refund->fresh()->status);
    }
}
