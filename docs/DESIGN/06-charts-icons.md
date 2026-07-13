# Charts & Icons

## Chart.js Configuration

**Library:** Chart.js via npm or CDN.

```javascript
// resources/js/charts.js
import Chart from 'chart.js/auto';

// Global defaults
Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.color = '#666666';
Chart.defaults.borderColor = '#E5E5E5';
```

### Chart Color Scheme

| Element | Light Mode | Dark Mode |
|---------|-----------|-----------|
| Primary line | `#FF6B00` | `#FF6B00` |
| Secondary line | `#000000` | `#FFFFFF` |
| Tertiary line | `#E5E5E5` | `#404040` |
| Grid lines | `#E5E5E5` | `#333333` |
| Tooltip bg | `#000000` | `#FFFFFF` |
| Tooltip text | `#FFFFFF` | `#000000` |

### Chart Components

```blade
{{-- Line Chart --}}
<x-charts.line 
    :labels="['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']"
    :datasets="[
        ['label' => 'Pendapatan', 'data' => [50000, 75000, 60000, ...]],
    ]"
/>

{{-- Bar Chart --}}
<x-charts.bar 
    :labels="['Cuci Kering', 'Cuci Basah', 'Setrika', 'Dry Clean']"
    :datasets="[
        ['label' => 'Jumlah Order', 'data' => [45, 30, 20, 5]],
    ]"
/>

{{-- Pie/Doughnut --}}
<x-charts.pie 
    :labels="['Cash', 'Transfer', 'QRIS', 'Gateway']"
    :data="[60, 20, 15, 5]"
    :colors="['#FF6B00', '#000000', '#E5E5E5', '#10B981']"
/>

{{-- Area Chart (filled line) --}}
<x-charts.area 
    :labels="$dates"
    :datasets="[
        ['label' => 'Order', 'data' => $orderCounts],
    ]"
/>
```

### Chart Sizes

| Context | Size |
|---------|------|
| Dashboard widget | `h-72` or `h-80` |
| Full report | `h-96` |
| Modal/preview | `h-48` |

## Icons

**Library:** Iconify with Lucide icon set (same as landing page).

```html
{{-- Usage in Blade --}}
<span class="iconify text-lg" data-icon="lucide:shirt"></span>
<span class="iconify text-xl text-lo" data-icon="lucide:message-circle"></span>

{{-- Or via component --}}
<x-icon name="shirt" class="text-lg" />
<x-icon name="message-circle" class="text-xl text-lo" />
```

### Icon Catalog (Key Icons)

| Icon (Lucide) | Usage |
|---------------|-------|
| `shirt` | Cuci kering / laundry |
| `sparkles` | Cuci basah / premium |
| `gem` | Dry cleaning |
| `layers-2` | Karpet / bedding |
| `footprints` | Sepatu & tas |
| `scan-barcode` | QR / barcode scanning |
| `message-circle` | WhatsApp |
| `truck` | Antar-jemput |
| `clock` | Tepat waktu |
| `shield-check` | Garansi |
| `search` | Search / cek lokasi |
| `map-pin` | Alamat / location |
| `phone` | Telepon |
| `instagram` | Social media |
| `mail` | Email |
| `bell` | Notifications |
| `user` | User avatar |
| `settings` | Settings |
| `bar-chart-3` | Reports / analytics |
| `package` | Inventory |
| `shopping-cart` | POS / orders |
| `credit-card` | Payment |
| `users` | Customers |
| `tag` | Promotions |
| `file-text` | Invoices |
| `calendar` | Calendar / dates |
| `activity` | Live tracking |
| `lock` | Security / data isolation |
| `plus` | Add new |
| `trash-2` | Delete |
| `pencil` | Edit |
| `eye` | View detail |
| `download` | Export |
| `upload` | Import |
| `check-circle` | Success |
| `alert-circle` | Warning |
| `x-circle` | Error |
| `info` | Info |
| `sun` | Light mode |
| `moon` | Dark mode |
| `menu` | Mobile menu |
| `x` | Close |
| `chevron-down` | Dropdown |
| `chevron-up` | Collapse |
| `chevron-right` | Forward / next |
| `chevron-left` | Back |
| `more-horizontal` | Three dots |
| `copy` | Copy to clipboard |
| `refresh-cw` | Sync / reload |
| `printer` | Print receipt |
| `filter` | Filter |
| `grid` | Grid view |
| `list` | List view |

### SVG Icon Components (Blade)

For icons used frequently, create Blade SVG components:

```blade
{{-- resources/views/components/icons/shirt.blade.php --}}
<svg {{ $attributes->merge(['class' => 'inline-block']) }} 
     width="24" height="24" viewBox="0 0 24 24" 
     fill="none" stroke="currentColor" stroke-width="2">
    <path d="M6 5L3 9l3 3 ..." />
</svg>
```

```blade
{{-- Usage --}}
<x-icons.shirt class="w-5 h-5 text-lo" />
```
