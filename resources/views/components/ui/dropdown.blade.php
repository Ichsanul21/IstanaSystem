@props(['align' => 'left', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-dark-900'])

@php
    $alignmentClasses = match ($align) {
        'left' => 'left-0 origin-top-left',
        'right' => 'right-0 origin-top-right',
        default => 'left-0 origin-top-left',
    };

    $widthClasses = match ($width) {
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
        default => 'w-48',
    };
@endphp

<div x-data="{ open: false }" @click.away="open = false" @close.stop="open = false" class="relative">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $alignmentClasses }} {{ $widthClasses }} rounded-lg shadow-theme-lg"
         style="display: none;"
         @click="open = false">
        <div class="rounded-lg border border-lo-gray dark:border-dark-700 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
