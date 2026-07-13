<x-layouts.admin title="Edit Pelanggan">
    <x-slot:header>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.customers.show', $customer) }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Pelanggan</h1>
        </div>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <x-ui.input name="name" label="Nama" required value="{{ old('name', $customer->name) }}" />
                <x-ui.input name="phone" label="Telepon" type="tel" required value="{{ old('phone', $customer->phone) }}" />
                <x-ui.input name="email" label="Email" type="email" value="{{ old('email', $customer->email) }}" />
                <x-ui.select name="membership_tier_id" label="Tier Member" :options="$tiers" placeholder="Pilih Tier" value="{{ old('membership_tier_id', $customer->membership_tier_id) }}" />
            </div>
            <x-ui.textarea name="address" label="Alamat" rows="3">{{ old('address', $customer->address) }}</x-ui.textarea>
            <div class="flex items-center gap-3 border-t border-gray-200 dark:border-gray-700 pt-6">
                <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
                <x-ui.button href="{{ route('admin.customers.show', $customer) }}" variant="secondary">Batal</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.admin>