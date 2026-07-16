<?php

namespace App\Http\Controllers\Web;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\ServicePricing;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index()
    {
        $orders = Order::forCurrentBranch()
            ->when(request('status'), fn($q, $v) => $q->where('status', $v))
            ->when(request('date_from'), fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when(request('date_to'), fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when(request('customer'), fn($q, $v) => $q->whereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$v}%")))
            ->with('customer')
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $pricings = ServicePricing::with('service')
            ->forCurrentBranch()
            ->where('is_active', true)
            ->get();

        return view('orders.create', compact('pricings'));
    }

    public function store(OrderRequest $request)
    {
        $data = $request->validated();
        $data['branch_id'] = currentBranchId();

        try {
            $order = $this->orderService->createOrder($data);

            if (($data['paid_amount'] ?? 0) > 0 && !empty($data['payment_method'])) {
                $this->orderService->processPayment($order, [
                    'amount' => (float) $data['paid_amount'],
                    'payment_method' => $data['payment_method'],
                ]);
            }

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Transaksi berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load(['items.service', 'payments', 'refunds', 'customer']);

        $statusTimeline = $order->items->flatMap->statusLogs->sortBy('created_at');

        return view('orders.show', compact('order', 'statusTimeline'));
    }

    public function edit(Order $order)
    {
        $services = ServicePricing::with('service')
            ->forCurrentBranch()
            ->where('is_active', true)
            ->get();

        return view('orders.edit', compact('order', 'services'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'nullable|string|in:' . implode(',', array_map(fn($c) => $c->value, OrderStatus::cases())),
            'notes' => 'nullable|string|max:1000',
        ]);

        $order->update($data);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Pesanan berhasil diperbarui.');
    }

    public function receipt(Order $order)
    {
        $order->load(['items', 'payments', 'customer']);

        return view('orders.receipt', compact('order'));
    }

    public function destroy(Order $order)
    {
        if (!in_array($order->status, [OrderStatus::Draft->value, OrderStatus::Pending->value, OrderStatus::Cancelled->value])) {
            return back()->withErrors(['order' => 'Only draft, pending, or cancelled orders can be deleted.']);
        }

        DB::transaction(function () use ($order) {
            $order->items()->delete();
            $order->payments()->delete();
            $order->delete();
        });

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}
