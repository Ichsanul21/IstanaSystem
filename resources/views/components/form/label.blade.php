@props(['value' => null, 'required' => false])

<label {{ $attributes->merge(['class' => 'text-xs font-bold tracking-wider uppercase text-black/40 dark:text-white/40 mb-1.5 block']) }}>
    {{ $value ?? $slot }}
    @if ($required)
        <span class="text-error ml-0.5">*</span>
    @endif
</label>
