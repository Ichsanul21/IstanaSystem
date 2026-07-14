@props([
    'name' => null,
    'label' => null,
    'error' => null,
    'help' => null,
    'required' => false,
])

@php
    $resolvedError = $error ?: ($name ? $errors->first($name) : null);
@endphp

<div>
    @if ($label)
        <x-form.label :for="$name" :required="$required">{{ $label }}</x-form.label>
    @endif
    <div class="{{ $label ? 'mt-1.5' : '' }}">
        <textarea
            @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
            @if ($required) required @endif
            {{ $attributes->merge([
                'class' => 'block w-full rounded-lg border bg-white dark:bg-dark-900 text-gray-900 dark:text-gray-100 transition-colors px-4 py-3 text-sm ' . (
                    $resolvedError
                    ? 'border-error focus:border-error focus:ring-error'
                    : 'border-lo-gray dark:border-dark-700 focus:border-lo focus:ring-lo'
                ) . ' disabled:bg-gray-50 dark:disabled:bg-dark-900 disabled:cursor-not-allowed',
                'aria-invalid' => $resolvedError ? 'true' : 'false',
                'rows' => $attributes->get('rows', 3),
            ]) }}
        >{{ $slot }}</textarea>
    </div>
    @if ($help && !$resolvedError)
        <p class="mt-1 text-xs text-black/40 dark:text-white/40">{{ $help }}</p>
    @endif
    @if ($resolvedError)
        <x-form.error :field="$name" />
    @endif
</div>
