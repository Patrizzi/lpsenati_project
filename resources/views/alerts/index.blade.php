@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md font-bold text-black dark:text-white">
                Stock bajo mínimo
            </h1>
            <p class="text-sm font-medium text-body dark:text-bodydark mt-1">
                Productos con existencias iguales o inferiores al umbral configurado. Requieren reposición inmediata.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('movements.index') }}" 
               class="inline-flex items-center justify-center rounded-md bg-primary py-2.5 px-5 text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition">
                Registrar entrada general →
            </a>
        </div>
    </div>

    <!-- 3 TailAdmin Alert KPI Cards -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6 2xl:gap-7.5">
        <!-- Card 1: Total Alertas -->
        <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-meta-1/10 text-meta-1">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-meta-1">
                        {{ $totalAlerts ?? count($lowStockProducts ?? []) }}
                    </h4>
                    <span class="text-sm font-medium text-body dark:text-bodydark">Total Productos en Alerta</span>
                </div>
                <span class="text-xs font-semibold uppercase text-meta-1">
                    Atención
                </span>
            </div>
        </div>

        <!-- Card 2: Críticos / Agotados -->
        <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-danger/10 text-danger">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-danger">
                        {{ $criticalCount ?? 0 }}
                    </h4>
                    <span class="text-sm font-medium text-body dark:text-bodydark">Completamente Agotados (Stock 0)</span>
                </div>
                <span class="text-xs font-semibold uppercase text-danger">
                    Crítico
                </span>
            </div>
        </div>

        <!-- Card 3: Déficit Total -->
        <div class="rounded-sm border border-stroke bg-white py-5 px-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-warning/10 text-warning">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 11.293a1 1 0 001.414 1.414l2.586-2.586A1 1 0 0012 9.586V7z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ $totalDeficit ?? 0 }} u.
                    </h4>
                    <span class="text-sm font-medium text-body dark:text-bodydark">Déficit Total Acumulado</span>
                </div>
                <span class="text-xs font-semibold uppercase text-warning">
                    Por Reponer
                </span>
            </div>
        </div>
    </div>

    <!-- Low Stock Products Table Card -->
    <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-stroke dark:border-strokedark mb-4 gap-2">
            <div>
                <h3 class="text-xl font-semibold text-black dark:text-white">Artículos con Déficit de Inventario</h3>
                <p class="text-xs text-body dark:text-bodydark mt-0.5">Calculado en base a stock físico real contra umbral de reposición</p>
            </div>
            <span class="text-xs font-semibold text-meta-1">
                Total: {{ count($lowStockProducts ?? []) }} artículos
            </span>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Producto / SKU</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Categoría</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Proveedor</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center">Stock actual</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center">Stock mínimo</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center">Déficit calculado</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right">Acción de reposición</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eee] dark:divide-strokedark">
                    @forelse($lowStockProducts ?? [] as $product)
                        @php
                            $isZero = $product->stock == 0;
                            $deficit = $product->deficit;
                        @endphp
                        <tr>
                            <!-- Producto / SKU -->
                            <td class="py-4 px-4">
                                <h5 class="text-sm font-medium text-black dark:text-white">{{ $product->nombre }}</h5>
                                <p class="text-xs font-mono text-body dark:text-bodydark mt-0.5">
                                    SKU: <span class="font-bold text-black dark:text-white">{{ $product->sku }}</span>
                                </p>
                            </td>

                            <!-- Categoría -->
                            <td class="py-4 px-4 text-sm text-body dark:text-bodydark">
                                {{ $product->category->nombre ?? 'N/A' }}
                            </td>

                            <!-- Proveedor -->
                            <td class="py-4 px-4 text-sm text-body dark:text-bodydark">
                                {{ $product->supplier->nombre ?? 'N/A' }}
                            </td>

                            <!-- Stock actual -->
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex rounded-full py-1 px-3 text-xs font-semibold {{ $isZero ? 'bg-danger/10 text-danger' : 'bg-warning/10 text-warning' }}">
                                    {{ $product->stock }} {{ $product->stock === 1 ? 'u.' : 'u.' }}
                                </span>
                            </td>

                            <!-- Stock mínimo -->
                            <td class="py-4 px-4 text-center text-sm font-medium text-body dark:text-bodydark">
                                {{ $product->stock_minimo }} u.
                            </td>

                            <!-- Déficit -->
                            <td class="py-4 px-4 text-center text-sm font-bold text-danger">
                                {{ $deficit > 0 ? '-' . $deficit . ' u.' : 'En umbral' }}
                            </td>

                            <!-- Acción directa -->
                            <td class="py-4 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('movements.index', ['product_id' => $product->id, 'tipo' => 'entrada']) }}" 
                                   class="inline-flex items-center justify-center rounded-md bg-primary py-1.5 px-4 text-xs font-medium text-white hover:bg-opacity-90 shadow-default transition">
                                    + Registrar entrada
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-sm text-body dark:text-bodydark">
                                Ningún producto requiere reposición en este momento. Todos los niveles de stock son saludables.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
