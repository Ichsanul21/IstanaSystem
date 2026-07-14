@props([
    'name',
    'maxWidth' => '2xl',
    'title' => null,
])

@php
$maxWidthClasses = match ($maxWidth) {
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
    '4xl' => 'sm:max-w-4xl',
    '5xl' => 'sm:max-w-5xl',
    '6xl' => 'sm:max-w-6xl',
    '7xl' => 'sm:max-w-7xl',
    default => 'sm:max-w-2xl',
};
@endphp

<div
    x-data="{ show: false }"
    x-init="$watch('show', value => { document.body.classList.toggle('overflow-y-hidden', value) })"
    x-on:open-modal.window="$event.detail === '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail === '{{ $name }}' ? show = false : null"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <div x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-dark/50 dark:bg-dark/80"
         x-on:click="show = false">
    </div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative w-full {{ $maxWidthClasses }} rounded-xl bg-white dark:bg-dark-900 shadow-theme-xl">
            @isset($title)
                <div class="flex items-center justify-between border-b border-lo-gray dark:border-dark-700 px-6 py-4">
                    <h3 class="text-lg font-bold text-dark dark:text-white">{{ $title }}</h3>
                    <button x-on:click="show = false" class="rounded-lg p-1 text-black/40 hover:text-black hover:bg-gray-100 dark:hover:bg-dark-800 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @else
                <div class="absolute right-4 top-4">
                    <button x-on:click="show = false" class="rounded-lg p-1 text-black/40 hover:text-black hover:bg-gray-100 dark:hover:bg-dark-800 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endisset

            @isset($body)
                <div class="px-6 py-4">
                    {{ $body }}
                </div>
            @else
                <div class="px-6 py-4">
                    {{ $slot }}
                </div>
            @endisset

            @isset($footer)
                <div class="flex items-center justify-end gap-3 border-t border-lo-gray dark:border-dark-700 px-6 py-4">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
