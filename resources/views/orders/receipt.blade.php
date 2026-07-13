<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt - Order #{{ $order->order_number ?? $order['order_number'] ?? '-' }}</title>
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
        .footer { text-align: center; font-size: 10px; border-top: 1px dashed #000; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ISTANA LAUNDRY</h1>
        <p>{{ $order->branch->name ?? 'Cabang' }}</p>
    </div>

    <div class="info">
        <table>
            <tr><td>Order</td><td>#{{ $order->order_number ?? '-' }}</td></tr>
            <tr><td>Tgl</td><td>{{ optional($order->created_at ?? null)->format('d M Y H:i') ?? ($order['created_at'] ?? '-') }}</td></tr>
            <tr><td>Kasir</td><td>{{ $order->user->name ?? $order['cashier'] ?? '-' }}</td></tr>
            <tr><td>Pelanggan</td><td>{{ $order->customer->name ?? $order->customer_name ?? 'Walk-in' }}</td></tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr><th>Item</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
            @foreach(($order->items ?? $order['items'] ?? []) as $item)
            <tr>
                <td>{{ $item->service?->name ?? '' ?? $item['service_name'] }}</td>
                <td>{{ $item->quantity ?? $item['quantity'] }}</td>
                <td>{{ number_format($item->price_per_unit ?? $item['unit_price'], 0, ',', '.') }}</td>
                <td>{{ number_format($item->subtotal ?? $item['subtotal'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <table>
            <tr><td>Subtotal</td><td>Rp {{ number_format($order->total_amount ?? $order['subtotal'], 0, ',', '.') }}</td></tr>
            @if(($order->discount_amount ?? $order['discount'] ?? 0) > 0)
            <tr><td>Diskon</td><td>-Rp {{ number_format($order->discount_amount ?? $order['discount'], 0, ',', '.') }}</td></tr>
            @endif
            @if(($order->tax ?? $order['tax'] ?? 0) > 0)
            <tr><td>Pajak</td><td>Rp {{ number_format($order->tax ?? $order['tax'], 0, ',', '.') }}</td></tr>
            @endif
            <tr style="font-weight: bold; border-top: 1px dashed #000;"><td>Grand Total</td><td>Rp {{ number_format($order->grand_total ?? $order['total'], 0, ',', '.') }}</td></tr>
            @if(($order->paid_amount ?? $order['paid_amount'] ?? 0) > 0)
            <tr><td>Tunai</td><td>Rp {{ number_format($order->paid_amount ?? $order['paid_amount'], 0, ',', '.') }}</td></tr>
            <tr><td>Kembali</td><td>Rp {{ number_format(($order->paid_amount ?? $order['paid_amount']) - ($order->grand_total ?? $order['total']), 0, ',', '.') }}</td></tr>
            @endif
        </table>
    </div>

    <div class="footer">
        <p>Status: {{ ($order->payment_status ?? $order['payment_status'] ?? 'unpaid') === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}</p>
        <p style="margin-top: 4px;">Terima kasih telah menggunakan Istana Laundry</p>
    </div>
</body>
</html>
