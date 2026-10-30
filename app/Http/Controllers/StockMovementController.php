<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    /**
     * Muestra el formulario de registro de movimientos y el historial auditable.
     */
    public function index(Request $request): View
    {
        $products = Product::orderBy('nombre')->get();

        $movementsQuery = StockMovement::with('product.category')
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        if ($request->filled('filter_tipo') && in_array($request->input('filter_tipo'), ['entrada', 'salida'], true)) {
            $movementsQuery->where('tipo', $request->input('filter_tipo'));
        }

        if ($request->filled('filter_product')) {
            $movementsQuery->where('product_id', $request->input('filter_product'));
        }

        $movements = $movementsQuery->paginate(20)->withQueryString();

        $selectedProductId = $request->input('product_id');
        $selectedTipo = $request->input('tipo', 'entrada');

        return view('movements.index', compact(
            'products',
            'movements',
            'selectedProductId',
            'selectedTipo'
        ));
    }

    /**
     * Registra un nuevo movimiento de inventario garantizando trazabilidad y bloqueo de stock insuficiente.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'tipo' => ['required', 'in:entrada,salida'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'motivo' => ['nullable', 'string', 'max:255'],
        ], [
            'product_id.required' => 'Debe seleccionar un producto.',
            'product_id.exists' => 'El producto seleccionado no es válido.',
            'tipo.required' => 'Debe especificar el tipo de movimiento.',
            'tipo.in' => 'El tipo de movimiento debe ser Entrada o Salida.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad mínima a operar es de 1 unidad.',
            'motivo.max' => 'El motivo no debe superar los 255 caracteres.',
        ]);

        DB::transaction(function () use ($validated) {
            /** @var Product $product */
            $product = Product::whereKey($validated['product_id'])->lockForUpdate()->firstOrFail();
            $cantidad = (int) $validated['cantidad'];
            $stockAnterior = (int) $product->stock;

            if ($validated['tipo'] === 'salida' && $stockAnterior < $cantidad) {
                throw ValidationException::withMessages([
                    'cantidad' => "Stock insuficiente. El producto {$product->nombre} solo dispone de {$stockAnterior} unidades.",
                ]);
            }

            $stockPosterior = $validated['tipo'] === 'entrada'
                ? $stockAnterior + $cantidad
                : $stockAnterior - $cantidad;

            $product->stock = $stockPosterior;
            $product->save();

            StockMovement::create([
                'product_id' => $product->id,
                'tipo' => $validated['tipo'],
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $stockPosterior,
                'motivo' => $validated['motivo'] ?: ($validated['tipo'] === 'entrada' ? 'Entrada manual de existencias' : 'Salida manual de existencias'),
                'fecha' => now(),
            ]);
        });

        return redirect()->route('movements.index')
            ->with('success', 'Movimiento de stock registrado correctamente.');
    }
}
