# Form Components

All form components live under the `x-form.*` namespace (10 components total: 7 base + 3 compound).

```
resources/views/components/form/
├── input.blade.php          x-form.input
├── select.blade.php         x-form.select
├── textarea.blade.php       x-form.textarea
├── label.blade.php          x-form.label
├── error.blade.php          x-form.error
├── checkbox.blade.php       x-form.checkbox
├── radio.blade.php          x-form.radio
├── input-group.blade.php    x-form.input-group
├── datepicker.blade.php     x-form.datepicker
└── multi-select.blade.php   x-form.multi-select
```

## Common Patterns

All field components (`input`, `select`, `textarea`, `input-group`) share:
- **Auto error resolution** — if `error` prop is not provided, reads from `$errors->first($name)` automatically
- **Consistent border styling** — `border-lo-gray` normal, `border-error` when error
- **Focus ring** — `focus:border-lo focus:ring-lo`
- **Dark mode** — `dark:bg-dark-900 dark:border-dark-700 dark:text-gray-100`
- **Disabled state** — `disabled:bg-gray-50 dark:disabled:bg-dark-900 disabled:cursor-not-allowed`

---

## x-form.input

Text input field with label, error handling, and help text.

```blade
<x-form.input
    name="name"
    label="Nama Pelanggan"
    placeholder="Masukkan nama"
    :value="old('name')"
    required
    error="$errors->first('name')"
    help="Nama lengkap sesuai KTP"
/>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | `string\|null` | `null` | Input `name` and `id` attribute |
| `label` | `string\|null` | `null` | Label text displayed above input |
| `error` | `string\|null` | `null` | Error message; auto-resolved from `$errors` if null |
| `help` | `string\|null` | `null` | Help text below input (hidden when error is shown) |
| `required` | `bool` | `false` | Adds `required` attribute and red `*` to label |

**Renders:**
```
NAMA PELANGGAN  (x-form.label — uppercase, tracking-wider)
[___________________________]  ← border-lo-gray, focus:border-lo, px-4 py-3 text-sm
Nama lengkap sesuai KTP        ← help text (xs, text-black/40)
```

---

## x-form.select

Native `<select>` with custom styling and chevron icon. Options passed as key-value array.

```blade
<x-form.select
    name="role"
    label="Role"
    placeholder="Pilih role..."
    :options="[
        'admin' => 'Admin',
        'cashier' => 'Cashier',
        'operator' => 'Operator',
    ]"
    :value="old('role')"
    required
/>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | `string\|null` | `null` | Select `name` and `id` attribute |
| `label` | `string\|null` | `null` | Label text |
| `error` | `string\|null` | `null` | Error message (auto-resolved) |
| `help` | `string\|null` | `null` | Help text below select |
| `required` | `bool` | `false` | Adds `required` attribute and red `*` |
| `options` | `array` | `[]` | Key-value pairs: `['value' => 'Label']` |
| `placeholder` | `string\|null` | `null` | Empty option text (rendered as `<option value="">`) |
| `value` | `string\|null` | `null` | Pre-selected value |

**Renders:** Custom-styled `<select>` with `appearance-none` and an SVG chevron icon positioned at `right-4`.

---

## x-form.textarea

Multi-line text input with auto-resizing rows.

```blade
<x-form.textarea
    name="address"
    label="Alamat"
    rows="2"
    placeholder="Jl. Contoh No. 123"
    :value="old('address')"
/>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | `string\|null` | `null` | Textarea `name` and `id` |
| `label` | `string\|null` | `null` | Label text |
| `error` | `string\|null` | `null` | Error message (auto-resolved) |
| `help` | `string\|null` | `null` | Help text |
| `required` | `bool` | `false` | Adds `required` and red `*` |
| `rows` | `int` | `3` | Number of rows (passed via attributes) |

---

## x-form.label

Reusable form label with uppercase styling and optional required indicator.

```blade
<x-form.label for="name" :required="true">Nama Pelanggan</x-form.label>
{{-- Or using value prop --}}
<x-form.label value="Email" :required="false" />
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `value` | `string\|null` | `null` | Label text (alternative to slot) |
| `required` | `bool` | `false` | Shows red `*` indicator |

**Renders:** `<label class="text-xs font-bold tracking-wider uppercase text-black/40 dark:text-white/40 mb-1.5 block">`

---

## x-form.error

Validation error message display. Used internally by `input`, `select`, `textarea`, `input-group`.

```blade
<x-form.error name="email" />
{{-- Renders error for "email" field if it exists in $errors --}}
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | `string\|null` | `null` | Field name to check (alias: `field`) |
| `field` | `string\|null` | `null` | Alias for `name` |

**Renders:** `<p class="mt-1 text-xs text-error">` — only shown when `$errors->has($fieldName)`.

---

## x-form.checkbox

Styled checkbox with label.

```blade
<x-form.checkbox name="agree" label="Saya setuju dengan syarat" :checked="false" />
<x-form.checkbox name="terms" value="accepted">
    <span class="text-sm">Saya menyetujui <a href="#" class="text-lo underline">syarat</a></span>
</x-form.checkbox>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | `string\|null` | `null` | Checkbox `name` and `id` |
| `label` | `string\|null` | `null` | Label text next to checkbox |
| `checked` | `bool` | `false` | Initial checked state |
| `value` | `string` | `'1'` | Value when checked |

**Renders:** `<label class="flex items-center gap-3 cursor-pointer group">` with `h-4 w-4 rounded border-lo-gray text-lo focus:ring-lo` checkbox.

---

## x-form.radio

Styled radio button with label.

