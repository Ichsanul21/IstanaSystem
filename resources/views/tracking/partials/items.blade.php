@props(['items'])
<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700">
                <th class="text-left py-2 px-3 font-medium text-gray-500">Layanan</th>
                <th class="text-left py-2 px-3 font-medium text-gray-500">Qty</th>
                <th class="text-left py-2 px-3 font-medium text-gray-500">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr class="border-b border-gray-100 dark:border-gray-800">
                <td class="py-3 px-3">{{ $item->service?->name ?? '' }}</td>
                <td class="py-3 px-3">{{ $item->quantity }} {{ $item->servicePricing?->service?->unit ?? '' }}</td>
                <td class="py-3 px-3">
                    @php $latestStatus = $item->productionStatuses->first(); @endphp
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $latestStatus ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-gray-500' }}">
                        {{ $latestStatus?->to_status ?? 'Belum diproses' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
