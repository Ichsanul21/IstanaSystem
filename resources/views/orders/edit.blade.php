<x-layouts.admin title="Edit Order">
    <x-slot:header>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Order #{{ $order->order_number ?? $order['order_number'] ?? '-' }}</h1>
    </x-slot:header>

    <form method="POST" action="{{ route('admin.orders.update', $order->id ?? $order['id']) }}">
        @csrf
        @method('PUT')
        <x-ui.card class="max-w-2xl">
            <x-slot:header>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Status & Catatan</h2>
            </x-slot:header>
            <div class="space-y-4">
                @php $statuses = \App\Enums\OrderStatus::cases(); @endphp
                <x-ui.select name="status" label="Status Order" :options="collect($statuses)->reject(fn($s) => $s === \App\Enums\OrderStatus::Draft)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()" :value="old('status', $order->status ?? $order['status'] ?? 'pending')" required />
                <x-ui.textarea name="notes" label="Catatan">{{ old('notes', $order->notes ?? $order['notes'] ?? '') }}</x-ui.textarea>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-ui.button type="submit" variant="primary">Simpan Perubahan</x-ui.button>
                <x-ui.button href="{{ route('admin.orders.show', $order->id ?? $order['id']) }}" variant="ghost">Batal</x-ui.button>
            </div>
        </x-ui.card>
    </form>
</x-layouts.admin>