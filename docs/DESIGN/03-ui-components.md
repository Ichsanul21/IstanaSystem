# UI Components

All components follow TailAdmin Pro patterns adapted to Blade + Alpine.js.

## Buttons

```blade
{{-- Primary (Orange) --}}
<x-ui.button variant="primary">Simpan</x-ui.button>
{{-- Renders: bg-lo text-white hover:bg-lo-600 cta-main --}}

{{-- Dark (Black) --}}
<x-ui.button variant="dark">Batal</x-ui.button>
{{-- Renders: bg-black text-white hover:bg-black/80 --}}

{{-- Outline --}}
<x-ui.button variant="outline">Lihat Detail</x-ui.button>
{{-- Renders: border border-lo-gray text-black hover:border-black --}}

{{-- Ghost --}}
<x-ui.button variant="ghost">Hapus</x-ui.button>
{{-- Renders: text-black/50 hover:text-black --}}

{{-- Danger --}}
<x-ui.button variant="danger">Hapus</x-ui.button>
{{-- Renders: bg-error text-white --}}

{{-- With Icon --}}
<x-ui.button variant="primary" icon="lucide:plus">Tambah</x-ui.button>

{{-- Sizes: sm, md (default), lg --}}
<x-ui.button size="sm">Kecil</x-ui.button>
<x-ui.button size="lg">Besar</x-ui.button>
```

**CTA Shine Effect** (from landing page):
```css
.cta-main {
    position: relative; overflow: hidden;
    transition: all .3s cubic-bezier(.22,1,.36,1);
}
.cta-main::after {
    content: ''; position: absolute; top: 0; left: -100%;
    width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.18), transparent);
    transition: left .5s;
}
.cta-main:hover::after { left: 100%; }
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

```blade
{{-- Modal with Alpine --}}
<div x-data="{ open: false }">
    <x-ui.button @click="open = true">Buka Modal</x-ui.button>
    
    <x-ui.modal x-show="open" @click.outside="open = false">
        <x-slot:title>Konfirmasi</x-slot:title>
        <p>Apakah Anda yakin?</p>
        <x-slot:footer>
            <x-ui.button variant="outline" @click="open = false">Batal</x-ui.button>
            <x-ui.button variant="primary">Ya, Hapus</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>
```

**Modal Structure (TailAdmin pattern):**
```
┌──────────────────────────────────────┐
│  Modal Title                    [✕]  │
├──────────────────────────────────────┤
│  Content area                        │
│                                      │
├──────────────────────────────────────┤
│  [Cancel]  [Confirm]                 │
└──────────────────────────────────────┘
```

## Alerts

```blade
<x-ui.alert type="success">Pembayaran berhasil!</x-ui.alert>
<x-ui.alert type="warning">Stok hampir habis.</x-ui.alert>
<x-ui.alert type="error">Gagal memproses order.</x-ui.alert>
<x-ui.alert type="info">Sistem akan maintenance.</x-ui.alert>
```

| Type | Border | Icon | Text |
|------|--------|------|------|
| success | `border-l-success` | check-circle | Dark green |
| warning | `border-l-warning` | alert | Dark amber |
| error | `border-l-error` | error | Dark red |
| info | `border-l-info` | info | Dark blue |

## Badges

```blade
<x-ui.badge variant="success">Selesai</x-ui.badge>
<x-ui.badge variant="warning">Proses</x-ui.badge>
<x-ui.badge variant="error">Gagal</x-ui.badge>
<x-ui.badge variant="info">Info</x-ui.badge>
<x-ui.badge variant="lo">Baru</x-ui.badge> {{-- Orange accent --}}
<x-ui.badge variant="dark">Draft</x-ui.badge>
```

## Tabs (TailAdmin Pattern)

```blade
<div x-data="{ tab: 'info' }">
    <x-ui.tabs>
        <x-ui.tab @click="tab = 'info'" :active="tab === 'info'">Informasi</x-ui.tab>
        <x-ui.tab @click="tab = 'orders'" :active="tab === 'orders'">Order</x-ui.tab>
        <x-ui.tab @click="tab = 'points'" :active="tab === 'points'">Poin</x-ui.tab>
    </x-ui.tabs>
    
    <div x-show="tab === 'info'">...</div>
    <div x-show="tab === 'orders'" x-cloak>...</div>
    <div x-show="tab === 'points'" x-cloak>...</div>
</div>
```

**Tab Styles:**
- Active: `border-b-2 border-lo text-black font-bold`
- Inactive: `text-black/40 hover:text-black/70`

## Pagination

```blade
<x-ui.pagination :paginator="$orders" />
{{-- Renders numbered page buttons with prev/next --}}
```

**Style:** Numbers in `w-9 h-9` buttons, active has `bg-lo text-white`, inactive has `border border-lo-gray text-black hover:bg-gray-50`.

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
<x-ui.progress :value="75" variant="lo" />
{{-- Renders: gray bg, orange fill, animated --}}
```

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
