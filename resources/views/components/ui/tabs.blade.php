@props([
    'tabs' => [],
    'active' => null,
])

@php
$activeTab = $active ?: ($tabs[0]['id'] ?? '');
@endphp

<div x-data="{ activeTab: '{{ $activeTab }}' }" {{ $attributes }}>
    <div class="border-b border-gray-200 dark:border-gray-700" role="tablist">
        <nav class="flex -mb-px space-x-1 overflow-x-auto" aria-label="Tabs">
            @foreach($tabs as $tab)
                <button type="button"
                        role="tab"
                        x-on:click="activeTab = '{{ $tab['id'] }}'"
                        :class="activeTab === '{{ $tab['id'] }}' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
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
