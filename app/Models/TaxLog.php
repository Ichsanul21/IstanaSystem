<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxLog extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'regime',
        'base_amount',
        'rate',
        'tax_amount',
        'period',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'rate' => 'decimal:2',
    ];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
