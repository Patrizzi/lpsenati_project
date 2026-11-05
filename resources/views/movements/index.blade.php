@extends('layouts.app')

@section('content')
@php
    $activeTab = 'historial';
    if (request()->filled('product_id') || request('tab') === 'formulario' || $errors->any()) {
        $activeTab = 'formulario';
    } elseif (request('tab') === 'graficos') {
        $activeTab = 'graficos';
    }
@endphp

<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md font-bold text-black dark:text-white">
                Movimientos de stock
            </h1>
            <p class="text-sm font-medium text-body dark:text-bodydark mt-1">
                Registra entradas y salidas con trazabilidad atómica y auditoría continua de existencias.
            </p>
        </div>

        <div class="inline-flex items-center gap-2 rounded-md border border-stroke bg-white px-4 py-2 text-xs font-medium text-black dark:border-strokedark dark:bg-boxdark dark:text-white shadow-default">
            <span>Total Movimientos:</span>
            <span class="font-bold text-primary">{{ method_exists($movements, 'total') ? $movements->total() : (is_countable($movements) ? count($movements) : 0) }}</span>
        </div>
    </div>

    <!-- Horizontal Navigation Menu (Tabs) -->
    <div class="rounded-sm border border-stroke bg-white px-4 pt-3 shadow-default dark:border-strokedark dark:bg-boxdark">
        <nav class="flex flex-wrap items-center gap-2 sm:gap-6 border-b border-stroke dark:border-strokedark -mb-px overflow-x-auto" aria-label="Secciones de movimientos">
            <!-- Tab 1: Historial de Movimientos -->
            <button type="button"
                    id="tab-btn-historial"
                    onclick="switchMovementTab('historial')"
                    class="group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'historial' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                <span>Historial y Auditoría</span>
                <span class="ml-1 rounded-full bg-gray-2 px-2 py-0.5 text-xs font-bold text-black dark:bg-meta-4 dark:text-white">
                    {{ method_exists($movements, 'total') ? $movements->total() : (is_countable($movements) ? count($movements) : 0) }}
                </span>
            </button>

            <!-- Tab 2: Registrar Nuevo Movimiento -->
            <button type="button"
                    id="tab-btn-formulario"
                    onclick="switchMovementTab('formulario')"
                    class="group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'formulario' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l4 4a1 1 0 01-1.414 1.414L11 6.414V13a1 1 0 11-2 0V6.414L6.707 8.707a1 1 0 01-1.414-1.414l4-4A1 1 0 0110 3zm-6 13a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>Registrar Entrada / Salida</span>
                @if(request()->filled('product_id'))
                    <span class="ml-1 h-2 w-2 rounded-full bg-primary"></span>
                @endif
            </button>

            <!-- Tab 3: Gráficos de Movimientos -->
            <button type="button"
                    id="tab-btn-graficos"
                    onclick="switchMovementTab('graficos')"
                    class="group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'graficos' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                </svg>
                <span>Gráficos de Flujo</span>
            </button>
        </nav>
    </div>

    <!-- ==================== SECCIÓN 1: HISTORIAL DE MOVIMIENTOS ==================== -->
    <div id="tab-panel-historial" class="{{ $activeTab === 'historial' ? '' : 'hidden' }}">
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
            <!-- Filter Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-stroke dark:border-strokedark mb-4 gap-3">
                <div>
                    <h3 class="text-xl font-semibold text-black dark:text-white">Historial de Movimientos</h3>
                    <p class="text-xs text-body dark:text-bodydark mt-0.5">Auditoría cronológica con stock anterior y posterior</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Filter by type -->
                    <form method="GET" action="{{ route('movements.index') }}" class="flex items-center gap-2">
                        <select name="filter_tipo" 
                                onchange="this.form.submit()" 
                                class="rounded-md border border-stroke bg-transparent py-1.5 px-3 text-xs text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white">
                            <option value="" class="dark:bg-boxdark">Todos los tipos</option>
                            <option value="entrada" {{ request('filter_tipo') === 'entrada' ? 'selected' : '' }} class="dark:bg-boxdark">Solo Entradas (+)</option>
                            <option value="salida" {{ request('filter_tipo') === 'salida' ? 'selected' : '' }} class="dark:bg-boxdark">Solo Salidas (-)</option>
                        </select>
                        @if(request('filter_tipo'))
                            <a href="{{ route('movements.index') }}" 
                               class="text-xs text-body hover:text-black dark:hover:text-white px-1">
                                ✕
                            </a>
                        @endif
                    </form>

                    <button type="button"
                            onclick="switchMovementTab('formulario')"
                            class="inline-flex items-center gap-1.5 rounded-md bg-primary/10 px-3.5 py-1.5 text-xs font-semibold text-primary hover:bg-primary hover:text-white transition cursor-pointer">
                        <span>+ Nuevo movimiento</span>
                    </button>
                </div>
            </div>

            <!-- Movements Table -->
            <div class="box-scroller max-w-full">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Fecha y hora</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Producto / SKU</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Tipo</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right bg-gray-2 dark:bg-meta-4">Cantidad</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right bg-gray-2 dark:bg-meta-4">Balance</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Motivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#eee] dark:divide-strokedark">
                        @forelse($movements as $m)
                            @php
                                $isEntrada = strtolower($m->tipo) === 'entrada';
                            @endphp
                            <tr>
                                <!-- Fecha -->
                                <td class="py-4 px-4 text-xs font-mono text-body dark:text-bodydark whitespace-nowrap">
                                    {{ $m->fecha ? $m->fecha->format('d/m/Y H:i') : ($m->created_at ? $m->created_at->format('d/m/Y H:i') : '—') }}
                                </td>

                                <!-- Producto & SKU -->
                                <td class="py-4 px-4">
                                    <h5 class="text-sm font-medium text-black dark:text-white">{{ $m->product->nombre ?? 'Producto eliminado' }}</h5>
                                    <p class="text-xs font-mono text-body dark:text-bodydark">{{ $m->product->sku ?? '—' }}</p>
                                </td>

                                <!-- Tipo Pill -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="inline-flex rounded-full py-1 px-3 text-xs font-medium {{ $isEntrada ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">
                                        {{ ucfirst($m->tipo) }}
                                    </span>
                                </td>

                                <!-- Cantidad -->
                                <td class="py-4 px-4 text-right text-sm font-semibold {{ $isEntrada ? 'text-success' : 'text-danger' }} whitespace-nowrap">
                                    {{ $isEntrada ? '+' : '-' }}{{ $m->cantidad }}
                                </td>

                                <!-- Balance -->
                                <td class="py-4 px-4 text-right text-xs text-body dark:text-bodydark whitespace-nowrap">
                                    {{ $m->stock_anterior }} → <span class="font-bold text-black dark:text-white">{{ $m->stock_posterior }}</span>
                                </td>

                                <!-- Motivo -->
                                <td class="py-4 px-4 text-xs text-body dark:text-bodydark max-w-[180px] truncate" title="{{ $m->motivo }}">
                                    {{ $m->motivo ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-sm text-body dark:text-bodydark">
                                    No hay registros de movimientos que coincidan con el filtro.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($movements, 'links'))
                <div class="mt-4 pt-4 border-t border-stroke dark:border-strokedark">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== SECCIÓN 2: FORMULARIO REGISTRAR MOVIMIENTO ==================== -->
    <div id="tab-panel-formulario" class="{{ $activeTab === 'formulario' ? '' : 'hidden' }}">
        <div class="rounded-sm border border-stroke bg-white p-6.5 shadow-default dark:border-strokedark dark:bg-boxdark max-w-2xl">
            <div class="pb-3 border-b border-stroke dark:border-strokedark mb-5">
                <h3 class="text-base font-bold text-black dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary fill-current" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l4 4a1 1 0 01-1.414 1.414L11 6.414V13a1 1 0 11-2 0V6.414L6.707 8.707a1 1 0 01-1.414-1.414l4-4A1 1 0 0110 3zm-6 13a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>Registrar nuevo movimiento</span>
                </h3>
                <p class="text-xs text-body dark:text-bodydark mt-0.5">Operación transaccional inmediata sobre almacén</p>
            </div>

            <form method="POST" action="{{ route('movements.store') }}" class="space-y-4">
                @csrf

                <!-- Product Select -->
                <div>
                    <label for="product_id" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Producto objetivo *
                    </label>
                    <select id="product_id" 
                            name="product_id" 
                            required 
                            class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-3 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                        <option value="" class="dark:bg-boxdark">Selecciona un producto del catálogo...</option>
                        @foreach($products ?? [] as $product)
                            <option value="{{ $product->id }}" 
                                    {{ (old('product_id', $selectedProductId ?? request('product_id')) == $product->id) ? 'selected' : '' }}
                                    class="dark:bg-boxdark">
                                {{ $product->nombre }} (SKU: {{ $product->sku }} — Stock: {{ $product->stock }} u.)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipo Movement (Radio / Buttons) -->
                <div>
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Tipo de movimiento *
                    </span>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="rounded-md border border-stroke bg-white p-3 flex items-center justify-between cursor-pointer hover:border-success dark:border-strokedark dark:bg-boxdark has-[:checked]:border-success has-[:checked]:bg-success/5 transition">
                            <div class="flex items-center gap-2">
                                <input type="radio" 
                                       name="tipo" 
                                       value="entrada" 
                                       {{ old('tipo', $selectedTipo ?? request('tipo', 'entrada')) === 'entrada' ? 'checked' : '' }} 
                                       class="h-4 w-4 text-success focus:ring-success">
                                <span class="text-xs uppercase font-bold text-success">
                                    + Entrada
                                </span>
                            </div>
                            <span class="text-[11px] text-body dark:text-bodydark">Ingreso</span>
                        </label>

                        <label class="rounded-md border border-stroke bg-white p-3 flex items-center justify-between cursor-pointer hover:border-danger dark:border-strokedark dark:bg-boxdark has-[:checked]:border-danger has-[:checked]:bg-danger/5 transition">
                            <div class="flex items-center gap-2">
                                <input type="radio" 
                                       name="tipo" 
                                       value="salida" 
                                       {{ old('tipo', $selectedTipo ?? request('tipo')) === 'salida' ? 'checked' : '' }} 
                                       class="h-4 w-4 text-danger focus:ring-danger">
                                <span class="text-xs uppercase font-bold text-danger">
                                    - Salida
                                </span>
                            </div>
                            <span class="text-[11px] text-body dark:text-bodydark">Egreso</span>
                        </label>
                    </div>
                </div>

                <!-- Cantidad -->
                <div>
                    <label for="cantidad" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Cantidad de unidades *
                    </label>
                    <input type="number" 
                           id="cantidad" 
                           name="cantidad" 
                           min="1" 
                           value="{{ old('cantidad', 1) }}" 
                           required 
                           placeholder="1"
                           class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm font-mono text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                </div>

                <!-- Motivo -->
                <div>
                    <label for="motivo" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Motivo u orden de referencia (opcional)
                    </label>
                    <textarea id="motivo" 
                              name="motivo" 
                              rows="3" 
                              placeholder="Ej. Compra de lote a proveedor, venta directa, ajuste de inventario..."
                              class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">{{ old('motivo') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 flex items-center justify-end gap-3">
                    <button type="button"
                            onclick="switchMovementTab('historial')"
                            class="rounded-md border border-stroke py-2.5 px-5 text-sm font-medium text-body hover:text-black dark:border-strokedark dark:text-bodydark dark:hover:text-white transition cursor-pointer">
                        Volver al historial
                    </button>
                    <button type="submit" 
                            class="rounded-md bg-primary py-2.5 px-6 text-center text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition flex items-center justify-center gap-2 cursor-pointer">
                        <span>Confirmar movimiento</span>
                        <span>→</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-stroke dark:border-strokedark text-xs text-body dark:text-bodydark">
                <span class="font-semibold text-black dark:text-white">Control de integridad:</span> El sistema bloquea automáticamente salidas que superen las existencias físicas disponibles.
            </div>
        </div>
    </div>

    <!-- ==================== SECCIÓN 3: GRÁFICOS DE MOVIMIENTOS ==================== -->
    <div id="tab-panel-graficos" class="{{ $activeTab === 'graficos' ? '' : 'hidden' }}">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <!-- Chart 1: Cronología Entradas vs Salidas (8 cols) -->
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-8">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-black dark:text-white">Flujo Cronológico de Movimientos (Entradas vs. Salidas)</h3>
                        <p class="text-xs text-body dark:text-bodydark">Unidades operadas por transacción en el historial reciente</p>
                    </div>
                    <span class="rounded bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">ApexCharts</span>
                </div>
                <div id="mov-chart-timeline" class="w-full min-h-[320px]"></div>
            </div>

            <!-- Chart 2: Proporción Total Entradas vs Salidas (4 cols) -->
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-4">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Balance de Unidades Operadas</h3>
                    <p class="text-xs text-body dark:text-bodydark">Volumen acumulado de ingresos (+) frente a egresos (-)</p>
                </div>
                <div id="mov-chart-donut" class="w-full min-h-[320px] flex items-center justify-center"></div>
            </div>
        </div>
    </div>
</div>

@php
    $movList = ($allMovementsForCharts ?? collect())->take(-15);
    $movLabels = $movList->map(fn($m) => ($m->fecha ? $m->fecha->format('d/m H:i') : '') . ' (' . ($m->product->sku ?? '') . ')')->values();
    $movEntradas = $movList->map(fn($m) => strtolower($m->tipo) === 'entrada' ? (int) $m->cantidad : 0)->values();
    $movSalidas = $movList->map(fn($m) => strtolower($m->tipo) === 'salida' ? (int) $m->cantidad : 0)->values();

    $totalUnitsIn = (int) ($allMovementsForCharts ?? collect())->filter(fn($m) => strtolower($m->tipo) === 'entrada')->sum('cantidad');
    $totalUnitsOut = (int) ($allMovementsForCharts ?? collect())->filter(fn($m) => strtolower($m->tipo) === 'salida')->sum('cantidad');
@endphp

<script>
    function switchMovementTab(tabName) {
        const tabs = ['historial', 'formulario', 'graficos'];
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

        const elTimeline = document.querySelector('#mov-chart-timeline');
        if (elTimeline) {
            new ApexCharts(elTimeline, {
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Satoshi, sans-serif' },
                series: [
                    { name: 'Entradas (+)', data: @json($movEntradas) },
                    { name: 'Salidas (-)', data: @json($movSalidas) }
                ],
                colors: ['#10B981', '#FB5454'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($movLabels) },
                legend: { position: 'top' }
            }).render();
        }

        const elDonut = document.querySelector('#mov-chart-donut');
        if (elDonut) {
            new ApexCharts(elDonut, {
                chart: { type: 'donut', height: 320, fontFamily: 'Satoshi, sans-serif' },
                series: [{{ $totalUnitsIn }}, {{ $totalUnitsOut }}],
                labels: ['Total Unidades Ingresadas (+)', 'Total Unidades Egresadas (-)'],
                colors: ['#10B981', '#FB5454'],
                legend: { position: 'bottom' },
                plotOptions: { pie: { donut: { size: '62%' } } }
            }).render();
        }
    });
</script>
@endsection
