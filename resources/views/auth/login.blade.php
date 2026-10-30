<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — TechStock</title>

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
    </script>

    <!-- Satoshi Font (Authentic TailAdmin Typography) -->
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-satoshi bg-whiten text-body antialiased dark:bg-boxdark-2 dark:text-bodydark flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    
    <!-- Top System Switcher Bar -->
    <div class="fixed top-4 right-4 z-50">
        <button type="button" 
                onclick="toggleTechStockTheme()" 
                class="flex h-10 w-10 items-center justify-center rounded-full border border-stroke bg-white text-body shadow-default hover:text-primary dark:border-strokedark dark:bg-boxdark dark:text-bodydark dark:hover:text-white transition"
                title="Alternar modo oscuro">
            <!-- Sun icon in dark mode -->
            <svg class="w-5 h-5 hidden dark:block fill-current" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
            </svg>
            <!-- Moon icon in light mode -->
            <svg class="w-5 h-5 block dark:hidden fill-current" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
            </svg>
        </button>
    </div>

    <!-- Centered Card Container -->
    <div class="mx-auto w-full max-w-5xl px-4 sm:px-6">
        <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex flex-wrap items-center">
                
                <!-- Left Side: TechStock Hero Branding & Demo Info -->
                <div class="hidden w-full xl:block xl:w-1/2">
                    <div class="py-17.5 px-12 text-center">
                        <div class="mb-5.5 inline-flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-white font-bold text-2xl shadow-default">
                                TS
                            </div>
                            <span class="text-3xl font-extrabold text-black dark:text-white">TechStock</span>
                        </div>

                        <p class="font-medium text-body dark:text-bodydark mb-8 max-w-sm mx-auto">
                            Sistema Integral de Gestión de Inventarios, existencias en tiempo real, trazabilidad de movimientos y control de alertas.
                        </p>

                        <!-- Demo credentials badge in TailAdmin style -->
                        <div class="rounded-sm border border-stroke bg-gray-2 p-5 text-left dark:border-strokedark dark:bg-meta-4 max-w-sm mx-auto">
                            <div class="flex items-center gap-2 mb-2 text-xs font-bold uppercase tracking-wider text-primary">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span>Credenciales del Sistema</span>
                            </div>
                            <div class="space-y-1 text-sm font-medium text-black dark:text-white">
                                <div class="flex justify-between">
                                    <span class="text-body dark:text-bodydark">Usuario:</span>
                                    <code class="font-mono bg-white dark:bg-boxdark px-2 py-0.5 rounded text-primary font-bold">admin</code>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-body dark:text-bodydark">Contraseña:</span>
                                    <code class="font-mono bg-white dark:bg-boxdark px-2 py-0.5 rounded text-primary font-bold">1234</code>
                                </div>
                            </div>
                        </div>

                        <!-- System feature pill list -->
                        <div class="mt-8 flex flex-wrap justify-center gap-2 text-xs font-medium text-body dark:text-bodydark">
                            <span class="rounded bg-gray px-3 py-1 dark:bg-meta-4">Importación CSV</span>
                            <span class="rounded bg-gray px-3 py-1 dark:bg-meta-4">Control de Stock Mínimo</span>
                            <span class="rounded bg-gray px-3 py-1 dark:bg-meta-4">Reportes de Valuación</span>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Sign In Form -->
                <div class="w-full border-stroke dark:border-strokedark xl:w-1/2 xl:border-l-2">
                    <div class="w-full p-6 sm:p-12.5 xl:p-15">
                        <span class="mb-1.5 block font-medium text-primary">Panel de Control</span>
                        <h2 class="mb-8 text-2xl font-bold text-black dark:text-white sm:text-title-xl">
                            Iniciar Sesión en TechStock
                        </h2>

                        <!-- Status / Error Notifications -->
                        @if (session('status'))
                            <div class="mb-5 flex items-center gap-2 rounded-sm border border-primary/30 bg-primary/10 p-4 text-sm font-medium text-primary">
                                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-5 flex items-center gap-2 rounded-sm border border-danger/30 bg-danger/10 p-4 text-sm font-medium text-danger">
                                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-5 rounded-sm border border-danger/30 bg-danger/10 p-4 text-sm font-medium text-danger">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ Route::has('login.post') ? route('login.post') : route('login') }}">
                            @csrf

                            <!-- Username Input -->
                            <div class="mb-4">
                                <label for="username" class="mb-2.5 block font-medium text-black dark:text-white">
                                    Usuario
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="username" 
                                           name="username" 
                                           value="{{ old('username', 'admin') }}" 
                                           required 
                                           autofocus
                                           placeholder="Ingresa tu usuario"
                                           class="w-full rounded-lg border border-stroke bg-transparent py-4 pl-6 pr-10 text-black outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                                    <span class="absolute right-4 top-4 text-body">
                                        <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <!-- Password Input -->
                            <div class="mb-6">
                                <label for="password" class="mb-2.5 block font-medium text-black dark:text-white">
                                    Contraseña
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           autocomplete="current-password"
                                           placeholder="••••••••"
                                           class="w-full rounded-lg border border-stroke bg-transparent py-4 pl-6 pr-10 text-black outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                                    <span class="absolute right-4 top-4 text-body">
                                        <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="mb-5">
                                <button type="submit" 
                                        class="w-full cursor-pointer rounded-lg border border-primary bg-primary p-4 font-medium text-white transition hover:bg-opacity-90 shadow-default">
                                    Acceder al Sistema
                                </button>
                            </div>

                            <!-- Mobile demo hint for small screens -->
                            <div class="block xl:hidden mt-6 text-center text-xs text-body dark:text-bodydark">
                                Acceso de prueba: <span class="font-bold text-black dark:text-white">admin</span> / <span class="font-bold text-black dark:text-white">1234</span>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
