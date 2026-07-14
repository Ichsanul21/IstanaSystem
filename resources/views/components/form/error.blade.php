@props(['name' => null, 'field' => null])

@php
    $fieldName = $field ?: $name;
@endphp

@if ($fieldName && $errors->has($fieldName))
    <p {{ $attributes->merge(['class' => 'mt-1 text-xs text-error']) }}>{{ $errors->first($fieldName) }}</p>
@endif
