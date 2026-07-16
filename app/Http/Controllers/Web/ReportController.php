<?php

namespace App\Http\Controllers\Web;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderItemStatusLog;
use App\Models\Payment;
use App\Models\TaxLog;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function revenue()
    {
        $period = request('period', 'daily');

        abort_if(!in_array($period, ['daily', 'monthly', 'yearly']), 422);

        $revenues = Payment::whereHas('order', fn($q) => $q->forCurrentBranch())
            ->when($period === 'daily', fn($q) => $q->selectRaw('DATE(paid_at) as date, SUM(amount) as total')->groupBy('date')->orderBy('date', 'desc'))
            ->when($period === 'monthly', function ($q) {
                $dateFormat = DB::connection()->getDriverName() === 'sqlite'
                    ? "strftime('%Y-%m', paid_at)"
                    : "DATE_FORMAT(paid_at, '%Y-%m')";
                return $q->selectRaw("{$dateFormat} as date, SUM(amount) as total")->groupBy('date')->orderBy('date', 'desc');
            })
            ->when($period === 'yearly', fn($q) => $q->selectRaw('YEAR(paid_at) as date, SUM(amount) as total')->groupBy('date')->orderBy('date', 'desc'))
            ->get();

        return view('reports.revenue', compact('revenues', 'period'));
    }

    public function orders()
    {
        $query = Order::forCurrentBranch()
            ->when(request('status'), fn($q, $v) => $q->where('status', $v))
            ->when(request('date_from'), fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when(request('date_to'), fn($q, $v) => $q->whereDate('created_at', '<=', $v));

        $totalPending = (clone $query)->where('status', OrderStatus::Pending->value)->count();
        $totalProcessing = (clone $query)->whereIn('status', [OrderStatus::Received->value, OrderStatus::Washed->value, OrderStatus::Dried->value, OrderStatus::Ironed->value, OrderStatus::Packed->value])->count();
        $totalCompleted = (clone $query)->whereIn('status', [OrderStatus::ReadyForPickup->value, OrderStatus::PickedUp->value])->count();

        $orders = $query->with('customer')->latest()->paginate(15);

        return view('reports.orders', compact('orders', 'totalPending', 'totalProcessing', 'totalCompleted'));
    }

    public function customers()
    {
        $totalCustomers = Customer::forCurrentBranch()->count();
        $totalOrders = Order::forCurrentBranch()->count();
        $totalRevenue = Order::forCurrentBranch()->sum('grand_total');

        $customers = Customer::forCurrentBranch()
            ->withCount('orders')
            ->withSum('orders', 'grand_total')
            ->latest()
            ->paginate(15);

        return view('reports.customers', compact('customers', 'totalCustomers', 'totalOrders', 'totalRevenue'));
    }

    public function inventory()
    {
        $branchId = currentBranchId();
        $items = InventoryItem::with(['batches' => fn($q) => $q->where('branch_id', $branchId)])
            ->latest()
            ->paginate(15);

        $allItems = InventoryItem::with(['batches' => fn($q) => $q->where('branch_id', $branchId)])->get();
        $totalValue = $allItems->sum(fn($i) => $i->batches->sum(fn($b) => $b->quantity * $b->unit_cost));
        $lowStock = $allItems->filter(fn($i) => $i->batches->sum('quantity') < 10)->count();
        $outOfStock = $allItems->filter(fn($i) => $i->batches->sum('quantity') < 1)->count();

        return view('reports.inventory', compact('items', 'totalValue', 'lowStock', 'outOfStock'));
    }

    public function tax()
    {
        $query = TaxLog::when(request('date_from'), fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when(request('date_to'), fn($q, $v) => $q->whereDate('created_at', '<=', $v));

        $totalTax = (clone $query)->sum('tax_amount');
        $totalTaxable = (clone $query)->sum('base_amount');

        $logs = $query->with(['journalEntry'])->latest()->paginate(15);

        return view('reports.tax', compact('logs', 'totalTax', 'totalTaxable'));
    }

    public function production()
    {
        $query = OrderItemStatusLog::whereHas('orderItem.order', fn($q) => $q->forCurrentBranch())
            ->when(request('date_from'), fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when(request('date_to'), fn($q, $v) => $q->whereDate('created_at', '<=', $v));

        $uniqueItems = (clone $query)->distinct('order_item_id')->count('order_item_id');
        $uniqueWorkers = (clone $query)->distinct('scanned_by')->count('scanned_by');

        $logs = $query->with(['orderItem', 'scannedBy', 'productionStatus'])->latest()->paginate(15);

        return view('reports.production', compact('logs', 'uniqueItems', 'uniqueWorkers'));
    }
}
