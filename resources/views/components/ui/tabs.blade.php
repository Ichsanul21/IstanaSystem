@props([
    'tabs' => [],
    'active' => null,
])

@php
$activeTab = $active ?: ($tabs[0]['id'] ?? '');
@endphp

<div x-data="{ activeTab: '{{ $activeTab }}' }" {{ $attributes }}>
    <div class="border-b border-lo-gray dark:border-dark-700" role="tablist">
        <nav class="flex -mb-px space-x-1 overflow-x-auto" aria-label="Tabs">
            @foreach($tabs as $tab)
                <button type="button"
                        role="tab"
                        x-on:click="activeTab = '{{ $tab['id'] }}'"
                        :class="activeTab === '{{ $tab['id'] }}' ? 'border-lo text-black dark:text-white font-bold' : 'border-transparent text-black/40 dark:text-white/40 hover:text-black/70 dark:hover:text-white/70'"
                        class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors"
                        :aria-selected="activeTab === '{{ $tab['id'] }}' ? 'true' : 'false'">
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </nav>
    </div>
    <div class="mt-4">
        {{ $slot }}
    </div>
</div>
