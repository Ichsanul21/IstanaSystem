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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

    <div x-show="$store.sidebar.mobileOpen"
         x-on:click="$store.sidebar.closeMobile()"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
         style="display: none;">
    </div>

    @php
        $navItems = [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home', 'can' => null],
            ['label' => 'POS', 'route' => 'admin.pos.index', 'icon' => 'shopping-bag', 'can' => 'pos-access'],
            ['label' => 'Orders', 'route' => 'admin.orders.index', 'icon' => 'clipboard-list', 'can' => null],
            ['label' => 'Workshop / Produksi', 'route' => 'admin.workshop.index', 'icon' => 'wrench', 'can' => null],
            ['label' => 'Customers / CRM', 'route' => 'admin.customers.index', 'icon' => 'users', 'can' => null],
            ['label' => 'Promotions', 'route' => 'admin.promotions.index', 'icon' => 'tag', 'can' => null],
            ['label' => 'Inventory', 'route' => 'admin.inventory.index', 'icon' => 'cube', 'can' => null],
            ['label' => 'Finance', 'route' => 'admin.finance.index', 'icon' => 'currency-dollar', 'can' => null],
            ['label' => 'Reports', 'route' => 'admin.reports.revenue', 'icon' => 'chart-bar', 'can' => 'reports-access'],
            ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'cog', 'can' => null],
            ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'user-group', 'can' => 'admin-access'],
            ['label' => 'Audit Log', 'route' => 'admin.audit.index', 'icon' => 'document-text', 'can' => 'audit-log-access'],
        ];
        $branches = \App\Models\Branch::where('is_active', true)->get();
        $currentBranchId = currentBranchId();
    @endphp

    <aside class="fixed inset-y-0 left-0 z-40 hidden lg:flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300"
           :class="$store.sidebar.collapsed ? 'w-20' : 'w-64'">
        <div class="flex h-16 items-center gap-3 border-b border-gray-200 dark:border-gray-700 px-4" :class="$store.sidebar.collapsed ? 'justify-center' : ''">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-white text-sm font-bold shrink-0">IL</div>
            <span x-show="!$store.sidebar.collapsed" class="text-lg font-bold text-gray-900 dark:text-white truncate">Istana Laundry</span>
        </div>
        <nav class="flex-1 overflow-y-auto p-3 space-y-1">
            @foreach($navItems as $item)
                @if($item['can'])
                    @can($item['can'])
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs($item['route']) ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-gray-600 dark:text-gray-400' }}"
                           :class="$store.sidebar.collapsed ? 'justify-center px-2' : ''">
                            @include('layouts.partials.nav-icon', ['icon' => $item['icon']])
                            <span x-show="!$store.sidebar.collapsed" class="truncate">{{ $item['label'] }}</span>
                        </a>
                    @endcan
                @else
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs($item['route']) ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-gray-600 dark:text-gray-400' }}"
                       :class="$store.sidebar.collapsed ? 'justify-center px-2' : ''">
                        @include('layouts.partials.nav-icon', ['icon' => $item['icon']])
                        <span x-show="!$store.sidebar.collapsed" class="truncate">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
        <div class="border-t border-gray-200 dark:border-gray-700 p-3">
            <button x-on:click="$store.sidebar.toggle()"
                    class="flex w-full items-center justify-center rounded-lg px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
                <span x-show="!$store.sidebar.collapsed" class="ml-3">Collapse</span>
            </button>
        </div>
    </aside>

    <aside class="fixed inset-y-0 left-0 z-40 flex lg:hidden flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 w-64"
           x-show="$store.sidebar.mobileOpen"
           x-transition:enter="transform transition ease-in-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transform transition ease-in-out duration-300"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           style="display: none;">
        <div class="flex h-16 items-center justify-between border-b border-gray-200 dark:border-gray-700 px-4">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-white text-sm font-bold">IL</div>
                <span class="text-lg font-bold text-gray-900 dark:text-white">Istana Laundry</span>
            </div>
            <button x-on:click="$store.sidebar.closeMobile()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto p-3 space-y-1">
            @foreach($navItems as $item)
                @if($item['can'])
                    @can($item['can'])
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs($item['route']) ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-gray-600 dark:text-gray-400' }}">
                            @include('layouts.partials.nav-icon', ['icon' => $item['icon']])
                            <span class="truncate">{{ $item['label'] }}</span>
                        </a>
                    @endcan
                @else
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs($item['route']) ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'text-gray-600 dark:text-gray-400' }}">
                        @include('layouts.partials.nav-icon', ['icon' => $item['icon']])
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
    </aside>

    <div class="flex flex-col min-h-screen transition-all duration-300"
         :class="$store.sidebar.collapsed ? 'lg:ml-20' : 'lg:ml-64'">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b bg-white dark:bg-gray-800 dark:border-gray-700 px-4 lg:px-6 shadow-sm">
            <button x-on:click="$store.sidebar.toggleMobile()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 lg:hidden">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>

            <div class="flex-1">
                <form method="POST" action="{{ route('admin.branch.switch', '__placeholder__') }}" x-data="{ branchId: '{{ $currentBranchId }}' }" x-on:change="$el.action = $el.action.replace('__placeholder__', branchId); $el.submit()">
                    @csrf
                    <select x-model="branchId" name="branch_id" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-300 px-3 py-1.5 focus:ring-primary focus:border-primary">
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($branch->id === $currentBranchId)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="flex items-center gap-2">
                <button x-on:click="$store.theme.toggle()"
                        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg x-show="!$store.theme.dark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
                    </svg>
                    <svg x-show="$store.theme.dark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
                    </svg>
                </button>

                <div class="relative" x-data="{ open: false }" x-on:click.outside="open = false">
                    <button x-on:click="open = !open"
                            class="flex items-center gap-2 rounded-lg p-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white text-sm font-semibold">{{ substr(Auth::user()?->name ?? 'U', 0, 1) }}</div>
                        <span class="hidden md:block text-sm font-medium">{{ Auth::user()?->name ?? 'User' }}</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-48 rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black/5"
                         style="display: none;">
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Profile') }}</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Log Out') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @isset($header)
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4">
                    {{ $header }}
                </div>
            </div>
        @endisset

        <div x-data="notification" data-message="{{ session('success') ?: session('error') ?: '' }}" data-type="{{ session('success') ? 'success' : (session('error') ? 'error' : '') }}" x-show="visible" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0" class="fixed top-4 right-4 z-50 max-w-sm w-full pointer-events-auto" style="display: none;">
            <div class="rounded-lg border shadow-lg p-4 flex items-start gap-3" x-bind:class="type === 'success' ? 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200'">
                <svg class="h-5 w-5 mt-0.5 shrink-0" x-bind:class="type === 'success' ? 'text-green-500' : 'text-red-500'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path x-bind:d="type === 'success' ? 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z' : 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z'" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium" x-text="message"></p>
                </div>
                <button x-on:click="visible = false" class="shrink-0 rounded-lg p-1 opacity-70 hover:opacity-100 transition-opacity">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <main class="flex-1 p-4 lg:p-6">
            {{ $slot }}
        </main>

        <footer class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-6 py-4 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Istana Laundry. All rights reserved.
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
