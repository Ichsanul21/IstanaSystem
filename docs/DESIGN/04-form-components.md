# Form Components

## Input

```blade
<x-form.input 
    name="name" 
    label="Nama Pelanggan" 
    placeholder="Masukkan nama"
    :value="old('name')"
    required
    error="$errors->first('name')"
/>
```

**Renders:**
```
┌─────────────────────────────────────┐
│ NAMA PELANGGAN  (label, uppercase tracking-wider) │
│ [___________________________]       │
│ border-lo-gray, focus:border-lo     │
│ px-4 py-3 text-sm                   │
└─────────────────────────────────────┘
```

**Variants:**
- Default: `border border-lo-gray`
- Error: `border border-error`
- Disabled: `bg-gray-50 cursor-not-allowed`
- With icon: leading icon inside input

## Select

```blade
<x-form.select 
    name="role" 
    label="Role"
    :options="[
        ['value' => 'admin', 'label' => 'Admin'],
        ['value' => 'cashier', 'label' => 'Cashier'],
    ]"
    :selected="old('role')"
/>
```

**Renders:** Custom select with chevron icon, same border styles as input.

## MultiSelect (TailAdmin style)

```blade
<div x-data="{ open: false, selected: [] }" class="relative">
    <label class="text-xs font-bold tracking-wider uppercase text-black/40 mb-1.5 block">
        Services
    </label>
    <div @click="open = !open" 
         class="border border-lo-gray px-4 py-3 text-sm cursor-pointer flex flex-wrap gap-1">
        <template x-for="item in selected" :key="item">
            <span class="inline-flex items-center gap-1 bg-lo-50 text-lo text-xs px-2 py-1">
                <span x-text="item"></span>
                <span @click.stop="selected = selected.filter(i => i !== item)" class="cursor-pointer">✕</span>
            </span>
        </template>
        <span x-show="!selected.length" class="text-black/30">Pilih...</span>
    </div>
    {{-- Dropdown options --}}
</div>
```

## Textarea

```blade
<x-form.textarea 
    name="address" 
    label="Alamat" 
    rows="2"
    placeholder="Jl. Contoh No. 123"
/>
```

## Label Pattern

```blade
<label class="text-xs font-bold tracking-wider uppercase text-black/40 mb-1.5 block">
    {{ $slot }}
</label>
```

## Date Picker

Using Flatpickr via Alpine.js:

```blade
<div x-data="{ picker: null }" x-init="picker = flatpickr($refs.input, { dateFormat: 'd/m/Y' })">
    <x-form.input x-ref="input" name="date" label="Tanggal" />
</div>
```

## Input Groups

```blade
{{-- Prepend/Append --}}
<div class="flex">
    <span class="inline-flex items-center px-4 bg-gray-100 border border-r-0 border-lo-gray text-sm">Rp</span>
    <x-form.input name="amount" class="rounded-none" />
</div>
```

## Form Layout (TailAdmin Pattern)

```blade
<form class="space-y-4">
    {{-- Single column --}}
    <div>
        <x-form.input name="name" label="Nama" />
    </div>
    
    {{-- Two columns --}}
    <div class="grid grid-cols-2 gap-4">
        <x-form.input name="phone" label="No. HP" />
        <x-form.select name="gender" label="Jenis Kelamin" :options="[...]" />
    </div>
    
    {{-- Full width --}}
    <div>
        <x-form.textarea name="address" label="Alamat" />
    </div>
    
    {{-- Actions --}}
    <div class="flex justify-end gap-3 pt-4">
        <x-ui.button variant="outline">Batal</x-ui.button>
        <x-ui.button variant="primary">Simpan</x-ui.button>
    </div>
</form>
```
