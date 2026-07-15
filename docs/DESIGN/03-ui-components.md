# UI Components

All components follow TailAdmin Pro patterns adapted to Blade + Alpine.js.

## Buttons

```blade
{{-- Primary (Orange) — includes .cta-main shine effect --}}
<x-ui.button variant="primary">Simpan</x-ui.button>
{{-- Renders: bg-lo text-white hover:bg-lo-600 focus:ring-lo shadow-sm cta-main --}}

{{-- Dark (Black) --}}
<x-ui.button variant="dark">Batal</x-ui.button>
{{-- Renders: bg-dark text-white hover:bg-dark-800 focus:ring-dark shadow-sm --}}

{{-- Outline --}}
<x-ui.button variant="outline">Lihat Detail</x-ui.button>
{{-- Renders: border border-lo-gray text-black hover:border-black focus:ring-lo --}}

{{-- Ghost --}}
<x-ui.button variant="ghost">Hapus</x-ui.button>
{{-- Renders: text-black/50 hover:text-black focus:ring-lo --}}

{{-- Danger --}}
<x-ui.button variant="danger">Hapus</x-ui.button>
{{-- Renders: bg-error text-white hover:bg-red-700 focus:ring-error shadow-sm --}}

{{-- Icon-only button (no label, square, p-2 padding) --}}
<x-ui.button variant="icon">
    <x-icon name="settings" class="text-sm" />
</x-ui.button>
{{-- Renders: text-black/50 hover:bg-gray-100 focus:ring-lo p-2 --}}

{{-- With leading SVG icon --}}
<x-ui.button variant="primary" icon="M12 4.5v15m7.5-7.5h-15">Tambah</x-ui.button>
{{-- icon prop: SVG path data (M... L... format), rendered as inline SVG --}}

{{-- Sizes: sm, md (default), lg --}}
<x-ui.button size="sm">Kecil</x-ui.button>
<x-ui.button size="md">Sedang</x-ui.button>
<x-ui.button size="lg">Besar</x-ui.button>

{{-- Loading state — shows spinner, disables button --}}
<x-ui.button variant="primary" :loading="true">Menyimpan...</x-ui.button>
{{-- Renders: animate-spin spinner SVG + text, button is disabled --}}

{{-- Link as button — renders <a> tag instead of <button> --}}
<x-ui.button variant="primary" href="{{ route('admin.orders.index') }}">Lihat Order</x-ui.button>
```

### Button Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `variant` | `string` | `primary` | `primary`, `dark`, `danger`, `outline`, `ghost`, `icon` |
| `size` | `string` | `md` | `sm`, `md`, `lg` |
| `icon` | `string\|null` | `null` | SVG path data (the `d` attribute content after `M`) |
| `loading` | `bool` | `false` | Shows spinner, disables button |
| `type` | `string` | `button` | HTML button type |
| `disabled` | `bool` | `false` | Disables the button |
| `href` | `string\|null` | `null` | If set, renders as `<a>` tag instead of `<button>` |

### Size Specs

| Size | Padding | Text | Gap | Icon |
|------|---------|------|-----|------|
| `sm` | `px-3 py-1.5` | `text-xs` | `gap-1.5` | `h-4 w-4` |
| `md` | `px-4 py-2` | `text-sm` | `gap-2` | `h-5 w-5` |
| `lg` | `px-6 py-3` | `text-base` | `gap-2.5` | `h-5 w-5` |

**CTA Shine Effect** (from landing page):
```css
.cta-main {
    position: relative; overflow: hidden;
    transition: all .3s cubic-bezier(.22,1,.36,1);
}
.cta-main::after {
    content: '';
    position: absolute;
    inset: 0;
    transform: translateX(-100%);
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.18), transparent);
    transition: transform .5s;
}
.cta-main:hover::after { transform: translateX(100%); }
.cta-main:hover { transform: scale(1.02); }
.cta-main:active { transform: scale(.97); }
```

## Cards

```blade
{{-- Default card --}}
<x-ui.card>
    <x-slot:header>
        <h3 class="text-lg font-bold">Judul Card</h3>
    </x-slot:header>
    <p>Content...</p>
</x-ui.card>

{{-- Metric/Stat card --}}
<x-ui.card variant="metric">
    <div class="text-3xl font-black tracking-tighter">Rp 1.240.000</div>
    <div class="text-xs text-black/40 mt-1">Total Pendapatan Hari Ini</div>
</x-ui.card>

{{-- Hover card (svc-card style) --}}
<x-ui.card variant="hover">
    {{-- Hover: translateY(-5px), border-black --}}
</x-ui.card>
```

**Card Styles:**
```css
.svc-card {
    border: 1px solid var(--color-gray-100); /* #E5E5E5 */
    transition: all .3s cubic-bezier(.22,1,.36,1);
}
.svc-card:hover {
    border-color: #000;
    transform: translateY(-5px);
    box-shadow: 0 20px 40px -12px rgba(0,0,0,.07);
}
```

