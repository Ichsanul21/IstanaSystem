<?php

namespace Tests\Unit\Services;

use App\Models\GatewayConfiguration;
use App\Models\Order;
use App\Services\Payment\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransServiceTest extends TestCase
{
    use RefreshDatabase;

    private MidtransService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(MidtransService::class);
    }

    public function test_verify_webhook_signature_with_valid_payload(): void
    {
        GatewayConfiguration::create([
            'server_key' => 'SB-Mid-server-test_key',
            'client_key' => 'SB-Mid-client-test_key',
            'merchant_id' => 'G123456',
            'is_production' => false,
            'is_active' => true,
        ]);

        $payload = [
            'transaction_status' => 'settlement',
            'order_id' => 'ORD-001',
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'currency' => 'IDR',
            'transaction_time' => '2026-07-09 12:00:00',
            'signature_key' => hash('sha512', 'ORD-001' . '200' . '50000.00' . 'SB-Mid-server-test_key'),
        ];

        $result = $this->service->verifyWebhookSignature($payload);

        $this->assertTrue($result);
    }

    public function test_verify_webhook_signature_with_invalid_payload(): void
    {
        GatewayConfiguration::create([
            'server_key' => 'SB-Mid-server-test_key',
            'client_key' => 'SB-Mid-client-test_key',
            'merchant_id' => 'G123456',
            'is_production' => false,
            'is_active' => true,
        ]);

        $payload = [
            'transaction_status' => 'settlement',
            'order_id' => 'ORD-001',
            'gross_amount' => '50000.00',
            'currency' => 'IDR',
            'transaction_time' => '2026-07-09 12:00:00',
            'signature_key' => 'invalid-signature',
        ];

        $result = $this->service->verifyWebhookSignature($payload);

        $this->assertFalse($result);
    }

    public function test_verify_webhook_signature_returns_false_without_config(): void
    {
        $payload = [
            'transaction_status' => 'settlement',
            'order_id' => 'ORD-001',
            'gross_amount' => '50000.00',
            'signature_key' => 'anything',
        ];

        $result = $this->service->verifyWebhookSignature($payload);

        $this->assertFalse($result);
    }

    public function test_handle_webhook_creates_gateway_payment(): void
    {
        GatewayConfiguration::create([
            'server_key' => 'SB-Mid-server-test_key',
            'client_key' => 'SB-Mid-client-test_key',
            'merchant_id' => 'G123456',
            'is_production' => false,
            'is_active' => true,
        ]);

        $order = Order::factory()->create([
            'order_number' => 'ORD-001',
        ]);

        $payload = [
            'transaction_status' => 'settlement',
            'order_id' => $order->order_number,
            'gross_amount' => '50000.00',
            'currency' => 'IDR',
            'transaction_time' => '2026-07-09 12:00:00',
            'transaction_id' => 'TRX-001',
            'payment_type' => 'bank_transfer',
            'bank' => 'bca',
            'va_numbers' => [['va_number' => '1234567890']],
            'status_code' => '200',
            'signature_key' => hash('sha512', $order->order_number . '200' . '50000.00' . 'SB-Mid-server-test_key'),
        ];

        $gatewayPayment = $this->service->handleWebhook($payload);

        $this->assertDatabaseHas('gateway_payments', [
            'order_id' => $order->id,
            'transaction_id' => 'TRX-001',
        ]);
        $this->assertEquals('settlement', $gatewayPayment->status);
    }
}
