<?php

namespace App\Services;

class DiscountService extends DiscountCalculator
{
    public function calculatePromotionDiscount($promotion, float $subtotal): float
    {
        $result = $this->calculate($promotion, $subtotal);

        return $result['discount_amount'] ?? 0;
    }
}
