# Design Tokens

## Brand Colors

| Token | Value | Usage | CSS Variable |
|-------|-------|-------|-------------|
| `--color-lo` | `#FF6B00` | Primary accent, CTAs, active states, highlights | `var(--color-lo)` |
| `--color-lo-50` | `#FFF0E0` | Light tint for badges/alerts | |
| `--color-lo-100` | `#FFD6A8` | | |
| `--color-lo-200` | `#FFB870` | | |
| `--color-lo-300` | `#FF9A38` | | |
| `--color-lo-400` | `#FF8500` | | |
| `--color-lo-500` | `#FF6B00` | Base | |
| `--color-lo-600` | `#E05F00` | Hover state | |
| `--color-lo-700` | `#B84E00` | Active/pressed | |
| `--color-lo-800` | `#903D00` | | |
| `--color-lo-900` | `#682B00` | | |

## Neutral Palette

| Token | Value | Usage |
|-------|-------|-------|
| `--color-dark` | `#000000` | Pure black — headings, sidebar bg, dark sections |
| `--color-dark-900` | `#1A1A1A` | Card backgrounds in dark mode |
| `--color-dark-800` | `#2D2D2D` | Hover states in dark mode |
| `--color-dark-700` | `#404040` | Borders in dark mode |
| `--color-dark-600` | `#606060` | Muted text in dark mode |
| `--color-dark-500` | `#808080` | Icons in dark mode |
| `--color-gray-900` | `#111111` | |
| `--color-gray-800` | `#1F1F1F` | |
| `--color-gray-700` | `#333333` | |
| `--color-gray-600` | `#4D4D4D` | |
| `--color-gray-500` | `#666666` | |
| `--color-gray-400` | `#999999` | |
| `--color-gray-300` | `#B3B3B3` | |
| `--color-gray-200` | `#CCCCCC` | |
| `--color-gray-100` | `#E5E5E5` | **lo-gray** — borders, dividers |
| `--color-gray-50` | `#FAFAFA` | Section background alternate |
| `--color-white` | `#FFFFFF` | |

## Semantic Colors

| Token | Value | Usage |
|-------|-------|-------|
| `--color-success` | `#10B981` | Order finished, payment success, stock OK |
| `--color-warning` | `#F59E0B` | Pending, low stock alert |
| `--color-error` | `#EF4444` | Overdue, cancelled, insufficient stock |
| `--color-info` | `#3B82F6` | Information, tips |

## Typography

| Property | Value | Notes |
|----------|-------|-------|
| Font Family | `Inter`, sans-serif | Google Fonts |
| Weights | 300, 400, 500, 600, 700, 800, 900 | Light → Black |
| Heading Weight | 900 (`font-black`) | `tracking-tighter` |
| Body Weight | 400 (`font-normal`) | |
| Mono Weight | 500 (`font-medium`) | Status labels, codes |
| Mono Font | `ui-monospace`, SFMono, etc. | Technical metadata |

## Font Sizes (Tailwind)

| Class | Size | Usage |
|-------|------|-------|
| `text-[10px]` | 10px | Mono status labels, timestamps |
| `text-[11px]` | 11px | Section labels |  
| `text-xs` | 12px | Table cells, captions |
| `text-sm` | 14px | Body, form labels |
| `text-base` | 16px | Body text |
| `text-lg` | 18px | Lead text |
| `text-xl` | 20px | |
| `text-2xl` | 24px | |
| `text-3xl` | 30px | Section heading (mobile) |
| `text-4xl` | 36px | Section heading |
| `text-5xl` | 48px | Hero heading |

## Spacing

| Token | Value | Usage |
|-------|-------|-------|
| Page padding | `px-5 lg:px-8` | Content padding |
| Section gap | `py-20 lg:py-28` | Section vertical spacing |
| Card padding | `p-5 lg:p-8` | Default card padding |
| Grid gap | `gap-4 md:gap-6` | Grid spacing |

## Shadows (TailAdmin Tokens)

| Token | Value |
|-------|-------|
| `--shadow-theme-xs` | `0 1px 2px 0 rgb(0 0 0 / 0.05)` |
| `--shadow-theme-sm` | `0 1px 3px 0 rgb(0 0 0 / 0.08)` |
| `--shadow-theme-md` | `0 4px 12px -2px rgb(0 0 0 / 0.08)` |
| `--shadow-theme-lg` | `0 12px 30px -8px rgb(0 0 0 / 0.1)` |
| `--shadow-theme-xl` | `0 20px 40px -12px rgb(0 0 0 / 0.12)` |

## Dark Mode

Applied by adding `.dark` class to `<html>` element:

```css
/* Light mode (default) */
.bg-white { background-color: #FFFFFF; }
.text-black { color: #000000; }
.border-lo-gray { border-color: #E5E5E5; }

/* Dark mode */
.dark .bg-white { background-color: #1A1A1A; }
.dark .text-black { color: #FFFFFF; }
.dark .border-lo-gray { border-color: #404040; }
```

## Tailwind CSS v4 Configuration

