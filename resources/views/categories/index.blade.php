@extends('layouts.app')

@section('content')
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
            <span class="font-bold text-primary">{{ is_countable($categories) ? count($categories) : ($categories->count() ?? 0) }}</span>
        </div>
    </div>

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

    <!-- Main Grid: Form on Left (4 cols), Table on Right (8 cols) -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-start">
        <!-- Create / Edit Form Card -->
        <div class="rounded-sm border border-stroke bg-white p-6.5 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-4">
            <div class="pb-3 border-b border-stroke dark:border-strokedark mb-5 flex items-center justify-between">
                <h3 class="text-base font-bold text-black dark:text-white">
                    {{ isset($editingCategory) ? 'Editar categoría' : 'Nueva categoría' }}
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

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full rounded-md bg-primary py-2.5 px-4 text-center text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition">
                        {{ isset($editingCategory) ? 'Actualizar categoría' : 'Guardar categoría' }}
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-stroke dark:border-strokedark text-xs text-body dark:text-bodydark">
                <span class="font-semibold text-black dark:text-white">Regla de negocio:</span> No se puede eliminar una categoría que tenga productos asociados en el catálogo.
            </div>
        </div>

        <!-- Categories Table Card -->
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1 lg:col-span-8">
            <div class="pb-3 border-b border-stroke dark:border-strokedark mb-4">
                <h3 class="text-xl font-semibold text-black dark:text-white">Familias Registradas</h3>
                <p class="text-xs text-body dark:text-bodydark mt-0.5">Listado y balance de artículos asociados</p>
            </div>

            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Categoría</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Descripción</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center">Artículos</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-center">Stock acumulado</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right">Acciones</th>
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
                                        <a href="{{ route('categories.index', ['edit' => $category->id]) }}" 
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
</div>
@endsection
