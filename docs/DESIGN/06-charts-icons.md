# Charts & Icons

## Chart.js Setup

### npm Import + Global Defaults

Chart.js is imported via npm and configured with global defaults matching the Istana design tokens.

```javascript
// resources/js/app.js or a dedicated charts module
import Chart from 'chart.js/auto';

// Global defaults — match Inter font + Istana color palette
Chart.defaults.font.family = '"Inter", ui-sans-serif, system-ui, sans-serif';
Chart.defaults.color = '#666666';
Chart.defaults.borderColor = '#E5E5E5';
```

All `<x-charts.*>` components use Alpine.js `x-data` to instantiate Chart.js on `<canvas>` elements. No additional import is needed in Blade templates — Chart.js must be available globally (via bundled JS or CDN).

### Chart Color Scheme

| Element | Light Mode | Dark Mode |
|---------|-----------|-----------|
| Primary line/bar | `#FF6B00` (lo) | `#FF6B00` (lo) |
| Secondary line/bar | `#000000` (dark) | `#FFFFFF` (white) |
| Tertiary line/bar | `#E5E5E5` (gray-100) | `#404040` (dark-700) |
| Grid lines | `#E5E5E5` | `#333333` |
| Tooltip background | `#000000` | `#FFFFFF` |
| Tooltip text | `#FFFFFF` | `#000000` |
| Legend text | `#666666` (gray-500) | `#999999` (gray-400) |

---

## x-charts.line

Line chart with multiple datasets. Uses `Chart.js type: 'line'`.

```blade
<x-charts.line
    :labels="['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']"
    :datasets="[
        ['label' => 'Pendapatan', 'data' => [50000, 75000, 60000, 80000, 70000, 90000, 45000], 'borderColor' => '#FF6B00', 'tension' => 0.3],
    ]"
    height="h-80"
/>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `labels` | `array` | `[]` | X-axis labels (e.g. day names, dates) |
| `datasets` | `array` | `[]` | Array of Chart.js dataset objects: `['label', 'data', 'borderColor', ...]` |
| `height` | `string` | `h-80` | Tailwind height class for the container |

**Chart.js options:** `responsive: true`, `maintainAspectRatio: false`, `legend: position: 'bottom'`, `y: { beginAtZero: true }`.

---

## x-charts.bar

Bar chart with multiple datasets. Uses `Chart.js type: 'bar'`.

```blade
<x-charts.bar
    :labels="['Cuci Kering', 'Cuci Basah', 'Setrika', 'Dry Clean']"
    :datasets="[
        ['label' => 'Jumlah Order', 'data' => [45, 30, 20, 5], 'backgroundColor' => '#FF6B00'],
    ]"
    height="h-80"
/>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `labels` | `array` | `[]` | X-axis category labels |
| `datasets` | `array` | `[]` | Array of Chart.js dataset objects |
| `height` | `string` | `h-80` | Tailwind height class |

---

## x-charts.pie

Doughnut/pie chart with custom colors. Uses `Chart.js type: 'doughnut'`.

```blade
<x-charts.pie
    :labels="['Cash', 'Transfer', 'QRIS', 'Gateway']"
    :data="[60, 20, 15, 5]"
    :colors="['#FF6B00', '#000000', '#E5E5E5', '#10B981']"
    height="h-72"
/>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `labels` | `array` | `[]` | Segment labels |
| `data` | `array` | `[]` | Numeric values for each segment |
| `colors` | `array` | `['#FF6B00', '#000000', '#E5E5E5', '#10B981']` | Background colors per segment |
| `height` | `string` | `h-72` | Tailwind height class |

**Note:** This component uses a flat `data` array (not `datasets`). Colors default to the brand palette.

---

## x-charts.area

Filled line chart (area chart). Uses `Chart.js type: 'line'` with `fill: true`. Default fill color is `rgba(255, 107, 0, 0.1)` (lo at 10% opacity). Line tension is set to `0.3` for smooth curves.

```blade
<x-charts.area
    :labels="$dates"
    :datasets="[
        ['label' => 'Order', 'data' => $orderCounts, 'backgroundColor' => 'rgba(255, 107, 0, 0.1)', 'borderColor' => '#FF6B00'],
    ]"
    height="h-80"
/>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `labels` | `array` | `[]` | X-axis labels |
| `datasets` | `array` | `[]` | Array of Chart.js dataset objects; `fill` is set to `true` automatically |
| `height` | `string` | `h-80` | Tailwind height class |

---

## Chart Sizes

| Context | Recommended Height |
|---------|-------------------|
| Dashboard widget | `h-72` or `h-80` |
| Full report page | `h-96` |
| Modal / preview | `h-48` |

---

## Icons

### Approach: Iconify CDN + `<x-icon>` Component

Icons are rendered via the **Iconify** JavaScript library (loaded via CDN in the base layout). The `<x-icon>` component wraps Iconify's `<span class="iconify">` pattern.

**CDN (in layout):**
```html
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@latest/dist/iconify-icon.min.js"></script>
```

### x-icon Component

```blade
{{-- resources/views/components/icon.blade.php --}}
@props(['name' => ''])
<span {{ $attributes->merge(['class' => 'iconify']) }} data-icon="lucide:{{ $name }}"></span>
```

**Usage:**

```blade
{{-- Basic icon — defaults to lucide set --}}
<x-icon name="shirt" class="text-lg" />
{{-- Renders: <span class="iconify text-lg" data-icon="lucide:shirt"></span> --}}

<x-icon name="message-circle" class="text-xl text-lo" />
<x-icon name="check-circle" class="text-lg text-success" />
<x-icon name="x" class="text-sm" />

{{-- Inline with text --}}
<span class="flex items-center gap-2">
    <x-icon name="user" class="text-sm" />
    <span>Profile</span>
</span>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | `string` | `''` | Lucide icon name (e.g. `shirt`, `check-circle`) |

**Attributes** are merged onto the `<span>`, so you can pass `class`, `data-icon` (override), or any other HTML attribute.

### Alternative: Direct Iconify Usage

```blade
<span class="iconify text-lg" data-icon="lucide:shirt"></span>
<span class="iconify text-xl text-lo" data-icon="lucide:message-circle"></span>
```

Both approaches produce the same output. The `<x-icon>` component is a convenience wrapper.

---

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
