<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_returns_order_with_valid_token(): void
    {
        $branch = Branch::factory()->create();
        $customer = Customer::factory()->create(['branch_id' => $branch->id]);
        $order = Order::factory()->create([
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'qr_token' => 'test-token-123',
        ]);

        $response = $this->getJson("/api/v1/track/test-token-123");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $order->id)
            ->assertJsonPath('data.qr_token', 'test-token-123');
    }

    public function test_status_returns_404_with_invalid_token(): void
    {
        $response = $this->getJson('/api/v1/track/invalid-token');

        $response->assertNotFound()
            ->assertJson(['message' => 'Order not found.']);
    }

    public function test_verify_with_correct_pin(): void
    {
        $branch = Branch::factory()->create();
        $customer = Customer::factory()->create([
            'branch_id' => $branch->id,
            'pin' => '1234',
        ]);
        $order = Order::factory()->create([
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'qr_token' => 'test-token-456',
        ]);

        $response = $this->postJson('/api/v1/track/test-token-456/verify', [
            'pin' => '1234',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.verified', true);
    }

    public function test_verify_with_incorrect_pin(): void
    {
        $branch = Branch::factory()->create();
        $customer = Customer::factory()->create([
            'branch_id' => $branch->id,
            'pin' => '1234',
        ]);
        Order::factory()->create([
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'qr_token' => 'test-token-789',
        ]);

        $response = $this->postJson('/api/v1/track/test-token-789/verify', [
            'pin' => 'wrong-pin',
        ]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Invalid PIN.']);
    }

    public function test_verify_returns_404_with_invalid_token(): void
    {
        $response = $this->postJson('/api/v1/track/invalid/verify', [
            'pin' => '1234',
        ]);

        $response->assertNotFound()
            ->assertJson(['message' => 'Order not found.']);
    }
}
