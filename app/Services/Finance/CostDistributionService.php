<?php

namespace App\Services\Finance;

use App\Models\Branch;
use App\Models\Order;

class CostDistributionService
{
    public function distributeWorkshopCost(float $totalCost, int $workshopId, string $period): array
    {
        $branches = Branch::where('workshop_id', $workshopId)->get();

        if ($branches->isEmpty()) {
            return [];
        }

        $dates = $this->parsePeriod($period);
        $orderCounts = [];

        foreach ($branches as $branch) {
            $count = Order::where('branch_id', $branch->id)
                ->whereBetween('created_at', [$dates['start'], $dates['end']])
                ->count();

            $orderCounts[$branch->id] = $count;
        }

        $totalOrders = array_sum($orderCounts);

        if ($totalOrders <= 0) {
            $equalShare = $totalCost / $branches->count();
            $distribution = [];

            foreach ($branches as $branch) {
                $distribution[$branch->id] = round($equalShare, 2);
            }

            return $distribution;
        }

        $distribution = [];

        foreach ($branches as $branch) {
            $ratio = $orderCounts[$branch->id] / $totalOrders;
            $distribution[$branch->id] = round($totalCost * $ratio, 2);
        }

        return $distribution;
    }

    private function parsePeriod(string $period): array
    {
        $parts = explode('-', $period);

        if (count($parts) === 2) {
            return [
                'start' => $parts[0] . '-01',
                'end' => $parts[1] . '-01',
            ];
        }

        $year = (int) $period;
        $month = (int) date('m');

        if (strlen($period) === 7) {
            $parts = explode('-', $period);
            $year = (int) $parts[0];
            $month = (int) $parts[1];
        }

        $start = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        $end = date('Y-m-t', strtotime($start));

        return [
            'start' => $start,
            'end' => $end,
        ];
    }
}
