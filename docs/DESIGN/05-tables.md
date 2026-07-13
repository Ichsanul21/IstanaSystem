# Tables

## Basic Tables (TailAdmin Pattern)

```blade
<x-tables.table :headers="['Kode', 'Nama', 'Harga', 'Status', 'Aksi']">
    <tr>
        <td class="px-6 py-4 text-sm">CK</td>
        <td class="px-6 py-4 text-sm font-medium">Cuci Kering</td>
        <td class="px-6 py-4 text-sm">Rp 5.000</td>
        <td class="px-6 py-4"><x-ui.badge variant="success">Aktif</x-ui.badge></td>
        <td class="px-6 py-4">
            <div class="flex items-center gap-2">
                <x-ui.button size="sm" variant="ghost">Edit</x-ui.button>
                <x-ui.button size="sm" variant="ghost">Hapus</x-ui.button>
            </div>
        </td>
    </tr>
</x-tables.table>
```

**Table Styles (TailAdmin base):**
```
┌──────┬──────────┬──────────┬──────────┬──────────┐
│ Kode │ Nama     │ Harga    │ Status   │ Aksi     │
├──────┼──────────┼──────────┼──────────┼──────────┤
│ CK   │ Cuci...  │ Rp 5.000│ ✓ Aktif  │ [Edit]   │
│ CB   │ Cuci...  │ Rp 7.000│ ✓ Aktif  │ [Edit]   │
└──────┴──────────┴──────────┴──────────┴──────────┘
```

**Styling:**
- Header: `text-xs font-bold tracking-wider uppercase text-black/40 bg-gray-50 px-6 py-3`
- Body: `text-sm` with `border-t border-lo-gray`
- Row hover: `hover:bg-gray-50` (or dark: `hover:bg-dark-800`)
- Border: `border-collapse` with `divide-y divide-lo-gray`

## Data Tables (with Alpine.js)

```blade
<div x-data="dataTable()">
    {{-- Search + Filters --}}
    <div class="flex items-center gap-4 mb-4">
        <x-form.input name="search" placeholder="Cari..." 
                      @input.debounce="search = $event.target.value" />
        <x-form.select name="filter" :options="[...]"
                       @change="filter = $event.target.value" />
    </div>
    
    {{-- Table --}}
    <x-tables.table :headers="headers">
        <template x-for="row in filteredRows" :key="row.id">
            <tr>
                <td x-text="row.code" class="px-6 py-4 text-sm"></td>
                ...
            </tr>
        </template>
    </x-tables.table>
    
    {{-- Pagination --}}
    <div class="flex items-center justify-between mt-4">
        <span class="text-sm text-black/40" x-text="`Menampilkan ${from} - ${to} dari ${total}`"></span>
        <x-ui.pagination />
    </div>
</div>
```

## Status Badges in Tables

| Status | Badge |
|--------|-------|
| Pending | `<x-ui.badge variant="warning">Pending</x-ui.badge>` |
| Process | `<x-ui.badge variant="info">Proses</x-ui.badge>` |
| Finished | `<x-ui.badge variant="success">Selesai</x-ui.badge>` |
| Cancelled | `<x-ui.badge variant="error">Batal</x-ui.badge>` |
| Paid | `<x-ui.badge variant="success">Lunas</x-ui.badge>` |
| Unpaid | `<x-ui.badge variant="warning">Belum Bayar</x-ui.badge>` |

## Production Status Timeline (in table)

```blade
{{-- For each order item, show 8 dots --}}
<div class="flex gap-1">
    @foreach($statuses as $status)
        <div class="w-2.5 h-2.5 rounded-full {{ $status->is_completed ? 'bg-lo' : 'bg-gray-100' }}"
             title="{{ $status->name }}"></div>
    @endforeach
</div>
```

## Empty State

```blade
<div class="col-span-full text-center py-12">
    <span class="iconify text-4xl text-gray-200" data-icon="lucide:package"></span>
    <p class="mt-3 text-sm text-black/40 font-mono">Belum ada data</p>
</div>
```
