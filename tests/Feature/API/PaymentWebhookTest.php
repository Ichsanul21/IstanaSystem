<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_midtrans_webhook_returns_error_without_server_key(): void
    {
        $response = $this->postJson('/api/v1/payments/midtrans/callback', [
            'order_id' => 'CAB-0001-TEST',
            'status_code' => '200',
            'transaction_status' => 'capture',
            'gross_amount' => '50000.00',
            'signature_key' => 'test-signature',
        ]);

        $response->assertStatus(500);
    }

    public function test_midtrans_webhook_handles_invalid_signature(): void
    {
        config(['services.midtrans.server_key' => 'test-server-key']);

        $response = $this->postJson('/api/v1/payments/midtrans/callback', [
            'order_id' => 'CAB-0001-TEST',
            'status_code' => '200',
            'transaction_status' => 'capture',
            'gross_amount' => '50000.00',
            'signature_key' => 'invalid-signature',
        ]);

        $response->assertStatus(401);
    }

    public function test_midtrans_webhook_handles_empty_payload(): void
    {
        config(['services.midtrans.server_key' => 'test-server-key']);

        $response = $this->postJson('/api/v1/payments/midtrans/callback', []);

        $response->assertStatus(401);
    }

    public function test_midtrans_webhook_does_not_require_csrf(): void
    {
        $response = $this->postJson('/api/v1/payments/midtrans/callback', [
            'order_id' => 'CAB-0001-TEST',
        ]);

        $response->assertStatus(500);
    }
}
