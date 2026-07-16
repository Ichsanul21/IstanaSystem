<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print - Order #{{ $order->order_number ?? $order['order_number'] ?? '-' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #000;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
        }
        .header { text-align: center; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed #000; }
        .header h1 { font-size: 16px; font-weight: bold; margin-bottom: 2px; }
        .header p { font-size: 10px; color: #555; }
        .info { margin-bottom: 10px; }
        .info table { width: 100%; font-size: 11px; }
        .info td { padding: 1px 0; }
        .info td:last-child { text-align: right; }
        .items { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .items th { border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 4px 0; text-align: left; font-size: 10px; }
        .items th:last-child, .items td:last-child { text-align: right; }
        .items td { padding: 3px 0; }
        .total { border-top: 1px dashed #000; padding-top: 5px; margin-bottom: 10px; }
        .total table { width: 100%; font-size: 11px; }
        .total td { padding: 1px 0; }
        .total td:last-child { text-align: right; }
        .total .grand { font-size: 14px; font-weight: bold; }
        .qrcode { text-align: center; margin: 10px 0; }
        .qrcode img, .qrcode svg { width: 80px; height: 80px; }
        .footer { text-align: center; font-size: 9px; color: #888; border-top: 1px dashed #000; padding-top: 8px; margin-top: 8px; }
        @media print {
            body { margin: 0; padding: 5px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center;margin-bottom:10px;">
        <button onclick="window.print()" style="padding:8px 20px;background:#FF6B00;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:14px;">Print</button>
        <button onclick="window.close()" style="padding:8px 20px;background:#ccc;color:#000;border:none;border-radius:4px;cursor:pointer;font-size:14px;">Tutup</button>
    </div>

    <div class="header">
        <h1>ISTANA LAUNDRY</h1>
        <p>{{ config('app.address', 'Jl. Contoh No. 123, Kota') }}</p>
        <p>Telp: {{ config('app.phone', '0812-3456-7890') }}</p>
    </div>

    <div class="info">
        <table>
            <tr><td>No. Order</td><td>#{{ $order->order_number ?? $order['order_number'] ?? '-' }}</td></tr>
            <tr><td>Pelanggan</td><td>{{ $order->customer->name ?? '-' }}</td></tr>
            <tr><td>Tanggal</td><td>{{ $order->created_at ?? '-' }}</td></tr>
            <tr><td>Status</td><td>{{ $order->status ?? '-' }}</td></tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Layanan</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->items as $item)
                <tr>
                    <td>{{ $item->service?->name ?? '' ?? '-' }}</td>
                    <td>{{ $item->quantity ?? 1 }}</td>
                    <td>Rp {{ number_format($item->price_per_unit ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4">-</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="total">
        <table>
            <tr><td>Subtotal</td><td>Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}</td></tr>
            <tr><td>Diskon</td><td>Rp {{ number_format($order->discount_amount ?? 0, 0, ',', '.') }}</td></tr>
            <tr><td>Dibayar</td><td>Rp {{ number_format($order->payments->sum('amount') ?? 0, 0, ',', '.') }}</td></tr>
            <tr class="grand"><td>Total</td><td>Rp {{ number_format($order->grand_total ?? 0, 0, ',', '.') }}</td></tr>
        </table>
    </div>

    <div class="qrcode">
        <div style="font-size:10px;">Track: {{ url('/track/' . ($order->qr_token ?? $order->order_number)) }}</div>
    </div>

    <div class="footer">
        <p>Terima kasih telah menggunakan layanan Istana Laundry</p>
        <p>www.istanalaundry.com</p>
    </div>
</body>
</html>