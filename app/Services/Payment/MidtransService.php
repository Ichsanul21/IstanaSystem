<?php

namespace App\Services\Payment;

use App\Models\GatewayConfiguration;
use App\Models\GatewayPayment;
use App\Models\Order;
use App\Services\Payment\PaymentGatewayInterface;

class MidtransService implements PaymentGatewayInterface
{
    public function createTransaction(Order $order): array
    {
        return [];
    }

    public function verifyPayment(array $payload): array
    {
        return $payload;
    }

    public function checkStatus(string $transactionId): array
    {
        return [];
    }

    public function handleWebhook(array $payload): GatewayPayment
    {
        $orderNumber = $payload['order_id'];
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $gatewayPayment = GatewayPayment::updateOrCreate(
            ['transaction_id' => $payload['transaction_id']],
            [
                'order_id' => $order->id,
                'gross_amount' => $payload['gross_amount'],
                'status' => $payload['transaction_status'],
                'payment_type' => $payload['payment_type'],
                'fraud_status' => $payload['fraud_status'] ?? null,
                'raw_response' => $payload,
                'paid_at' => $payload['transaction_time'] ?? now(),
            ]
        );

        $status = $payload['transaction_status'];

        if ($status === 'settlement' || $status === 'capture') {
            if ($gatewayPayment->wasRecentlyCreated || $gatewayPayment->status !== 'settlement') {
                $order->update(['payment_status' => 'paid']);
            }
        } elseif ($status === 'deny' || $status === 'cancel' || $status === 'expire') {
            $order->update(['payment_status' => 'unpaid']);
        }

        return $gatewayPayment;
    }

    public function verifyWebhookSignature(array $payload): bool
    {
        $signature = $payload['signature_key'] ?? '';
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $config = $this->getConfig();

        if (! $config) {
            return false;
        }

        $calculated = hash('sha512', $orderId . $statusCode . $grossAmount . $config->server_key);

        return $signature && hash_equals($calculated, $signature);
    }

    private function getConfig(): ?GatewayConfiguration
    {
        return GatewayConfiguration::where('is_active', true)->first();
    }
}
