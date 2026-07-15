<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\InventoryBatch;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\ChartOfAccount;
use App\Models\OrderItem;
use App\Models\TaxLog;
use App\Enums\OrderStatus;
use App\Services\Export\ExportService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function __construct(protected ExportService $exportService) {}

    public function revenueExcel(Request $request)
    {
        try {
            $orders = Order::forCurrentBranch()
                ->whereIn('status', [OrderStatus::ReadyForPickup->value, OrderStatus::PickedUp->value])
                ->selectRaw("DATE(created_at) as date, payment_method, SUM(grand_total) as total")
                ->groupBy('date', 'payment_method')
                ->orderBy('date')
                ->get();

            $data = $orders->map(fn($o) => [
                $o->date,
                number_format($o->total, 0, ',', '.'),
                $o->payment_method ?? '-',
            ])->toArray();

            $headers = ['Tanggal', 'Pendapatan', 'Metode Pembayaran'];

            return $this->exportService->excel($data, 'revenue.xlsx', $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data pendapatan.');
        }
    }

    public function revenuePdf(Request $request)
    {
        try {
            $revenues = Order::forCurrentBranch()->with('customer', 'branch')
                ->whereIn('status', [OrderStatus::ReadyForPickup->value, OrderStatus::PickedUp->value])
                ->orderBy('created_at')
                ->get()
                ->map(fn($o) => [
                    'date' => $o->created_at?->format('d/m/Y'),
                    'order_number' => $o->order_number,
                    'customer_name' => $o->customer?->name ?? $o->customer_name ?? '-',
                    'branch_name' => $o->branch?->name ?? '-',
                    'amount' => (float) $o->grand_total,
                ]);

            $total = $revenues->sum('amount');

            return $this->exportService->pdf('exports.revenue', [
                'revenues' => $revenues,
                'total' => $total,
                'startDate' => $request->date_from ?? $revenues->first()['date'] ?? '-',
                'endDate' => $request->date_to ?? $revenues->last()['date'] ?? '-',
            ], 'revenue.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor PDF pendapatan.');
        }
    }

    public function ordersExcel(Request $request)
    {
        try {
            $orders = Order::forCurrentBranch()->with('customer')
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $orders->map(fn($o) => [
                $o->order_number,
                $o->customer?->name ?? $o->customer_name ?? '-',
                $o->created_at?->format('d/m/Y H:i'),
                $o->status,
                number_format($o->grand_total, 0, ',', '.'),
            ])->toArray();

            $headers = ['No. Order', 'Pelanggan', 'Tanggal', 'Status', 'Total'];

            return $this->exportService->excel($data, 'orders.xlsx', $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data pesanan.');
        }
    }

    public function customersExcel(Request $request)
    {
        try {
            $customers = Customer::forCurrentBranch()
                ->withCount('orders')
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $customers->map(fn($c) => [
                $c->name,
                $c->phone ?? '-',
                $c->email ?? '-',
                $c->orders_count,
                $c->created_at?->format('d/m/Y'),
            ])->toArray();

            $headers = ['Nama', 'No. Telepon', 'Email', 'Total Pesanan', 'Terdaftar'];

            return $this->exportService->excel($data, 'customers.xlsx', $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data pelanggan.');
        }
    }

    public function inventoryExcel(Request $request)
    {
        try {
            $items = InventoryItem::with(['batches' => fn($q) => $q->where('branch_id', currentBranchId())])
                ->orderBy('name')
                ->get();

            $data = $items->map(fn($i) => [
                $i->code,
                $i->name,
                $i->category ?? '-',
                (float) $i->batches->sum('quantity'),
                $i->unit ?? 'pcs',
            ])->toArray();

            $headers = ['Kode', 'Nama', 'Kategori', 'Stok', 'Satuan'];

            return $this->exportService->excel($data, 'inventory.xlsx', $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data inventaris.');
        }
    }

    public function taxExcel(Request $request)
    {
        try {
            $logs = TaxLog::with('order')
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $logs->map(fn($l) => [
                $l->created_at?->format('m/Y'),
                number_format($l->taxable_amount ?? 0, 0, ',', '.'),
                number_format($l->tax_amount ?? 0, 0, ',', '.'),
                $l->regime ?? '-',
            ])->toArray();

            $headers = ['Periode', 'Pendapatan', 'Pajak', 'Status'];

            return $this->exportService->excel($data, 'tax.xlsx', $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data pajak.');
        }
    }

    public function productionExcel(Request $request)
    {
    try {
            $items = OrderItem::whereHas('order', fn($q) => $q->forCurrentBranch())
                ->with(['statusLogs' => fn($q) => $q->latest(), 'order'])
                ->get();

            $data = $items->map(fn($i) => [
                $i->created_at?->format('d/m/Y'),
                $i->order?->order_number . ' - ' . ($i->service_name ?? 'Item'),
                $i->statusLogs->first()?->productionStatus?->name ?? '-',
                $i->statusLogs->isNotEmpty()
                    ? $i->statusLogs->first()->created_at?->diffInHours($i->created_at) . ' jam'
                    : '-',
            ])->toArray();

            $headers = ['Tanggal', 'Item', 'Status', 'Durasi'];

            return $this->exportService->excel($data, 'production.xlsx', $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data produksi.');
        }
    }

    public function journalExcel(Request $request)
    {
        try {
            $entries = JournalEntry::forCurrentBranch()
                ->with('lines.account')
                ->orderBy('entry_date', 'desc')
                ->get();

            $data = [];
            foreach ($entries as $entry) {
                foreach ($entry->lines as $line) {
                    $data[] = [
                        $entry->entry_date?->format('d/m/Y'),
                        $entry->entry_number,
                        $line->account?->name ?? '-',
                        $line->type === 'debit' ? number_format($line->amount, 0, ',', '.') : '0',
                        $line->type === 'credit' ? number_format($line->amount, 0, ',', '.') : '0',
                    ];
                }
            }

            $headers = ['Tanggal', 'No. Jurnal', 'Akun', 'Debit', 'Kredit'];

            return $this->exportService->excel($data, 'journal.xlsx', $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data jurnal.');
        }
    }
}
