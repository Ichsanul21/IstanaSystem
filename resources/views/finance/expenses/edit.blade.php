<x-layouts.admin title="Edit Expense">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.finance.expenses.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Expense</h1>
            </div>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-ui.card>
                <form method="POST" action="{{ route('admin.finance.expenses.update', $expense) }}" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.select name="category" label="Kategori" :options="$categories ?? []" placeholder="Pilih Kategori" required :value="old('category', $expense->category)" />
                        <x-ui.input name="posted_at" label="Tanggal" type="date" required :value="old('posted_at', $expense->posted_at->format('Y-m-d'))" />
                    </div>
                    <x-ui.textarea name="description" label="Deskripsi" rows="3" required>{{ old('description', $expense->description) }}</x-ui.textarea>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.input name="amount" label="Jumlah" type="number" step="0.01" min="0" required :value="old('amount', $expense->amount)" />
                        <x-ui.select name="payment_method" label="Metode Pembayaran" :options="['cash' => 'Cash', 'transfer' => 'Transfer', 'credit_card' => 'Credit Card', 'other' => 'Lainnya']" placeholder="Pilih Metode" :value="old('payment_method', $expense->payment_method)" />
                    </div>
                    <x-ui.input name="reference" label="Referensi" :value="old('reference', $expense->reference)" help="No. invoice / nota (opsional)" />
                    <x-ui.textarea name="notes" label="Catatan" rows="2" help="Catatan tambahan (opsional)">{{ old('notes', $expense->notes) }}</x-ui.textarea>
                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <x-ui.button href="{{ route('admin.finance.expenses.index') }}" variant="ghost">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-layouts.admin>