## Modals

Dual API: supports both **event-driven** (existing Breeze pattern) and **inline x-data** (TailAdmin pattern).

```blade
{{-- API 1: Event-driven (name prop + $dispatch) --}}
<x-ui.button x-on:click="$dispatch('open-modal', 'confirm-modal')">Buka Modal</x-ui.button>

<x-ui.modal name="confirm-modal" maxWidth="lg">
    <x-slot:title>Konfirmasi</x-slot:title>
    <p>Apakah Anda yakin?</p>
    <x-slot:footer>
        <x-ui.button variant="outline" x-on:click="$dispatch('close-modal', 'confirm-modal')">Batal</x-ui.button>
        <x-ui.button variant="primary">Ya, Hapus</x-ui.button>
    </x-slot:footer>
</x-ui.modal>

{{-- API 2: Inline x-data (TailAdmin pattern) --}}
<div x-data="{ open: false }">
    <x-ui.button @click="open = true">Buka Modal</x-ui.button>
    
    <div x-show="open" @click.outside="open = false" class="fixed inset-0 z-50">...</div>
</div>
```

**Modal Structure:**
```
┌──────────────────────────────────────┐
│  Modal Title                    [✕]  │
├──────────────────────────────────────┤
│  Content area / $body or $slot       │
│                                      │
├──────────────────────────────────────┤
│  Footer actions (optional)           │
└──────────────────────────────────────┘
```

## Alerts

```blade
{{-- Basic alerts --}}
<x-ui.alert type="success">Pembayaran berhasil!</x-ui.alert>
<x-ui.alert type="warning">Stok hampir habis.</x-ui.alert>
<x-ui.alert type="error">Gagal memproses order.</x-ui.alert>
<x-ui.alert type="info">Sistem akan maintenance.</x-ui.alert>

{{-- With title --}}
<x-ui.alert type="success" title="Berhasil">Pembayaran sebesar Rp 50.000 telah diterima.</x-ui.alert>

{{-- Dismissible --}}
<x-ui.alert type="info" :dismissible="true">Notifikasi ini bisa ditutup.</x-ui.alert>
```

### Alert Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | `string` | `info` | `success`, `warning`, `error`, `info` |
| `dismissible` | `bool` | `false` | Shows close button, hides alert on click via Alpine.js |
| `title` | `string\|null` | `null` | Optional bold title above the message |

### Left-Border Variant Colors

| Type | Left Border | Icon | Text Color |
|------|------------|------|------------|
| `success` | `border-l-success` (green) | `check-circle` | `text-success` |
| `warning` | `border-l-warning` (amber) | `alert-circle` | `text-warning` |
| `error` | `border-l-error` (red) | `x-circle` | `text-error` |
| `info` | `border-l-info` (blue) | `info` | `text-info` |

**Structure:** 4px left border + Icon + Content (optional title + message) + optional dismiss button.

## Badges

```blade
<x-ui.badge variant="success">Selesai</x-ui.badge>
<x-ui.badge variant="warning">Proses</x-ui.badge>
<x-ui.badge variant="danger">Gagal</x-ui.badge>
<x-ui.badge variant="info">Info</x-ui.badge>
<x-ui.badge variant="lo">Baru</x-ui.badge> {{-- Orange accent --}}
<x-ui.badge variant="dark">Draft</x-ui.badge>
<x-ui.badge variant="gray">Default</x-ui.badge> {{-- Gray neutral --}}
<x-ui.badge variant="primary">Utama</x-ui.badge> {{-- Same as lo --}}

{{-- Sizes --}}
<x-ui.badge variant="success" size="sm">Kecil</x-ui.badge>
<x-ui.badge variant="success" size="md">Sedang</x-ui.badge>
```

### Badge Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `variant` | `string` | `gray` | `success`, `warning`, `danger`, `info`, `gray`, `primary`, `lo`, `dark` |
| `size` | `string` | `md` | `sm`, `md` |

### Badge Variants

| Variant | Light | Dark Mode |
|---------|-------|-----------|
| `success` | `bg-green-100 text-green-700` | `bg-green-900/40 text-green-300` |
| `warning` | `bg-yellow-100 text-yellow-700` | `bg-yellow-900/40 text-yellow-300` |
| `danger` | `bg-red-100 text-red-700` | `bg-red-900/40 text-red-300` |
| `info` | `bg-blue-100 text-blue-700` | `bg-blue-900/40 text-blue-300` |
| `gray` | `bg-gray-100 text-gray-700` | `bg-dark-700 text-gray-300` |
| `lo` / `primary` | `bg-lo-50 text-lo` | `bg-lo/20 text-lo-200` |
| `dark` | `bg-dark text-white` | `bg-white text-dark` |

