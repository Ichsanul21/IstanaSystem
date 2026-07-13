<x-layouts.admin title="Tambah Promosi">
    <x-slot:header>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.promotions.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Promosi</h1>
        </div>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.promotions.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-ui.input name="code" label="Kode Promosi" required />
                <x-ui.input name="name" label="Nama Promosi" required />
                <x-ui.select name="type" label="Tipe" :options="['percentage' => 'Persentase', 'fixed' => 'Nominal Tetap', 'buy_get' => 'Beli Dapat']" required />
                <x-ui.input name="value" label="Nilai" type="number" step="0.01" required />
                <x-ui.input name="min_order_amount" label="Min. Pembelian" type="number" step="0.01" />
                <x-ui.input name="start_date" label="Tanggal Mulai" type="date" required />
                <x-ui.input name="end_date" label="Tanggal Berakhir" type="date" required />
            </div>
            <x-ui.textarea name="description" label="Deskripsi" rows="3" />

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Terapkan ke Cabang</p>
                <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
                    @foreach($branches as $branch)
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <input type="checkbox" name="branches[]" value="{{ $branch->id }}" class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $branch->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
                <x-ui.button href="{{ route('admin.promotions.index') }}" variant="secondary">Batal</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.admin>