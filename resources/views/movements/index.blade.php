@extends('layouts.app')

@section('content')
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
            <span class="font-bold text-primary">{{ is_countable($movements) ? count($movements) : ($movements->total() ?? 0) }}</span>
        </div>
    </div>

    <!-- Main Two-Column Layout: Form on Left (5 cols), History on Right (7 cols) -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-start">
        
        <!-- Left: Form Card -->
        <div class="rounded-sm border border-stroke bg-white p-6.5 shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-5">
            <div class="pb-3 border-b border-stroke dark:border-strokedark mb-5">
                <h3 class="text-base font-bold text-black dark:text-white">Registrar nuevo movimiento</h3>
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
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full rounded-md bg-primary py-3 px-4 text-center text-sm font-medium text-white hover:bg-opacity-90 shadow-default transition flex items-center justify-center gap-2">
                        <span>Confirmar movimiento</span>
                        <span>→</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-stroke dark:border-strokedark text-xs text-body dark:text-bodydark">
                <span class="font-semibold text-black dark:text-white">Control de integridad:</span> El sistema bloquea automáticamente salidas que superen las existencias físicas disponibles.
            </div>
        </div>

        <!-- Right: Historial de Movimientos Table (7 cols) -->
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1 lg:col-span-7">
            <!-- Filter Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-stroke dark:border-strokedark mb-4 gap-3">
                <div>
                    <h3 class="text-xl font-semibold text-black dark:text-white">Historial de Movimientos</h3>
                    <p class="text-xs text-body dark:text-bodydark mt-0.5">Auditoría cronológica con stock anterior y posterior</p>
                </div>

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
            </div>

            <!-- Movements Table -->
            <div class="max-w-full overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-2 text-left dark:bg-meta-4">
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Fecha y hora</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Producto / SKU</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Tipo</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right">Cantidad</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase text-right">Balance</th>
                            <th class="py-4 px-4 font-medium text-black dark:text-white text-xs uppercase">Motivo</th>
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
</div>
@endsection
