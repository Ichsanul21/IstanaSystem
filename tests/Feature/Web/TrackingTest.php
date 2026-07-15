<?php

namespace Tests\Feature\Web;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
    }

    public function test_show_with_valid_token(): void
    {
        $customer = Customer::factory()->create([
            'branch_id' => $this->branch->id,
            'phone' => '0812345678',
            'pin' => '78',
        ]);

        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'customer_phone' => $customer->phone,
            'tracking_token' => 'valid-token-123',
        ]);

        $this->get(route('tracking.show', 'valid-token-123'))
            ->assertOk();
    }

    public function test_show_with_invalid_token(): void
    {
        $this->get(route('tracking.show', 'non-existent-token'))
            ->assertNotFound();
    }

    public function test_verify_pin_with_correct_pin(): void
    {
        $customer = Customer::factory()->create([
            'branch_id' => $this->branch->id,
            'phone' => '0812345678',
            'pin' => '78',
        ]);

        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'customer_phone' => $customer->phone,
            'tracking_token' => 'verify-token-123',
        ]);

        $this->postJson(route('tracking.verify', 'verify-token-123'), [
            'pin' => '78',
        ])
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_verify_pin_with_incorrect_pin(): void
    {
        $customer = Customer::factory()->create([
            'branch_id' => $this->branch->id,
            'phone' => '0812345678',
            'pin' => '78',
        ]);

        $order = Order::factory()->create([
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'customer_phone' => $customer->phone,
            'tracking_token' => 'verify-wrong-token',
        ]);

        $this->postJson(route('tracking.verify', 'verify-wrong-token'), [
            'pin' => '00',
        ])
            ->assertUnprocessable()
            ->assertJson(['error' => 'PIN salah.']);
    }
}
