<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Muestra las métricas analíticas, distribución por categoría y ranking de inventario.
     */
    public function index(Request $request): View
    {
        $categories = Category::orderBy('nombre')->get();
        $selectedCategoryId = $request->input('category_id');

        $query = Product::with(['category', 'supplier']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $selectedCategoryId);
        }

        $totalProducts = (clone $query)->count();
        $totalUnits = (int) (clone $query)->sum('stock');
        $totalValuation = (float) ((clone $query)->selectRaw('COALESCE(SUM(precio * stock), 0) as total')->value('total') ?? 0);
        $lowStockCount = (clone $query)->lowStock()->count();

        $topStockProducts = (clone $query)
            ->orderByDesc('stock')
            ->limit(10)
            ->get();

        // Distribución por categorías
        $allCategoriesWithProducts = Category::with('products')->orderBy('nombre')->get();
        $grandTotalUnits = (int) Product::sum('stock');

        $categoryBreakdown = $allCategoriesWithProducts->map(function (Category $cat) use ($grandTotalUnits) {
            $units = (int) $cat->products->sum('stock');
            $valuation = (float) $cat->products->sum(fn(Product $p) => $p->precio * $p->stock);
            $percentage = $grandTotalUnits > 0 ? round(($units / $grandTotalUnits) * 100, 1) : 0;

            return (object) [
                'id' => $cat->id,
                'nombre' => $cat->nombre,
                'products_count' => $cat->products->count(),
                'total_units' => $units,
                'total_stock' => $units,
                'units' => $units,
                'total_valuation' => $valuation,
                'valuation' => $valuation,
                'percentage_units' => $percentage,
            ];
        });

        return view('reports.index', compact(
            'categories',
            'selectedCategoryId',
            'totalProducts',
            'totalUnits',
            'totalValuation',
            'lowStockCount',
            'topStockProducts',
            'categoryBreakdown'
        ));
    }

    /**
     * Exporta el catálogo e inventario filtrado a formato CSV con protección anti-fórmulas.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Product::with(['category', 'supplier'])->orderBy('nombre');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $products = $query->get();

        $filename = 'reporte_inventario_techstock_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($products) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 para visualización correcta de tildes y símbolos en Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Función para mitigar inyección de fórmulas CSV (=, +, -, @)
            $sanitize = function ($value): string {
                $str = (string) $value;
                if (preg_match('/^[=+\-@\t\r]/', $str)) {
                    return "'" . $str;
                }
                return $str;
            };

            // Encabezados requeridos
            fputcsv($handle, [
                'SKU',
                'Producto',
                'Categoria',
                'Proveedor',
                'Precio Unitario (S/)',
                'Stock Actual',
                'Stock Minimo',
                'Estado',
                'Valor Estimado (S/)',
            ]);

            foreach ($products as $p) {
                $estado = 'Normal';
                if ($p->stock <= 0) {
                    $estado = 'Agotado';
                } elseif ($p->stock <= $p->stock_minimo) {
                    $estado = 'Bajo stock';
                }

                $valorEstimado = $p->precio * $p->stock;

                fputcsv($handle, [
                    $sanitize($p->sku),
                    $sanitize($p->nombre),
                    $sanitize($p->category?->nombre ?? 'Sin categoría'),
                    $sanitize($p->supplier?->nombre ?? 'Sin proveedor'),
                    number_format((float) $p->precio, 2, '.', ''),
                    $p->stock,
                    $p->stock_minimo,
                    $estado,
                    number_format($valorEstimado, 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
