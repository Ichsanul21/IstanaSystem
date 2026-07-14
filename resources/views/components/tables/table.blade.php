@props([
    'headers' => [],
    'hoverable' => true,
    'striped' => false,
])

<div {{ $attributes->merge(['class' => 'w-full overflow-x-auto rounded-xl border border-lo-gray dark:border-dark-700']) }}>
    <table class="min-w-full border-collapse divide-y divide-lo-gray dark:divide-dark-700">
        @if (count($headers) > 0)
            <thead class="bg-gray-50 dark:bg-dark-900">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold tracking-wider uppercase text-black/40 dark:text-white/40"
                            @if(isset($header['width'])) style="width: {{ $header['width'] }}" @endif>
                            @if(isset($header['sortable']) && $header['sortable'])
                                <button type="button" class="flex items-center gap-1 group">
                                    {{ $header['label'] ?? $header }}
                                    <svg class="h-4 w-4 text-black/20 dark:text-white/20 group-hover:text-black/40 dark:group-hover:text-white/40" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
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
        <tbody class="divide-y divide-lo-gray dark:divide-dark-700 bg-white dark:bg-dark-900 {{ $hoverable ? 'table-hoverable' : '' }} {{ $striped ? 'table-striped' : '' }}">
            {{ $rows ?? $slot }}
        </tbody>
    </table>
</div>
