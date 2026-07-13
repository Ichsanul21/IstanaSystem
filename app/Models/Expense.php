<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'branch_id',
        'created_by',
        'category',
        'description',
        'amount',
        'is_taxable',
        'posted_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'posted_at' => 'date',
        'is_taxable' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
