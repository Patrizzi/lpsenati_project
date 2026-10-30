<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'TechStock' }} — Sistema de Gestión de Inventario</title>

    <!-- Immediate Theme Application (FOUC-free) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('color-theme') || localStorage.getItem('techstock_theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = savedTheme ? savedTheme === 'dark' : systemPrefersDark;
            document.documentElement.classList.toggle('dark', isDark);
        })();

        function toggleTechStockTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
            localStorage.setItem('techstock_theme', isDark ? 'dark' : 'light');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
                sidebar.classList.toggle('translate-x-0');
            }
            if (overlay) {
                overlay.classList.toggle('hidden');
            }
        }

        function toggleUserDropdown() {
            const dropdown = document.getElementById('user-dropdown-menu');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        window.addEventListener('click', function(e) {
            const userBtn = document.getElementById('user-dropdown-btn');
            const dropdown = document.getElementById('user-dropdown-menu');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                if (userBtn && !userBtn.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            }
        });
    </script>

    <!-- Satoshi Font (Authentic TailAdmin Typography) -->
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-satoshi bg-whiten text-body antialiased dark:bg-boxdark-2 dark:text-bodydark">
    @php
        $navLowStockCount = \Illuminate\Support\Facades\Schema::hasTable('products') 
            ? \App\Models\Product::whereColumn('stock', '<=', 'stock_minimo')->count() 
            : 0;
    @endphp

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 z-999 hidden bg-black/50 lg:hidden"></div>

    <div class="flex h-screen overflow-hidden">
        <!-- ===== SIDEBAR START ===== -->
        <aside id="sidebar" class="absolute left-0 top-0 z-9999 flex h-screen w-72.5 flex-col overflow-y-hidden bg-black duration-300 ease-linear dark:bg-boxdark lg:static lg:translate-x-0 -translate-x-full">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between gap-2 px-6 py-5.5 lg:py-6.5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary text-white font-bold text-lg shadow-default">
                        TS
                    </div>
                    <div>
                        <span class="text-xl font-bold text-white tracking-wide">TechStock</span>
                        <span class="block text-[11px] font-medium uppercase tracking-wider text-bodydark2">Control de Inventario</span>
                    </div>
                </a>

                <button type="button" onclick="toggleSidebar()" class="block text-white lg:hidden hover:text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Navigation Menu -->
            <div class="flex flex-col flex-1 overflow-y-auto duration-300 ease-linear">
                <nav class="mt-5 px-4 py-4 lg:mt-6 lg:px-6">
                    <div>
                        <h3 class="mb-4 ml-4 text-xs font-semibold uppercase tracking-wider text-bodydark2">
                            MENÚ PRINCIPAL
                        </h3>

                        <ul class="mb-6 flex flex-col gap-1.5">
                            <!-- 1. Dashboard -->
                            <li>
                                <a href="{{ route('dashboard') }}" 
                                   class="group relative flex items-center gap-2.5 rounded-sm px-4 py-2 font-medium duration-300 ease-in-out {{ request()->routeIs('dashboard') ? 'bg-graydark text-white dark:bg-meta-4' : 'text-bodydark1 hover:bg-graydark hover:text-white dark:hover:bg-meta-4' }}">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1V3zm0 9a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm9-9a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1h-5a1 1 0 01-1-1V3zm0 9a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1h-5a1 1 0 01-1-1v-5z"/>
                                    </svg>
                                    <span>Panel principal</span>
                                </a>
                            </li>

                            <!-- 2. Products -->
                            <li>
                                <a href="{{ route('products.index') }}" 
                                   class="group relative flex items-center gap-2.5 rounded-sm px-4 py-2 font-medium duration-300 ease-in-out {{ request()->routeIs('products.*') ? 'bg-graydark text-white dark:bg-meta-4' : 'text-bodydark1 hover:bg-graydark hover:text-white dark:hover:bg-meta-4' }}">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                                        <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Productos</span>
                                </a>
                            </li>

                            <!-- 3. Categories -->
                            <li>
                                <a href="{{ route('categories.index') }}" 
                                   class="group relative flex items-center gap-2.5 rounded-sm px-4 py-2 font-medium duration-300 ease-in-out {{ request()->routeIs('categories.*') ? 'bg-graydark text-white dark:bg-meta-4' : 'text-bodydark1 hover:bg-graydark hover:text-white dark:hover:bg-meta-4' }}">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h7a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Categorías</span>
                                </a>
                            </li>

                            <!-- 4. Suppliers -->
                            <li>
                                <a href="{{ route('suppliers.index') }}" 
                                   class="group relative flex items-center gap-2.5 rounded-sm px-4 py-2 font-medium duration-300 ease-in-out {{ request()->routeIs('suppliers.*') ? 'bg-graydark text-white dark:bg-meta-4' : 'text-bodydark1 hover:bg-graydark hover:text-white dark:hover:bg-meta-4' }}">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4zm3 1h6v2H7V5zm6 4H7v2h6V9zm-6 4h6v2H7v-2z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Proveedores</span>
                                </a>
                            </li>

                            <!-- 5. Movements -->
                            <li>
                                <a href="{{ route('movements.index') }}" 
                                   class="group relative flex items-center gap-2.5 rounded-sm px-4 py-2 font-medium duration-300 ease-in-out {{ request()->routeIs('movements.*') ? 'bg-graydark text-white dark:bg-meta-4' : 'text-bodydark1 hover:bg-graydark hover:text-white dark:hover:bg-meta-4' }}">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l4 4a1 1 0 01-1.414 1.414L11 6.414V13a1 1 0 11-2 0V6.414L6.707 8.707a1 1 0 01-1.414-1.414l4-4A1 1 0 0110 3zm-6 13a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Movimientos</span>
                                </a>
                            </li>

                            <!-- 6. Alerts -->
                            <li>
                                <a href="{{ route('alerts.index') }}" 
                                   class="group relative flex items-center justify-between rounded-sm px-4 py-2 font-medium duration-300 ease-in-out {{ request()->routeIs('alerts.*') ? 'bg-graydark text-white dark:bg-meta-4' : 'text-bodydark1 hover:bg-graydark hover:text-white dark:hover:bg-meta-4' }}">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Alertas</span>
                                    </div>
                                    @if($navLowStockCount > 0)
                                        <span class="inline-flex items-center justify-center rounded-full bg-meta-1 px-2 py-0.5 text-xs font-semibold text-white">
                                            {{ $navLowStockCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>

                            <!-- 7. Reports -->
                            <li>
                                <a href="{{ route('reports.index') }}" 
                                   class="group relative flex items-center gap-2.5 rounded-sm px-4 py-2 font-medium duration-300 ease-in-out {{ request()->routeIs('reports.*') ? 'bg-graydark text-white dark:bg-meta-4' : 'text-bodydark1 hover:bg-graydark hover:text-white dark:hover:bg-meta-4' }}">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                    </svg>
                                    <span>Reportes</span>
                                </a>
                            </li>

                            <!-- 8. Profile -->
                            <li>
                                <a href="{{ route('profile.edit') }}" 
                                   class="group relative flex items-center gap-2.5 rounded-sm px-4 py-2 font-medium duration-300 ease-in-out {{ request()->routeIs('profile.*') ? 'bg-graydark text-white dark:bg-meta-4' : 'text-bodydark1 hover:bg-graydark hover:text-white dark:hover:bg-meta-4' }}">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Mi perfil</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>

            <!-- Sidebar Footer / Logout -->
            <div class="border-t border-strokedark p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-sm bg-graydark px-4 py-2.5 text-sm font-medium text-white transition hover:bg-opacity-90 dark:bg-meta-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </aside>
        <!-- ===== SIDEBAR END ===== -->

        <!-- ===== CONTENT AREA START ===== -->
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <!-- ===== HEADER START ===== -->
            <header class="sticky top-0 z-999 flex w-full bg-white drop-shadow-1 dark:bg-boxdark dark:drop-shadow-none">
                <div class="flex flex-grow items-center justify-between px-4 py-4 shadow-2 md:px-6 2xl:px-11">
                    <!-- Hamburger / Mobile Brand -->
                    <div class="flex items-center gap-3 lg:hidden">
                        <button type="button" onclick="toggleSidebar()" class="z-99999 block rounded-sm border border-stroke bg-white p-1.5 shadow-sm dark:border-strokedark dark:bg-boxdark">
                            <svg class="w-5 h-5 text-black dark:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                            <span class="rounded bg-primary px-2 py-0.5 text-sm font-bold text-white">TS</span>
                            <span class="text-base font-bold text-black dark:text-white">TechStock</span>
                        </a>
                    </div>

                    <!-- Breadcrumb / Status text for desktop -->
                    <div class="hidden sm:flex items-center gap-2 text-sm">
                        <span class="font-medium text-black dark:text-white">{{ $title ?? 'Centro de Control' }}</span>
                        <span class="text-bodydark2">/</span>
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-success">
                            <span class="h-2 w-2 rounded-full bg-success"></span>
                            Sistema en línea
                        </span>
                    </div>

                    <!-- Header Right Controls -->
                    <div class="flex items-center gap-3 2xsm:gap-5">
                        <ul class="flex items-center gap-2 2xsm:gap-4">
                            <!-- Live Low-Stock Quick Notification Pill -->
                            <li>
                                <a href="{{ route('alerts.index') }}" 
                                   class="relative flex h-8.5 w-8.5 items-center justify-center rounded-full border border-stroke bg-gray hover:text-primary dark:border-strokedark dark:bg-meta-4 dark:text-white"
                                   title="{{ $navLowStockCount }} alertas de stock bajo">
                                    <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                    </svg>
                                    @if($navLowStockCount > 0)
                                        <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-meta-1 text-[10px] font-bold text-white">
                                            {{ $navLowStockCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>

                            <!-- TailAdmin Dark Mode Switcher -->
                            <li>
                                <button type="button" 
                                        onclick="toggleTechStockTheme()" 
                                        aria-label="Alternar tema claro y oscuro"
                                        class="flex h-8.5 w-8.5 items-center justify-center rounded-full border border-stroke bg-gray text-body hover:text-primary dark:border-strokedark dark:bg-meta-4 dark:text-bodydark dark:hover:text-white transition">
                                    <!-- Sun icon in dark mode -->
                                    <svg class="w-4.5 h-4.5 hidden dark:block fill-current" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                                    </svg>
                                    <!-- Moon icon in light mode -->
                                    <svg class="w-4.5 h-4.5 block dark:hidden fill-current" viewBox="0 0 20 20">
                                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                                    </svg>
                                </button>
                            </li>
                        </ul>

                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            <button id="user-dropdown-btn" 
                                    type="button" 
                                    onclick="toggleUserDropdown()" 
                                    class="flex items-center gap-3">
                                <span class="hidden text-right lg:block">
                                    <span class="block text-sm font-medium text-black dark:text-white">
                                        {{ auth()->user()->name ?? 'Administrador' }}
                                    </span>
                                    <span class="block text-xs font-medium text-body">
                                        {{ auth()->user()->username ?? 'admin' }}
                                    </span>
                                </span>

                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary font-bold text-white shadow-default">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                </span>

                                <svg class="hidden fill-current sm:block w-3 h-3 text-body" viewBox="0 0 12 8">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.41 0.91a1 1 0 011.42 0L6 5.09l4.17-4.18a1 1 0 111.42 1.42l-4.88 4.88a1 1 0 01-1.42 0L0.41 2.33a1 1 0 010-1.42z"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="user-dropdown-menu" class="absolute right-0 mt-3 hidden w-62.5 flex-col rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark z-50">
                                <ul class="flex flex-col gap-2 border-b border-stroke p-4 dark:border-strokedark">
                                    <li>
                                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 text-sm font-medium text-body hover:text-primary dark:text-bodydark dark:hover:text-white">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            </svg>
                                            Mi perfil
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 text-sm font-medium text-body hover:text-primary dark:text-bodydark dark:hover:text-white">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                            </svg>
                                            Reportes
                                        </a>
                                    </li>
                                </ul>
                                <div class="p-3">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-2 rounded-sm px-3 py-2 text-sm font-medium text-danger hover:bg-danger/10 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Cerrar sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- ===== HEADER END ===== -->

            <!-- ===== MAIN CONTENT START ===== -->
            <main>
                <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 space-y-6">
                    <!-- System Flash Notifications -->
                    @if (session('success'))
                        <div class="flex items-center justify-between rounded-sm border border-success/30 bg-success/10 px-6 py-4 text-success shadow-default">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-medium">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="flex items-center justify-between rounded-sm border border-danger/30 bg-danger/10 px-6 py-4 text-danger shadow-default">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-medium">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="flex items-center justify-between rounded-sm border border-primary/30 bg-primary/10 px-6 py-4 text-primary shadow-default">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-medium">{{ session('status') }}</span>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="rounded-sm border border-danger/30 bg-danger/10 px-6 py-4 text-danger shadow-default">
                            <div class="flex items-center gap-2 mb-2 font-semibold text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span>Por favor corrige los errores señalados:</span>
                            </div>
                            <ul class="list-disc list-inside text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Yield Content -->
                    @yield('content')
                </div>
            </main>
            <!-- ===== MAIN CONTENT END ===== -->
        </div>
        <!-- ===== CONTENT AREA END ===== -->
    </div>
</body>
</html>
