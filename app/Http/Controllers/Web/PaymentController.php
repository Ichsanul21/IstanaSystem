<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Order\OrderService;

class PaymentController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function create(Order $order)
    {
        return view('payments.create', compact('order'));
    }

    public function store(PaymentRequest $request, Order $order)
    {
        $payment = $this->orderService->processPayment($order, $request->validated());

        return redirect()->route('admin.orders.payments.show', ['order' => $order, 'payment' => $payment])->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function show(Order $order, Payment $payment)
    {
        $payment->load('order');

        return view('payments.show', compact('payment'));
    }
}
