<x-layouts.admin title="Tambah Jurnal">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Jurnal Manual</h1>
            <a href="{{ route('admin.finance.journal') }}" class="text-sm text-primary hover:text-primary-dark">Kembali ke Jurnal</a>
        </div>
    </x-slot:header>

    <form method="POST" action="{{ route('admin.finance.journal.store') }}" x-data="journalForm()" x-on:submit.prevent="if(isBalanced) $el.submit()" class="space-y-6">
        @csrf
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Jurnal</h3>
            </x-slot:header>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-ui.label for="description" required>Deskripsi Jurnal</x-ui.label>
                    <x-ui.textarea name="description" id="description" rows="2" placeholder="Deskripsi transaksi..." required />
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Baris Jurnal</h3>
                    <x-ui.button type="button" variant="outline" size="sm" x-on:click="addRow">+ Tambah Baris</x-ui.button>
                </div>
            </x-slot:header>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 w-1/2">Akun</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Debit (Rp)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kredit (Rp)</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 w-16"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        <template x-for="(row, index) in rows" :key="index">
                            <tr>
                                <td class="px-4 py-3">
                                    <select x-model="row.account" :name="`lines[${index}][account_id]`" required
                                            class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary focus:ring-primary text-sm">
                                        <option value="">Pilih Akun</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->code }} {{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" x-model="row.debit" x-on:input="updateTotals" :name="`lines[${index}][debit]`" min="0" step="0.01"
                                           class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary focus:ring-primary text-sm">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" x-model="row.credit" x-on:input="updateTotals" :name="`lines[${index}][credit]`" min="0" step="0.01"
                                           class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-primary focus:ring-primary text-sm">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" x-on:click="removeRow(index)" x-show="rows.length > 2"
                                            class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-800 font-semibold">
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">Total</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white" x-text="formatCurrency(totalDebit)"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white" x-text="formatCurrency(totalCredit)"></td>
                            <td></td>
                        </tr>
                        <tr x-show="!isBalanced && rows.length > 0">
                            <td colspan="4" class="px-4 py-2 text-sm text-red-600 dark:text-red-400">
                                Total Debit dan Kredit harus sama (selisih: <span x-text="formatCurrency(Math.abs(totalDebit - totalCredit))"></span>)
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-ui.card>

        <div class="flex items-center justify-end gap-3">
            <x-ui.button type="button" variant="ghost">Batal</x-ui.button>
            <x-ui.button type="submit" variant="primary">Simpan Jurnal</x-ui.button>
        </div>
    </form>

    @push('scripts')
    <script>
        function journalForm() {
            return {
                rows: [
                    { account: '', debit: '', credit: '' },
                    { account: '', debit: '', credit: '' }
                ],
                get totalDebit() {
                    return this.rows.reduce((sum, row) => sum + (parseFloat(row.debit) || 0), 0);
                },
                get totalCredit() {
                    return this.rows.reduce((sum, row) => sum + (parseFloat(row.credit) || 0), 0);
                },
                get isBalanced() {
                    return this.totalDebit === this.totalCredit;
                },
                updateTotals() {},
                addRow() {
                    this.rows.push({ account: '', debit: '', credit: '' });
                },
                removeRow(index) {
                    if (this.rows.length > 2) {
                        this.rows.splice(index, 1);
                    }
                },
                validateForm(e) {
                    if (!this.isBalanced) {
                        e.preventDefault();
                        alert('Total Debit dan Kredit harus sama!');
                    }
                },
                formatCurrency(value) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>