```css
/* resources/css/app.css */
@import "tailwindcss";

@theme {
    --font-family-sans: "Inter", ui-sans-serif, system-ui, sans-serif;
    
    --color-lo-50: #FFF0E0;
    --color-lo-100: #FFD6A8;
    --color-lo-200: #FFB870;
    --color-lo-300: #FF9A38;
    --color-lo-400: #FF8500;
    --color-lo-500: #FF6B00;
    --color-lo-600: #E05F00;
    --color-lo-700: #B84E00;
    --color-lo-800: #903D00;
    --color-lo-900: #682B00;
    --color-lo: #FF6B00;
    
    --color-gray-50: #FAFAFA;
    --color-gray-100: #E5E5E5;
    --color-gray-200: #CCCCCC;
    --color-gray-300: #B3B3B3;
    --color-gray-400: #999999;
    --color-gray-500: #666666;
    --color-gray-600: #4D4D4D;
    --color-gray-700: #333333;
    --color-gray-800: #1F1F1F;
    --color-gray-900: #111111;
    
    --color-lo-gray: #E5E5E5;
    --color-surface: #F5F5F5;
    --color-white: #FFFFFF;
    
    --color-success: #10B981;
    --color-warning: #F59E0B;
    --color-error: #EF4444;
    --color-info: #3B82F6;
    
    --shadow-theme-xs: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-theme-sm: 0 1px 3px 0 rgb(0 0 0 / 0.08);
    --shadow-theme-md: 0 4px 12px -2px rgb(0 0 0 / 0.08);
    --shadow-theme-lg: 0 12px 30px -8px rgb(0 0 0 / 0.1);
    --shadow-theme-xl: 0 20px 40px -12px rgb(0 0 0 / 0.12);
}

@custom-variant dark (&:where(.dark, .dark *));
```

## Additional Theme Tokens (from `app.css`)

These tokens exist in the actual `@theme` block but aren't covered above:

| Token | Value | Usage |
|-------|-------|-------|
| `--color-primary` | `#FF6B00` | Alias for `--color-lo`, used in `bg-primary` / `text-primary` |
| `--color-primary-dark` | `#E55F00` | Darker shade for hover states |
| `--color-primary-light` | `#FF8533` | Lighter shade for tints |
| `--color-lo-gray` | `#E5E5E5` | Alias for `gray-100`, used as default border color throughout |
| `--color-surface` | `#F5F5F5` | Alternate section background |
| `--color-white` | `#FFFFFF` | Pure white reference |

## CSS Utility Classes

### CTA Shine Effect (`.cta-main`)

Applied automatically to `<x-ui.button variant="primary">`. Provides a hover shine sweep + scale animation.

```css
.cta-main {
    position: relative;
    overflow: hidden;
    transition: all .3s cubic-bezier(.22,1,.36,1);
}
.cta-main::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.18), transparent);
    transform: translateX(-100%);
    transition: left .5s;
}
.cta-main:hover::after {
    left: 100%;
}
.cta-main:hover {
    transform: scale(1.02);
}
.cta-main:active {
    transform: scale(.97);
}
```

**Usage:** `<x-ui.button variant="primary">Simpan</x-ui.button>` — the `cta-main` class is included in the `primary` variant styles automatically.

### Service Card Hover (`.svc-card`)

Applied via `<x-ui.card variant="hover">`. Lifts card on hover with border color change and shadow.

```css
.svc-card {
    border: 1px solid var(--color-lo-gray);
    transition: all .3s cubic-bezier(.22,1,.36,1);
}
.svc-card:hover {
    border-color: #000;
    transform: translateY(-5px);
    box-shadow: 0 20px 40px -12px rgba(0,0,0,.07);
}
.dark .svc-card:hover {
    border-color: #fff;
    box-shadow: 0 20px 40px -12px rgba(255,255,255,.07);
}
```

**Usage:** `<x-ui.card variant="hover">...</x-ui.card>` — the `svc-card` class is applied to the card wrapper.

### Table Utility Classes

| Class | Description |
|-------|-------------|
| `.table-hoverable` | Adds `hover:bg-gray-50` (dark: `hover:bg-dark-800`) to table rows |
| `.table-striped` | Alternating row backgrounds using `odd:bg-gray-50` (dark: `odd:bg-dark-800` at 50% opacity) |

These are applied automatically by `<x-ui.table>` and `<x-tables.table>` via the `hoverable` and `striped` props.

### Multi-Select Token (`.multiselect-token`)

Styled tag shown for each selected item in `<x-form.multi-select>`.

```css
.multiselect-token {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    background-color: var(--color-lo-50);
    color: var(--color-lo);
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
}
.dark .multiselect-token {
    background-color: color-mix(in srgb, var(--color-lo) 20%, transparent);
    color: var(--color-lo-200);
}
```

### Flatpickr Calendar Override

```css
.flatpickr-calendar {
    border-radius: 0.75rem !important;
    border-color: var(--color-lo-gray) !important;
    box-shadow: var(--shadow-theme-lg) !important;
}
```

## Font Family — Inter

The project uses **Inter** from Google Fonts, loaded as the sole sans-serif font.

| Weight | Tailwind Class | Usage |
|--------|---------------|-------|
| 300 | `font-light` | — |
| 400 | `font-normal` | Body text, form inputs |
| 500 | `font-medium` | Mono/status labels, button text |
| 600 | `font-semibold` | Table headers |
| 700 | `font-bold` | Section labels, tab active state |
| 800 | `font-extrabold` | — |
| 900 | `font-black` | Headings, metric values (`tracking-tighter`) |

The font is declared in the `@theme` block as:
```css
--font-family-sans: "Inter", ui-sans-serif, system-ui, sans-serif;
```

This makes all Tailwind `font-sans` utilities use Inter by default.
