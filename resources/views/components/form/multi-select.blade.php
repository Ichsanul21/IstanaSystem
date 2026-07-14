@props([
    'name' => null,
    'label' => null,
    'options' => [],
    'selected' => [],
])

<div x-data="{ open: false, selected: @js($selected) }" class="relative">
    @if ($label)
        <x-form.label>{{ $label }}</x-form.label>
    @endif
    <div @click="open = !open"
         class="border border-lo-gray dark:border-dark-700 rounded-lg px-4 py-3 text-sm cursor-pointer flex flex-wrap gap-1 min-h-[44px] items-center bg-white dark:bg-dark-900">
        <template x-for="(item, index) in selected" :key="index">
            <span class="multiselect-token">
                <span x-text="item"></span>
                <span @click.stop="selected = selected.filter((_, i) => i !== index)" class="cursor-pointer hover:opacity-70">&times;</span>
            </span>
        </template>
        <span x-show="!selected.length" class="text-black/30 dark:text-white/30">Pilih...</span>
    </div>
    <div x-show="open"
         @click.outside="open = false"
         class="absolute z-50 mt-1 w-full rounded-lg border border-lo-gray dark:border-dark-700 bg-white dark:bg-dark-900 shadow-theme-lg max-h-48 overflow-y-auto"
         style="display: none;">
        @foreach($options as $value => $label)
            <label class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-dark-800 cursor-pointer">
                <input type="checkbox"
                       value="{{ $value }}"
                       x-model="selected"
                       @if($name) :name="`{{ $name }}[]`" @endif
                       class="h-4 w-4 rounded border-lo-gray text-lo focus:ring-lo">
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>
</div>
