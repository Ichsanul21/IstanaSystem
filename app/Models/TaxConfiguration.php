<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxConfiguration extends Model
{
    use HasFactory;
    protected $fillable = [
        'regime',
        'rate',
        'effective_date',
        'revenue_account_id',
        'payable_account_id',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'effective_date' => 'date',
    ];

    public function revenueAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'revenue_account_id');
    }

    public function payableAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'payable_account_id');
    }
}
