<?php

namespace App\Services;

use App\Models\Promotion;

class DiscountCalculator
{
    public function calculate(Promotion $promotion, float $subtotal, array $items = []): array
    {
        $type = $promotion->type->value;

        return match ($type) {
            'percentage' => $this->calculatePercentage($promotion, $subtotal),
            'fixed' => $this->calculateFixed($promotion, $subtotal),
            'buy_get' => $this->calculateBuyGet($promotion, $items),
            default => $this->defaultResult(),
        };
    }

    private function calculatePercentage(Promotion $promotion, float $subtotal): array
    {
        $discount = $subtotal * ($promotion->value / 100);

        if ($promotion->max_discount && $discount > $promotion->max_discount) {
            $discount = $promotion->max_discount;
        }

        return [
            'discount_amount' => round($discount, 2),
            'type' => 'percentage',
            'description' => $promotion->value . '% discount',
        ];
    }

    private function calculateFixed(Promotion $promotion, float $subtotal): array
    {
        return [
            'discount_amount' => round(min($promotion->value, $subtotal), 2),
            'type' => 'fixed',
            'description' => 'Fixed discount of ' . $promotion->value,
        ];
    }

    private function calculateBuyGet(Promotion $promotion, array $items): array
    {
        if (empty($items)) {
            return $this->defaultResult();
        }

        $eligibleItems = collect($items)
            ->where('quantity', '>=', $promotion->buy_quantity)
            ->sortBy('price_per_unit');

        if ($eligibleItems->isEmpty()) {
            return $this->defaultResult();
        }

        $cheapestItem = $eligibleItems->first();
        $discount = $cheapestItem['price_per_unit'] * ($promotion->get_value ?? 1);

        return [
            'discount_amount' => round($discount, 2),
            'type' => 'buy_get',
            'description' => 'Buy ' . $promotion->buy_quantity . ' get ' . ($promotion->get_value ?? 1) . ' free',
        ];
    }

    private function defaultResult(): array
    {
        return [
            'discount_amount' => 0,
            'type' => 'none',
            'description' => '',
        ];
    }
}
