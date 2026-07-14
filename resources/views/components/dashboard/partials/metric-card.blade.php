@props(['label', 'value', 'icon', 'trend' => null, 'trendUp' => true])
<div {{ $attributes->merge(['class' => 'rounded-xl border border-lo-gray dark:border-dark-700 bg-white dark:bg-dark-900 p-5 lg:p-8']) }}>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-bold tracking-wider uppercase text-black/40 dark:text-white/40">{{ $label }}</p>
            <p class="text-3xl font-black tracking-tighter text-dark dark:text-white mt-1">{{ $value }}</p>
            @if($trend)
            <p class="mt-1 text-xs font-medium" x-bind:class="{{ $trendUp }} ? 'text-success' : 'text-error'">
                {{ $trend }}
            </p>
            @endif
        </div>
        @if($icon)
        <div class="h-12 w-12 rounded-lg bg-lo-50 dark:bg-lo/10 flex items-center justify-center text-lo">
            <span class="iconify text-2xl" data-icon="lucide:{{ $icon }}"></span>
        </div>
        @endif
    </div>
</div>
