<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class AlertController extends Controller
{
    /**
     * Muestra la bandeja de alertas tempranas para productos con stock crítico o bajo el mínimo.
     */
    public function index(): View
    {
        $alertProducts = Product::with(['category', 'supplier'])
            ->lowStock()
            ->orderByRaw('(stock_minimo - stock) DESC')
            ->orderBy('stock', 'ASC')
            ->get();

        $lowStockProducts = $alertProducts;
        $totalAlerts = $alertProducts->count();
        $criticalCount = $alertProducts->where('stock', '<=', 0)->count();
        $totalDeficit = $alertProducts->sum(fn(Product $p) => max(0, $p->stock_minimo - $p->stock));

        return view('alerts.index', compact(
            'alertProducts',
            'lowStockProducts',
            'criticalCount',
            'totalAlerts',
            'totalDeficit'
        ));
    }
}
