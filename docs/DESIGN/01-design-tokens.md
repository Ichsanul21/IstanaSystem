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
    --font-sans: 'Inter', sans-serif;
    
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