## Tabs (Data-Driven)

The `<x-ui.tabs>` component uses a data-driven approach — pass an array of tab definitions and it manages the active state internally via Alpine.js.

```blade
<x-ui.tabs
    :tabs="[
        ['id' => 'info', 'label' => 'Informasi'],
        ['id' => 'orders', 'label' => 'Order'],
        ['id' => 'points', 'label' => 'Poin'],
    ]"
    active="info"
>
    {{-- Tab content (all rendered, shown/hidden via x-show) --}}
    <div x-show="activeTab === 'info'">Content informasi...</div>
    <div x-show="activeTab === 'orders'" x-cloak>Content order...</div>
    <div x-show="activeTab === 'points'" x-cloak>Content poin...</div>
</x-ui.tabs>
```

### Tabs Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `tabs` | `array` | `[]` | Array of `['id' => string, 'label' => string]` |
| `active` | `string\|null` | First tab's `id` | Initially active tab |

**Tab Styles:**
- Active: `border-b-2 border-lo text-black dark:text-white font-bold`
- Inactive: `border-transparent text-black/40 dark:text-white/40 hover:text-black/70 dark:hover:text-white/70`

## Pagination

Custom component (not Laravel's default `@paginator`). Accepts a LengthAwarePaginator and renders page numbers, prev/next arrows, and a "Menampilkan X - Y dari Z" summary.

```blade
<x-ui.pagination :paginator="$orders" />
```

### Pagination Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `paginator` | `LengthAwarePaginator` | Yes | The paginated result from the controller |

**Renders:**
```
Menampilkan 1 hingga 15 dari 120    [<] [1] [2] [3] ... [8] [>]
```

**Style:** Numbers in `w-9 h-9 rounded-lg` buttons, active has `bg-lo text-white`, inactive has `border border-lo-gray text-black hover:bg-gray-50`. Prev/next use `<x-icon>` chevron arrows. Disabled states use `text-black/20 cursor-not-allowed`.

## Dropdowns

```blade
<div x-data="{ open: false }" class="relative">
    <x-ui.button @click="open = !open" variant="ghost">
        Aksi <span class="iconify" data-icon="lucide:chevron-down"></span>
    </x-ui.button>
    
    <div x-show="open" @click.outside="open = false"
         class="absolute right-0 mt-1 w-48 bg-white border border-lo-gray shadow-theme-lg z-50">
        <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50">Edit</button>
        <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50">Hapus</button>
    </div>
</div>
```

## Progress Bars

```blade
{{-- Default progress bar --}}
<x-ui.progress :value="75" />
{{-- Renders: gray bg, primary (orange) fill, animated, shows "75%" label --}}

{{-- Custom color and label --}}
<x-ui.progress :value="3" :max="8" color="lo" label="Produksi" />
{{-- Renders: "Produksi 38%" with orange fill --}}

{{-- Without label --}}
<x-ui.progress :value="60" :showLabel="false" />

{{-- Size variants --}}
<x-ui.progress :value="75" size="sm" />  {{-- h-1.5 --}}
<x-ui.progress :value="75" size="md" />  {{-- h-2.5 (default) --}}
<x-ui.progress :value="75" size="lg" />  {{-- h-4 --}}

{{-- Color variants --}}
<x-ui.progress :value="75" color="primary" />  {{-- bg-primary --}}
<x-ui.progress :value="75" color="lo" />        {{-- bg-lo --}}
<x-ui.progress :value="75" color="green" />     {{-- bg-green-500 --}}
<x-ui.progress :value="75" color="blue" />      {{-- bg-blue-500 --}}
<x-ui.progress :value="75" color="red" />       {{-- bg-red-500 --}}
```

### Progress Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `value` | `int\|float` | `0` | Current value |
| `max` | `int\|float` | `100` | Maximum value (percentage calculated automatically) |
| `color` | `string` | `primary` | `primary`, `lo`, `green`, `blue`, `red` |
| `size` | `string` | `md` | `sm` (h-1.5), `md` (h-2.5), `lg` (h-4) |
| `showLabel` | `bool` | `true` | Show/hide the label + percentage text |
| `label` | `string` | `'Progress'` | Custom label text (passed via `$attributes`) |

**Structure:** Optional label row (`justify-between`) + track (`bg-gray-200 rounded-full`) + fill bar (`{color} rounded-full transition-all duration-300`).

## Notifications / Toasts

```blade
{{-- Alpine.js notification system --}}
<div x-data="notificationStore()" 
     x-show="show" 
     x-transition.duration.300ms
     class="fixed top-20 right-4 z-[9999] ...">
    <div :class="type === 'success' ? 'bg-success' : 'bg-error'" class="...">
        <span x-text="message"></span>
    </div>
</div>
```
