<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Refund;
use App\Services\Finance\TaxService;
use App\Services\Tracking\TrackingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $branch = \App\Models\Branch::findOrFail($data['branch_id']);
            $orderNumber = generateOrderNumber($branch->code);

            $itemsData = $data['items'];
            unset($data['items']);

            $discountAmount = 0;
            $promotionId = null;

            if (!empty($data['promotion_code'])) {
                $promotion = \App\Models\Promotion::where('code', $data['promotion_code'])
                    ->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                    })
                    ->first();

                if ($promotion) {
                    $subtotal = 0;
                    foreach ($itemsData as $itemData) {
                        $pricing = \App\Models\ServicePricing::findOrFail($itemData['service_pricing_id']);
                        $subtotal += $pricing->price * $itemData['quantity'];
                    }

                    if ($subtotal >= ($promotion->min_order_amount ?? 0)) {
                        $discountAmount = $promotion->type === 'percentage'
                            ? $subtotal * ($promotion->value / 100)
                            : $promotion->value;

                        if (($promotion->max_discount ?? 0) > 0 && $discountAmount > $promotion->max_discount) {
                            $discountAmount = $promotion->max_discount;
                        }

                        $discountAmount = min($discountAmount, $subtotal);
                        $promotionId = $promotion->id;
                    }
                }
            }

            unset($data['payment_method'], $data['paid_amount'], $data['promotion_code']);

            $order = Order::create(array_merge($data, [
                'order_number' => $orderNumber,
                'created_by' => Auth::id(),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'total_amount' => 0,
                'discount_amount' => $discountAmount,
                'grand_total' => 0,
            ]));

            if ($promotionId) {
                \App\Models\PromotionUsage::create([
                    'promotion_id' => $promotionId,
                    'order_id' => $order->id,
                    'customer_id' => $data['customer_id'] ?? null,
                    'discount_amount' => $discountAmount,
                ]);
            }

            $subtotal = 0;

            foreach ($itemsData as $itemData) {
                $pricing = \App\Models\ServicePricing::findOrFail($itemData['service_pricing_id']);
                $unitPrice = $pricing->price;
                $quantity = $itemData['quantity'];
                $itemSubtotal = $unitPrice * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $pricing->service_id,
                    'quantity' => $quantity,
                    'price_per_unit' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                ]);

                $subtotal += $itemSubtotal;
            }

            $order->update(['total_amount' => $subtotal]);

            $taxService = app(TaxService::class);
            $taxAmount = $taxService->calculateTax($order);

            $order->update([
                'tax_amount' => $taxAmount,
                'grand_total' => $subtotal - $discountAmount + $taxAmount,
            ]);

            $trackingService = app(TrackingService::class);
            $trackingService->generateTokenForOrder($order);

            return $order;
        });
    }

    public function processPayment(Order $order, array $paymentData): Payment
    {
        return DB::transaction(function () use ($order, $paymentData) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $paymentData['amount'],
                'method' => $paymentData['payment_method'],
                'reference' => $paymentData['reference'] ?? null,
                'paid_at' => now(),
                'created_by' => Auth::id(),
                'notes' => $paymentData['notes'] ?? null,
            ]);

            $totalPaid = $order->payments()->sum('amount');
            $paymentStatus = $totalPaid >= $order->grand_total ? 'paid' : 'partial';

            $order->update([
                'payment_status' => $paymentStatus,
            ]);

            if ($paymentStatus === 'paid') {
                $order->update(['status' => 'process']);
            }

            return $payment;
        });
    }

    public function processRefund(Order $order, Refund $refund): Refund
    {
        return DB::transaction(function () use ($order, $refund) {
            $refund->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            $order->decrement('grand_total', $refund->amount);

            $totalRefunded = $order->refunds()->where('status', 'approved')->sum('amount');
            $totalPaid = $order->payments()->sum('amount');

            if ($totalRefunded >= $totalPaid) {
                $order->update(['payment_status' => 'refunded']);
            } elseif ($totalRefunded > 0) {
                $order->update(['payment_status' => 'partial_refund']);
            }

            return $refund;
        });
    }
}
