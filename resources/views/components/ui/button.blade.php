@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'loading' => false,
    'type' => 'button',
    'disabled' => false,
    'href' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary' => 'bg-lo text-white hover:bg-lo-600 focus:ring-lo shadow-sm cta-main',
    'dark' => 'bg-dark text-white hover:bg-dark-800 focus:ring-dark shadow-sm',
    'danger' => 'bg-error text-white hover:bg-red-700 focus:ring-error shadow-sm',
    'outline' => 'border border-lo-gray text-black dark:text-white hover:border-black dark:hover:border-white focus:ring-lo',
    'ghost' => 'text-black/50 dark:text-white/50 hover:text-black dark:hover:text-white focus:ring-lo',
    'icon' => 'text-black/50 dark:text-white/50 hover:bg-gray-100 dark:hover:bg-dark-800 focus:ring-lo',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs gap-1.5',
    'md' => 'px-4 py-2 text-sm gap-2',
    'lg' => 'px-6 py-3 text-base gap-2.5',
];

$iconSizes = [
    'sm' => 'h-4 w-4',
    'md' => 'h-5 w-5',
    'lg' => 'h-5 w-5',
];

$classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);

if ($variant === 'icon') {
    $classes = $baseClasses . ' ' . $variants['icon'] . ' p-2';
    $size = 'md';
}
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($loading)
            <svg class="animate-spin {{ $iconSizes[$size] }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif ($icon)
            <svg class="{{ $iconSizes[$size] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M{{ implode(' ', $icon) }}" />
            </svg>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes, 'disabled' => $disabled || $loading]) }}>
        @if ($loading)
            <svg class="animate-spin {{ $iconSizes[$size] }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @elseif ($icon)
            <svg class="{{ $iconSizes[$size] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M{{ implode(' ', $icon) }}" />
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
