<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Models\MembershipTier;
use App\Models\Order;

class MembershipService
{
    public function checkUpgrade(Customer $customer): void
    {
        $currentTier = $customer->membershipTier;
        $points = $customer->total_points;

        $nextTier = MembershipTier::where('is_active', true)
            ->where('min_points', '<=', $points)
            ->orderBy('min_points', 'desc')
            ->first();

        if ($nextTier && (!$currentTier || $nextTier->min_points > $currentTier->min_points)) {
            $customer->update(['membership_tier_id' => $nextTier->id]);
        }
    }

    public function getBenefits(Customer $customer): array
    {
        $tier = $customer->membershipTier;

        if (!$tier) {
            return [
                'name' => 'Regular',
                'discount_percent' => 0,
                'color' => '#6B7280',
            ];
        }

        return [
            'name' => $tier->name,
            'discount_percent' => $tier->discount_percent,
            'color' => $tier->color,
            'min_points' => $tier->min_points,
        ];
    }

    public function applyMemberDiscount(Order $order): float
    {
        $customer = $order->customer;

        if (!$customer || !$customer->membershipTier) {
            return 0;
        }

        $discountPercent = $customer->membershipTier->discount_percent;

        if ($discountPercent <= 0) {
            return 0;
        }

        $discount = $order->total_amount * ($discountPercent / 100);

        return round($discount, 2);
    }
}
