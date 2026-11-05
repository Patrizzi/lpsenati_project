@extends('layouts.app')

@section('content')
@php
    $activeTab = request('tab', 'resumen');
@endphp

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
            <input type="hidden" name="tab" id="report-active-tab-input" value="{{ $activeTab }}">
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

    <!-- Horizontal Navigation Menu (Tabs) -->
    <div class="rounded-sm border border-stroke bg-white px-4 pt-3 shadow-default dark:border-strokedark dark:bg-boxdark">
        <nav class="flex flex-wrap items-center gap-2 sm:gap-6 border-b border-stroke dark:border-strokedark -mb-px overflow-x-auto" aria-label="Secciones de reportes">
            <!-- Tab 1: Resumen Ejecutivo (KPIs) -->
            <button type="button"
                    id="tab-btn-resumen"
                    onclick="switchReportTab('resumen')"
                    class="group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'resumen' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                </svg>
                <span>Resumen Ejecutivo (KPIs)</span>
            </button>

            <!-- Tab 2: Distribución por Familias -->
            <button type="button"
                    id="tab-btn-familias"
                    onclick="switchReportTab('familias')"
                    class="group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'familias' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h7a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>Distribución por Familias</span>
                <span class="ml-1 rounded-full bg-gray-2 px-2 py-0.5 text-xs font-bold text-black dark:bg-meta-4 dark:text-white">
                    {{ is_countable($categoryBreakdown ?? []) ? count($categoryBreakdown) : 0 }}
                </span>
            </button>

            <!-- Tab 3: Mayor Stock y Valoración -->
            <button type="button"
                    id="tab-btn-ranking"
                    onclick="switchReportTab('ranking')"
                    class="group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'ranking' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                </svg>
                <span>Ranking Mayor Stock y Valoración</span>
            </button>

            <!-- Tab 4: Centro de Gráficos del Sistema (Con Subsecciones) -->
            <button type="button"
                    id="tab-btn-centro-graficos"
                    onclick="switchReportTab('centro-graficos')"
                    class="group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'centro-graficos' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Gráficos del Sistema (Subsecciones)</span>
            </button>
        </nav>
    </div>

    <!-- ==================== SECCIÓN 1: RESUMEN EJECUTIVO (4 KPIs + GRÁFICOS) ==================== -->
    <div id="tab-panel-resumen" class="space-y-6 {{ $activeTab === 'resumen' ? '' : 'hidden' }}">
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

        <!-- Executive Summary Charts -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-8">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-black dark:text-white">Valorización Económica por Familia (S/)</h3>
                        <p class="text-xs text-body dark:text-bodydark">Distribución de capital invertido en almacén según categoría</p>
                    </div>
                    <span class="rounded bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">ApexCharts</span>
                </div>
                <div id="rep-resumen-val-chart" class="w-full min-h-[320px]"></div>
            </div>

            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-4">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Estado Global del Catálogo</h3>
                    <p class="text-xs text-body dark:text-bodydark">Disponibles vs. Stock Bajo y Agotados</p>
                </div>
                <div id="rep-resumen-health-chart" class="w-full min-h-[320px] flex items-center justify-center"></div>
            </div>
        </div>
    </div>

    <!-- ==================== SECCIÓN 2: DISTRIBUCIÓN POR FAMILIAS ==================== -->
    <div id="tab-panel-familias" class="space-y-6 {{ $activeTab === 'familias' ? '' : 'hidden' }}">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-start">
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-6">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-5">
                    <h3 class="text-base font-bold text-black dark:text-white">Distribución por Familia de Producto</h3>
                    <p class="text-xs text-body dark:text-bodydark mt-0.5">Participación física y valor en soles</p>
                </div>

                <div class="box-scroller space-y-5 pr-2">
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
                            <div class="relative h-2.5 w-full rounded-sm bg-stroke dark:bg-strokedark overflow-hidden">
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

            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-6">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-black dark:text-white">Proporción de Unidades por Familia</h3>
                        <p class="text-xs text-body dark:text-bodydark">Gráfico interactivo de participación de inventario</p>
                    </div>
                    <span class="rounded bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">ApexCharts</span>
                </div>
                <div id="rep-familias-donut-chart" class="w-full min-h-[340px] flex items-center justify-center"></div>
            </div>
        </div>
    </div>

    <!-- ==================== SECCIÓN 3: RANKING MAYOR STOCK Y VALORACIÓN ==================== -->
    <div id="tab-panel-ranking" class="space-y-6 {{ $activeTab === 'ranking' ? '' : 'hidden' }}">
        <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-black dark:text-white">Gráfico Comparativo: Top 10 Mayor Stock y Valor Total</h3>
                    <p class="text-xs text-body dark:text-bodydark">Visualización de existencias físicas y valorización (S/) de los productos líderes</p>
                </div>
                <span class="rounded bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">ApexCharts</span>
            </div>
            <div id="rep-ranking-bar-chart" class="w-full min-h-[300px]"></div>
        </div>

        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-4 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
            <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                <h3 class="text-xl font-semibold text-black dark:text-white">Mayor Stock y Valoración</h3>
                <p class="text-xs text-body dark:text-bodydark mt-0.5">Ranking de mayor concentración de capital en almacén</p>
            </div>

            <div class="box-scroller max-w-full">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4"># / Producto</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Categoría</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right bg-gray-2 dark:bg-meta-4">Precio unitario</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center bg-gray-2 dark:bg-meta-4">Stock</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right bg-gray-2 dark:bg-meta-4">Valor total</th>
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

    <!-- ==================== SECCIÓN 4: CENTRO DE GRÁFICOS DEL SISTEMA (CON SUBSECCIONES) ==================== -->
    <div id="tab-panel-centro-graficos" class="space-y-6 {{ $activeTab === 'centro-graficos' ? '' : 'hidden' }}">
        <!-- Horizontal Sub-menu for System Chart Subsections -->
        <div class="rounded-sm border border-stroke bg-gray-2 p-3 shadow-default dark:border-strokedark dark:bg-meta-4">
            <div class="flex flex-wrap items-center gap-2">
                <!-- Subsección 1: Panel Principal -->
                <button type="button"
                        id="subtab-btn-dashboard"
                        onclick="switchReportSubTab('dashboard')"
                        class="inline-flex items-center gap-2 rounded-md py-2 px-4 text-xs font-semibold transition cursor-pointer bg-primary text-white shadow-default">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                        <path d="M2 3a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1V3zm0 9a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm9-9a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1h-5a1 1 0 01-1-1V3zm0 9a1 1 0 011-1h5a1 1 0 011 1v5a1 1 0 01-1 1h-5a1 1 0 01-1-1v-5z"/>
                    </svg>
                    <span>1. Panel Principal</span>
                </button>

                <!-- Subsección 2: Productos -->
                <button type="button"
                        id="subtab-btn-productos"
                        onclick="switchReportSubTab('productos')"
                        class="inline-flex items-center gap-2 rounded-md py-2 px-4 text-xs font-semibold transition cursor-pointer bg-white text-body hover:text-black dark:bg-boxdark dark:text-bodydark dark:hover:text-white">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                        <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>2. Productos</span>
                </button>

                <!-- Subsección 3: Categorías -->
                <button type="button"
                        id="subtab-btn-categorias"
                        onclick="switchReportSubTab('categorias')"
                        class="inline-flex items-center gap-2 rounded-md py-2 px-4 text-xs font-semibold transition cursor-pointer bg-white text-body hover:text-black dark:bg-boxdark dark:text-bodydark dark:hover:text-white">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h7a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>3. Categorías</span>
                </button>

                <!-- Subsección 4: Movimientos -->
                <button type="button"
                        id="subtab-btn-movimientos"
                        onclick="switchReportSubTab('movimientos')"
                        class="inline-flex items-center gap-2 rounded-md py-2 px-4 text-xs font-semibold transition cursor-pointer bg-white text-body hover:text-black dark:bg-boxdark dark:text-bodydark dark:hover:text-white">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l4 4a1 1 0 01-1.414 1.414L11 6.414V13a1 1 0 11-2 0V6.414L6.707 8.707a1 1 0 01-1.414-1.414l4-4A1 1 0 0110 3zm-6 13a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>4. Movimientos</span>
                </button>

                <!-- Subsección 5: Alertas KPI -->
                <button type="button"
                        id="subtab-btn-alertas"
                        onclick="switchReportSubTab('alertas')"
                        class="inline-flex items-center gap-2 rounded-md py-2 px-4 text-xs font-semibold transition cursor-pointer bg-white text-body hover:text-black dark:bg-boxdark dark:text-bodydark dark:hover:text-white">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>5. Alertas y Criticidad</span>
                </button>
            </div>
        </div>

        <!-- SUBSECCIÓN 1: PANEL PRINCIPAL -->
        <div id="subtab-panel-dashboard" class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-8">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Panel Principal — Volumen de Existencias por Familia</h3>
                    <p class="text-xs text-body dark:text-bodydark">Unidades en stock y cantidad de productos por categoría</p>
                </div>
                <div id="sub-chart-dash-cat" class="w-full min-h-[320px]"></div>
            </div>
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-4">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Panel Principal — Salud del Stock</h3>
                    <p class="text-xs text-body dark:text-bodydark">Disponibles vs. Stock bajo y Agotados</p>
                </div>
                <div id="sub-chart-dash-health" class="w-full min-h-[320px] flex items-center justify-center"></div>
            </div>
        </div>

        <!-- SUBSECCIÓN 2: PRODUCTOS -->
        <div id="subtab-panel-productos" class="hidden grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-7">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Productos — Top 10 Stock Actual vs. Stock Mínimo</h3>
                    <p class="text-xs text-body dark:text-bodydark">Comparativa directa entre stock físico y umbral mínimo</p>
                </div>
                <div id="sub-chart-prod-stock" class="w-full min-h-[320px]"></div>
            </div>
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-5">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Productos — Participación por Proveedor</h3>
                    <p class="text-xs text-body dark:text-bodydark">Artículos registrados por proveedor homologado</p>
                </div>
                <div id="sub-chart-prod-sup" class="w-full min-h-[320px] flex items-center justify-center"></div>
            </div>
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-12">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Productos — Top 10 Mayor Valorización en Almacén (S/)</h3>
                    <p class="text-xs text-body dark:text-bodydark">Valor monetario acumulado por artículo</p>
                </div>
                <div id="sub-chart-prod-val" class="w-full min-h-[320px]"></div>
            </div>
        </div>

        <!-- SUBSECCIÓN 3: CATEGORÍAS -->
        <div id="subtab-panel-categorias" class="hidden grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-5">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Categorías — Distribución de Stock Físico</h3>
                    <p class="text-xs text-body dark:text-bodydark">Participación porcentual de unidades por familia</p>
                </div>
                <div id="sub-chart-cat-donut" class="w-full min-h-[320px] flex items-center justify-center"></div>
            </div>
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-7">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Categorías — Artículos Registrados vs. Stock Acumulado</h3>
                    <p class="text-xs text-body dark:text-bodydark">Diversidad de catálogo y existencias por familia</p>
                </div>
                <div id="sub-chart-cat-bars" class="w-full min-h-[320px]"></div>
            </div>
        </div>

        <!-- SUBSECCIÓN 4: MOVIMIENTOS -->
        <div id="subtab-panel-movimientos" class="hidden grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-8">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Movimientos — Flujo Cronológico de Entradas vs. Salidas</h3>
                    <p class="text-xs text-body dark:text-bodydark">Unidades operadas por transacción en el historial</p>
                </div>
                <div id="sub-chart-mov-timeline" class="w-full min-h-[320px]"></div>
            </div>
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-4">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Movimientos — Balance de Unidades Operadas</h3>
                    <p class="text-xs text-body dark:text-bodydark">Proporción total de ingresos (+) y egresos (-)</p>
                </div>
                <div id="sub-chart-mov-donut" class="w-full min-h-[320px] flex items-center justify-center"></div>
            </div>
        </div>

        <!-- SUBSECCIÓN 5: ALERTAS KPI -->
        <div id="subtab-panel-alertas" class="hidden grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-8">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Alertas — Brecha de Reposición por Artículo Crítico</h3>
                    <p class="text-xs text-body dark:text-bodydark">Stock Actual vs. Stock Mínimo y Déficit</p>
                </div>
                <div id="sub-chart-alert-deficit" class="w-full min-h-[320px]"></div>
            </div>
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-4">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Alertas — Severidad de Criticidad</h3>
                    <p class="text-xs text-body dark:text-bodydark">Agotados (Stock 0) frente a Stock Bajo en umbral</p>
                </div>
                <div id="sub-chart-alert-severity" class="w-full min-h-[320px] flex items-center justify-center"></div>
            </div>
        </div>
    </div>
