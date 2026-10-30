<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Muestra el panel principal de control y métricas del inventario.
     */
    public function index(): View
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();
        $totalUnits = (int) Product::sum('stock');
        $totalValue = (float) (Product::selectRaw('COALESCE(SUM(precio * stock), 0) as total')->value('total') ?? 0);
        $lowStockCount = Product::lowStock()->count();

        $categoryDistribution = Category::withCount('products')
            ->withSum('products', 'stock')
            ->orderBy('nombre')
            ->get();

        $lowStockProducts = Product::with(['category', 'supplier'])
            ->lowStock()
            ->orderBy('stock', 'asc')
            ->get();

        $recentMovements = StockMovement::with('product.category')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalSuppliers',
            'totalUnits',
            'totalValue',
            'lowStockCount',
            'categoryDistribution',
            'lowStockProducts',
            'recentMovements'
        ));
    }
}
