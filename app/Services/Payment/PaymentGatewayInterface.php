<?php

namespace App\Services\Payment;

use App\Models\Order;

interface PaymentGatewayInterface
{
    public function createTransaction(Order $order): array;

    public function verifyPayment(array $payload): array;

    public function checkStatus(string $transactionId): array;
}