</div>

@php
    $repCatNames = ($categoryBreakdown ?? collect())->pluck('nombre')->values();
    $repCatUnits = ($categoryBreakdown ?? collect())->map(fn($c) => (int) ($c->total_stock ?? $c->units ?? 0))->values();
    $repCatCounts = ($categoryBreakdown ?? collect())->map(fn($c) => (int) ($c->products_count ?? 0))->values();
    $repCatVals = ($categoryBreakdown ?? collect())->map(fn($c) => round((float) ($c->total_valuation ?? $c->valuation ?? 0), 2))->values();

    $repZeroCount = (int) ($criticalCount ?? 0);
    $repWarnCount = max(0, (int) ($lowStockCount ?? 0) - $repZeroCount);
    $repHealthyCount = max(0, (int) ($totalProducts ?? 0) - (int) ($lowStockCount ?? 0));

    $repTopNames = ($topStockProducts ?? collect())->pluck('nombre')->values();
    $repTopStocks = ($topStockProducts ?? collect())->pluck('stock')->map(fn($v) => (int) $v)->values();
    $repTopMins = ($topStockProducts ?? collect())->pluck('stock_minimo')->map(fn($v) => (int) $v)->values();
    $repTopVals = ($topStockProducts ?? collect())->map(fn($p) => round((float) ($p->precio * $p->stock), 2))->values();

    $repValSorted = ($allProducts ?? collect())->sortByDesc(fn($p) => $p->precio * $p->stock)->take(10);
    $repValSortedNames = $repValSorted->pluck('nombre')->values();
    $repValSortedAmounts = $repValSorted->map(fn($p) => round((float) ($p->precio * $p->stock), 2))->values();

    $repSupNames = ($suppliersBreakdown ?? collect())->pluck('nombre')->values();
    $repSupCounts = ($suppliersBreakdown ?? collect())->map(fn($s) => (int) ($s->products_count ?? 0))->values();

    $repMovList = ($allMovements ?? collect())->take(-15);
    $repMovLabels = $repMovList->map(fn($m) => ($m->fecha ? $m->fecha->format('d/m H:i') : '') . ' (' . ($m->product->sku ?? '') . ')')->values();
    $repMovIn = $repMovList->map(fn($m) => strtolower($m->tipo) === 'entrada' ? (int) $m->cantidad : 0)->values();
    $repMovOut = $repMovList->map(fn($m) => strtolower($m->tipo) === 'salida' ? (int) $m->cantidad : 0)->values();
    $repTotalIn = (int) ($allMovements ?? collect())->filter(fn($m) => strtolower($m->tipo) === 'entrada')->sum('cantidad');
    $repTotalOut = (int) ($allMovements ?? collect())->filter(fn($m) => strtolower($m->tipo) === 'salida')->sum('cantidad');

    $repAlertNames = ($alertProducts ?? collect())->pluck('nombre')->values();
    $repAlertStock = ($alertProducts ?? collect())->pluck('stock')->map(fn($v) => (int) $v)->values();
    $repAlertMin = ($alertProducts ?? collect())->pluck('stock_minimo')->map(fn($v) => (int) $v)->values();
    $repAlertDef = ($alertProducts ?? collect())->map(fn($p) => max(0, (int) $p->stock_minimo - (int) $p->stock))->values();
