@props([
    'name' => null,
    'label' => null,
    'error' => null,
    'prepend' => null,
    'append' => null,
    'required' => false,
])

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
                ) . ' ' . ($error ? 'border-error' : 'border-lo-gray dark:border-dark-700 focus:border-lo focus:ring-lo'),
            ]) }}
        />
        @if ($append)
            <span class="inline-flex items-center px-4 bg-gray-100 dark:bg-dark-800 border border-l-0 border-lo-gray dark:border-dark-700 rounded-r-lg text-sm text-black/60 dark:text-white/60">{{ $append }}</span>
        @endif
    </div>
    @if ($error)
        <x-form.error :field="$name" />
    @endif
</div>
