@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Top Action & Title Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md font-bold text-black dark:text-white">
                Inventario en tiempo real
            </h1>
            <p class="text-sm font-medium text-body dark:text-bodydark mt-1">
                Auditoría centralizada de catálogo tecnológico, existencias físicas y flujo de transacciones.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('products.index', ['create' => 1]) }}" 
               class="inline-flex items-center justify-center rounded-md bg-primary py-2.5 px-5 text-center font-medium text-white hover:bg-opacity-90 shadow-default transition">
                + Nuevo producto
            </a>
            <a href="{{ route('movements.index') }}" 
               class="inline-flex items-center justify-center rounded-md border border-stroke bg-white py-2.5 px-5 text-center font-medium text-black hover:border-primary hover:text-primary dark:border-strokedark dark:bg-boxdark dark:text-white dark:hover:border-primary transition">
                Registrar stock
            </a>
        </div>
    </div>

    <!-- 4 TailAdmin KPI Data Stats Cards -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7.5">
        <!-- Card 1: Total Products -->
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
                    <span class="text-sm font-medium text-body dark:text-bodydark">Catálogo Activo</span>
                </div>
                <span class="text-xs font-medium text-body dark:text-bodydark">
                    {{ $totalUnits ?? 0 }} u. físicas
                </span>
            </div>
        </div>

        <!-- Card 2: Total Categories -->
        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4 text-secondary">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h7a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ $totalCategories ?? 0 }}
                    </h4>
                    <span class="text-sm font-medium text-body dark:text-bodydark">Familias / Categorías</span>
                </div>
                <span class="text-xs font-medium text-body dark:text-bodydark">
                    Clasificación
                </span>
            </div>
        </div>

        <!-- Card 3: Total Suppliers -->
        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4 text-meta-5">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4zm3 1h6v2H7V5zm6 4H7v2h6V9zm-6 4h6v2H7v-2z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ $totalSuppliers ?? 0 }}
                    </h4>
                    <span class="text-sm font-medium text-body dark:text-bodydark">Proveedores Homologados</span>
                </div>
                <span class="text-xs font-medium text-body dark:text-bodydark">
                    Abastecimiento
                </span>
            </div>
        </div>

        <!-- Card 4: Low Stock Products -->
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
                <span class="text-xs font-medium {{ ($lowStockCount ?? 0) > 0 ? 'text-meta-1' : 'text-success' }}">
                    {{ ($lowStockCount ?? 0) > 0 ? 'Reposición requerida' : 'En nivel óptimo' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Valorization Banner -->
    <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.21V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.21V5z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-body dark:text-bodydark">Valorización Total de Existencias</span>
                <div class="text-xl font-bold text-black dark:text-white">
                    S/ {{ number_format($totalValuation ?? $totalValue ?? 0, 2) }}
                </div>
            </div>
        </div>
        <div class="text-xs font-medium text-body dark:text-bodydark sm:text-right">
            <span>Cálculo de inventario valorizado: ∑ (Precio × Stock actual)</span>
        </div>
    </div>

    <!-- Two-Column Operational Split: Categories & Critical Attention -->
    <div class="grid grid-cols-1 gap-4 md:gap-6 xl:grid-cols-12 2xl:gap-7.5">
        <!-- Left: Category Distribution (5 cols) -->
        <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark xl:col-span-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-stroke dark:border-strokedark mb-5">
                    <div>
                        <h3 class="text-base font-bold text-black dark:text-white">Distribución por Categoría</h3>
                        <p class="text-xs text-body dark:text-bodydark">Proporción de existencias en almacén</p>
                    </div>
                    <a href="{{ route('categories.index') }}" class="text-xs font-medium text-primary hover:underline">
                        Ver todas
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($categoryDistribution ?? [] as $cat)
                        @php
                            $catStock = $cat->total_stock ?? $cat->products_sum_stock ?? 0;
                            $percent = ($totalUnits ?? 0) > 0 ? round(($catStock / $totalUnits) * 100, 1) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-black dark:text-white">{{ $cat->nombre }}</span>
                                <div class="text-xs text-body dark:text-bodydark">
                                    <span class="font-semibold text-black dark:text-white">{{ $catStock }} u.</span>
                                    <span class="ml-1">({{ $percent }}%)</span>
                                </div>
                            </div>
                            <!-- TailAdmin Progress Bar -->
                            <div class="relative h-2 w-full rounded-sm bg-stroke dark:bg-strokedark overflow-hidden">
                                <div class="absolute left-0 top-0 h-full rounded-sm bg-primary" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex justify-between items-center text-xs text-body dark:text-bodydark mt-1">
                                <span>{{ $cat->products_count ?? $cat->products->count() ?? 0 }} productos</span>
                                <span class="truncate">{{ $cat->descripcion ?? 'Familia técnica' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-sm text-body dark:text-bodydark">
                            No hay categorías registradas aún.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-5 mt-5 border-t border-stroke dark:border-strokedark flex items-center justify-between text-xs font-medium text-body dark:text-bodydark">
                <span>Total Familias: {{ count($categoryDistribution ?? []) }}</span>
                <span>Total Unidades: {{ $totalUnits ?? 0 }}</span>
            </div>
        </div>

        <!-- Right: Attention Needed (Low Stock Products) (7 cols) -->
        <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark xl:col-span-7 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-stroke dark:border-strokedark mb-5">
                    <div>
                        <h3 class="text-base font-bold text-black dark:text-white">Necesitan Atención</h3>
                        <p class="text-xs text-body dark:text-bodydark">Productos con stock bajo o agotados</p>
                    </div>
                    <a href="{{ route('alerts.index') }}" class="text-xs font-semibold text-meta-1 hover:underline">
                        Ver todas las alertas ({{ $lowStockCount ?? 0 }})
                    </a>
                </div>

                @if(!empty($lowStockProducts) && count($lowStockProducts) > 0)
                    <div class="divide-y divide-stroke dark:divide-strokedark">
                        @foreach($lowStockProducts as $item)
                            <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-sm text-black dark:text-white">{{ $item->nombre }}</span>
                                        <span class="rounded bg-gray px-2 py-0.5 text-xs font-mono font-medium text-black dark:bg-meta-4 dark:text-white">
                                            {{ $item->sku }}
                                        </span>
                                    </div>
                                    <div class="mt-1 flex items-center gap-3 text-xs text-body dark:text-bodydark">
                                        <span>Cat: {{ $item->category->nombre ?? 'N/A' }}</span>
                                        <span>·</span>
                                        <span>Prov: {{ $item->supplier->nombre ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    <div class="text-right">
                                        <span class="inline-flex rounded-full py-1 px-3 text-xs font-semibold {{ $item->stock == 0 ? 'bg-meta-1/10 text-meta-1' : 'bg-warning/10 text-warning' }}">
                                            {{ $item->stock }} / Mín {{ $item->stock_minimo }}
                                        </span>
                                        <span class="block text-[11px] text-body dark:text-bodydark mt-0.5">
                                            Déficit: {{ $item->deficit }} u.
                                        </span>
                                    </div>

                                    <a href="{{ route('movements.index', ['product_id' => $item->id, 'tipo' => 'entrada']) }}" 
                                       class="inline-flex items-center justify-center rounded bg-primary py-1.5 px-3 text-xs font-medium text-white hover:bg-opacity-90 transition">
                                        + Entrada
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-success/10 text-success">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-success">Sin alertas críticas</h4>
                        <p class="text-xs text-body dark:text-bodydark mt-1">Todos los artículos del catálogo se encuentran en o por encima del nivel mínimo.</p>
                    </div>
                @endif
            </div>

            <div class="pt-4 border-t border-stroke dark:border-strokedark text-xs text-body dark:text-bodydark">
                Criterio de alerta: Existencias actuales ≤ Stock mínimo establecido
            </div>
        </div>
    </div>

    <!-- Bottom Full-Width Table: Movimientos Recientes -->
    <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-stroke dark:border-strokedark gap-2">
            <div>
                <h3 class="text-xl font-semibold text-black dark:text-white">Movimientos Recientes</h3>
                <p class="text-xs text-body dark:text-bodydark mt-0.5">Trazabilidad de entradas y salidas de almacén</p>
            </div>
            <a href="{{ route('movements.index') }}" class="text-sm font-medium text-primary hover:underline">
                Historial completo →
            </a>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Fecha y Hora</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Producto / SKU</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Tipo</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right">Cantidad</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right">Balance</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Motivo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eee] dark:divide-strokedark">
                    @forelse($recentMovements ?? [] as $m)
                        <tr>
                            <td class="py-4 px-4 text-sm text-body dark:text-bodydark whitespace-nowrap">
                                {{ $m->fecha ? $m->fecha->format('d/m/Y H:i') : ($m->created_at ? $m->created_at->format('d/m/Y H:i') : '—') }}
                            </td>
                            <td class="py-4 px-4">
                                <h5 class="text-sm font-medium text-black dark:text-white">
                                    {{ $m->product->nombre ?? 'Producto eliminado' }}
                                </h5>
                                <p class="text-xs font-mono text-body dark:text-bodydark">
                                    {{ $m->product->sku ?? '—' }}
                                </p>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex rounded-full py-1 px-3 text-xs font-medium {{ strtolower($m->tipo) === 'entrada' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">
                                    {{ ucfirst($m->tipo) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right text-sm font-semibold {{ strtolower($m->tipo) === 'entrada' ? 'text-success' : 'text-danger' }}">
                                {{ strtolower($m->tipo) === 'entrada' ? '+' : '-' }}{{ $m->cantidad }}
                            </td>
                            <td class="py-4 px-4 text-right text-xs text-body dark:text-bodydark whitespace-nowrap">
                                {{ $m->stock_anterior }} → <span class="font-bold text-black dark:text-white">{{ $m->stock_posterior }}</span>
                            </td>
                            <td class="py-4 px-4 text-xs text-body dark:text-bodydark max-w-xs truncate">
                                {{ $m->motivo ?: 'Sin motivo especificado' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-body dark:text-bodydark">
                                No hay registros de movimientos en el inventario.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
