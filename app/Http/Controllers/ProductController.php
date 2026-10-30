<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /**
     * Lista productos con filtros de búsqueda, categoría y stock bajo.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'supplier'])->orderBy('nombre');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->boolean('low_stock') || $request->input('low_stock') === '1') {
            $query->lowStock();
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('nombre')->get();
        $suppliers = Supplier::orderBy('nombre')->get();

        $editingProduct = null;
        if ($request->filled('edit')) {
            $editingProduct = Product::find($request->input('edit'));
        }

        return view('products.index', compact(
            'products',
            'categories',
            'suppliers',
            'editingProduct'
        ));
    }

    /**
     * Registra un nuevo producto en el catálogo e inicializa su movimiento de inventario si aplica.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:180'],
            'sku' => ['required', 'string', 'max:64', 'unique:products,sku'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
        ], [
            'sku.unique' => 'El código SKU ingresado ya se encuentra registrado en el sistema.',
            'category_id.exists' => 'La categoría seleccionada no es válida.',
            'supplier_id.exists' => 'El proveedor seleccionado no es válido.',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::create($validated);

            if ($product->stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'tipo' => 'entrada',
                    'cantidad' => $product->stock,
                    'stock_anterior' => 0,
                    'stock_posterior' => $product->stock,
                    'motivo' => 'Inventario inicial al registrar producto',
                    'fecha' => now(),
                ]);
            }
        });

        return redirect()->route('products.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    /**
     * Actualiza la información de un producto y registra ajustes de stock para trazabilidad.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:180'],
            'sku' => ['required', 'string', 'max:64', Rule::unique('products', 'sku')->ignore($product->id)],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
        ], [
            'sku.unique' => 'El código SKU ingresado ya pertenece a otro producto.',
            'category_id.exists' => 'La categoría seleccionada no es válida.',
            'supplier_id.exists' => 'El proveedor seleccionado no es válido.',
        ]);

        DB::transaction(function () use ($product, $validated) {
            $oldStock = (int) $product->stock;
            $newStock = (int) $validated['stock'];

            if ($newStock !== $oldStock) {
                $diff = abs($newStock - $oldStock);
                $tipo = $newStock > $oldStock ? 'entrada' : 'salida';

                StockMovement::create([
                    'product_id' => $product->id,
                    'tipo' => $tipo,
                    'cantidad' => $diff,
                    'stock_anterior' => $oldStock,
                    'stock_posterior' => $newStock,
                    'motivo' => 'Ajuste manual de existencias al editar producto',
                    'fecha' => now(),
                ]);
            }

            $product->update($validated);
        });

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Elimina un producto del catálogo.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Descarga una plantilla CSV de ejemplo para la importación masiva.
     */
    public function templateCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_productos.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['nombre', 'sku', 'categoria', 'proveedor', 'precio', 'stock', 'stock_minimo']);
            fputcsv($handle, ['Monitor Gamer ASUS 27" 165Hz', 'MON-00001-01', 'Periféricos', 'Tottus', '999.00', '15', '5']);
            fputcsv($handle, ['Laptop ASUS ROG Strix', 'COM-00004-04', 'Computadoras', 'Falabella', '5499.00', '8', '3']);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Importa masivamente productos desde un archivo CSV con validación estricta fila por fila.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'max:2048', function ($attribute, $value, $fail) {
                $ext = strtolower($value->getClientOriginalExtension());
                if (!in_array($ext, ['csv', 'txt'], true)) {
                    $fail('El archivo debe tener extensión .csv o .txt.');
                }
            }],
        ], [
            'csv_file.required' => 'Debe seleccionar un archivo CSV para importar.',
            'csv_file.max' => 'El archivo CSV no debe superar los 2 MB de tamaño.',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($lines)) {
            return back()->with('error', 'El archivo CSV está vacío.');
        }

        // Remover UTF-8 BOM si existe
        $lines[0] = preg_replace('/^\xEF\xBB\xBF/', '', $lines[0]);

        // Detectar delimitador (, o ;)
        $delimiter = substr_count($lines[0], ';') > substr_count($lines[0], ',') ? ';' : ',';

        $headerRow = str_getcsv($lines[0], $delimiter);
        $headerMap = [];
        foreach ($headerRow as $index => $header) {
            $clean = mb_strtolower(trim($header));
            $headerMap[$clean] = $index;
        }

        // Normalizar alias de cabeceras
        $colNombre = $headerMap['nombre'] ?? null;
        $colSku = $headerMap['sku'] ?? null;
        $colCategoria = $headerMap['categoria'] ?? ($headerMap['category'] ?? null);
        $colProveedor = $headerMap['proveedor'] ?? ($headerMap['supplier'] ?? null);
        $colPrecio = $headerMap['precio'] ?? ($headerMap['price'] ?? null);
        $colStock = $headerMap['stock'] ?? null;
        $colMinimo = $headerMap['stock_minimo'] ?? ($headerMap['minimo'] ?? ($headerMap['stock_min'] ?? null));

        $missingHeaders = [];
        if ($colNombre === null) $missingHeaders[] = 'nombre';
        if ($colSku === null) $missingHeaders[] = 'sku';
        if ($colPrecio === null) $missingHeaders[] = 'precio';
        if ($colStock === null) $missingHeaders[] = 'stock';

        if (!empty($missingHeaders)) {
            return back()->with('error', 'El archivo CSV no contiene las cabeceras requeridas: ' . implode(', ', $missingHeaders) . '.');
        }

        // Cargar categorías y proveedores existentes en memoria para búsqueda rápida
        $existingCategories = Category::all()->keyBy(fn($c) => mb_strtolower(trim($c->nombre)));
        $existingSuppliers = Supplier::all()->keyBy(fn($s) => mb_strtolower(trim($s->nombre)));
        $existingSkus = Product::pluck('sku')->map(fn($s) => mb_strtoupper(trim($s)))->flip()->toArray();

        $seenSkusInFile = [];
        $validRows = [];
        $errors = [];
        $readCount = 0;

        for ($i = 1; $i < count($lines); $i++) {
            $rawLine = trim($lines[$i]);
            if ($rawLine === '') {
                continue;
            }

            $readCount++;
            $rowNum = $i + 1; // Línea en el archivo (1-indexed)
            $row = str_getcsv($rawLine, $delimiter);

            $nombre = isset($row[$colNombre]) ? trim($row[$colNombre]) : '';
            $sku = isset($row[$colSku]) ? mb_strtoupper(trim($row[$colSku])) : '';
            $rawCat = $colCategoria !== null && isset($row[$colCategoria]) ? trim($row[$colCategoria]) : '';
            $rawSup = $colProveedor !== null && isset($row[$colProveedor]) ? trim($row[$colProveedor]) : '';
            $rawPrecio = isset($row[$colPrecio]) ? trim($row[$colPrecio]) : '';
            $rawStock = isset($row[$colStock]) ? trim($row[$colStock]) : '';
            $rawMinimo = $colMinimo !== null && isset($row[$colMinimo]) ? trim($row[$colMinimo]) : '5';

            $rowErrors = [];

            // Validación de Nombre
            if ($nombre === '') {
                $rowErrors[] = 'el nombre es obligatorio';
            } elseif (mb_strlen($nombre) > 180) {
                $rowErrors[] = 'el nombre no debe exceder 180 caracteres';
            }

            // Validación de SKU
            if ($sku === '') {
                $rowErrors[] = 'el SKU es obligatorio';
            } elseif (mb_strlen($sku) > 64) {
                $rowErrors[] = 'el SKU no debe exceder 64 caracteres';
            } elseif (isset($seenSkusInFile[$sku])) {
                $rowErrors[] = "el SKU '{$sku}' está duplicado en el mismo archivo";
            } elseif (isset($existingSkus[$sku])) {
                $rowErrors[] = "el SKU '{$sku}' ya existe en el sistema";
            }

            // Validación de Precio
            if ($rawPrecio === '' || !is_numeric($rawPrecio) || (float) $rawPrecio < 0) {
                $rowErrors[] = 'el precio debe ser un número mayor o igual a 0';
            }

            // Validación de Stock
            if ($rawStock === '' || !is_numeric($rawStock) || (int) $rawStock < 0 || (string) (int) $rawStock !== (string) $rawStock) {
                $rowErrors[] = 'el stock debe ser un entero mayor o igual a 0';
            }

            // Validación de Stock Mínimo
            if ($rawMinimo === '') {
                $rawMinimo = 5;
            } elseif (!is_numeric($rawMinimo) || (int) $rawMinimo < 0 || (string) (int) $rawMinimo !== (string) $rawMinimo) {
                $rowErrors[] = 'el stock mínimo debe ser un entero mayor o igual a 0';
            }

            // Validación de Categoría
            $catId = null;
            if ($rawCat === '') {
                $rowErrors[] = 'la categoría es obligatoria';
            } else {
                $catKey = mb_strtolower($rawCat);
                if ($existingCategories->has($catKey)) {
                    $catId = $existingCategories->get($catKey)->id;
                } elseif (is_numeric($rawCat) && Category::where('id', $rawCat)->exists()) {
                    $catId = (int) $rawCat;
                } else {
                    $rowErrors[] = "la categoría '{$rawCat}' no existe en el sistema";
                }
            }

            // Validación de Proveedor
            $supId = null;
            if ($rawSup === '') {
                $rowErrors[] = 'el proveedor es obligatorio';
            } else {
                $supKey = mb_strtolower($rawSup);
                if ($existingSuppliers->has($supKey)) {
                    $supId = $existingSuppliers->get($supKey)->id;
                } elseif (is_numeric($rawSup) && Supplier::where('id', $rawSup)->exists()) {
                    $supId = (int) $rawSup;
                } else {
                    $rowErrors[] = "el proveedor '{$rawSup}' no existe en el sistema";
                }
            }

            if (!empty($rowErrors)) {
                $errors[] = "Fila {$rowNum}: " . implode(', ', $rowErrors) . '.';
            } else {
                $seenSkusInFile[$sku] = true;
                $validRows[] = [
                    'nombre' => $nombre,
                    'sku' => $sku,
                    'category_id' => $catId,
                    'supplier_id' => $supId,
                    'precio' => (float) $rawPrecio,
                    'stock' => (int) $rawStock,
                    'stock_minimo' => (int) $rawMinimo,
                ];
            }
        }

        if (!empty($validRows)) {
            DB::transaction(function () use ($validRows) {
                foreach ($validRows as $row) {
                    $product = Product::create($row);

                    if ($product->stock > 0) {
                        StockMovement::create([
                            'product_id' => $product->id,
                            'tipo' => 'entrada',
                            'cantidad' => $product->stock,
                            'stock_anterior' => 0,
                            'stock_posterior' => $product->stock,
                            'motivo' => 'Inventario inicial por importación CSV',
                            'fecha' => now(),
                        ]);
                    }
                }
            });
        }

        $importedCount = count($validRows);
        $rejectedCount = count($errors);

        $summary = [
            'read' => $readCount,
            'imported' => $importedCount,
            'rejected' => $rejectedCount,
            'errors' => $errors,
            'total_filas' => $readCount,
            'total' => $readCount,
            'importadas' => $importedCount,
            'rechazadas' => $rejectedCount,
            'errores' => $errors,
        ];

        session()->flash('import_summary', $summary);

        if ($rejectedCount === 0 && $importedCount > 0) {
            return back()->with('success', "Importación completada: se importaron {$importedCount} productos exitosamente.");
        }

        if ($importedCount > 0 && $rejectedCount > 0) {
            return back()->with('success', "Importación parcial: {$importedCount} productos importados y {$rejectedCount} filas rechazadas.");
        }

        return back()->with('error', "No se importó ningún producto. Se detectaron {$rejectedCount} errores en el archivo.");
    }
}
