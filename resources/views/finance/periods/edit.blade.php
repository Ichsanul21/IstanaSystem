<x-layouts.admin title="Edit Periode">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.finance.periods.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Periode — {{ $period->name }}</h1>
            </div>
        </div>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.finance.periods.update', $period) }}" class="space-y-4">
            @csrf @method('PUT')
            <x-ui.input name="name" label="Nama Periode" required :value="old('name', $period->name)" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="start_date" label="Start Date" type="date" required :value="old('start_date', $period->start_date->format('Y-m-d'))" />
                <x-ui.input name="end_date" label="End Date" type="date" required :value="old('end_date', $period->end_date->format('Y-m-d'))" />
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                <x-ui.button href="{{ route('admin.finance.periods.index') }}" variant="ghost">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.admin>
