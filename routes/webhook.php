<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PaymentWebhookController;

Route::post('/v1/payments/midtrans/callback', [PaymentWebhookController::class, 'midtrans'])->name('midtrans');
