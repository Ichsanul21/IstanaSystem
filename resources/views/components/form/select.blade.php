@props([
    'name' => null,
    'label' => null,
    'error' => null,
    'help' => null,
    'required' => false,
    'options' => [],
    'placeholder' => null,
    'value' => null,
])

@php
    $resolvedError = $error ?: ($name ? $errors->first($name) : null);
@endphp

<div>
    @if ($label)
        <x-form.label :for="$name" :required="$required">{{ $label }}</x-form.label>
    @endif
    <div class="{{ $label ? 'mt-1.5' : '' }} relative">
        <select
            @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
            @if ($required) required @endif
            {{ $attributes->merge([
                'class' => 'block w-full rounded-lg border bg-white dark:bg-dark-900 text-gray-900 dark:text-gray-100 transition-colors px-4 py-3 text-sm appearance-none ' . (
                    $resolvedError
                    ? 'border-error focus:border-error focus:ring-error'
                    : 'border-lo-gray dark:border-dark-700 focus:border-lo focus:ring-lo'
                ) . ' disabled:bg-gray-50 dark:disabled:bg-dark-900 disabled:cursor-not-allowed',
                'aria-invalid' => $resolvedError ? 'true' : 'false',
            ]) }}
        >
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected($value == $optionValue)>{{ $optionLabel }}</option>
            @endforeach
            {{ $slot }}
        </select>
        <svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 h-4 w-4 text-black/40 dark:text-white/40" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
        </svg>
    </div>
    @if ($help && !$resolvedError)
        <p class="mt-1 text-xs text-black/40 dark:text-white/40">{{ $help }}</p>
    @endif
    @if ($resolvedError)
        <x-form.error :field="$name" />
    @endif
</div>
