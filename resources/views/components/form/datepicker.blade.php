@props([
    'name' => null,
    'label' => null,
    'error' => null,
    'required' => false,
])

<div x-data="{ picker: null }" x-init="picker = flatpickr($refs.input, { dateFormat: 'd/m/Y', allowInput: true })">
    <x-form.input
        x-ref="input"
        :name="$name"
        :label="$label"
        :error="$error"
        :required="$required"
        placeholder="DD/MM/YYYY"
        {{ $attributes }}
    />
</div>
