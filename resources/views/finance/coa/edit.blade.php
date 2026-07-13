<x-layouts.admin title="Edit Akun">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.finance.coa.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Akun — {{ $account->name }}</h1>
            </div>
        </div>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.finance.coa.update', $account) }}" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="code" label="Code" required :value="old('code', $account->code)" help="Kode unik akun, contoh: 1-1000" />
                <x-ui.input name="name" label="Nama Akun" required :value="old('name', $account->name)" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $types = ['asset' => 'Asset', 'liability' => 'Liability', 'equity' => 'Equity', 'revenue' => 'Revenue', 'expense' => 'Expense'];
                @endphp
                <x-ui.select name="type" label="Tipe" :options="$types" placeholder="Pilih Tipe" required :value="old('type', $account->type)" />
                <x-ui.select name="normal_balance" label="Saldo Normal" :options="['debit' => 'Debit', 'credit' => 'Credit']" placeholder="Pilih Saldo Normal" required :value="old('normal_balance', $account->normal_balance)" />
            </div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $account->is_active)) class="rounded border-gray-300 text-primary focus:ring-primary">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktif</span>
            </label>
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                <x-ui.button href="{{ route('admin.finance.coa.index') }}" variant="ghost">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.admin>
