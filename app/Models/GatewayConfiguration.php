<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatewayConfiguration extends Model
{
    protected $fillable = [
        'merchant_id',
        'server_key',
        'client_key',
        'is_production',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_production' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