```blade
<div class="flex gap-4">
    <x-form.radio name="payment_method" value="cash" label="Cash" :checked="true" />
    <x-form.radio name="payment_method" value="transfer" label="Transfer" />
    <x-form.radio name="payment_method" value="qris" label="QRIS" />
</div>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | `string\|null` | `null` | Radio `name` and `id` (same `name` = same group) |
| `label` | `string\|null` | `null` | Label text |
| `checked` | `bool` | `false` | Initial checked state |
| `value` | `string` | `'1'` | Radio value |

**Renders:** `<label class="flex items-center gap-3 cursor-pointer group">` with `h-4 w-4 border-lo-gray text-lo focus:ring-lo` radio input.

---

## x-form.input-group

Input with prepend/append addon elements (currency prefix, unit suffix, etc.).

```blade
{{-- Currency prefix --}}
<x-form.input-group
    name="amount"
    label="Jumlah Bayar"
    prepend="Rp"
    :value="old('amount')"
/>

{{-- Unit suffix --}}
<x-form.input-group
    name="weight"
    label="Berat"
    append="kg"
/>

{{-- Both prepend and append --}}
<x-form.input-group
    name="price"
    prepend="Rp"
    append="/kg"
/>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | `string\|null` | `null` | Input `name` and `id` |
| `label` | `string\|null` | `null` | Label text |
| `error` | `string\|null` | `null` | Error message |
| `prepend` | `string\|null` | `null` | Text prepended before input (e.g. "Rp") |
| `append` | `string\|null` | `null` | Text appended after input (e.g. "kg") |
| `required` | `bool` | `false` | Adds `required` and red `*` |

**Renders:**
```
JUMLAH BAYAR
┌──────┬──────────────────┐
│ Rp   │ [____________]   │
└──────┴──────────────────┘
 prepend   input (rounded-none)
 bg-gray-100, border border-r-0
```

---

## x-form.datepicker

Date input powered by **Flatpickr** (loaded via CDN). Wraps `x-form.input` with Alpine.js flatpickr initialization.

```blade
<x-form.datepicker
    name="due_date"
    label="Tanggal Jatuh Tempo"
    placeholder="DD/MM/YYYY"
/>

{{-- Custom date format via attributes --}}
<x-form.datepicker
    name="start_date"
    label="Tanggal Mulai"
    x-init="picker = flatpickr($refs.input, { dateFormat: 'Y-m-d', allowInput: true })"
/>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | `string\|null` | `null` | Input `name` and `id` |
| `label` | `string\|null` | `null` | Label text |
| `error` | `string\|null` | `null` | Error message |
| `required` | `bool` | `false` | Adds `required` and red `*` |

**Default config:** `dateFormat: 'd/m/Y'`, `allowInput: true` (user can type manually).

**Flatpickr CSS override** (in `app.css`):
```css
.flatpickr-calendar {
    border-radius: 0.75rem !important;
    border-color: var(--color-lo-gray) !important;
    box-shadow: var(--shadow-theme-lg) !important;
}
```

---

## x-form.multi-select

Dropdown multi-select with tag-style selected items. Uses Alpine.js for state management.

```blade
<x-form.multi-select
    name="services"
    label="Services"
    :options="[
        'cuci_kering' => 'Cuci Kering',
        'cuci_basah' => 'Cuci Basah',
        'setrika' => 'Setrika',
        'dry_clean' => 'Dry Clean',
    ]"
    :selected="['cuci_kering']"
/>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | `string\|null` | `null` | Input name (sends as `name[]` array) |
| `label` | `string\|null` | `null` | Label text |
| `options` | `array` | `[]` | Key-value pairs: `['value' => 'Label']` |
| `selected` | `array` | `[]` | Pre-selected values |

**Renders:**
```
SERVICES
┌──────────────────────────────────────┐
│ [Cuci Kering ×] [Cuci Basah ×]       │ ← selected tokens
└──────────────────────────────────────┘
┌──────────────────────────────────────┐
│ ☑ Cuci Kering                        │ ← dropdown checkboxes
│ ☑ Cuci Basah                         │
│ ☐ Setrika                            │
│ ☐ Dry Clean                          │
└──────────────────────────────────────┘
```

**Token style:** `.multiselect-token` — `bg-lo-50 text-lo text-xs` (see Design Tokens).

---

## Form Layout Pattern

```blade
<form class="space-y-4">
    {{-- Single column --}}
    <div>
        <x-form.input name="name" label="Nama" required />
    </div>

    {{-- Two columns --}}
    <div class="grid grid-cols-2 gap-4">
        <x-form.input name="phone" label="No. HP" />
        <x-form.select name="gender" label="Jenis Kelamin" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" />
    </div>

    {{-- With datepicker --}}
    <div class="grid grid-cols-2 gap-4">
        <x-form.datepicker name="start_date" label="Tanggal Mulai" />
        <x-form.datepicker name="end_date" label="Tanggal Selesai" />
    </div>

    {{-- Currency input --}}
    <div class="grid grid-cols-2 gap-4">
        <x-form.input-group name="amount" label="Harga" prepend="Rp" />
        <x-form.input-group name="weight" label="Berat" append="kg" />
    </div>

    {{-- Full width --}}
    <div>
        <x-form.textarea name="address" label="Alamat" rows="2" />
    </div>

    {{-- Checkboxes --}}
    <div>
        <x-form.checkbox name="terms" label="Setuju dengan syarat & ketentuan" />
    </div>

    {{-- Actions --}}
    <div class="flex justify-end gap-3 pt-4">
        <x-ui.button variant="outline">Batal</x-ui.button>
        <x-ui.button variant="primary">Simpan</x-ui.button>
    </div>
</form>
```
