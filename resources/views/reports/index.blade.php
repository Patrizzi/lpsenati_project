@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md font-bold text-black dark:text-white">
                Reportes del inventario
            </h1>
            <p class="text-sm font-medium text-body dark:text-bodydark mt-1">
                Resumen ejecutivo de existencias, valoración monetaria en soles y distribución analítica por familias.
            </p>
        </div>

        <div>
            <!-- Export CSV Action Button -->
            <a href="{{ route('reports.export', request()->only('category_id')) }}" 
               class="inline-flex items-center gap-2 rounded-md bg-primary py-2.5 px-5 text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                <span>Exportar reporte CSV</span>
            </a>
        </div>
    </div>

    <!-- Category Filter Bar (Scope Filter) -->
    <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <label for="category_id" class="text-xs font-semibold uppercase tracking-wider text-black dark:text-white whitespace-nowrap">
                Ámbito del reporte:
            </label>
            <select id="category_id" 
                    name="category_id" 
                    onchange="this.form.submit()" 
                    class="rounded-md border border-stroke bg-transparent py-2 px-3 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                <option value="" class="dark:bg-boxdark">Consolidado global (todas las categorías)</option>
                @foreach($categories ?? [] as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }} class="dark:bg-boxdark">
                        {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>
            @if(request('category_id'))
                <a href="{{ route('reports.index') }}" 
                   class="text-xs font-medium text-body hover:text-black dark:text-bodydark dark:hover:text-white transition">
                    Restablecer ámbito
                </a>
            @endif
        </form>

        <div class="text-xs font-mono text-body dark:text-bodydark">
            Generado: {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>

    <!-- 4 TailAdmin KPI Data Stats Cards -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7.5">
        <!-- 1. Artículos auditados -->
        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4 text-primary">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                    <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ $totalProducts ?? 0 }}
                    </h4>
                    <span class="text-sm font-medium text-body dark:text-bodydark">Artículos Auditados</span>
                </div>
                <span class="text-xs font-medium text-body dark:text-bodydark">
                    Registros
                </span>
            </div>
        </div>

        <!-- 2. Unidades totales -->
        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4 text-secondary">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ $totalUnits ?? 0 }}
                    </h4>
                    <span class="text-sm font-medium text-body dark:text-bodydark">Unidades Físicas</span>
                </div>
                <span class="text-xs font-medium text-body dark:text-bodydark">
                    Volumen total
                </span>
            </div>
        </div>

        <!-- 3. Valor estimado del stock -->
        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4 text-success">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.21V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.21V5z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        S/ {{ number_format($totalValuation ?? $totalValue ?? 0, 2) }}
                    </h4>
                    <span class="text-sm font-medium text-body dark:text-bodydark">Valorización Económica</span>
                </div>
                <span class="text-xs font-semibold text-success">
                    Líquida
                </span>
            </div>
        </div>

        <!-- 4. Stock bajo mínimo -->
        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full {{ ($lowStockCount ?? 0) > 0 ? 'bg-meta-1/10 text-meta-1' : 'bg-success/10 text-success' }}">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold {{ ($lowStockCount ?? 0) > 0 ? 'text-meta-1' : 'text-success' }}">
                        {{ $lowStockCount ?? 0 }}
                    </h4>
                    <span class="text-sm font-medium text-body dark:text-bodydark">Stock Bajo Mínimo</span>
                </div>
                <a href="{{ route('alerts.index') }}" class="text-xs font-semibold text-meta-1 hover:underline">
                    Ver alertas →
                </a>
            </div>
        </div>
    </div>

    <!-- Two-Column Analytical Breakdown -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-start">
        <!-- Left: Category Breakdown (5 cols) -->
        <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-5">
            <div class="pb-3 border-b border-stroke dark:border-strokedark mb-5">
                <h3 class="text-base font-bold text-black dark:text-white">Distribución por Familia de Producto</h3>
                <p class="text-xs text-body dark:text-bodydark mt-0.5">Participación física y valor en soles</p>
            </div>

            <div class="space-y-5">
                @forelse($categoryBreakdown ?? [] as $item)
                    @php
                        $cUnits = $item->total_stock ?? $item->units ?? 0;
                        $cValuation = $item->total_valuation ?? $item->valuation ?? 0;
                        $pctUnits = ($totalUnits ?? 0) > 0 ? round(($cUnits / $totalUnits) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="font-medium text-black dark:text-white">{{ $item->nombre ?? $item->name }}</span>
                            <span class="text-xs font-bold text-black dark:text-white font-mono">
                                S/ {{ number_format($cValuation, 2) }}
                            </span>
                        </div>
                        <div class="relative h-2 w-full rounded-sm bg-stroke dark:bg-strokedark overflow-hidden">
                            <div class="absolute left-0 top-0 h-full rounded-sm bg-primary" style="width: {{ $pctUnits }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-body dark:text-bodydark mt-1">
                            <span>{{ $item->products_count ?? $item->count ?? 0 }} productos</span>
                            <span>{{ $cUnits }} unidades ({{ $pctUnits }}%)</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-body dark:text-bodydark">
                        No hay datos de distribución disponibles.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Top Stock & Valuation Products (7 cols) -->
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1 lg:col-span-7">
            <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                <h3 class="text-xl font-semibold text-black dark:text-white">Mayor Stock y Valoración</h3>
                <p class="text-xs text-body dark:text-bodydark mt-0.5">Ranking de mayor concentración de capital en almacén</p>
            </div>

            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase"># / Producto</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Categoría</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right">Precio unitario</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center">Stock</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right">Valor total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#eee] dark:divide-strokedark">
                        @forelse($topStockProducts ?? [] as $index => $prod)
                            @php
                                $lineTotal = $prod->valor_total ?? ($prod->precio * $prod->stock);
                            @endphp
                            <tr>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gray text-xs font-bold text-black dark:bg-meta-4 dark:text-white shrink-0">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <h5 class="text-sm font-medium text-black dark:text-white">{{ $prod->nombre }}</h5>
                                            <p class="text-xs font-mono text-body dark:text-bodydark">{{ $prod->sku }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-sm text-body dark:text-bodydark">
                                    {{ $prod->category->nombre ?? 'N/A' }}
                                </td>
                                <td class="py-4 px-4 text-right text-sm font-mono text-body dark:text-bodydark whitespace-nowrap">
                                    S/ {{ number_format($prod->precio, 2) }}
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-black dark:text-white text-sm">
                                    {{ $prod->stock }} u.
                                </td>
                                <td class="py-4 px-4 text-right font-bold text-black dark:text-white text-sm whitespace-nowrap">
                                    S/ {{ number_format($lineTotal, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-sm text-body dark:text-bodydark">
                                    No hay registros de productos para el ranking.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
