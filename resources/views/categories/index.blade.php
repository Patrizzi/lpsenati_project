@extends('layouts.app')

@section('content')
@php
    $activeTab = 'listado';
    if (isset($editingCategory) || request('tab') === 'formulario' || $errors->any()) {
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
                Gestionar categorías
            </h1>
            <p class="text-sm font-medium text-body dark:text-bodydark mt-1">
                Organiza los productos por familias y características técnicas para control y filtrado rápido.
            </p>
        </div>

        <div class="inline-flex items-center gap-2 rounded-md border border-stroke bg-white px-4 py-2 text-xs font-medium text-black dark:border-strokedark dark:bg-boxdark dark:text-white shadow-default">
            <span>Total Familias:</span>
            <span class="font-bold text-primary">{{ method_exists($categories, 'total') ? $categories->total() : (is_countable($categories) ? count($categories) : ($categories->count() ?? 0)) }}</span>
        </div>
    </div>

    <!-- Horizontal Navigation Menu (Tabs) -->
    <div class="rounded-sm border border-stroke bg-white px-4 pt-3 shadow-default dark:border-strokedark dark:bg-boxdark">
        <nav class="flex flex-wrap items-center gap-2 sm:gap-6 border-b border-stroke dark:border-strokedark -mb-px overflow-x-auto" aria-label="Secciones de categorías">
            <!-- Tab 1: Familias Registradas -->
            <button type="button"
                    id="tab-btn-listado"
                    onclick="switchCategoryTab('listado')"
                    class=" group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'listado' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h7a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>Familias y Búsqueda</span>
                <span class="ml-1 rounded-full bg-gray-2 px-2 py-0.5 text-xs font-bold text-black dark:bg-meta-4 dark:text-white">
                    {{ method_exists($categories, 'total') ? $categories->total() : (is_countable($categories) ? count($categories) : ($categories->count() ?? 0)) }}
                </span>
            </button>

            <!-- Tab 2: Nueva / Editar Categoría -->
            <button type="button"
                    id="tab-btn-formulario"
                    onclick="switchCategoryTab('formulario')"
                    class="group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'formulario' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                @if(isset($editingCategory))
                    <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    <span>Editar Categoría ({{ $editingCategory->nombre }})</span>
                    <span class="ml-1 h-2 w-2 rounded-full bg-warning"></span>
                @else
                    <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>Nueva Categoría</span>
                @endif
            </button>

            <!-- Tab 3: Gráficos de Categorías -->
            <button type="button"
                    id="tab-btn-graficos"
                    onclick="switchCategoryTab('graficos')"
                    class="group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'graficos' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                </svg>
                <span>Gráficos por Familia</span>
            </button>
        </nav>
    </div>

    <!-- ==================== SECCIÓN 1: LISTADO Y BÚSQUEDA ==================== -->
    <div id="tab-panel-listado" class="space-y-6 {{ $activeTab === 'listado' ? '' : 'hidden' }}">
        <!-- Search Bar -->
        <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <form method="GET" action="{{ route('categories.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Buscar por nombre o descripción de categoría..."
                           class="w-full rounded-md border border-stroke bg-transparent py-2.5 pl-4 pr-10 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                    @if(request('search'))
                        <a href="{{ route('categories.index') }}" 
                           class="absolute right-3 top-2.5 text-body hover:text-black dark:hover:text-white text-sm">
                            ✕
                        </a>
                    @endif
                </div>

                <button type="submit" 
                        class="w-full sm:w-auto rounded-md bg-primary py-2.5 px-6 text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition whitespace-nowrap">
                    Buscar
                </button>
                @if(request('search'))
                    <a href="{{ route('categories.index') }}" 
                       class="w-full sm:w-auto rounded-md border border-stroke py-2.5 px-4 text-center text-sm font-medium text-body hover:text-black dark:border-strokedark dark:text-bodydark dark:hover:text-white transition whitespace-nowrap">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        <!-- Categories Table Card -->
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-stroke dark:border-strokedark mb-4 gap-2">
                <div>
                    <h3 class="text-xl font-semibold text-black dark:text-white">Familias Registradas</h3>
                    <p class="text-xs text-body dark:text-bodydark mt-0.5">Listado y balance de artículos asociados</p>
                </div>
                <button type="button"
                        onclick="switchCategoryTab('formulario')"
                        class="inline-flex items-center gap-1.5 rounded-md bg-primary/10 px-3.5 py-2 text-xs font-semibold text-primary hover:bg-primary hover:text-white transition cursor-pointer">
                    <span>+ Nueva categoría</span>
                </button>
            </div>

            <div class="box-scroller max-w-full">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Categoría</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Descripción</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center bg-gray-2 dark:bg-meta-4">Artículos</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center bg-gray-2 dark:bg-meta-4">Stock acumulado</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right bg-gray-2 dark:bg-meta-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#eee] dark:divide-strokedark">
                        @forelse($categories as $category)
                            @php
                                $itemCount = $category->products_count ?? $category->products->count() ?? 0;
                                $stockSum = $category->products_sum_stock ?? ($category->products ? $category->products->sum('stock') : 0);
                            @endphp
                            <tr>
                                <td class="py-4 px-4 font-medium text-black dark:text-white text-sm">
                                    {{ $category->nombre }}
                                </td>
                                <td class="py-4 px-4 text-xs text-body dark:text-bodydark max-w-xs truncate">
                                    {{ $category->descripcion ?: '—' }}
                                </td>
                                <td class="py-4 px-4 text-center font-semibold text-black dark:text-white text-sm">
                                    {{ $itemCount }}
                                </td>
                                <td class="py-4 px-4 text-center text-xs text-body dark:text-bodydark font-mono">
                                    {{ $stockSum }} u.
                                </td>
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2 text-xs">
                                        <!-- Edit -->
                                        <a href="{{ route('categories.index', ['edit' => $category->id, 'tab' => 'formulario']) }}" 
                                           class="rounded border border-stroke px-2.5 py-1 text-body hover:border-primary hover:text-primary dark:border-strokedark dark:text-bodydark dark:hover:text-white transition">
                                            Editar
                                        </a>

                                        <!-- Delete -->
                                        <form method="POST" 
                                              action="{{ route('categories.destroy', $category) }}" 
                                              onsubmit="return confirm('¿Eliminar la categoría \'{{ $category->nombre }}\'? Solo procederá si no tiene productos asociados.')" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="rounded border border-danger/30 px-2.5 py-1 text-danger hover:bg-danger hover:text-white transition cursor-pointer {{ $itemCount > 0 ? 'opacity-50' : '' }}"
                                                    title="{{ $itemCount > 0 ? 'Tiene productos vinculados' : 'Eliminar categoría' }}">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-sm text-body dark:text-bodydark">
                                    No se encontraron categorías registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($categories, 'links'))
                <div class="mt-4 pt-4 border-t border-stroke dark:border-strokedark">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== SECCIÓN 2: FORMULARIO CREAR / EDITAR CATEGORÍA ==================== -->
    <div id="tab-panel-formulario" class="{{ $activeTab === 'formulario' ? '' : 'hidden' }}">
        <div class="rounded-sm border border-stroke bg-white p-6.5 shadow-default dark:border-strokedark dark:bg-boxdark max-w-2xl">
            <div class="pb-3 border-b border-stroke dark:border-strokedark mb-5 flex items-center justify-between">
                <h3 class="text-base font-bold text-black dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary fill-current" viewBox="0 0 20 20">
                        @if(isset($editingCategory))
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        @else
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        @endif
                    </svg>
                    <span>{{ isset($editingCategory) ? 'Editar categoría: ' . $editingCategory->nombre : 'Registrar nueva categoría' }}</span>
                </h3>
                @if(isset($editingCategory))
                    <a href="{{ route('categories.index') }}" 
                       class="text-xs font-semibold text-danger hover:underline">
                        ✕ Cancelar
                    </a>
                @endif
            </div>

            <form method="POST" 
                  action="{{ isset($editingCategory) ? route('categories.update', $editingCategory) : route('categories.store') }}" 
                  class="space-y-4">
                @csrf
                @if(isset($editingCategory))
                    @method('PUT')
                @endif

                <div>
                    <label for="nombre" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Nombre de categoría *
                    </label>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           value="{{ old('nombre', $editingCategory->nombre ?? '') }}" 
                           required 
                           placeholder="Ej. Computadoras, Periféricos..."
                           class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                </div>

                <div>
                    <label for="descripcion" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Descripción o alcance
                    </label>
                    <textarea id="descripcion" 
                              name="descripcion" 
                              rows="3" 
                              placeholder="Ej. Laptops, servidores y estaciones de trabajo..."
                              class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">{{ old('descripcion', $editingCategory->descripcion ?? '') }}</textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-3">
                    @if(isset($editingCategory))
                        <a href="{{ route('categories.index') }}"
                           class="rounded-md border border-stroke py-2.5 px-5 text-sm font-medium text-body hover:text-black dark:border-strokedark dark:text-bodydark dark:hover:text-white transition">
                            Cancelar
                        </a>
                    @else
                        <button type="button"
                                onclick="switchCategoryTab('listado')"
                                class="rounded-md border border-stroke py-2.5 px-5 text-sm font-medium text-body hover:text-black dark:border-strokedark dark:text-bodydark dark:hover:text-white transition cursor-pointer">
                            Volver al listado
                        </button>
                    @endif
                    <button type="submit" 
                            class="rounded-md bg-primary py-2.5 px-6 text-center text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition cursor-pointer">
                        {{ isset($editingCategory) ? 'Actualizar categoría' : 'Guardar categoría' }}
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-stroke dark:border-strokedark text-xs text-body dark:text-bodydark">
                <span class="font-semibold text-black dark:text-white">Regla de negocio:</span> No se puede eliminar una categoría que tenga productos asociados en el catálogo.
            </div>
        </div>
    </div>

    <!-- ==================== SECCIÓN 3: GRÁFICOS DE CATEGORÍAS ==================== -->
    <div id="tab-panel-graficos" class="{{ $activeTab === 'graficos' ? '' : 'hidden' }}">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <!-- Chart 1: Donut Participación de Stock por Categoría (5 cols) -->
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-5">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-black dark:text-white">Distribución de Stock Físico</h3>
                        <p class="text-xs text-body dark:text-bodydark">Proporción de unidades almacenadas por familia</p>
                    </div>
                    <span class="rounded bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">ApexCharts</span>
                </div>
                <div id="cat-chart-donut" class="w-full min-h-[320px] flex items-center justify-center"></div>
            </div>

            <!-- Chart 2: Bar Artículos vs Stock por Categoría (7 cols) -->
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-7">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Artículos Registrados vs. Stock Acumulado</h3>
                    <p class="text-xs text-body dark:text-bodydark">Comparativa entre diversidad de referencias y volumen total por categoría</p>
                </div>
                <div id="cat-chart-bars" class="w-full min-h-[320px]"></div>
            </div>
        </div>
    </div>
