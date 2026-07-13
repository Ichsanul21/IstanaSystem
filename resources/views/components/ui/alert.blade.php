@props([
    'type' => 'info',
    'dismissible' => false,
    'title' => null,
])

@php
$config = match ($type) {
    'success' => ['bg' => 'bg-green-50 dark:bg-green-900/30', 'border' => 'border-green-200 dark:border-green-800', 'text' => 'text-green-800 dark:text-green-200', 'icon' => 'text-green-500', 'path' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    'error' => ['bg' => 'bg-red-50 dark:bg-red-900/30', 'border' => 'border-red-200 dark:border-red-800', 'text' => 'text-red-800 dark:text-red-200', 'icon' => 'text-red-500', 'path' => 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z'],
    'warning' => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/30', 'border' => 'border-yellow-200 dark:border-yellow-800', 'text' => 'text-yellow-800 dark:text-yellow-200', 'icon' => 'text-yellow-500', 'path' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.008v.008H12v-.008Z'],
    'info' => ['bg' => 'bg-blue-50 dark:bg-blue-900/30', 'border' => 'border-blue-200 dark:border-blue-800', 'text' => 'text-blue-800 dark:text-blue-200', 'icon' => 'text-blue-500', 'path' => 'm11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z'],
};
@endphp

<div x-data="{ visible: true }" x-show="visible"
     {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-lg border p-4 {$config['bg']} {$config['border']} {$config['text']}"]) }}>
    <svg class="h-5 w-5 mt-0.5 shrink-0 {{ $config['icon'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $config['path'] }}"/>
    </svg>
    <div class="flex-1 min-w-0">
        @isset($title)
            <p class="text-sm font-semibold">{{ $title }}</p>
        @endisset
        <div class="text-sm {{ isset($title) ? 'mt-1' : '' }}">
            {{ $slot }}
        </div>
    </div>
    @if ($dismissible)
        <button x-on:click="visible = false" class="shrink-0 rounded-lg p-1 opacity-70 hover:opacity-100 transition-opacity" type="button">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>
    @endif
</div>
