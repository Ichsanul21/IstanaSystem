<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\Refund;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefundService
{
    public function processRefund(Order $order, array $data): Refund
    {
        return DB::transaction(function () use ($order, $data) {
            return Refund::create([
                'order_id' => $order->id,
                'payment_id' => $data['payment_id'] ?? null,
                'amount' => $data['amount'],
                'reason' => $data['reason'] ?? null,
                'status' => 'requested',
                'requested_by' => Auth::id(),
            ]);
        });
    }

    public function approve(Refund $refund): Refund
    {
        return DB::transaction(function () use ($refund) {
            $refund->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            $order = $refund->order;
            $order->decrement('total', $refund->amount);

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

    public function reject(Refund $refund, string $reason): Refund
    {
        return DB::transaction(function () use ($refund, $reason) {
            $refund->update([
                'status' => 'rejected',
                'reason' => $reason,
            ]);

            return $refund;
        });
    }

    public function complete(Refund $refund): Refund
    {
        return DB::transaction(function () use ($refund) {
            $refund->update([
                'status' => 'completed',
            ]);

            return $refund;
        });
    }
}
