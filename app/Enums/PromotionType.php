<?php

namespace App\Enums;

enum PromotionType: string
{
    case Percentage = 'percentage';
    case Fixed = 'fixed';
    case BuyXGetY = 'buy_x_get_y';

    public function label(): string
    {
        return match ($this) {
            self::Percentage => 'Persentase',
            self::Fixed => 'Nominal Tetap',
            self::BuyXGetY => 'Beli Dapat',
        };
    }

    public function isPercentage(): bool
    {
        return $this === self::Percentage;
    }

    public function isFixed(): bool
    {
        return $this === self::Fixed;
    }
}
