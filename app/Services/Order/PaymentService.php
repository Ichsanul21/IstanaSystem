<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function processPayment(Order $order, array $data): Payment
    {
        return DB::transaction(function () use ($order, $data) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'reference' => $data['reference'] ?? null,
                'paid_at' => now(),
                'user_id' => Auth::id(),
                'notes' => $data['notes'] ?? null,
            ]);

            $totalPaid = $order->payments()->sum('amount');
            $paymentStatus = $totalPaid >= $order->grand_total ? 'paid' : 'partial';

            $order->update([
                'payment_status' => $paymentStatus,
            ]);

            if ($paymentStatus === 'paid') {
                $order->update(['status' => 'processing']);
            }

            return $payment;
        });
    }
}
