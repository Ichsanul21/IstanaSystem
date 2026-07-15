@props([
    'title' => '',
    'description' => '',
    'group' => 'general',
])

<x-layouts.admin title="{{ $title }} - Pengaturan">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pengaturan</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $description }}</p>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <aside class="lg:col-span-1">
            @include('settings.partials.nav', ['currentGroup' => $group])
        </aside>

        <div class="lg:col-span-3">
            <form method="POST" action="{{ route('admin.settings.group.update', $group) }}" class="space-y-6">
                @csrf
                {{ $slot }}

                <div class="flex items-center justify-end gap-3">
                    <x-ui.button type="reset" variant="ghost">Reset</x-ui.button>
                    <x-ui.button type="submit" variant="primary">Simpan Pengaturan</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
