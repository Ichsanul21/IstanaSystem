<x-layouts.admin title="Tambah Expense">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.finance.expenses.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Expense</h1>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-ui.card>
                <form method="POST" action="{{ route('admin.finance.expenses.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.select name="category" label="Kategori" :options="$categories ?? []" placeholder="Pilih Kategori" required />
                        <x-ui.input name="posted_at" label="Tanggal" type="date" required :value="old('posted_at', date('Y-m-d'))" />
                    </div>
                    <x-ui.textarea name="description" label="Deskripsi" rows="3" required />
                    <x-ui.input name="amount" label="Jumlah" type="number" step="0.01" min="0" required />
                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <x-ui.button href="{{ route('admin.finance.expenses.index') }}" variant="ghost">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-layouts.admin>
