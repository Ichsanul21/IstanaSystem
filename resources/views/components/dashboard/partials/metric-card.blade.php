@props(['label', 'value', 'icon', 'trend' => null, 'trendUp' => true])
<div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 md:p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</p>
            <p class="text-2xl font-black tracking-tighter text-gray-900 dark:text-white mt-1">{{ $value }}</p>
            @if($trend)
            <p class="mt-1 text-xs" x-bind:class="{{ $trendUp }} ? 'text-green-600' : 'text-red-600'">
                {{ $trend }}
            </p>
            @endif
        </div>
        @if($icon)
        <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
            {!! $icon !!}
        </div>
        @endif
    </div>
</div>
