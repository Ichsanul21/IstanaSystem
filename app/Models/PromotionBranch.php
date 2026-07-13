<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionBranch extends Model
{
    protected $fillable = [
        'promotion_id',
        'branch_id',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