</div>

@php
    $catNames = ($categories ?? collect())->pluck('nombre')->values();
    $catStocks = ($categories ?? collect())->map(fn($c) => (int) ($c->products_sum_stock ?? ($c->products ? $c->products->sum('stock') : 0)))->values();
    $catItemCounts = ($categories ?? collect())->map(fn($c) => (int) ($c->products_count ?? ($c->products ? $c->products->count() : 0)))->values();
@endphp

<script>
    function switchCategoryTab(tabName) {
        const tabs = ['listado', 'formulario', 'graficos'];
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

        const elDonut = document.querySelector('#cat-chart-donut');
        if (elDonut) {
            new ApexCharts(elDonut, {
                chart: { type: 'donut', height: 320, fontFamily: 'Satoshi, sans-serif' },
                series: @json($catStocks),
                labels: @json($catNames),
                colors: ['#3C50E0', '#10B981', '#F0950C', '#FB5454', '#80CAEE', '#6577F3', '#8FD0EF', '#0FADCF'],
                legend: { position: 'bottom' },
                plotOptions: { pie: { donut: { size: '62%' } } }
            }).render();
        }

        const elBars = document.querySelector('#cat-chart-bars');
        if (elBars) {
            new ApexCharts(elBars, {
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Satoshi, sans-serif' },
                series: [
                    { name: 'Stock Acumulado (u.)', data: @json($catStocks) },
                    { name: 'N° de Productos', data: @json($catItemCounts) }
                ],
                colors: ['#3C50E0', '#10B981'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($catNames) },
                legend: { position: 'top' }
            }).render();
        }
    });
</script>
@endsection
