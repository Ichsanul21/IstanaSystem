<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\GatewayPayment;
use App\Services\Payment\MidtransService;
use App\Services\Order\PaymentService;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService,
        protected PaymentService $paymentService
    ) {}

    public function snapToken(Request $request)
    {
        $request->validate(['order_id' => 'required|exists:orders,id']);

        $order = Order::with('items.service')->findOrFail($request->order_id);

        $transactionDetails = [
            'order_id' => $order->order_number,
            'gross_amount' => (int) $order->grand_total,
        ];

        $customerDetails = [
            'first_name' => $order->customer?->name ?? $order->customer_name,
            'phone' => $order->customer?->phone ?? $order->customer_phone ?? '',
        ];

        $itemDetails = $order->items->map(fn($item) => [
            'id' => $item->service?->code,
            'price' => (int) $item->price_per_unit,
            'quantity' => (int) $item->quantity,
            'name' => $item->service?->name,
        ])->toArray();

        $snapToken = 'snap-mock-' . uniqid();
        $redirectUrl = 'https://app.midtrans.com/snap/v2/vtweb/' . $snapToken;

        try {
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $snapToken = \Midtrans\Snap::getSnapToken([
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
            ]);
            $redirectUrl = 'https://app.midtrans.com/snap/v2/vtweb/' . $snapToken;
        } catch (\Exception $e) {
        }

        return ApiResponse::success([
            'snap_token' => $snapToken,
            'snap_redirect_url' => $redirectUrl,
        ]);
    }

    public function callback(Request $request)
    {
        $webhook = app(PaymentWebhookController::class);
        return $webhook->midtrans($request);
    }

    public function status($orderId)
    {
        $order = Order::findOrFail($orderId);
        $payment = $order->payments()->latest()->first();
        $gatewayPayment = GatewayPayment::where('order_id', $orderId)->latest()->first();

        return ApiResponse::success([
            'transaction_id' => $gatewayPayment?->transaction_id ?? $payment?->reference ?? '',
            'status' => $gatewayPayment?->status ?? $order->payment_status ?? 'unpaid',
            'payment_type' => $gatewayPayment?->payment_type ?? $payment?->method ?? '',
            'paid_at' => $payment?->created_at ?? $gatewayPayment?->updated_at ?? null,
        ]);
    }

    public function verify(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $payment = $order->payments()->latest()->first();
        if (!$payment) {
            return ApiResponse::error('No payment found', null, 404);
        }

        $payment->update(['status' => 'confirmed']);

        return ApiResponse::success(null, 'Payment verified');
    }
}
