@props([
    'name' => null,
    'label' => null,
    'error' => null,
    'prepend' => null,
    'append' => null,
    'required' => false,
])

@php
    $resolvedError = $error ?: ($name ? $errors->first($name) : null);
@endphp

<div>
    @if ($label)
        <x-form.label :for="$name" :required="$required">{{ $label }}</x-form.label>
    @endif
    <div class="flex {{ $label ? 'mt-1.5' : '' }}">
        @if ($prepend)
            <span class="inline-flex items-center px-4 bg-gray-100 dark:bg-dark-800 border border-r-0 border-lo-gray dark:border-dark-700 rounded-l-lg text-sm text-black/60 dark:text-white/60">{{ $prepend }}</span>
        @endif
        <input
            @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
            @if ($required) required @endif
            {{ $attributes->merge([
                'class' => 'block w-full border bg-white dark:bg-dark-900 text-gray-900 dark:text-gray-100 transition-colors px-4 py-3 text-sm ' . (
                    $prepend && $append ? 'rounded-none' : ($prepend ? 'rounded-r-lg rounded-l-none' : ($append ? 'rounded-l-lg rounded-r-none' : 'rounded-lg'))
                ) . ' ' . ($resolvedError ? 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500' : 'border-lo-gray dark:border-dark-700 focus:border-lo focus:ring-lo'),
                'aria-invalid' => $resolvedError ? 'true' : 'false',
                'aria-describedby' => $resolvedError && $name ? $name . '-error' : null,
            ]) }}
        />
        @if ($append)
            <span class="inline-flex items-center px-4 bg-gray-100 dark:bg-dark-800 border border-l-0 border-lo-gray dark:border-dark-700 rounded-r-lg text-sm text-black/60 dark:text-white/60">{{ $append }}</span>
        @endif
    </div>
    @if ($resolvedError)
        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400" id="{{ $name }}-error">{{ $resolvedError }}</p>
    @endif
</div>
