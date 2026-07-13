<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Models\LoyaltyPointsTransaction;
use App\Models\Order;
use App\Models\Setting;
use App\Services\Customer\MembershipService;

class LoyaltyPointsService
{
    public function getPointsRatio(): int
    {
        return (int) Setting::where('group', 'loyalty')->where('key', 'points_ratio')->value('value') ?? 1000;
    }

    public function getRedeemRate(): int
    {
        return (int) Setting::where('group', 'loyalty')->where('key', 'redeem_rate')->value('value') ?? 100;
    }

    public function getExpiryDays(): int
    {
        return (int) Setting::where('group', 'loyalty')->where('key', 'points_expiry_days')->value('value') ?? 90;
    }

    public function earnPoints(Order $order, Customer $customer): LoyaltyPointsTransaction
    {
        $ratio = $this->getPointsRatio();
        $points = (int) floor($order->grand_total / $ratio);

        if ($points <= 0) {
            $points = 0;
        }

        $expiryDays = $this->getExpiryDays();
        $expiresAt = $expiryDays > 0 ? now()->addDays($expiryDays) : null;

        $transaction = LoyaltyPointsTransaction::create([
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'points' => $points,
            'type' => 'earn',
            'reference' => 'Poin dari pesanan #' . $order->order_number,
            'expires_at' => $expiresAt,
        ]);

        if ($points > 0) {
            $customer->increment('total_points', $points);

            app(MembershipService::class)->checkUpgrade($customer);
        }

        return $transaction;
    }

    public function redeemPoints(Customer $customer, int $points, Order $order): LoyaltyPointsTransaction
    {
        $balance = $this->getBalance($customer);

        if ($points > $balance) {
            throw new \InvalidArgumentException('Poin tidak mencukupi. Saldo: ' . $balance);
        }

        $transaction = LoyaltyPointsTransaction::create([
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'points' => -$points,
            'type' => 'redeem',
            'reference' => 'Tukar poin untuk pesanan #' . $order->order_number,
        ]);

        $customer->decrement('total_points', $points);

        return $transaction;
    }

    public function expirePoints(): void
    {
        $transactions = LoyaltyPointsTransaction::where('type', 'earn')
            ->where('points', '>', 0)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->whereNull('expired_at')
            ->get();

        foreach ($transactions as $transaction) {
            $transaction->update(['expired_at' => now()]);

            $customer = $transaction->customer;
            if ($customer) {
                $customer->decrement('total_points', $transaction->points);
            }

            LoyaltyPointsTransaction::create([
                'customer_id' => $transaction->customer_id,
                'points' => -$transaction->points,
                'type' => 'expire',
                'reference' => 'Poin kadaluarsa: ' . $transaction->reference,
                'expires_at' => null,
            ]);
        }
    }

    public function getBalance(Customer $customer): int
    {
        return $customer->total_points ?? 0;
    }

    public function getRedeemValue(int $points): float
    {
        $rate = $this->getRedeemRate();

        return ($points / $rate) * 1000;
    }

    public function getPointsFromAmount(float $amount): int
    {
        $ratio = $this->getPointsRatio();

        return (int) floor($amount / $ratio);
    }
}
