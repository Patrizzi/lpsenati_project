<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="0; url={{ auth()->check() ? route('dashboard') : (Route::has('login') ? route('login') : '/login') }}">
    <title>TechStock — Redirigiendo...</title>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('color-theme') || localStorage.getItem('techstock_theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = savedTheme ? savedTheme === 'dark' : systemPrefersDark;
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>

    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-satoshi bg-whiten text-body antialiased dark:bg-boxdark-2 dark:text-bodydark flex flex-col items-center justify-center p-6">
    <div class="max-w-md w-full rounded-sm border border-stroke bg-white p-8 text-center shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-primary text-white font-bold text-2xl shadow-default mb-4">
            TS
        </div>
        <h1 class="text-xl font-bold text-black dark:text-white mb-2">TechStock</h1>
        <p class="text-sm text-body dark:text-bodydark mb-6">Redirigiendo al sistema de inventario...</p>
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" 
           class="inline-flex items-center justify-center rounded-lg border border-primary bg-primary px-6 py-2.5 font-medium text-white transition hover:bg-opacity-90">
            Continuar
        </a>
    </div>
</body>
</html>
