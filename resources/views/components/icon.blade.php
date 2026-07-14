@props(['name' => ''])

<span {{ $attributes->merge(['class' => 'iconify']) }} data-icon="lucide:{{ $name }}"></span>
