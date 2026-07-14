<x-layouts.admin title="Refunds">
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Refund Requests</h1>
        </div>
    </x-slot:header>

    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('admin.refunds.index') }}" class="flex items-center gap-4">
            <x-ui.select name="status" label="Filter Status" placeholder="Semua Status" :options="['pending' => 'Pending', 'approved' => 'Disetujui', 'completed' => 'Selesai', 'rejected' => 'Ditolak']" :value="request('status')" />
            <div class="flex items-end gap-2">
                <x-ui.button type="submit" variant="primary">Filter</x-ui.button>
                <x-ui.button href="{{ route('admin.refunds.index') }}" variant="ghost">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card class="p-0">
        <x-ui.table :headers="[
            ['label' => 'Order'],
            ['label' => 'Jumlah'],
            ['label' => 'Alasan'],
            ['label' => 'Status'],
            ['label' => 'Diminta Oleh'],
            ['label' => 'Tanggal'],
            ['label' => 'Aksi'],
        ]">
            @forelse($refunds ?? [] as $refund)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 text-sm font-medium text-primary">#{{ $refund->order_number ?? $refund['order_number'] ?? $refund->order->order_number ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($refund->amount ?? $refund['amount'] ?? 0, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">{{ $refund->reason ?? $refund['reason'] ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @php
                            $rs = $refund->status ?? $refund['status'] ?? '';
                            $rm = ['pending' => 'warning', 'approved' => 'info', 'completed' => 'success', 'rejected' => 'danger'];
                        @endphp
                        <x-ui.badge :variant="$rm[$rs] ?? 'gray'">{{ $rs }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $refund->requested_by_name ?? $refund['requested_by_name'] ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $refund->created_at ?? $refund['created_at'] ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if(($refund->status ?? $refund['status'] ?? '') === 'pending')
                            <div class="flex items-center gap-1">
                                @can('approve_refunds')
                                <form method="POST" action="{{ route('admin.refunds.approve', $refund->id ?? $refund['id']) }}" class="inline">
                                    @csrf
                                    <x-ui.button type="submit" variant="primary" size="sm">Setujui</x-ui.button>
                                </form>
                                @endcan
                                @can('reject_refunds')
                                <form method="POST" action="{{ route('admin.refunds.reject', $refund->id ?? $refund['id']) }}" class="inline">
                                    @csrf
                                    <x-ui.button type="submit" variant="danger" size="sm">Tolak</x-ui.button>
                                </form>
                                @endcan
                            </div>
                        @elseif(($refund->status ?? $refund['status'] ?? '') === 'approved')
                            @can('complete_refunds')
                            <form method="POST" action="{{ route('admin.refunds.complete', $refund->id ?? $refund['id']) }}" class="inline">
                                @csrf
                                <x-ui.button type="submit" variant="primary" size="sm">Selesaikan</x-ui.button>
                            </form>
                            @endcan
                        @else
                            <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada refund request.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </x-ui.card>
</x-layouts.admin>