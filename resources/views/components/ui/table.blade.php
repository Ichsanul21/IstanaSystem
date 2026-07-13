@props([
    'headers' => [],
    'striped' => false,
    'hoverable' => false,
])

<div {{ $attributes->merge(['class' => 'w-full overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700']) }}>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        @if (count($headers) > 0)
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
                            @if(isset($header['width'])) style="width: {{ $header['width'] }}" @endif>
                            @if(isset($header['sortable']) && $header['sortable'])
                                <button type="button" class="flex items-center gap-1 group">
                                    {{ $header['label'] ?? $header }}
                                    <svg class="h-4 w-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/>
                                    </svg>
                                </button>
                            @else
                                {{ $header['label'] ?? $header }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
            {{ $rows ?? $slot }}
        </tbody>
    </table>
</div>

@if ($striped || $hoverable)
    @pushOnce('styles')
    <style>
        @if ($striped)
        .table-striped tbody tr:nth-child(odd) {
            background-color: var(--color-gray-50);
        }
        .dark .table-striped tbody tr:nth-child(odd) {
            background-color: color-mix(in srgb, var(--color-gray-800) 50%, transparent);
        }
        @endif
        @if ($hoverable)
        .table-hoverable tbody tr:hover {
            background-color: var(--color-gray-50);
        }
        .dark .table-hoverable tbody tr:hover {
            background-color: var(--color-gray-800);
        }
        @endif
    </style>
    @endPushOnce
@endif
