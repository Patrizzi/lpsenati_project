@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md font-bold text-black dark:text-white">
                Mi perfil
            </h1>
            <p class="text-sm font-medium text-body dark:text-bodydark mt-1">
                Actualiza los datos personales de tu cuenta y modifica la contraseña de acceso al sistema.
            </p>
        </div>

        <div>
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center gap-1.5 rounded-md border border-stroke bg-white py-2.5 px-4 text-sm font-medium text-body hover:border-primary hover:text-primary dark:border-strokedark dark:bg-boxdark dark:text-bodydark dark:hover:text-white shadow-default transition">
                <span>← Volver al inicio</span>
            </a>
        </div>
    </div>

    <!-- Main Two-Column Grid: Left (Forms, 7 cols), Right (Account Card, 5 cols) -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-start">
        <!-- Left: Forms for Profile Update and Password Change -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- 1. Información personal -->
            <div class="rounded-sm border border-stroke bg-white p-6.5 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-5">
                    <h3 class="text-base font-bold text-black dark:text-white">Información personal</h3>
                    <p class="text-xs text-body dark:text-bodydark mt-0.5">Identificación del operador del sistema</p>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nombre completo -->
                        <div>
                            <label for="name" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                                Nombre completo *
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', auth()->user()->name ?? '') }}" 
                                   required 
                                   class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                                Nombre de usuario *
                            </label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username', auth()->user()->username ?? '') }}" 
                                   required 
                                   class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm font-mono text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Email -->
                        <div class="sm:col-span-2">
                            <label for="email" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                                Correo electrónico *
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', auth()->user()->email ?? '') }}" 
                                   required 
                                   class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                        </div>

                        <!-- Age -->
                        <div>
                            <label for="age" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                                Edad (opcional)
                            </label>
                            <input type="number" 
                                   id="age" 
                                   name="age" 
                                   min="0" 
                                   max="120"
                                   value="{{ old('age', auth()->user()->age ?? '') }}" 
                                   placeholder="—"
                                   class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm font-mono text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" 
                                class="rounded-md bg-primary py-2.5 px-6 text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>

            <!-- 2. Cambiar contraseña -->
            <div class="rounded-sm border border-stroke bg-white p-6.5 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-5">
                    <h3 class="text-base font-bold text-black dark:text-white">Cambiar contraseña</h3>
                    <p class="text-xs text-body dark:text-bodydark mt-0.5">Ingresa tu contraseña actual y define una nueva clave</p>
                </div>

                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                            Contraseña actual *
                        </label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               required 
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                                Nueva contraseña *
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="••••••••"
                                   class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                                Confirmar nueva contraseña *
                            </label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="••••••••"
                                   class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" 
                                class="rounded-md bg-primary py-2.5 px-6 text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition">
                            Actualizar contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Ficha de Cuenta Administradora (5 cols) -->
        <div class="rounded-sm border border-stroke bg-white p-6.5 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-5">
            <div class="pb-3 border-b border-stroke dark:border-strokedark mb-5">
                <span class="text-xs font-semibold uppercase tracking-wider text-body dark:text-bodydark block mb-1">Ficha de usuario</span>
                <h3 class="text-base font-bold text-black dark:text-white">Cuenta Administradora</h3>
            </div>

            <!-- Avatar & Profile Header -->
            <div class="flex items-center gap-4 pb-5 border-b border-stroke dark:border-strokedark">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary font-bold text-white text-2xl uppercase shrink-0 shadow-default">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <h4 class="text-lg font-bold text-black dark:text-white leading-tight">
                        {{ auth()->user()->name ?? 'Administrador' }}
                    </h4>
                    <span class="inline-block text-xs font-medium text-success uppercase tracking-wider mt-0.5">
                        Administrador del Sistema
                    </span>
                    <p class="text-xs text-body dark:text-bodydark mt-0.5">
                        Cuenta autorizada
                    </p>
                </div>
            </div>

            <!-- Details List -->
            <div class="divide-y divide-stroke dark:divide-strokedark text-sm">
                <div class="py-3 flex justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-body dark:text-bodydark">Usuario</span>
                    <span class="font-mono font-bold text-black dark:text-white">{{ auth()->user()->username ?? 'admin' }}</span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-body dark:text-bodydark">Correo</span>
                    <span class="text-black dark:text-white">{{ auth()->user()->email ?? '—' }}</span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-body dark:text-bodydark">Edad</span>
                    <span class="text-black dark:text-white">{{ auth()->user()->age ? auth()->user()->age . ' años' : 'Sin especificar' }}</span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-body dark:text-bodydark">Actualizado</span>
                    <span class="text-body dark:text-bodydark">{{ auth()->user()->updated_at ? auth()->user()->updated_at->format('d/m/Y H:i') : 'Al registrar' }}</span>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-stroke dark:border-strokedark text-xs text-body dark:text-bodydark leading-relaxed">
                Esta cuenta ostenta privilegios globales para la administración del catálogo de existencias, control de proveedores y movimientos de inventario de TechStock.
            </div>
        </div>
    </div>
</div>
@endsection
