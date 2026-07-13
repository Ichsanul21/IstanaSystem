@props([
    'name' => null,
    'label' => null,
    'error' => null,
    'help' => null,
    'model' => null,
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
        <x-ui.label :for="$name" :required="$required">{{ $label }}</x-ui.label>
        @if ($help && !$resolvedError)
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $help }}</p>
        @endif
    @endif
    <div class="{{ $label ? 'mt-1' : '' }}">
        <select
            @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
            @if ($model) wire:model="{{ $model }}" @endif
            @if ($required) required @endif
            {{ $attributes->merge([
                'class' => 'block w-full rounded-lg border bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-colors px-4 py-2.5 ' . (
                    $resolvedError
                    ? 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500'
                    : 'border-gray-300 dark:border-gray-600 focus:border-primary focus:ring-primary'
                ) . ' disabled:bg-gray-50 dark:disabled:bg-gray-900 disabled:text-gray-500',
                'aria-invalid' => $resolvedError ? 'true' : 'false',
                'aria-describedby' => $resolvedError ? $name . '-error' : null,
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
    </div>
    @if ($resolvedError)
        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400" id="{{ $name }}-error">{{ $resolvedError }}</p>
    @endif
    @if ($help && !$resolvedError && !$label)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif
</div>
