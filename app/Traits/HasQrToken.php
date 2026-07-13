<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasQrToken
{
    public function generateQrToken(): string
    {
        $this->qr_token = Str::random(32);
        return $this->qr_token;
    }

    public function getQrRoute(): string
    {
        return route('tracking.show', $this->qr_token);
    }
}
