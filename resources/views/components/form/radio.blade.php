@props([
    'name' => null,
    'label' => null,
    'checked' => false,
    'value' => '1',
])

<label class="flex items-center gap-3 cursor-pointer group">
    <input
        type="radio"
        @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
        value="{{ $value }}"
        @checked($checked)
        {{ $attributes->merge([
            'class' => 'h-4 w-4 border-lo-gray dark:border-dark-700 text-lo focus:ring-lo bg-white dark:bg-dark-900 transition-colors cursor-pointer',
        ]) }}
    />
    @if ($label)
        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100 transition-colors">{{ $label }}</span>
    @endif
    {{ $slot }}
</label>
