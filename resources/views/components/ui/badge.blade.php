@props([
    'variant' => 'gray',
    'size' => 'md',
])

@php
$variants = [
    'success' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
    'warning' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
    'danger' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
    'info' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
    'gray' => 'bg-gray-100 text-gray-700 dark:bg-dark-700 dark:text-gray-300',
    'primary' => 'bg-lo-50 text-lo dark:bg-lo/20 dark:text-lo-200',
    'lo' => 'bg-lo-50 text-lo dark:bg-lo/20 dark:text-lo-200',
    'dark' => 'bg-dark text-white dark:bg-white dark:text-dark',
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-1 text-sm',
];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center font-medium rounded-full ' . ($variants[$variant] ?? $variants['gray']) . ' ' . ($sizes[$size] ?? $sizes['md'])]) }}>
    {{ $slot }}
</span>
