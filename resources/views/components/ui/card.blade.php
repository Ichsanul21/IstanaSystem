@props([
    'padding' => 'md',
    'class' => '',
    'variant' => 'default',
])

@php
$paddingClasses = match ($padding) {
    'none' => 'p-0',
    'sm' => 'p-4 lg:p-5',
    'md' => 'p-5 lg:p-8',
    'lg' => 'p-6 lg:p-10',
    default => 'p-5 lg:p-8',
};

$variantClasses = match ($variant) {
    'metric' => '',
    'hover' => 'svc-card',
    default => '',
};
@endphp

<div {{ $attributes->merge(['class' => "bg-white dark:bg-dark-900 rounded-xl shadow-theme-sm border border-lo-gray dark:border-dark-700 {$variantClasses} {$class}"]) }}>
    @isset($header)
        <div class="border-b border-lo-gray dark:border-dark-700 {{ $paddingClasses }}">
            {{ $header }}
        </div>
    @endisset

    @isset($body)
        <div class="{{ $paddingClasses }}">
            {{ $body }}
        </div>
    @else
        <div class="{{ $paddingClasses }}">
            {{ $slot }}
        </div>
    @endisset

    @isset($footer)
        <div class="border-t border-lo-gray dark:border-dark-700 {{ $paddingClasses }}">
            {{ $footer }}
        </div>
    @endisset
</div>
