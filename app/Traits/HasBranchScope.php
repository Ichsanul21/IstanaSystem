<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasBranchScope
{
    public function scopeForBranch(Builder $query, int $branchId): Builder
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForCurrentBranch(Builder $query): Builder
    {
        return $query->where('branch_id', currentBranchId());
    }
}
