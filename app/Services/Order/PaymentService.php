<?php

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Models\ChartOfAccount;
use App\Models\Order;
use App\Models\Payment;
use App\Services\FinanceService;
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
                'method' => $data['payment_method'] ?? $data['method'],
                'reference' => $data['reference'] ?? null,
                'paid_at' => now(),
                'created_by' => Auth::id(),
                'notes' => $data['notes'] ?? null,
            ]);

            $totalPaid = $order->payments()->sum('amount');
            $paymentStatus = $totalPaid >= $order->grand_total ? 'paid' : 'partial';

            $order->update([
                'payment_status' => $paymentStatus,
            ]);

            if ($paymentStatus === 'paid') {
                $order->update(['status' => OrderStatus::Received->value]);
            }

            $this->createPaymentJournal($order, (float) $data['amount']);

            return $payment;
        });
    }

    private function createPaymentJournal(Order $order, float $paidAmount): void
    {
        $kasAccount = ChartOfAccount::where('code', config('finance.kas_account_code'))->first();
        $revenueAccount = ChartOfAccount::where('code', config('finance.revenue_account_code'))->first();

        if (!$kasAccount || !$revenueAccount) {
            return;
        }

        $lines = [
            ['account_id' => $kasAccount->id, 'debit' => $paidAmount, 'credit' => 0, 'description' => 'Pembayaran tunai'],
            ['account_id' => $revenueAccount->id, 'debit' => 0, 'credit' => $order->grand_total, 'description' => 'Pendapatan jasa'],
        ];

        if ((float) $order->discount_amount > 0) {
            $promoAccount = ChartOfAccount::where('code', config('finance.promo_expense_code'))->first();
            if ($promoAccount) {
                $lines[] = [
                    'account_id' => $promoAccount->id,
                    'debit' => (float) $order->discount_amount,
                    'credit' => 0,
                    'description' => 'Beban promosi / diskon',
                ];
            }
        }

        try {
            app(FinanceService::class)->createJournalEntry(
                "Pembayaran pesanan {$order->order_number}",
                $lines,
                $order->branch_id
            );
        } catch (\Exception $e) {
            report($e);
        }
    }
}
