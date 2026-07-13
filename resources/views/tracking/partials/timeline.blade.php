@props(['items', 'progress'])
<div class="space-y-4">
    @php
    $statuses = [
        ['code' => 'received', 'label' => 'Terima', 'icon' => '📥'],
        ['code' => 'washed', 'label' => 'Cuci', 'icon' => '🧺'],
        ['code' => 'dried', 'label' => 'Kering', 'icon' => '💨'],
        ['code' => 'ironed', 'label' => 'Setrika', 'icon' => '👔'],
        ['code' => 'packed', 'label' => 'Packing', 'icon' => '📦'],
        ['code' => 'ready_for_pickup', 'label' => 'Siap Ambil', 'icon' => '✅'],
        ['code' => 'picked_up', 'label' => 'Diambil', 'icon' => '🚶'],
    ];
    $highestIndex = 0;
    foreach ($items as $item) {
        $latest = $item->productionStatuses->first();
        if ($latest) {
            $idx = array_search($latest->to_status, array_column($statuses, 'code'));
            if ($idx !== false && $idx > $highestIndex) {
                $highestIndex = $idx;
            }
        }
    }
    @endphp

    <div class="flex items-center justify-between">
        @foreach($statuses as $i => $s)
        <div class="flex flex-col items-center">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg {{ $highestIndex === $i ? 'bg-primary text-white' : ($i < $highestIndex ? 'bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400') }}">
                {{ $s['icon'] }}
            </div>
            <span class="text-xs mt-1 {{ $highestIndex === $i ? 'text-primary font-semibold' : 'text-gray-500' }}">{{ $s['label'] }}</span>
        </div>
        @if(!$loop->last)
        <div class="flex-1 h-0.5 mx-2 {{ $i < $highestIndex ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
        @endif
        @endforeach
    </div>
</div>
