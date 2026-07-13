<?php

namespace App\Services\Tracking;

use App\Models\Order;
use Illuminate\Support\Str;

class TrackingService
{
    public function generateToken(): string
    {
        return Str::uuid()->toString();
    }

    public function generateTokenForOrder(Order $order): string
    {
        $token = $this->generateToken();
        $order->update(['tracking_token' => $token]);
        return $token;
    }

    public function findByToken(string $token): ?Order
    {
        return Order::where('tracking_token', $token)->first();
    }

    public function verifyPin(Order $order, string $pin): bool
    {
        $phone = $order->customer?->phone ?? $order->customer_phone;
        if (!$phone) return false;
        $expectedPin = substr($phone, -2);
        return $pin === $expectedPin;
    }

    public function getOrderStatus(Order $order): array
    {
        $order->load(['items.servicePricing.service', 'items.statusLogs.productionStatus' => function ($q) {
            $q->latest()->take(1);
        }]);

        $totalStatuses = 7;
        $items = $order->items;
        $completedStatuses = 0;

        foreach ($items as $item) {
            $latestStatus = $item->statusLogs->first();
            if ($latestStatus && $latestStatus->productionStatus) {
                $statusIndex = array_search($latestStatus->productionStatus->code, [
                    'TERIMA', 'CUCI', 'KERING', 'LIPAT', 'CEK', 'SIAP', 'DIAMBIL'
                ]);
                $completedStatuses += $statusIndex !== false ? min($statusIndex + 1, $totalStatuses) : 0;
            }
        }

        $totalPossible = $items->count() * $totalStatuses;
        $progress = $totalPossible > 0 ? round(($completedStatuses / $totalPossible) * 100) : 0;

        return [
            'order' => $order,
            'items' => $items,
            'progress' => $progress,
        ];
    }

    public function getWaLink(Order $order): ?string
    {
        $phone = $order->customer?->phone ?? $order->customer_phone;
        if (!$phone) return null;

        $customerName = $order->customer?->name ?? $order->customer_name;
        $trackingUrl = route('tracking.show', $order->tracking_token);
        $message = "Halo kak {$customerName},\n\n";
        $message .= "Status pesanan Anda telah berubah:\n\n";
        $message .= "📦 Pesanan: #{$order->order_number}\n";
        $message .= "📍 Status: {$order->status}\n\n";
        $message .= "Pantau terus pesanan Anda di sini:\n{$trackingUrl}\n\n";
        $message .= "Terima kasih telah menggunakan Istana Laundry ❤️";

        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }
}
