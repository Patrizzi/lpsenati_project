@extends('layouts.app')

@section('content')
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
            <span class="font-bold text-primary">{{ is_countable($suppliers) ? count($suppliers) : ($suppliers->count() ?? 0) }}</span>
        </div>
    </div>

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

    <!-- Main Grid: Form on Left (4 cols), Table on Right (8 cols) -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-start">
        <!-- Create / Edit Form Card -->
        <div class="rounded-sm border border-stroke bg-white p-6.5 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-4">
            <div class="pb-3 border-b border-stroke dark:border-strokedark mb-5 flex items-center justify-between">
                <h3 class="text-base font-bold text-black dark:text-white">
                    {{ isset($editingSupplier) ? 'Editar proveedor' : 'Nuevo proveedor' }}
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

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full rounded-md bg-primary py-2.5 px-4 text-center text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition">
                        {{ isset($editingSupplier) ? 'Actualizar proveedor' : 'Guardar proveedor' }}
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-stroke dark:border-strokedark text-xs text-body dark:text-bodydark">
                <span class="font-semibold text-black dark:text-white">Regla de negocio:</span> No se puede eliminar un proveedor si tiene productos asignados en el catálogo.
            </div>
        </div>

        <!-- Suppliers Table Card -->
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1 lg:col-span-8">
            <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                <h3 class="text-xl font-semibold text-black dark:text-white">Proveedores Registrados</h3>
                <p class="text-xs text-body dark:text-bodydark mt-0.5">Contactos de abastecimiento y volumen de catálogo</p>
            </div>

            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Proveedor</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Correo electrónico</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Teléfono</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center">Artículos</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right">Acciones</th>
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
                                        <a href="{{ route('suppliers.index', ['edit' => $supplier->id]) }}" 
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
</div>
@endsection
