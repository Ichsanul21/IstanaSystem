<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Export\ExportService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function __construct(protected ExportService $exportService) {}

    public function revenueExcel(Request $request)
    {
        try {
            $data = []; // collect revenue data here
            $headers = ['Tanggal', 'Pendapatan', 'Metode Pembayaran'];

            return $this->exportService->excel($data, 'revenue.xlsx', $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data pendapatan.');
        }
    }

    public function revenuePdf(Request $request)
    {
        try {
            $data = []; // collect revenue data here

            return $this->exportService->pdf('exports.revenue', $data, 'revenue.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor PDF pendapatan.');
        }
    }

    public function ordersExcel(Request $request)
    {
        try {
            $data = [];
            $headers = ['No. Order', 'Pelanggan', 'Tanggal', 'Status', 'Total'];

            return $this->exportService->excel($data, 'orders.xlsx', $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data pesanan.');
        }
    }

    public function customersExcel(Request $request)
    {
        try {
            $data = [];
            $headers = ['Nama', 'No. Telepon', 'Email', 'Total Pesanan', 'Terdaftar'];

            return $this->exportService->excel($data, 'customers.xlsx', $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data pelanggan.');
        }
    }

    public function inventoryExcel(Request $request)
    {
        try {
            $data = [];
            $headers = ['Kode', 'Nama', 'Kategori', 'Stok', 'Satuan'];

            return $this->exportService->excel($data, 'inventory.xlsx', $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data inventaris.');
        }
    }
}
