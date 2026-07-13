<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionStatus extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'sequence',
        'color',
        'description',
    ];
}
