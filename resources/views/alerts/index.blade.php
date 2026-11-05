@extends('layouts.app')

@section('content')
@php
    $activeTab = request('tab', 'articulos');
@endphp

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
            <a href="{{ route('movements.index', ['tab' => 'formulario', 'tipo' => 'entrada']) }}" 
               class="inline-flex items-center justify-center rounded-md bg-primary py-2.5 px-5 text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition">
                Registrar entrada general →
            </a>
        </div>
    </div>

    <!-- Horizontal Navigation Menu (Tabs) -->
    <div class="rounded-sm border border-stroke bg-white px-4 pt-3 shadow-default dark:border-strokedark dark:bg-boxdark">
        <nav class="flex flex-wrap items-center gap-2 sm:gap-6 border-b border-stroke dark:border-strokedark -mb-px overflow-x-auto" aria-label="Secciones de alertas">
            <!-- Tab 1: Artículos con Déficit -->
            <button type="button"
                    id="tab-btn-articulos"
                    onclick="switchAlertTab('articulos')"
                    class="group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'articulos' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>Artículos con Déficit</span>
                <span class="ml-1 rounded-full bg-meta-1/10 px-2 py-0.5 text-xs font-bold text-meta-1">
                    {{ $totalAlerts ?? count($lowStockProducts ?? []) }}
                </span>
            </button>

            <!-- Tab 2: Indicadores de Criticidad -->
            <button type="button"
                    id="tab-btn-indicadores"
                    onclick="switchAlertTab('indicadores')"
                    class="group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'indicadores' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                </svg>
                <span>Resumen e Indicadores KPI</span>
            </button>
        </nav>
    </div>

    <!-- ==================== SECCIÓN 1: TABLA DE ARTÍCULOS CON DÉFICIT ==================== -->
    <div id="tab-panel-articulos" class="{{ $activeTab === 'articulos' ? '' : 'hidden' }}">
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

            <div class="box-scroller max-w-full">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Producto / SKU</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Categoría</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Proveedor</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center bg-gray-2 dark:bg-meta-4">Stock actual</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center bg-gray-2 dark:bg-meta-4">Stock mínimo</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center bg-gray-2 dark:bg-meta-4">Déficit calculado</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right bg-gray-2 dark:bg-meta-4">Acción de reposición</th>
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
                                    <a href="{{ route('movements.index', ['product_id' => $product->id, 'tipo' => 'entrada', 'tab' => 'formulario']) }}" 
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

    <!-- ==================== SECCIÓN 2: INDICADORES KPI DE ALERTAS ==================== -->
    <div id="tab-panel-indicadores" class="space-y-6 {{ $activeTab === 'indicadores' ? '' : 'hidden' }}">
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

        <!-- ApexCharts inside Resumen e Indicadores KPI -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <!-- Chart 1: Stock Actual vs Stock Mínimo en Alerta (8 cols) -->
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-8">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-black dark:text-white">Brecha de Reposición por Artículo en Alerta</h3>
                        <p class="text-xs text-body dark:text-bodydark">Comparación entre Stock Actual, Stock Mínimo requerido y Déficit</p>
                    </div>
                    <span class="rounded bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">ApexCharts</span>
                </div>
                <div id="alert-chart-deficit" class="w-full min-h-[320px]"></div>
            </div>

            <!-- Chart 2: Severidad de Alertas (4 cols) -->
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-4">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Severidad de Alertas</h3>
                    <p class="text-xs text-body dark:text-bodydark">Agotados (Stock 0) frente a artículos en límite mínimo</p>
                </div>
                <div id="alert-chart-severity" class="w-full min-h-[320px] flex items-center justify-center"></div>
            </div>
        </div>
    </div>
</div>

@php
    $alertItems = ($lowStockProducts ?? collect());
    $alertNames = $alertItems->pluck('nombre')->values();
    $alertCurrent = $alertItems->pluck('stock')->map(fn($v) => (int) $v)->values();
    $alertMin = $alertItems->pluck('stock_minimo')->map(fn($v) => (int) $v)->values();
    $alertDeficitArr = $alertItems->map(fn($p) => max(0, (int) $p->stock_minimo - (int) $p->stock))->values();

    $zeroCount = (int) ($criticalCount ?? 0);
    $warningOnlyCount = max(0, (int) ($totalAlerts ?? $alertItems->count()) - $zeroCount);
@endphp

<script>
    function switchAlertTab(tabName) {
        const tabs = ['articulos', 'indicadores'];
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

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof ApexCharts === 'undefined') return;

        const elDeficit = document.querySelector('#alert-chart-deficit');
        if (elDeficit) {
            new ApexCharts(elDeficit, {
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Satoshi, sans-serif' },
                series: [
                    { name: 'Stock Actual', data: @json($alertCurrent) },
                    { name: 'Stock Mínimo', data: @json($alertMin) },
                    { name: 'Déficit Calculado', data: @json($alertDeficitArr) }
                ],
                colors: ['#F0950C', '#3C50E0', '#FB5454'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($alertNames) },
                legend: { position: 'top' }
            }).render();
        }

        const elSeverity = document.querySelector('#alert-chart-severity');
        if (elSeverity) {
            new ApexCharts(elSeverity, {
                chart: { type: 'donut', height: 320, fontFamily: 'Satoshi, sans-serif' },
                series: [{{ $zeroCount }}, {{ $warningOnlyCount }}],
                labels: ['Críticos Agotados (Stock 0)', 'En Umbral de Stock Bajo'],
                colors: ['#FB5454', '#F0950C'],
                legend: { position: 'bottom' },
                plotOptions: { pie: { donut: { size: '62%' } } }
            }).render();
        }
    });
</script>
@endsection
