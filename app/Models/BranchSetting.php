<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchSetting extends Model
{
    protected $fillable = [
        'branch_id',
        'group',
        'key',
        'value',
        'type',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
