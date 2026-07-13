<x-layouts.admin title="POS - Pesanan Baru">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">POS - Pesanan Baru</h1>
        </div>
    </x-slot:header>

    @include('orders.create')
</x-layouts.admin>
