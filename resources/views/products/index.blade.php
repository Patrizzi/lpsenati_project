@extends('layouts.app')

@section('content')
@php
    $activeTab = 'catalogo';
    if (isset($editingProduct) || request('tab') === 'formulario' || ($errors->any() && !$errors->has('csv_file'))) {
        $activeTab = 'formulario';
    } elseif (session('import_summary') || request('tab') === 'importar' || $errors->has('csv_file')) {
        $activeTab = 'importar';
    } elseif (request('tab') === 'graficos') {
        $activeTab = 'graficos';
    }
@endphp

<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md font-bold text-black dark:text-white">
                Productos y existencias
            </h1>
            <p class="text-sm font-medium text-body dark:text-bodydark mt-1">
                Busca por nombre o SKU, filtra por categoría y vigila el stock mínimo para reposición inmediata.
            </p>
        </div>

        <!-- Quick CSV Template Download -->
        <div class="flex items-center gap-3">
            <a href="{{ route('products.template') }}" 
               class="inline-flex items-center justify-center gap-2 rounded-md border border-stroke bg-white py-2 px-4 text-center text-sm font-medium text-black hover:border-primary hover:text-primary dark:border-strokedark dark:bg-boxdark dark:text-white dark:hover:border-primary shadow-default transition"
               title="Descargar plantilla CSV">
                <svg class="w-4 h-4 fill-current text-primary" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                <span>Descargar Plantilla CSV</span>
            </a>
        </div>
    </div>

    <!-- Horizontal Navigation Menu (Tabs) -->
    <div class="rounded-sm border border-stroke bg-white px-4 pt-3 shadow-default dark:border-strokedark dark:bg-boxdark">
        <nav class="flex flex-wrap items-center gap-2 sm:gap-6 border-b border-stroke dark:border-strokedark -mb-px overflow-x-auto" aria-label="Secciones de productos">
            <!-- Tab 1: Catálogo y Filtros -->
            <button type="button"
                    id="tab-btn-catalogo"
                    onclick="switchProductTab('catalogo')"
                    class="product-tab-btn group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'catalogo' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                    <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>Catálogo y Filtros</span>
                <span class="ml-1 rounded-full bg-gray-2 px-2 py-0.5 text-xs font-bold text-black dark:bg-meta-4 dark:text-white">
                    {{ method_exists($products, 'total') ? $products->total() : (is_countable($products) ? count($products) : 0) }}
                </span>
            </button>

            <!-- Tab 2: Registrar / Editar Producto -->
            <button type="button"
                    id="tab-btn-formulario"
                    onclick="switchProductTab('formulario')"
                    class="product-tab-btn group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'formulario' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                @if(isset($editingProduct))
                    <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    <span>Editar Producto ({{ $editingProduct->sku }})</span>
                    <span class="ml-1 h-2 w-2 rounded-full bg-warning"></span>
                @else
                    <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>Nuevo Producto</span>
                @endif
            </button>

            <!-- Tab 3: Importar CSV -->
            <button type="button"
                    id="tab-btn-importar"
                    onclick="switchProductTab('importar')"
                    class="product-tab-btn group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'importar' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>Importar CSV</span>
                @if(session('import_summary'))
                    <span class="ml-1 h-2 w-2 rounded-full bg-success"></span>
                @endif
            </button>

            <!-- Tab 4: Gráficos de Productos -->
            <button type="button"
                    id="tab-btn-graficos"
                    onclick="switchProductTab('graficos')"
                    class="product-tab-btn group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'graficos' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                </svg>
                <span>Gráficos de Productos</span>
            </button>
        </nav>
    </div>

    <!-- ==================== SECCIÓN 1: CATÁLOGO Y FILTROS ==================== -->
    <div id="tab-panel-catalogo" class="product-tab-panel space-y-6 {{ $activeTab === 'catalogo' ? '' : 'hidden' }}">
        <!-- Filter & Search Card -->
        <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Search Text -->
                <div class="md:col-span-5">
                    <label for="search" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Búsqueda por nombre o código SKU
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Ej. Ryzen 7, COM-00001-01, Monitor..."
                               class="w-full rounded-md border border-stroke bg-transparent py-2.5 pl-4 pr-10 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                        @if(request('search'))
                            <a href="{{ route('products.index', request()->except('search')) }}" 
                               class="absolute right-3 top-2.5 text-body hover:text-black dark:hover:text-white text-sm">
                                ✕
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Category Filter Select -->
                <div class="md:col-span-3">
                    <label for="category_id" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Filtrar por categoría
                    </label>
                    <select id="category_id" 
                            name="category_id" 
                            class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-3 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                        <option value="" class="dark:bg-boxdark">Todas las categorías</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }} class="dark:bg-boxdark">
                                {{ $category->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Low Stock Checkbox -->
                <div class="md:col-span-2 flex items-center h-10">
                    <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-medium text-meta-1">
                        <input type="checkbox" 
                               name="low_stock" 
                               value="1" 
                               {{ request('low_stock') ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-stroke text-primary focus:ring-primary dark:border-strokedark dark:bg-boxdark">
                        <span>Solo stock bajo</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="md:col-span-2 flex items-center gap-2">
                    <button type="submit" 
                            class="flex-1 rounded-md bg-primary py-2.5 px-4 text-center text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'category_id', 'low_stock']))
                        <a href="{{ route('products.index') }}" 
                           class="rounded-md border border-stroke py-2.5 px-3 text-center text-sm font-medium text-body hover:text-black dark:border-strokedark dark:text-bodydark dark:hover:text-white transition">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Products Table Card -->
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-stroke dark:border-strokedark mb-4 gap-2">
                <div>
                    <h3 class="text-xl font-semibold text-black dark:text-white">Catálogo de Existencias</h3>
                    <p class="text-xs text-body dark:text-bodydark mt-0.5">
                        Mostrando {{ is_countable($products) ? count($products) : ($products->count() ?? 0) }} artículos registrados
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-medium">
                    <span class="flex items-center gap-1.5 text-success">
                        <span class="h-2 w-2 rounded-full bg-success"></span> Disponible
                    </span>
                    <span class="flex items-center gap-1.5 text-danger">
                        <span class="h-2 w-2 rounded-full bg-danger"></span> Stock bajo
                    </span>
                </div>
            </div>

            <div class="box-scroller max-w-full">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Producto / SKU</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Categoría</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Proveedor</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right bg-gray-2 dark:bg-meta-4">Precio unitario</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center bg-gray-2 dark:bg-meta-4">Existencias</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center bg-gray-2 dark:bg-meta-4">Estado</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right bg-gray-2 dark:bg-meta-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#eee] dark:divide-strokedark">
                        @forelse($products as $product)
                            @php
                                $isLow = $product->isLowStock();
                            @endphp
                            <tr>
                                <!-- Product Name & SKU -->
                                <td class="py-4 px-4">
                                    <h5 class="text-sm font-medium text-black dark:text-white">{{ $product->nombre }}</h5>
                                    <p class="text-xs font-mono text-body dark:text-bodydark mt-0.5">
                                        SKU: <span class="font-bold text-black dark:text-white">{{ $product->sku }}</span>
                                    </p>
                                </td>

                                <!-- Category -->
                                <td class="py-4 px-4 text-sm text-body dark:text-bodydark">
                                    {{ $product->category->nombre ?? 'Sin categoría' }}
                                </td>

                                <!-- Supplier -->
                                <td class="py-4 px-4 text-sm text-body dark:text-bodydark">
                                    {{ $product->supplier->nombre ?? 'Sin proveedor' }}
                                </td>

                                <!-- Price -->
                                <td class="py-4 px-4 text-right text-sm font-semibold text-black dark:text-white whitespace-nowrap">
                                    S/ {{ number_format($product->precio, 2) }}
                                </td>

                                <!-- Stock -->
                                <td class="py-4 px-4 text-center">
                                    <span class="text-sm font-bold {{ $isLow ? 'text-danger' : 'text-black dark:text-white' }}">
                                        {{ $product->stock }}
                                    </span>
                                    <span class="block text-xs text-body dark:text-bodydark">
                                        Mín: {{ $product->stock_minimo }}
                                    </span>
                                </td>

                                <!-- State Pill -->
                                <td class="py-4 px-4 text-center">
                                    @if($isLow)
                                        <span class="inline-flex rounded-full bg-danger/10 py-1 px-3 text-xs font-medium text-danger">
                                            Stock bajo
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-success/10 py-1 px-3 text-xs font-medium text-success">
                                            Disponible
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2 text-xs">
                                        <!-- Edit Link -->
                                        <a href="{{ route('products.index', ['edit' => $product->id, 'tab' => 'formulario']) }}" 
                                           class="rounded border border-stroke px-2.5 py-1 text-body hover:border-primary hover:text-primary dark:border-strokedark dark:text-bodydark dark:hover:text-white transition"
                                           title="Editar producto">
                                            Editar
                                        </a>

                                        <!-- Movement Link -->
                                        <a href="{{ route('movements.index', ['product_id' => $product->id]) }}" 
                                           class="rounded border border-stroke px-2.5 py-1 text-body hover:border-primary hover:text-primary dark:border-strokedark dark:text-bodydark dark:hover:text-white transition"
                                           title="Registrar movimiento">
                                            Stock ±
                                        </a>

                                        <!-- Delete Form -->
                                        <form method="POST" 
                                              action="{{ route('products.destroy', $product) }}" 
                                              onsubmit="return confirm('¿Eliminar este producto del catálogo? Esta acción no se puede deshacer.')" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="rounded border border-danger/30 px-2.5 py-1 text-danger hover:bg-danger hover:text-white transition cursor-pointer"
                                                    title="Eliminar producto">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-sm text-body dark:text-bodydark">
                                    No se encontraron productos que coincidan con los criterios de búsqueda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($products, 'links'))
                <div class="mt-4 pt-4 border-t border-stroke dark:border-strokedark">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== SECCIÓN 2: FORMULARIO CREAR / EDITAR PRODUCTO ==================== -->
    <div id="tab-panel-formulario" class="product-tab-panel {{ $activeTab === 'formulario' ? '' : 'hidden' }}">
        <div id="formulario-producto" class="rounded-sm border border-stroke bg-white p-6.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="pb-4 border-b border-stroke dark:border-strokedark mb-5 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-black dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary fill-current" viewBox="0 0 20 20">
                            @if(isset($editingProduct))
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                            @else
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                            @endif
                        </svg>
                        <span>{{ isset($editingProduct) ? 'Modificar datos de producto: ' . $editingProduct->sku : 'Registrar nuevo producto en catálogo' }}</span>
                    </h3>
                    <p class="text-xs text-body dark:text-bodydark mt-0.5">
                        Todos los campos con asterisco (*) son de diligenciamiento obligatorio.
                    </p>
                </div>
                @if(isset($editingProduct))
                    <a href="{{ route('products.index') }}" 
                       class="text-xs font-semibold text-danger hover:underline">
                        ✕ Cancelar edición
                    </a>
                @endif
            </div>

            <form method="POST" 
                  action="{{ isset($editingProduct) ? route('products.update', $editingProduct) : route('products.store') }}" 
                  class="grid grid-cols-1 md:grid-cols-12 gap-5">
                @csrf
                @if(isset($editingProduct))
                    @method('PUT')
                @endif

                <!-- Nombre -->
                <div class="md:col-span-5">
                    <label for="nombre" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Nombre del producto *
                    </label>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           value="{{ old('nombre', $editingProduct->nombre ?? '') }}" 
                           required 
                           placeholder="Ej. Lenovo Legion 5 Pro"
                           class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                </div>

                <!-- SKU -->
                <div class="md:col-span-3">
                    <label for="sku" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Código SKU único *
                    </label>
                    <input type="text" 
                           id="sku" 
                           name="sku" 
                           value="{{ old('sku', $editingProduct->sku ?? '') }}" 
                           required 
                           placeholder="COM-00002-02"
                           class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm font-mono uppercase text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                </div>

                <!-- Precio -->
                <div class="md:col-span-4">
                    <label for="precio" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Precio unitario (S/) *
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-sm font-medium text-body dark:text-bodydark">S/</span>
                        <input type="number" 
                               id="precio" 
                               name="precio" 
                               step="0.01" 
                               min="0"
                               value="{{ old('precio', $editingProduct->precio ?? '') }}" 
                               required 
                               placeholder="4500.00"
                               class="w-full rounded-md border border-stroke bg-transparent py-2.5 pl-8 pr-4 text-sm font-mono text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                    </div>
                </div>

                <!-- Category -->
                <div class="md:col-span-4">
                    <label for="form_category_id" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Categoría *
                    </label>
                    <select id="form_category_id" 
                            name="category_id" 
                            required 
                            class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-3 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                        <option value="" class="dark:bg-boxdark">Selecciona una categoría...</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $editingProduct->category_id ?? '') == $category->id ? 'selected' : '' }} class="dark:bg-boxdark">
                                {{ $category->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Supplier -->
                <div class="md:col-span-4">
                    <label for="form_supplier_id" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Proveedor *
                    </label>
                    <select id="form_supplier_id" 
                            name="supplier_id" 
                            required 
                            class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-3 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                        <option value="" class="dark:bg-boxdark">Selecciona un proveedor...</option>
                        @foreach($suppliers ?? [] as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $editingProduct->supplier_id ?? '') == $supplier->id ? 'selected' : '' }} class="dark:bg-boxdark">
                                {{ $supplier->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Stock Actual -->
                <div class="md:col-span-2">
                    <label for="stock" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Stock actual *
                    </label>
                    <input type="number" 
                           id="stock" 
                           name="stock" 
                           min="0"
                           value="{{ old('stock', $editingProduct->stock ?? 0) }}" 
                           required 
                           class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm font-mono text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                </div>

                <!-- Stock Mínimo -->
                <div class="md:col-span-2">
                    <label for="stock_minimo" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Stock mínimo *
                    </label>
                    <input type="number" 
                           id="stock_minimo" 
                           name="stock_minimo" 
                           min="0"
                           value="{{ old('stock_minimo', $editingProduct->stock_minimo ?? 5) }}" 
                           required 
                           class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm font-mono text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                </div>

                <!-- Form Submit Buttons -->
                <div class="md:col-span-12 flex items-center justify-end gap-3 pt-2">
                    @if(isset($editingProduct))
                        <a href="{{ route('products.index') }}" 
                           class="rounded-md border border-stroke py-2.5 px-5 text-sm font-medium text-body hover:text-black dark:border-strokedark dark:text-bodydark dark:hover:text-white transition">
                            Cancelar
                        </a>
                    @else
                        <button type="button"
                                onclick="switchProductTab('catalogo')"
                                class="rounded-md border border-stroke py-2.5 px-5 text-sm font-medium text-body hover:text-black dark:border-strokedark dark:text-bodydark dark:hover:text-white transition cursor-pointer">
                            Volver al catálogo
                        </button>
                    @endif
                    <button type="submit" 
                            class="rounded-md bg-primary py-2.5 px-6 text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition cursor-pointer">
                        {{ isset($editingProduct) ? 'Guardar cambios de producto' : 'Registrar producto' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== SECCIÓN 3: IMPORTACIÓN MASIVA CSV ==================== -->
    <div id="tab-panel-importar" class="product-tab-panel {{ $activeTab === 'importar' ? '' : 'hidden' }}">
        <div id="panel-importar-csv" class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between pb-4 border-b border-stroke dark:border-strokedark gap-4 mb-5">
                <div>
                    <h3 class="text-base font-bold text-black dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary fill-current" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Importación masiva de catálogo CSV</span>
                    </h3>
                    <p class="text-xs text-body dark:text-bodydark mt-1">
                        Columnas requeridas: <code class="font-mono text-black dark:text-white bg-gray dark:bg-meta-4 px-1.5 py-0.5 rounded">nombre, sku, precio, stock, stock_minimo, categoria, proveedor</code>
                    </p>
                </div>

                <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    @csrf
                    <input type="file" 
                           name="csv_file" 
                           accept=".csv,text/csv" 
                           required 
                           class="text-xs text-body dark:text-bodydark file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary hover:file:text-white file:transition cursor-pointer">
                    <button type="submit" 
                            class="inline-flex items-center justify-center rounded-md bg-primary py-2 px-5 text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition whitespace-nowrap cursor-pointer">
                        Procesar CSV →
                    </button>
                </form>
            </div>

            <!-- Import Summary Report if present in session -->
            @if(session('import_summary'))
                @php $summary = session('import_summary'); @endphp
                <div class="rounded-sm border border-stroke bg-gray-2 p-4 text-xs font-sans space-y-3 dark:border-strokedark dark:bg-meta-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-2 border-b border-stroke dark:border-strokedark gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-black dark:text-white">
                            Resumen de auditoría de importación
                        </span>
                        <div class="flex items-center gap-4 text-xs">
                            <span class="text-body dark:text-bodydark">Total leídas: <strong class="text-black dark:text-white">{{ $summary['read'] ?? $summary['total_filas'] ?? $summary['total'] ?? 0 }}</strong></span>
                            <span class="text-success">Importadas: <strong>{{ $summary['imported'] ?? $summary['importadas'] ?? 0 }}</strong></span>
                            <span class="text-danger">Rechazadas: <strong>{{ $summary['rejected'] ?? $summary['rechazadas'] ?? 0 }}</strong></span>
                        </div>
                    </div>

                    @php $errList = $summary['errors'] ?? $summary['errores'] ?? []; @endphp
                    @if(!empty($errList))
                        <div>
                            <div class="font-semibold text-danger uppercase mb-1">
                                Detalle de incidencias:
                            </div>
                            <ul class="max-h-36 overflow-y-auto space-y-1 text-xs text-danger bg-danger/10 p-3 rounded border border-danger/20">
                                @foreach($errList as $err)
                                    <li>• {{ is_array($err) ? json_encode($err) : $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @else
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-sm border border-stroke bg-gray-2 p-4 text-xs text-body dark:border-strokedark dark:bg-meta-4 dark:text-bodydark">
                    <div>
                        <span class="font-semibold text-black dark:text-white">¿Primera vez importando?</span>
                        Descarga la plantilla CSV con el formato oficial para evitar errores de validación en las columnas.
                    </div>
                    <a href="{{ route('products.template') }}"
                       class="inline-flex items-center justify-center gap-1.5 rounded-md border border-stroke bg-white py-2 px-4 font-medium text-black hover:border-primary hover:text-primary dark:border-strokedark dark:bg-boxdark dark:text-white dark:hover:border-primary transition whitespace-nowrap">
                        <svg class="w-4 h-4 fill-current text-primary" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        <span>Descargar ejemplo CSV</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== SECCIÓN 4: GRÁFICOS DE PRODUCTOS ==================== -->
    <div id="tab-panel-graficos" class="product-tab-panel space-y-6 {{ $activeTab === 'graficos' ? '' : 'hidden' }}">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <!-- Chart 1: Top 10 Stock Actual vs Mínimo (7 cols) -->
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-7">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-black dark:text-white">Top 10 Productos: Stock Actual vs. Stock Mínimo</h3>
                        <p class="text-xs text-body dark:text-bodydark">Comparativa de existencias físicas frente al umbral mínimo de seguridad</p>
                    </div>
                    <span class="rounded bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">ApexCharts</span>
                </div>
                <div id="prod-chart-stock-vs-min" class="w-full min-h-[320px]"></div>
            </div>

            <!-- Chart 2: Distribución de Productos por Proveedor (5 cols) -->
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-5">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Participación por Proveedor</h3>
                    <p class="text-xs text-body dark:text-bodydark">Cantidad de referencias suministradas por cada proveedor</p>
                </div>
                <div id="prod-chart-suppliers" class="w-full min-h-[320px] flex items-center justify-center"></div>
            </div>

            <!-- Chart 3: Top 10 Valorización por Producto (12 cols) -->
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-12">
                <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                    <h3 class="text-base font-bold text-black dark:text-white">Top 10 Productos con Mayor Valorización en Almacén (S/)</h3>
                    <p class="text-xs text-body dark:text-bodydark">Capital invertido por artículo calculado como Precio Unitario × Stock Actual</p>
                </div>
                <div id="prod-chart-valuation" class="w-full min-h-[320px]"></div>
            </div>
        </div>
    </div>
</div>

@php
    $chartProdList = ($allProductsForCharts ?? collect())->take(10);
    $prodChartNames = $chartProdList->pluck('nombre')->values();
    $prodChartStocks = $chartProdList->pluck('stock')->map(fn($v) => (int) $v)->values();
    $prodChartMins = $chartProdList->pluck('stock_minimo')->map(fn($v) => (int) $v)->values();

    $valProdList = ($allProductsForCharts ?? collect())
        ->sortByDesc(fn($p) => $p->precio * $p->stock)
        ->take(10);
    $prodValNames = $valProdList->pluck('nombre')->values();
    $prodValAmounts = $valProdList->map(fn($p) => round((float) ($p->precio * $p->stock), 2))->values();

    $supLabels = ($suppliers ?? collect())->pluck('nombre')->values();
    $supCounts = ($suppliers ?? collect())->map(fn($s) => (int) ($s->products_count ?? 0))->values();
@endphp

<script>
    function switchProductTab(tabName) {
        const tabs = ['catalogo', 'formulario', 'importar', 'graficos'];
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

        const elStockMin = document.querySelector('#prod-chart-stock-vs-min');
        if (elStockMin) {
            new ApexCharts(elStockMin, {
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Satoshi, sans-serif' },
                series: [
                    { name: 'Stock Actual', data: @json($prodChartStocks) },
                    { name: 'Stock Mínimo', data: @json($prodChartMins) }
                ],
                colors: ['#3C50E0', '#FB5454'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($prodChartNames) },
                legend: { position: 'top' }
            }).render();
        }

        const elSup = document.querySelector('#prod-chart-suppliers');
        if (elSup) {
            new ApexCharts(elSup, {
                chart: { type: 'donut', height: 320, fontFamily: 'Satoshi, sans-serif' },
                series: @json($supCounts),
                labels: @json($supLabels),
                colors: ['#3C50E0', '#10B981', '#F0950C', '#80CAEE', '#FB5454', '#6577F3'],
                legend: { position: 'bottom' },
                plotOptions: { pie: { donut: { size: '62%' } } }
            }).render();
        }

        const elVal = document.querySelector('#prod-chart-valuation');
        if (elVal) {
            new ApexCharts(elVal, {
                chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Satoshi, sans-serif' },
                series: [{ name: 'Valor en Almacén (S/)', data: @json($prodValAmounts) }],
                colors: ['#10B981'],
                plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '60%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: @json($prodValNames) }
            }).render();
        }
    });
</script>
@endsection
