<?php

namespace App\Http\Controllers\Web;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductionStatus;
use App\Models\Workshop;
use App\Enums\ProductionStatus as ProductionStatusEnum;
use App\Services\Workshop\WorkshopService;
use App\Services\Workshop\StatusTransitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkshopController extends Controller
{
    public function __construct(
        protected WorkshopService $workshopService,
        protected StatusTransitionService $transitionService
    ) {}

    public function index()
    {
        $grouped = OrderItem::whereHas('order', fn($q) => $q->forCurrentBranch()->whereNotIn('status', [OrderStatus::ReadyForPickup->value, OrderStatus::PickedUp->value, OrderStatus::Cancelled->value]))
            ->with(['order.customer', 'statusLogs' => fn($q) => $q->latest()])
            ->get()
            ->groupBy(function ($item) {
                $latestLog = $item->statusLogs->first();
                return $latestLog?->productionStatus?->code ?? 'TERIMA';
            });

        return view('workshop.index', compact('grouped'));
    }

    public function scan(Request $request)
    {
        $scannedItem = null;
        $scanError = null;

        $token = $request->query('token');

        if ($token) {
            $orderItem = OrderItem::whereHas('order', function ($q) use ($token) {
                $q->where('qr_token', $token)->orWhere('order_number', $token);
            })->with(['order.customer', 'statusLogs' => fn($q) => $q->latest()])->first();

            if ($orderItem) {
                $scannedItem = $orderItem;
            } else {
                $scanError = 'Item dengan token/nomor order tersebut tidak ditemukan.';
            }
        }

        return view('workshop.scan', compact('scannedItem', 'scanError'));
    }

    public function orderDetail(Order $order)
    {
        $order->load(['items.statusLogs.productionStatus', 'customer', 'branch']);

        $orderItem = request()->query('item')
            ? $order->items()->with(['statusLogs.productionStatus', 'statusLogs.scannedBy'])->find(request()->query('item'))
            : null;

        return view('workshop.order-detail', compact('order', 'orderItem'));
    }

    public function lookup(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $orderItem = OrderItem::whereHas('order', function ($q) use ($request) {
            $q->where('qr_token', $request->token)
                ->orWhere('order_number', $request->token);
        })->with(['order.customer', 'statusLogs.productionStatus'])->first();

        if (!$orderItem) {
            return back()->with('error', 'Item dengan token tersebut tidak ditemukan.');
        }

        return redirect()->route('admin.workshop.items.show', $orderItem);
    }

    public function updateStatus(OrderItem $orderItem, Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:' . implode(',', array_column(ProductionStatusEnum::cases(), 'value')),
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->transitionService->transition(
                $orderItem,
                $request->status,
                Auth::id(),
                $request->notes
            );

            return redirect()->route('admin.workshop.index')
                ->with('success', 'Status produksi berhasil diperbarui.')
                ->with('wa_notify', true);
        } catch (\App\Exceptions\InvalidStatusTransitionException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(OrderItem $orderItem)
    {
        $orderItem->load(['statusLogs.productionStatus', 'statusLogs.scannedBy', 'order.customer']);

        $item = $orderItem;

        return view('workshop.show', compact('item'));
    }

    public function list()
    {
        $workshops = Workshop::paginate(10);
        return view('workshops.index', compact('workshops'));
    }

    public function create()
    {
        return view('workshops.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:workshops,code',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        Workshop::create($validated);

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Workshop berhasil ditambahkan.');
    }

    public function edit(Workshop $workshop)
    {
        return view('workshops.edit', compact('workshop'));
    }

    public function update(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:workshops,code,' . $workshop->id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $workshop->update($validated);

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Workshop berhasil diperbarui.');
    }

    public function destroy(Workshop $workshop)
    {
        if ($workshop->branches()->exists()) {
            return redirect()->route('admin.workshops.index')
                ->with('error', 'Workshop tidak bisa dihapus karena masih memiliki cabang.');
        }

        $workshop->delete();

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Workshop berhasil dihapus.');
    }
}
