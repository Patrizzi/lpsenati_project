<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Lista proveedores con conteo de productos vinculados.
     */
    public function index(Request $request): View
    {
        $query = Supplier::withCount('products')->orderBy('nombre');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->get();

        $editingSupplier = null;
        if ($request->filled('edit')) {
            $editingSupplier = Supplier::find($request->input('edit'));
        }

        return view('suppliers.index', compact('suppliers', 'editingSupplier'));
    }

    /**
     * Registra un nuevo proveedor en el sistema.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'correo' => ['required', 'email', 'max:150'],
            'telefono' => ['required', 'string', 'max:40'],
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Debe ingresar un correo electrónico válido.',
            'telefono.required' => 'El teléfono de contacto es obligatorio.',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor registrado correctamente.');
    }

    /**
     * Actualiza la información de un proveedor.
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'correo' => ['required', 'email', 'max:150'],
            'telefono' => ['required', 'string', 'max:40'],
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Debe ingresar un correo electrónico válido.',
            'telefono.required' => 'El teléfono de contacto es obligatorio.',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    /**
     * Elimina un proveedor si no tiene productos asociados.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->products()->exists()) {
            return redirect()->route('suppliers.index')
                ->with('error', 'No se puede eliminar el proveedor porque tiene productos asociados.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
