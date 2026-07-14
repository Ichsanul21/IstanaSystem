# Tables

## Table Components

The project has two table components:

| Component | Namespace | Use Case |
|-----------|-----------|----------|
| `<x-tables.table>` | `tables` | **Primary** — branded table with hover/striped support, TailAdmin styling |
| `<x-ui.table>` | `ui` | Alternate — generic table wrapper (similar API, different base colors) |

Both live at:
```
resources/views/components/tables/table.blade.php    ← x-tables.table
resources/views/components/ui/table.blade.php        ← x-ui.table
```

---

## x-tables.table (Primary)

The main table component with TailAdmin-inspired styling. Automatically applies brand colors (`border-lo-gray`, `text-black/40` headers).

```blade
<x-tables.table
    :headers="['Kode', 'Nama', 'Harga', 'Status', 'Aksi']"
    :hoverable="true"
    :striped="false"
>
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

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `headers` | `array` | `[]` | Array of strings or `['label' => string, 'width' => string, 'sortable' => bool]` |
| `hoverable` | `bool` | `true` | Adds `.table-hoverable` class for row hover highlighting |
| `striped` | `bool` | `false` | Adds `.table-striped` class for alternating row backgrounds |

### Header Formats

```blade
{{-- Simple string headers --}}
<x-tables.table :headers="['Name', 'Email', 'Role']" />

{{-- Structured headers with width and sortability --}}
<x-tables.table :headers="[
    ['label' => 'Name', 'sortable' => true],
    ['label' => 'Email', 'width' => '30%'],
    ['label' => 'Role', 'sortable' => true],
    'Actions',
]" />
```

### Named Slot: `rows`

If you need to pass the body via a named slot instead of the default slot:

```blade
<x-tables.table :headers="['Name', 'Email']">
    <x-slot:rows>
        <tr><td>Alice</td><td>alice@test.com</td></tr>
        <tr><td>Bob</td><td>bob@test.com</td></tr>
    </x-slot:rows>
</x-tables.table>
```

---

## Table CSS Classes

### `.table-hoverable`

Applied automatically when `hoverable="true"` (default on `x-tables.table`).

```css
.table-hoverable tbody tr:hover {
    background-color: var(--color-gray-50);   /* #FAFAFA */
}
.dark .table-hoverable tbody tr:hover {
    background-color: var(--color-dark-800);  /* #2D2D2D */
}
```

### `.table-striped`

Applied automatically when `striped="true"`.

```css
.table-striped tbody tr:nth-child(odd) {
    background-color: var(--color-gray-50);
}
.dark .table-striped tbody tr:nth-child(odd) {
    background-color: color-mix(in srgb, var(--color-dark-800) 50%, transparent);
}
```

---

## x-ui.table (Alternate)

Similar API but uses slightly different base colors (`gray-200` instead of `lo-gray`). Supports the same `headers`, `striped`, and `hoverable` props.

```blade
<x-ui.table
    :headers="['Name', 'Email', 'Role']"
    :striped="true"
>
    <tr><td>Alice</td><td>alice@test.com</td><td>Admin</td></tr>
</x-ui.table>
```

---

## Table Styles Reference

**Base styles (both components):**
```
┌──────┬──────────┬──────────┬──────────┬──────────┐
│ Kode │ Nama     │ Harga    │ Status   │ Aksi     │ ← header: bg-gray-50
├──────┼──────────┼──────────┼──────────┼──────────┤
│ CK   │ Cuci...  │ Rp 5.000│ ✓ Aktif  │ [Edit]   │ ← body: bg-white
│ CB   │ Cuci...  │ Rp 7.000│ ✓ Aktif  │ [Edit]   │
└──────┴──────────┴──────────┴──────────┴──────────┘
```

**x-tables.table:**
- Header: `px-6 py-3 text-left text-xs font-bold tracking-wider uppercase text-black/40`
- Body: `text-sm` with `divide-y divide-lo-gray`
- Wrapper: `rounded-xl border border-lo-gray`
- `border-collapse` + `min-w-full`

---

## Data Tables (Alpine.js)

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
                <td x-text="row.name" class="px-6 py-4 text-sm font-medium"></td>
                <td x-text="row.status" class="px-6 py-4"></td>
            </tr>
        </template>
    </x-tables.table>

    {{-- Pagination --}}
    <div class="flex items-center justify-between mt-4">
        <span class="text-sm text-black/40"
              x-text="`Menampilkan ${from} - ${to} dari ${total}`"></span>
        <x-ui.pagination />
    </div>
</div>
```

---

## Status Badges in Tables

| Status | Badge |
|--------|-------|
| Pending | `<x-ui.badge variant="warning">Pending</x-ui.badge>` |
| Process | `<x-ui.badge variant="info">Proses</x-ui.badge>` |
| Finished | `<x-ui.badge variant="success">Selesai</x-ui.badge>` |
| Cancelled | `<x-ui.badge variant="danger">Batal</x-ui.badge>` |
| Paid | `<x-ui.badge variant="success">Lunas</x-ui.badge>` |
| Unpaid | `<x-ui.badge variant="warning">Belum Bayar</x-ui.badge>` |

---

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

---

## Empty State

```blade
<div class="col-span-full text-center py-12">
    <x-icon name="package" class="text-4xl text-gray-200" />
    <p class="mt-3 text-sm text-black/40 font-mono">Belum ada data</p>
</div>
```

---

## Pagination

See [UI Components — Pagination](03-ui-components.md#pagination) for `<x-ui.pagination>` documentation.

```blade
<div class="flex items-center justify-between mt-4">
    <span class="text-sm text-black/40">
        Menampilkan {{ $orders->firstItem() }} hingga {{ $orders->lastItem() }}
        dari {{ $orders->total() }}
    </span>
    <x-ui.pagination :paginator="$orders" />
</div>
```
