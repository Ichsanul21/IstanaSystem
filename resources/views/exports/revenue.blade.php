<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pendapatan</title>
    <style>
        body { font-family: 'Inter', sans-serif; font-size: 12px; color: #1f2937; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #FF6B00; padding-bottom: 20px; }
        .header h1 { font-size: 22px; font-weight: 700; color: #000; margin: 0 0 5px; }
        .header p { font-size: 13px; color: #6b7280; margin: 2px 0; }
        .header .brand { color: #FF6B00; }
        .info { margin-bottom: 20px; }
        .info table { width: 100%; }
        .info td { padding: 2px 10px; font-size: 12px; }
        .info td:first-child { font-weight: 600; color: #374151; width: 120px; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data th { background-color: #FF6B00; color: #fff; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        table.data td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        table.data tr:nth-child(even) { background-color: #f9fafb; }
        .total-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .total-table td { padding: 6px 12px; font-size: 13px; }
        .total-table .total-row { font-weight: 700; border-top: 2px solid #FF6B00; }
        .total-table .total-row td { padding-top: 10px; font-size: 15px; color: #FF6B00; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 10px; color: #9ca3af; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Laporan Pendapatan</p>
        <p>Periode: <span class="brand">{{ $startDate }} — {{ $endDate }}</span></p>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No. Pesanan</th>
                <th>Pelanggan</th>
                <th>Cabang</th>
                <th class="text-right">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($revenues as $revenue)
            <tr>
                <td>{{ $revenue->date ?? $revenue['date'] }}</td>
                <td>{{ $revenue->order_number ?? $revenue['order_number'] }}</td>
                <td>{{ $revenue->customer_name ?? $revenue['customer_name'] }}</td>
                <td>{{ $revenue->branch_name ?? $revenue['branch_name'] }}</td>
                <td class="text-right">Rp {{ number_format($revenue->amount ?? $revenue['amount'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="total-table">
        <tr class="total-row">
            <td>Total Pendapatan</td>
            <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Dicetak pada {{ now()->format('d/m/Y H:i') }} — {{ config('app.name') }}</p>
    </div>
</body>
</html>
