<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Promotion;
use App\Models\PromotionBranch;
use App\Models\PromotionUsage;
use Illuminate\Support\Collection;

class PromotionService
{
    public function getEligiblePromotions(?int $branchId = null, ?float $subtotal = null, ?int $itemCount = null): Collection
    {
        $query = Promotion::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());

        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->whereDoesntHave('branches')
                    ->orWhereHas('branches', fn($bq) => $bq->where('branch_id', $branchId));
            });
        }

        $promotions = $query->get();

        return $promotions->filter(function ($promotion) use ($subtotal) {
            if ($subtotal && $promotion->min_order_amount > 0 && $subtotal < $promotion->min_order_amount) {
                return false;
            }

            if ($promotion->total_usage_limit && $promotion->usages()->count() >= $promotion->total_usage_limit) {
                return false;
            }

            return true;
        })->values();
    }

    public function toggleBranch(Promotion $promotion, Branch $branch): PromotionBranch
    {
        $promotionBranch = PromotionBranch::firstOrNew([
            'promotion_id' => $promotion->id,
            'branch_id' => $branch->id,
        ]);

        $promotionBranch->is_active = !$promotionBranch->is_active;
        $promotionBranch->save();

        return $promotionBranch;
    }

    public function recordUsage(Promotion $promotion, int $orderId, ?int $customerId, float $discountAmount): PromotionUsage
    {
        return PromotionUsage::create([
            'promotion_id' => $promotion->id,
            'order_id' => $orderId,
            'customer_id' => $customerId,
            'discount_amount' => $discountAmount,
        ]);
    }

    public function getUsageCount(Promotion $promotion, ?int $customerId = null): int
    {
        $query = $promotion->usages();

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        return $query->count();
    }
}
