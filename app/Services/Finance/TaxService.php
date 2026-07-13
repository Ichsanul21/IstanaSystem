<?php

namespace App\Services\Finance;

use App\Models\Order;
use App\Models\TaxConfiguration;
use App\Models\TaxLog;

class TaxService
{
    public function calculateTax(Order $order): float
    {
        $taxConfig = $this->getActiveTaxConfig();

        if (!$taxConfig) {
            return 0;
        }

        $baseAmount = $order->total_amount - ($order->discount_amount ?? 0);
        $taxAmount = $baseAmount * ($taxConfig->rate / 100);

        TaxLog::create([
            'regime' => $taxConfig->regime,
            'base_amount' => $baseAmount,
            'rate' => $taxConfig->rate,
            'tax_amount' => $taxAmount,
        ]);

        return $taxAmount;
    }

    public function getActiveTaxRate(): float
    {
        $taxConfig = $this->getActiveTaxConfig();

        return $taxConfig ? (float) $taxConfig->rate : 0;
    }

    private function getActiveTaxConfig(): ?TaxConfiguration
    {
        return TaxConfiguration::where('is_active', true)
            ->where('effective_date', '<=', now()->toDateString())
            ->orderBy('effective_date', 'desc')
            ->first();
    }
}
