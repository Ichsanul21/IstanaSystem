@props(['title' => null])

<!DOCTYPE html>
<html lang="id" x-data x-bind:class="{ 'dark': $store.theme.dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Istana Laundry') }}@isset($title) - {{ $title }}@endisset</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 pt-9">

    {{-- Status Bar --}}
    <div class="fixed top-0 inset-x-0 h-9 bg-dark z-50 flex items-center justify-between px-4 lg:px-6">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-lo animate-pulse"></span>
            <span class="text-[11px] text-white/60 font-medium tracking-wide">Supported by Alenkosa</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-[11px] text-white/90 font-bold tracking-wider">ISTANA LAUNDRY</span>
            <span class="text-[11px] text-white/40 font-mono" x-data="{ time: '' }" x-init="time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }); setInterval(() => time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }), 1000)" x-text="time"></span>
        </div>
    </div>

    <div x-show="$store.sidebar.mobileOpen"
         x-on:click="$store.sidebar.closeMobile()"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-30 bg-dark/50 lg:hidden"
         style="display: none;">
    </div>

    @php
        $navGroups = [
            'main' => [
                'label' => null,
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home', 'can' => null],
                    ['label' => 'POS', 'route' => 'admin.pos.index', 'icon' => 'shopping-bag', 'can' => 'pos-access'],
                    ['label' => 'Orders', 'route' => 'admin.orders.index', 'icon' => 'clipboard-list', 'can' => null],
                    ['label' => 'Workshop / Produksi', 'icon' => 'wrench', 'can' => null, 'children' => [
                        ['label' => 'Queue', 'route' => 'admin.workshop.index', 'can' => null],
                        ['label' => 'Scan QR', 'route' => 'admin.scanner.index', 'can' => 'workshop.scan'],
                    ]],
                    ['label' => 'Customers / CRM', 'icon' => 'users', 'can' => null, 'children' => [
                        ['label' => 'Daftar Pelanggan', 'route' => 'admin.customers.index', 'can' => null],
                        ['label' => 'Membership Tiers', 'route' => 'admin.membership-tiers.index', 'can' => 'manage_tiers'],
                    ]],
                    ['label' => 'Promotions', 'route' => 'admin.promotions.index', 'icon' => 'tag', 'can' => null],
                    ['label' => 'Inventory', 'icon' => 'cube', 'can' => null, 'children' => [
                        ['label' => 'Items', 'route' => 'admin.inventory.index', 'can' => null],
                        ['label' => 'Stock', 'route' => 'admin.inventory.stock.index', 'can' => null],
                    ]],
                    ['label' => 'Finance', 'icon' => 'currency-dollar', 'can' => 'finance-access', 'children' => [
                        ['label' => 'Dashboard', 'route' => 'admin.finance.index', 'can' => 'finance.read'],
                        ['label' => 'Journal', 'route' => 'admin.finance.journal', 'can' => 'finance.read'],
                        ['label' => 'Chart of Accounts', 'route' => 'admin.finance.accounts', 'can' => 'finance.read'],
                    ]],
                    ['label' => 'Reports', 'route' => 'admin.reports.revenue', 'icon' => 'chart-bar', 'can' => 'reports-access'],
                    ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'cog', 'can' => null],
                    ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'user-group', 'can' => 'admin-access'],
                    ['label' => 'Audit Log', 'route' => 'admin.audit.index', 'icon' => 'document-text', 'can' => 'audit-log-access'],
                ],
            ],
        ];
        $branches = \App\Models\Branch::where('is_active', true)->get();
        $currentBranchId = currentBranchId();
    @endphp

    <aside class="fixed top-9 inset-y-9 left-0 z-40 hidden lg:flex flex-col bg-white dark:bg-dark-900 border-r border-lo-gray dark:border-dark-700 transition-all duration-300"
           :class="$store.sidebar.collapsed ? 'w-[90px]' : 'w-[290px]'">
        <div class="flex h-16 items-center gap-3 border-b border-lo-gray dark:border-dark-700 px-5" :class="$store.sidebar.collapsed ? 'justify-center px-0' : ''">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-lo text-white text-sm font-bold shrink-0">IL</div>
            <span x-show="!$store.sidebar.collapsed" class="text-lg font-black tracking-tighter text-dark dark:text-white truncate">Istana Laundry</span>
        </div>
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            @foreach($navGroups['main']['items'] as $item)
                @php
                    $hasChildren = !empty($item['children']);
                    $isActive = !$hasChildren && isset($item['route']) && (request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])));
                    $childActive = $hasChildren && collect($item['children'])->contains(fn($c) => request()->routeIs($c['route']) || request()->routeIs(str_replace('.index', '.*', $c['route'])));
                @endphp
                @if($item['can'] ?? false)
                    @can($item['can'])
                        @if($hasChildren)
                            <div x-data="{ open: $store.sidebar.isSubmenuOpen('{{ $item['label'] }}') }">
                                <button x-on:click="$store.sidebar.toggleSubmenu('{{ $item['label'] }}')"
                                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ $childActive ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}"
                                        :class="$store.sidebar.collapsed ? 'justify-center px-2' : ''">
                                    @include('layouts.partials.nav-icon', ['icon' => $item['icon']])
                                    <span x-show="!$store.sidebar.collapsed" class="flex-1 truncate text-left">{{ $item['label'] }}</span>
                                    <svg x-show="!$store.sidebar.collapsed" class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                </button>
                                <div x-show="open && !$store.sidebar.collapsed" x-collapse.duration.200ms>
                                    <div class="mt-1 ml-8 space-y-1">
                                        @foreach($item['children'] as $child)
                                            @if($child['can'] ?? false)
                                                @can($child['can'])
                                                    <a href="{{ route($child['route']) }}"
                                                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}">
                                                        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                                                        <span class="truncate">{{ $child['label'] }}</span>
                                                    </a>
                                                @endcan
                                            @else
                                                <a href="{{ route($child['route']) }}"
                                                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                                                    <span class="truncate">{{ $child['label'] }}</span>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ $isActive ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}"
                               :class="$store.sidebar.collapsed ? 'justify-center px-2' : ''">
                                @include('layouts.partials.nav-icon', ['icon' => $item['icon']])
                                <span x-show="!$store.sidebar.collapsed" class="truncate">{{ $item['label'] }}</span>
                            </a>
                        @endif
                    @endcan
                @else
                    @if($hasChildren)
                        <div x-data="{ open: $store.sidebar.isSubmenuOpen('{{ $item['label'] }}') }">
                            <button x-on:click="$store.sidebar.toggleSubmenu('{{ $item['label'] }}')"
                                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ $childActive ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}"
                                    :class="$store.sidebar.collapsed ? 'justify-center px-2' : ''">
                                @include('layouts.partials.nav-icon', ['icon' => $item['icon']])
                                <span x-show="!$store.sidebar.collapsed" class="flex-1 truncate text-left">{{ $item['label'] }}</span>
                                <svg x-show="!$store.sidebar.collapsed" class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <div x-show="open && !$store.sidebar.collapsed" x-collapse.duration.200ms>
                                <div class="mt-1 ml-8 space-y-1">
                                    @foreach($item['children'] as $child)
                                        @if($child['can'] ?? false)
                                            @can($child['can'])
                                                <a href="{{ route($child['route']) }}"
                                                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                                                    <span class="truncate">{{ $child['label'] }}</span>
                                                </a>
                                            @endcan
                                        @else
                                            <a href="{{ route($child['route']) }}"
                                               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                                                <span class="truncate">{{ $child['label'] }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ $isActive ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}"
                           :class="$store.sidebar.collapsed ? 'justify-center px-2' : ''">
                            @include('layouts.partials.nav-icon', ['icon' => $item['icon']])
                            <span x-show="!$store.sidebar.collapsed" class="truncate">{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endif
            @endforeach
        </nav>
        <div class="border-t border-lo-gray dark:border-dark-700 p-4">
            <button x-on:click="$store.sidebar.toggle()"
                    class="flex w-full items-center justify-center rounded-lg px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 dark:hover:bg-dark-800 transition-colors">
                <x-icon name="menu" class="text-lg" />
                <span x-show="!$store.sidebar.collapsed" class="ml-3 text-sm">Collapse</span>
            </button>
        </div>
    </aside>

    <aside class="fixed top-9 inset-y-9 left-0 z-40 flex lg:hidden flex-col bg-white dark:bg-dark-900 border-r border-lo-gray dark:border-dark-700 transition-all duration-300 w-[290px]"
           x-show="$store.sidebar.mobileOpen"
           x-transition:enter="transform transition ease-in-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transform transition ease-in-out duration-300"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           style="display: none;">
        <div class="flex h-16 items-center justify-between border-b border-lo-gray dark:border-dark-700 px-5">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-lo text-white text-sm font-bold">IL</div>
                <span class="text-lg font-black tracking-tighter text-dark dark:text-white">Istana Laundry</span>
            </div>
            <button x-on:click="$store.sidebar.closeMobile()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <x-icon name="x" class="text-lg" />
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            @foreach($navGroups['main']['items'] as $item)
                @php
                    $hasChildren = !empty($item['children']);
                    $isActive = !$hasChildren && isset($item['route']) && (request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])));
                    $childActive = $hasChildren && collect($item['children'])->contains(fn($c) => request()->routeIs($c['route']) || request()->routeIs(str_replace('.index', '.*', $c['route'])));
                @endphp
                @if($item['can'] ?? false)
                    @can($item['can'])
                        @if($hasChildren)
                            <div x-data="{ open: true }">
                                <button x-on:click="open = !open"
                                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ $childActive ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}">
                                    @include('layouts.partials.nav-icon', ['icon' => $item['icon']])
                                    <span class="flex-1 truncate text-left">{{ $item['label'] }}</span>
                                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                </button>
                                <div x-show="open" x-collapse.duration.200ms>
                                    <div class="mt-1 ml-8 space-y-1">
                                        @foreach($item['children'] as $child)
                                            @if($child['can'] ?? false)
                                                @can($child['can'])
                                                    <a href="{{ route($child['route']) }}"
                                                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}">
                                                        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                                                        <span class="truncate">{{ $child['label'] }}</span>
                                                    </a>
                                                @endcan
                                            @else
                                                <a href="{{ route($child['route']) }}"
                                                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                                                    <span class="truncate">{{ $child['label'] }}</span>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ $isActive ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}">
                                @include('layouts.partials.nav-icon', ['icon' => $item['icon']])
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        @endif
                    @endcan
                @else
                    @if($hasChildren)
                        <div x-data="{ open: true }">
                            <button x-on:click="open = !open"
                                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ $childActive ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}">
                                @include('layouts.partials.nav-icon', ['icon' => $item['icon']])
                                <span class="flex-1 truncate text-left">{{ $item['label'] }}</span>
                                <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <div x-show="open" x-collapse.duration.200ms>
                                <div class="mt-1 ml-8 space-y-1">
                                    @foreach($item['children'] as $child)
                                        @if($child['can'] ?? false)
                                            @can($child['can'])
                                                <a href="{{ route($child['route']) }}"
                                                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                                                    <span class="truncate">{{ $child['label'] }}</span>
                                                </a>
                                            @endcan
                                        @else
                                            <a href="{{ route($child['route']) }}"
                                               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs($child['route']) || request()->routeIs(str_replace('.index', '.*', $child['route'])) ? 'bg-lo' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                                                <span class="truncate">{{ $child['label'] }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-dark-800 {{ $isActive ? 'bg-lo-50 text-lo dark:bg-lo/10' : 'text-gray-600 dark:text-gray-400' }}">
                            @include('layouts.partials.nav-icon', ['icon' => $item['icon']])
                            <span class="truncate">{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endif
            @endforeach
        </nav>
    </aside>

    <div class="flex flex-col min-h-screen transition-all duration-300"
         :class="$store.sidebar.collapsed ? 'lg:ml-[90px]' : 'lg:ml-[290px]'">
        <header class="sticky top-9 z-20 flex h-16 items-center gap-4 border-b bg-white dark:bg-dark-900 border-lo-gray dark:border-dark-700 px-4 lg:px-6 shadow-theme-sm">
            <button x-on:click="$store.sidebar.toggleMobile()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 lg:hidden">
                <x-icon name="menu" class="text-lg" />
            </button>

            <div class="hidden xl:flex items-center flex-1 max-w-md">
                <div class="relative w-full">
                    <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm" />
                    <input type="text" placeholder="Cari menu... (Ctrl+K)"
                           x-on:focus="searchOpen = true"
                           x-on:click="searchOpen = true"
                           readonly
                           class="w-full rounded-lg border border-lo-gray dark:border-dark-700 bg-white dark:bg-dark-900 text-sm text-gray-700 dark:text-gray-300 pl-9 pr-3 py-2 cursor-pointer focus:ring-lo focus:border-lo">
                </div>
            </div>

            <div class="flex-1 xl:hidden">
                <form method="POST" action="{{ route('admin.branch.switch', '__placeholder__') }}" x-data="{ branchId: '{{ $currentBranchId }}' }" x-on:change="$el.action = $el.action.replace('__placeholder__', branchId); $el.submit()">
                    @csrf
                    <select x-model="branchId" name="branch_id" class="rounded-lg border border-lo-gray dark:border-dark-700 bg-white dark:bg-dark-900 text-sm text-gray-700 dark:text-gray-300 px-3 py-1.5 focus:ring-lo focus:border-lo">
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($branch->id === $currentBranchId)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="flex items-center gap-2">
                <button x-on:click="searchOpen = true"
                        class="xl:hidden rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-dark-800 transition-colors">
                    <x-icon name="search" class="text-lg" />
                </button>

                <div class="relative" x-data="notifBell()">
                    <button x-on:click="toggle()"
                            class="relative rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-dark-800 transition-colors">
                        <x-icon name="bell" class="text-lg" />
                        <span x-show="unread > 0"
                              x-text="unread"
                              class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-error rounded-full"></span>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-80 rounded-lg bg-white dark:bg-dark-900 shadow-theme-lg border border-lo-gray dark:border-dark-700"
                         style="display: none;">
                        <div class="px-4 py-3 border-b border-lo-gray dark:border-dark-700">
                            <h3 class="text-sm font-bold text-dark dark:text-white">Notifikasi</h3>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <template x-for="notif in notifications" :key="notif.id">
                                <div class="px-4 py-3 border-b border-lo-gray/50 dark:border-dark-700/50 hover:bg-gray-50 dark:hover:bg-dark-800 transition-colors">
                                    <p class="text-sm text-gray-700 dark:text-gray-300" x-text="notif.description"></p>
                                    <p class="text-xs text-gray-400 mt-1" x-text="notif.time"></p>
                                </div>
                            </template>
                            <div x-show="notifications.length === 0" class="px-4 py-6 text-center text-sm text-gray-400">
                                Tidak ada notifikasi
                            </div>
                        </div>
                    </div>
                </div>

                <button x-on:click="$store.theme.toggle()"
                        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-dark-800 transition-colors">
                    <x-icon x-show="!$store.theme.dark" name="moon" class="text-lg" />
                    <x-icon x-show="$store.theme.dark" name="sun" class="text-lg" style="display: none;" />
                </button>

                <div class="relative" x-data="{ open: false }" x-on:click.outside="open = false">
                    <button x-on:click="open = !open"
                            class="flex items-center gap-2 rounded-lg p-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-800 transition-colors">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-lo text-white text-sm font-semibold">{{ substr(Auth::user()?->name ?? 'U', 0, 1) }}</div>
                        <span class="hidden md:block text-sm font-medium">{{ Auth::user()?->name ?? 'User' }}</span>
                        <x-icon name="chevron-down" class="text-sm" />
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-48 rounded-lg bg-white dark:bg-dark-900 shadow-theme-lg border border-lo-gray dark:border-dark-700"
                         style="display: none;">
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-800">{{ __('Profile') }}</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-800">{{ __('Log Out') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @isset($header)
            <div class="bg-white dark:bg-dark-900 border-b border-lo-gray dark:border-dark-700">
                <div class="px-4 lg:px-8 py-4">
                    {{ $header }}
                </div>
            </div>
        @endisset

        <div x-data="notification" data-message="{{ session('success') ?: session('error') ?: '' }}" data-type="{{ session('success') ? 'success' : (session('error') ? 'error' : '') }}" x-show="visible" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0" class="fixed top-14 right-4 z-50 max-w-sm w-full pointer-events-auto" style="display: none;">
            <div class="rounded-lg border shadow-lg p-4 flex items-start gap-3" x-bind:class="type === 'success' ? 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200'">
                <x-icon x-bind:class="type === 'success' ? 'text-green-500 text-lg' : 'text-red-500 text-lg'" x-bind:name="type === 'success' ? 'check-circle' : 'x-circle'" />
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium" x-text="message"></p>
                </div>
                <button x-on:click="visible = false" class="shrink-0 rounded-lg p-1 opacity-70 hover:opacity-100 transition-opacity">
                    <x-icon name="x" class="text-sm" />
                </button>
            </div>
        </div>

        <main class="flex-1 p-4 md:p-6 mx-auto w-full max-w-(--breakpoint-2xl)">
            {{ $slot }}
        </main>

        <footer class="border-t border-lo-gray dark:border-dark-700 bg-white dark:bg-dark-900 px-6 py-4 text-center text-sm text-black/40 dark:text-white/40">
            &copy; {{ date('Y') }} Istana Laundry. All rights reserved.
        </footer>
    </div>

    {{-- Search Overlay --}}
    <div x-data="searchPalette()"
         x-show="open"
         x-on:keydown.escape.window="close()"
         x-on:keydown.ctrl.k.window.prevent="toggle()"
         x-on:keydown.meta.k.window.prevent="toggle()"
         x-on:click.away="close()"
         class="fixed inset-0 z-[100] flex items-start justify-center pt-[15vh]"
         style="display: none;">
        <div class="fixed inset-0 bg-dark/50" x-on:click="close()"></div>
        <div class="relative w-full max-w-lg bg-white dark:bg-dark-900 rounded-xl shadow-theme-xl border border-lo-gray dark:border-dark-700 overflow-hidden">
            <div class="flex items-center gap-3 px-4 py-3 border-b border-lo-gray dark:border-dark-700">
                <x-icon name="search" class="text-gray-400 text-lg" />
                <input type="text"
                       x-ref="searchInput"
                       x-model="query"
                       x-on:keydown.escape.window="close()"
                       placeholder="Cari menu..."
                       class="flex-1 bg-transparent border-none outline-none text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-0">
                <kbd class="hidden sm:inline-flex items-center px-2 py-1 text-[11px] font-medium text-gray-400 bg-gray-100 dark:bg-dark-800 rounded">ESC</kbd>
            </div>
            <div class="max-h-80 overflow-y-auto p-2">
                <template x-if="query.length === 0">
                    <div class="px-3 py-6 text-center text-sm text-gray-400">Ketik untuk mencari menu...</div>
                </template>
                <template x-for="(item, i) in filteredItems" :key="i">
                    <a :href="item.url"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-800 transition-colors"
                       x-on:click="close()">
                        <span class="flex h-6 w-6 items-center justify-center rounded bg-gray-100 dark:bg-dark-800 text-xs font-medium text-gray-500" x-text="i + 1"></span>
                        <span x-text="item.label"></span>
                    </a>
                </template>
                <div x-show="query.length > 0 && filteredItems.length === 0"
                     class="px-3 py-6 text-center text-sm text-gray-400">
                    Tidak ditemukan
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
    function searchPalette() {
        return {
            open: false,
            query: '',
            items: [
                @foreach($navGroups['main']['items'] as $item)
                    @if(isset($item['route']))
                        { label: '{{ $item['label'] }}', url: '{{ route($item['route']) }}' },
                    @endif
                    @if(!empty($item['children']))
                        @foreach($item['children'] as $child)
                            { label: '{{ $item['label'] }} > {{ $child['label'] }}', url: '{{ route($child['route']) }}' },
                        @endforeach
                    @endif
                @endforeach
            ],
            get filteredItems() {
                if (!this.query) return [];
                const q = this.query.toLowerCase();
                return this.items.filter(i => i.label.toLowerCase().includes(q));
            },
            toggle() { this.open = !this.open; if (this.open) this.$nextTick(() => this.$refs.searchInput?.focus()); },
            close() { this.open = false; this.query = ''; },
        }
    }
    function notifBell() {
        return {
            open: false,
            unread: 0,
            notifications: [],
            init() {
                fetch('/api/v1/activity-logs?per_page=5')
                    .then(r => r.json())
                    .then(res => {
                        if (res.data) {
                            this.notifications = (res.data.data ?? res.data).map(n => ({
                                id: n.id,
                                description: n.description || n.event || 'Aktivitas',
                                time: n.created_at ? new Date(n.created_at).toLocaleDateString('id-ID') : '',
                            }));
                        }
                    }).catch(() => {});
            },
            toggle() { this.open = !this.open; if (this.open) this.unread = 0; },
        }
    }
    </script>
</body>
</html>
