@props([
    'type' => 'info',
    'dismissible' => false,
    'title' => null,
])

@php
$config = match ($type) {
    'success' => ['border' => 'border-l-success', 'text' => 'text-success', 'icon' => 'check-circle'],
    'error' => ['border' => 'border-l-error', 'text' => 'text-error', 'icon' => 'x-circle'],
    'warning' => ['border' => 'border-l-warning', 'text' => 'text-warning', 'icon' => 'alert-circle'],
    'info' => ['border' => 'border-l-info', 'text' => 'text-info', 'icon' => 'info'],
};
@endphp

<div x-data="{ visible: true }" x-show="visible"
     {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-lg border border-l-4 border-lo-gray dark:border-dark-700 bg-white dark:bg-dark-900 p-4 {$config['border']}"]) }}>
    <x-icon :name="$config['icon']" class="text-lg mt-0.5 shrink-0 {{ $config['text'] }}" />
    <div class="flex-1 min-w-0">
        @isset($title)
            <p class="text-sm font-bold text-dark dark:text-white">{{ $title }}</p>
        @endisset
        <div class="text-sm text-black/60 dark:text-white/60 {{ isset($title) ? 'mt-1' : '' }}">
            {{ $slot }}
        </div>
    </div>
    @if ($dismissible)
        <button x-on:click="visible = false" class="shrink-0 rounded-lg p-1 opacity-70 hover:opacity-100 transition-opacity" type="button">
            <x-icon name="x" class="text-sm" />
        </button>
    @endif
</div>