@endphp

<script>
    function switchReportTab(tabName) {
        const tabs = ['resumen', 'familias', 'ranking', 'centro-graficos'];
        const hiddenInput = document.getElementById('report-active-tab-input');
        if (hiddenInput) {
            hiddenInput.value = tabName;
        }
        tabs.forEach(function(t) {
            const btn = document.getElementById('tab-btn-' + t);
            const panel = document.getElementById('tab-panel-' + t);
            if (!btn || !panel) return;

            if (t === tabName) {
                panel.classList.remove('hidden');
                btn.classList.add('border-primary', 'text-primary');
                btn.classList.remove('border-transparent', 'text-body', 'dark:text-bodydark');
            } else {
                panel.classList.add('hidden');
                btn.classList.remove('border-primary', 'text-primary');
                btn.classList.add('border-transparent', 'text-body', 'dark:text-bodydark');
            }
        });
        setTimeout(function() { window.dispatchEvent(new Event('resize')); }, 50);
    }

    function switchReportSubTab(subName) {
        const subs = ['dashboard', 'productos', 'categorias', 'movimientos', 'alertas'];
        subs.forEach(function(s) {
            const btn = document.getElementById('subtab-btn-' + s);
            const panel = document.getElementById('subtab-panel-' + s);
            if (!btn || !panel) return;

            if (s === subName) {
                panel.classList.remove('hidden');
                btn.className = 'inline-flex items-center gap-2 rounded-md py-2 px-4 text-xs font-semibold transition cursor-pointer bg-primary text-white shadow-default';
            } else {
                panel.classList.add('hidden');
                btn.className = 'inline-flex items-center gap-2 rounded-md py-2 px-4 text-xs font-semibold transition cursor-pointer bg-white text-body hover:text-black dark:bg-boxdark dark:text-bodydark dark:hover:text-white';
            }
        });
        setTimeout(function() { window.dispatchEvent(new Event('resize')); }, 50);
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof ApexCharts === 'undefined') return;

        const catNames = @json($repCatNames);
        const catUnits = @json($repCatUnits);
        const catCounts = @json($repCatCounts);
        const catVals = @json($repCatVals);

        // 1. Resumen Ejecutivo Charts
        const elResVal = document.querySelector('#rep-resumen-val-chart');
        if (elResVal) {
            new ApexCharts(elResVal, {
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Satoshi, sans-serif' },
                series: [{ name: 'Valorización (S/)', data: catVals }],
                colors: ['#3C50E0'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: catNames }
            }).render();
        }

        const elResHealth = document.querySelector('#rep-resumen-health-chart');
        if (elResHealth) {
            new ApexCharts(elResHealth, {
                chart: { type: 'donut', height: 320, fontFamily: 'Satoshi, sans-serif' },
                series: [{{ $repHealthyCount }}, {{ $repWarnCount }}, {{ $repZeroCount }}],
                labels: ['Disponibles', 'Stock Bajo', 'Agotados'],
                colors: ['#10B981', '#F0950C', '#FB5454'],
                legend: { position: 'bottom' },
                plotOptions: { pie: { donut: { size: '62%' } } }
            }).render();
        }

        // 2. Familias Tab Chart
        const elFamDonut = document.querySelector('#rep-familias-donut-chart');
        if (elFamDonut) {
            new ApexCharts(elFamDonut, {
                chart: { type: 'donut', height: 340, fontFamily: 'Satoshi, sans-serif' },
                series: catUnits,
                labels: catNames,
                colors: ['#3C50E0', '#10B981', '#F0950C', '#FB5454', '#80CAEE', '#6577F3', '#8FD0EF', '#0FADCF'],
                legend: { position: 'bottom' },
                plotOptions: { pie: { donut: { size: '62%' } } }
            }).render();
        }

        // 3. Ranking Tab Chart
        const elRankBar = document.querySelector('#rep-ranking-bar-chart');
        if (elRankBar) {
            new ApexCharts(elRankBar, {
                chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Satoshi, sans-serif' },
                series: [
                    { name: 'Stock Físico (u.)', data: @json($repTopStocks) }
                ],
                colors: ['#10B981'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($repTopNames) }
            }).render();
        }

        // 4. Centro de Gráficos — Subsección 1: Panel Principal
        const elSubDashCat = document.querySelector('#sub-chart-dash-cat');
        if (elSubDashCat) {
            new ApexCharts(elSubDashCat, {
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Satoshi, sans-serif' },
                series: [
                    { name: 'Unidades en Stock', data: catUnits },
                    { name: 'Artículos Registrados', data: catCounts }
                ],
                colors: ['#3C50E0', '#10B981'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: catNames },
                legend: { position: 'top' }
            }).render();
        }

        const elSubDashHealth = document.querySelector('#sub-chart-dash-health');
        if (elSubDashHealth) {
            new ApexCharts(elSubDashHealth, {
                chart: { type: 'donut', height: 320, fontFamily: 'Satoshi, sans-serif' },
                series: [{{ $repHealthyCount }}, {{ $repWarnCount }}, {{ $repZeroCount }}],
                labels: ['Disponibles (Óptimo)', 'Stock Bajo (En Umbral)', 'Agotados (Stock 0)'],
                colors: ['#10B981', '#F0950C', '#FB5454'],
                legend: { position: 'bottom' },
                plotOptions: { pie: { donut: { size: '62%' } } }
            }).render();
        }

        // Subsección 2: Productos
        const elSubProdStock = document.querySelector('#sub-chart-prod-stock');
        if (elSubProdStock) {
            new ApexCharts(elSubProdStock, {
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Satoshi, sans-serif' },
                series: [
                    { name: 'Stock Actual', data: @json($repTopStocks) },
                    { name: 'Stock Mínimo', data: @json($repTopMins) }
                ],
                colors: ['#3C50E0', '#FB5454'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($repTopNames) },
                legend: { position: 'top' }
            }).render();
        }

        const elSubProdSup = document.querySelector('#sub-chart-prod-sup');
        if (elSubProdSup) {
            new ApexCharts(elSubProdSup, {
                chart: { type: 'donut', height: 320, fontFamily: 'Satoshi, sans-serif' },
                series: @json($repSupCounts),
                labels: @json($repSupNames),
                colors: ['#3C50E0', '#10B981', '#F0950C', '#80CAEE', '#FB5454', '#6577F3'],
                legend: { position: 'bottom' },
                plotOptions: { pie: { donut: { size: '62%' } } }
            }).render();
        }

        const elSubProdVal = document.querySelector('#sub-chart-prod-val');
        if (elSubProdVal) {
            new ApexCharts(elSubProdVal, {
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Satoshi, sans-serif' },
                series: [{ name: 'Valor en Almacén (S/)', data: @json($repValSortedAmounts) }],
                colors: ['#10B981'],
                plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '60%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($repValSortedNames) }
            }).render();
        }

        // Subsección 3: Categorías
        const elSubCatDonut = document.querySelector('#sub-chart-cat-donut');
        if (elSubCatDonut) {
            new ApexCharts(elSubCatDonut, {
                chart: { type: 'donut', height: 320, fontFamily: 'Satoshi, sans-serif' },
                series: catUnits,
                labels: catNames,
                colors: ['#3C50E0', '#10B981', '#F0950C', '#FB5454', '#80CAEE', '#6577F3', '#8FD0EF', '#0FADCF'],
                legend: { position: 'bottom' },
                plotOptions: { pie: { donut: { size: '62%' } } }
            }).render();
        }

        const elSubCatBars = document.querySelector('#sub-chart-cat-bars');
        if (elSubCatBars) {
            new ApexCharts(elSubCatBars, {
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Satoshi, sans-serif' },
                series: [
                    { name: 'Stock Acumulado (u.)', data: catUnits },
                    { name: 'N° de Productos', data: catCounts }
                ],
                colors: ['#3C50E0', '#10B981'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: catNames },
                legend: { position: 'top' }
            }).render();
        }

        // Subsección 4: Movimientos
        const elSubMovTime = document.querySelector('#sub-chart-mov-timeline');
        if (elSubMovTime) {
            new ApexCharts(elSubMovTime, {
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Satoshi, sans-serif' },
                series: [
                    { name: 'Entradas (+)', data: @json($repMovIn) },
                    { name: 'Salidas (-)', data: @json($repMovOut) }
                ],
                colors: ['#10B981', '#FB5454'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($repMovLabels) },
                legend: { position: 'top' }
            }).render();
        }

        const elSubMovDonut = document.querySelector('#sub-chart-mov-donut');
        if (elSubMovDonut) {
            new ApexCharts(elSubMovDonut, {
                chart: { type: 'donut', height: 320, fontFamily: 'Satoshi, sans-serif' },
                series: [{{ $repTotalIn }}, {{ $repTotalOut }}],
                labels: ['Total Unidades Ingresadas (+)', 'Total Unidades Egresadas (-)'],
                colors: ['#10B981', '#FB5454'],
                legend: { position: 'bottom' },
                plotOptions: { pie: { donut: { size: '62%' } } }
            }).render();
        }

        // Subsección 5: Alertas KPI
        const elSubAlertDef = document.querySelector('#sub-chart-alert-deficit');
        if (elSubAlertDef) {
            new ApexCharts(elSubAlertDef, {
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Satoshi, sans-serif' },
                series: [
                    { name: 'Stock Actual', data: @json($repAlertStock) },
                    { name: 'Stock Mínimo', data: @json($repAlertMin) },
                    { name: 'Déficit Calculado', data: @json($repAlertDef) }
                ],
                colors: ['#F0950C', '#3C50E0', '#FB5454'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($repAlertNames) },
                legend: { position: 'top' }
            }).render();
        }

        const elSubAlertSev = document.querySelector('#sub-chart-alert-severity');
        if (elSubAlertSev) {
            new ApexCharts(elSubAlertSev, {
                chart: { type: 'donut', height: 320, fontFamily: 'Satoshi, sans-serif' },
                series: [{{ $repZeroCount }}, {{ $repWarnCount }}],
                labels: ['Críticos Agotados (Stock 0)', 'En Umbral de Stock Bajo'],
                colors: ['#FB5454', '#F0950C'],
                legend: { position: 'bottom' },
                plotOptions: { pie: { donut: { size: '62%' } } }
            }).render();
        }
    });
</script>
@endsection
