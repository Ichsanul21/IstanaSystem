@props(['value' => 0, 'max' => 100, 'color' => 'primary', 'size' => 'md', 'showLabel' => true])

@php
    $percent = $max > 0 ? min(100, ($value / $max) * 100) : 0;
    $sizes = ['sm' => 'h-1.5', 'md' => 'h-2.5', 'lg' => 'h-4'];
    $colors = ['primary' => 'bg-primary', 'lo' => 'bg-lo', 'green' => 'bg-green-500', 'blue' => 'bg-blue-500', 'red' => 'bg-red-500'];
    $barColor = $colors[$color] ?? $colors['primary'];
    $barSize = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="w-full">
    @if($showLabel)
        <div class="flex justify-between mb-1">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $attributes->get('label', 'Progress') }}</span>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ number_format($percent, 0) }}%</span>
        </div>
    @endif
    <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700 {{ $barSize }}">
        <div class="{{ $barColor }} {{ $barSize }} rounded-full transition-all duration-300" style="width: {{ $percent }}%"></div>
    </div>
</div>
