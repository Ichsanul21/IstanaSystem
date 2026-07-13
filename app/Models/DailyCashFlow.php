<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasBranchScope;

class DailyCashFlow extends Model
{
    use HasBranchScope;

    protected $fillable = [
        'branch_id',
        'date',
        'opening_balance',
        'total_revenue',
        'total_expense',
        'closing_balance',
        'is_reconciled',
    ];

    protected $casts = [
        'date' => 'date',
        'opening_balance' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'is_reconciled' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
