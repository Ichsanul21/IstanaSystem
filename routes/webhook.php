<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PaymentWebhookController;

Route::post('/midtrans', [PaymentWebhookController::class, 'midtrans'])->name('midtrans');

Route::post('/api/v1/payments/midtrans/callback', [\App\Http\Controllers\Api\V1\PaymentWebhookController::class, 'midtrans']);
