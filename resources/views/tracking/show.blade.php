<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lacak Pesanan - Istana Laundry</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .tracking-container {
            width: 100%;
            max-width: 480px;
        }
        .tracking-card {
            background: #fff;
            border-radius: 16px;
            padding: 32px 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            border: 1px solid #e5e5e5;
        }
        :root.dark .tracking-card {
            background: #1a1a2e;
            border-color: #333;
        }
        .brand {
            text-align: center;
            margin-bottom: 24px;
        }
        .brand h1 {
            font-size: 22px;
            font-weight: 900;
            color: #FF6B00;
        }
        .brand p {
            font-size: 12px;
            color: #888;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <div class="tracking-container">
        <div class="tracking-card">
            <div class="brand">
                <h1>ISTANA LAUNDRY</h1>
                <p>Lacak Status Pesanan Anda</p>
            </div>

            @if (!session('tracking_verified_'.$order->tracking_token))
                @include('tracking.partials.pin-form', ['token' => $order->tracking_token])
            @else
                <div class="space-y-6">
                    <div class="pb-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm text-gray-500">No. Pesanan</h2>
                        <p class="text-lg font-bold text-primary">#{{ $order->order_number }}</p>
                        <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-gray-500">Pelanggan</span>
                                <p class="font-medium">{{ $order->customer?->name ?? $order->customer_name }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Tanggal</span>
                                <p class="font-medium">{{ $order->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>

                    @include('tracking.partials.timeline', ['items' => $order->items, 'progress' => $progress])

                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Progress Pesanan</h3>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="bg-primary h-2.5 rounded-full transition-all duration-500" style="width: {{ $progress ?? 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $progress ?? 0 }}% selesai</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Daftar Items</h3>
                        @include('tracking.partials.items', ['items' => $order->items])
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700 text-center">
                        <p class="text-sm text-gray-500 mb-3">Ada pertanyaan? Hubungi kami</p>
                        @php
                            $waLink = app(\App\Services\Tracking\TrackingService::class)->getWaLink($order);
                        @endphp
                        @if($waLink)
                            <a href="{{ $waLink }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-full text-sm font-medium hover:bg-green-600 transition-colors">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                Hubungi via WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
