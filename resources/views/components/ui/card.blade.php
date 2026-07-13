@props([
    'padding' => 'md',
    'class' => '',
])

@php
$paddingClasses = match ($padding) {
    'none' => 'p-0',
    'sm' => 'p-4',
    'md' => 'p-6',
    'lg' => 'p-8',
    default => 'p-6',
};
@endphp

<div {{ $attributes->merge(['class' => "bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 {$class}"]) }}>
    @isset($header)
        <div class="border-b border-gray-200 dark:border-gray-700 {{ $paddingClasses }}">
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
        <div class="border-t border-gray-200 dark:border-gray-700 {{ $paddingClasses }}">
            {{ $footer }}
        </div>
    @endisset
</div>
