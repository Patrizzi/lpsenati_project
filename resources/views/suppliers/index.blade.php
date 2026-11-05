@extends('layouts.app')

@section('content')
@php
    $activeTab = 'listado';
    if (isset($editingSupplier) || request('tab') === 'formulario' || $errors->any()) {
        $activeTab = 'formulario';
    }
@endphp

<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md font-bold text-black dark:text-white">
                Gestionar proveedores
            </h1>
            <p class="text-sm font-medium text-body dark:text-bodydark mt-1">
                Mantén actualizados los datos de contacto y abastecimiento de tus proveedores tecnológicos.
            </p>
        </div>

        <div class="inline-flex items-center gap-2 rounded-md border border-stroke bg-white px-4 py-2 text-xs font-medium text-black dark:border-strokedark dark:bg-boxdark dark:text-white shadow-default">
            <span>Total Proveedores:</span>
            <span class="font-bold text-primary">{{ method_exists($suppliers, 'total') ? $suppliers->total() : (is_countable($suppliers) ? count($suppliers) : ($suppliers->count() ?? 0)) }}</span>
        </div>
    </div>

    <!-- Horizontal Navigation Menu (Tabs) -->
    <div class="rounded-sm border border-stroke bg-white px-4 pt-3 shadow-default dark:border-strokedark dark:bg-boxdark">
        <nav class="flex flex-wrap items-center gap-2 sm:gap-6 border-b border-stroke dark:border-strokedark -mb-px overflow-x-auto" aria-label="Secciones de proveedores">
            <!-- Tab 1: Proveedores Registrados -->
            <button type="button"
                    id="tab-btn-listado"
                    onclick="switchSupplierTab('listado')"
                    class="group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'listado' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4zm3 1h6v2H7V5zm6 4H7v2h6V9zm-6 4h6v2H7v-2z" clip-rule="evenodd"/>
                </svg>
                <span>Directorio y Búsqueda</span>
                <span class="ml-1 rounded-full bg-gray-2 px-2 py-0.5 text-xs font-bold text-black dark:bg-meta-4 dark:text-white">
                    {{ method_exists($suppliers, 'total') ? $suppliers->total() : (is_countable($suppliers) ? count($suppliers) : ($suppliers->count() ?? 0)) }}
                </span>
            </button>

            <!-- Tab 2: Nuevo / Editar Proveedor -->
            <button type="button"
                    id="tab-btn-formulario"
                    onclick="switchSupplierTab('formulario')"
                    class="group inline-flex items-center gap-2.5 border-b-2 py-3.5 px-3 text-sm font-semibold transition whitespace-nowrap cursor-pointer {{ $activeTab === 'formulario' ? 'border-primary text-primary' : 'border-transparent text-body hover:text-black dark:text-bodydark dark:hover:text-white' }}">
                @if(isset($editingSupplier))
                    <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                    <span>Editar Proveedor ({{ $editingSupplier->nombre }})</span>
                    <span class="ml-1 h-2 w-2 rounded-full bg-warning"></span>
                @else
                    <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>Nuevo Proveedor</span>
                @endif
            </button>
        </nav>
    </div>

    <!-- ==================== SECCIÓN 1: DIRECTORIO Y BÚSQUEDA ==================== -->
    <div id="tab-panel-listado" class="space-y-6 {{ $activeTab === 'listado' ? '' : 'hidden' }}">
        <!-- Search Bar -->
        <div class="rounded-sm border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <form method="GET" action="{{ route('suppliers.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Buscar por nombre, correo electrónico o teléfono..."
                           class="w-full rounded-md border border-stroke bg-transparent py-2.5 pl-4 pr-10 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                    @if(request('search'))
                        <a href="{{ route('suppliers.index') }}" 
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
                    <a href="{{ route('suppliers.index') }}" 
                       class="w-full sm:w-auto rounded-md border border-stroke py-2.5 px-4 text-center text-sm font-medium text-body hover:text-black dark:border-strokedark dark:text-bodydark dark:hover:text-white transition whitespace-nowrap">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        <!-- Suppliers Table Card -->
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-stroke dark:border-strokedark mb-4 gap-2">
                <div>
                    <h3 class="text-xl font-semibold text-black dark:text-white">Proveedores Registrados</h3>
                    <p class="text-xs text-body dark:text-bodydark mt-0.5">Contactos de abastecimiento y volumen de catálogo</p>
                </div>
                <button type="button"
                        onclick="switchSupplierTab('formulario')"
                        class="inline-flex items-center gap-1.5 rounded-md bg-primary/10 px-3.5 py-2 text-xs font-semibold text-primary hover:bg-primary hover:text-white transition cursor-pointer">
                    <span>+ Nuevo proveedor</span>
                </button>
            </div>

            <div class="box-scroller max-w-full">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Proveedor</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Correo electrónico</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase bg-gray-2 dark:bg-meta-4">Teléfono</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center bg-gray-2 dark:bg-meta-4">Artículos</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right bg-gray-2 dark:bg-meta-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#eee] dark:divide-strokedark">
                        @forelse($suppliers as $supplier)
                            @php
                                $itemCount = $supplier->products_count ?? $supplier->products->count() ?? 0;
                            @endphp
                            <tr>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary font-bold text-white text-xs uppercase shrink-0 shadow-default">
                                            {{ strtoupper(substr($supplier->nombre, 0, 1)) }}
                                        </div>
                                        <div class="font-medium text-black dark:text-white text-sm">
                                            {{ $supplier->nombre }}
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-sm font-mono text-body dark:text-bodydark">
                                    <a href="mailto:{{ $supplier->correo }}" class="hover:underline hover:text-primary">
                                        {{ $supplier->correo }}
                                    </a>
                                </td>
                                <td class="py-4 px-4 text-xs font-mono text-body dark:text-bodydark">
                                    {{ $supplier->telefono ?: '—' }}
                                </td>
                                <td class="py-4 px-4 text-center font-semibold text-black dark:text-white text-sm">
                                    {{ $itemCount }}
                                </td>
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2 text-xs">
                                        <!-- Edit -->
                                        <a href="{{ route('suppliers.index', ['edit' => $supplier->id, 'tab' => 'formulario']) }}" 
                                           class="rounded border border-stroke px-2.5 py-1 text-body hover:border-primary hover:text-primary dark:border-strokedark dark:text-bodydark dark:hover:text-white transition">
                                            Editar
                                        </a>

                                        <!-- Delete -->
                                        <form method="POST" 
                                              action="{{ route('suppliers.destroy', $supplier) }}" 
                                              onsubmit="return confirm('¿Eliminar al proveedor \'{{ $supplier->nombre }}\'? Solo procederá si no tiene productos asociados.')" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="rounded border border-danger/30 px-2.5 py-1 text-danger hover:bg-danger hover:text-white transition cursor-pointer {{ $itemCount > 0 ? 'opacity-50' : '' }}"
                                                    title="{{ $itemCount > 0 ? 'Tiene productos vinculados' : 'Eliminar proveedor' }}">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-sm text-body dark:text-bodydark">
                                    No se encontraron proveedores registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($suppliers, 'links'))
                <div class="mt-4 pt-4 border-t border-stroke dark:border-strokedark">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== SECCIÓN 2: FORMULARIO CREAR / EDITAR PROVEEDOR ==================== -->
    <div id="tab-panel-formulario" class="{{ $activeTab === 'formulario' ? '' : 'hidden' }}">
        <div class="rounded-sm border border-stroke bg-white p-6.5 shadow-default dark:border-strokedark dark:bg-boxdark max-w-2xl">
            <div class="pb-3 border-b border-stroke dark:border-strokedark mb-5 flex items-center justify-between">
                <h3 class="text-base font-bold text-black dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary fill-current" viewBox="0 0 20 20">
                        @if(isset($editingSupplier))
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        @else
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        @endif
                    </svg>
                    <span>{{ isset($editingSupplier) ? 'Editar proveedor: ' . $editingSupplier->nombre : 'Registrar nuevo proveedor' }}</span>
                </h3>
                @if(isset($editingSupplier))
                    <a href="{{ route('suppliers.index') }}" 
                       class="text-xs font-semibold text-danger hover:underline">
                        ✕ Cancelar
                    </a>
                @endif
            </div>

            <form method="POST" 
                  action="{{ isset($editingSupplier) ? route('suppliers.update', $editingSupplier) : route('suppliers.store') }}" 
                  class="space-y-4">
                @csrf
                @if(isset($editingSupplier))
                    @method('PUT')
                @endif

                <div>
                    <label for="nombre" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Razón social / Nombre comercial *
                    </label>
                    <input type="text" 
                           id="nombre" 
                           name="nombre" 
                           value="{{ old('nombre', $editingSupplier->nombre ?? '') }}" 
                           required 
                           placeholder="Ej. Falabella, Ripley, Tottus..."
                           class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                </div>

                <div>
                    <label for="correo" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Correo electrónico institucional *
                    </label>
                    <input type="email" 
                           id="correo" 
                           name="correo" 
                           value="{{ old('correo', $editingSupplier->correo ?? '') }}" 
                           required 
                           placeholder="contacto@proveedor.com"
                           class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                </div>

                <div>
                    <label for="telefono" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-black dark:text-white">
                        Teléfono de contacto
                    </label>
                    <input type="text" 
                           id="telefono" 
                           name="telefono" 
                           value="{{ old('telefono', $editingSupplier->telefono ?? '') }}" 
                           placeholder="Ej. (01) 611-5000 / 999 000 111"
                           class="w-full rounded-md border border-stroke bg-transparent py-2.5 px-4 text-sm font-mono text-black outline-none focus:border-primary dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                </div>

                <div class="pt-2 flex items-center justify-end gap-3">
                    @if(isset($editingSupplier))
                        <a href="{{ route('suppliers.index') }}"
                           class="rounded-md border border-stroke py-2.5 px-5 text-sm font-medium text-body hover:text-black dark:border-strokedark dark:text-bodydark dark:hover:text-white transition">
                            Cancelar
                        </a>
                    @else
                        <button type="button"
                                onclick="switchSupplierTab('listado')"
                                class="rounded-md border border-stroke py-2.5 px-5 text-sm font-medium text-body hover:text-black dark:border-strokedark dark:text-bodydark dark:hover:text-white transition cursor-pointer">
                            Volver al directorio
                        </button>
                    @endif
                    <button type="submit" 
                            class="rounded-md bg-primary py-2.5 px-6 text-center text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition cursor-pointer">
                        {{ isset($editingSupplier) ? 'Actualizar proveedor' : 'Guardar proveedor' }}
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-stroke dark:border-strokedark text-xs text-body dark:text-bodydark">
                <span class="font-semibold text-black dark:text-white">Regla de negocio:</span> No se puede eliminar un proveedor si tiene productos asignados en el catálogo.
            </div>
        </div>
    </div>
</div>

<script>
    function switchSupplierTab(tabName) {
        const tabs = ['listado', 'formulario'];
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
    }
</script>
@endsection
