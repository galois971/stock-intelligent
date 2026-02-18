<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockAlert;
use App\Models\StockMovement;
use App\Models\Forecast;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord principal avec les KPIs
     */
    public function index()
    {
        // KPIs principaux
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $activeAlerts = StockAlert::where('resolved', false)->count();

        // Valeur financière du stock
        $stockValue = Product::get()->sum(function ($product) {
            return $product->currentStock() * $product->price;
        });

        // Produits proches de la rupture
        $lowStockProducts = Product::get()->filter(function ($product) {
            return $product->currentStock() < $product->stock_min;
        })->take(10);

        // Produits en rupture
        $outOfStockProducts = Product::get()->filter(function ($product) {
            return $product->currentStock() <= 0;
        })->take(10);

        // Produits expirés
        $expiredProducts = Product::expired()->take(10)->get();

        // Produits expirant bientôt
        $expiringProducts = Product::expiringWithin(30)->take(10)->get();

        // Mouvements récents
        $recentMovements = StockMovement::with(['product', 'user'])
            ->where('movement_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('movement_date', 'desc')
            ->take(10)
            ->get();

        // Statistiques des mouvements par type
        $movementsByType = StockMovement::select('type', DB::raw('SUM(quantity) as total'))
            ->where('movement_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('type')
            ->get()
            ->pluck('total', 'type');

        // Mouvements par catégorie
        $movementsByCategory = StockMovement::join('products', 'stock_movements.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(CASE WHEN stock_movements.type = "exit" THEN stock_movements.quantity ELSE 0 END) as sorties'))
            ->where('stock_movements.movement_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('sorties', 'desc')
            ->take(10)
            ->get();

        // Évolution du stock
        $stockEvolution = $this->getStockEvolution(30);

        // Taux de rotation
        $rotationRate = $this->calculateRotationRate();

        // Prévisions
        $predictions = $this->getPredictions();
        $latestForecasts = Forecast::with('product')->orderBy('created_at', 'desc')->take(10)->get();

        // Alertes par type
        $alertsByType = StockAlert::select('alert_type', DB::raw('COUNT(*) as count'))
            ->where('is_resolved', false)
            ->groupBy('alert_type')
            ->get()
            ->pluck('count', 'alert_type');

        return view('dashboard', compact(
            'totalProducts',
            'totalCategories',
            'activeAlerts',
            'stockValue',
            'lowStockProducts',
            'outOfStockProducts',
            'expiredProducts',
            'expiringProducts',
            'recentMovements',
            'movementsByType',
            'movementsByCategory',
            'stockEvolution',
            'rotationRate',
            'predictions',
            'latestForecasts',
            'alertsByType'
        ));
    }

    /**
     * Calcule l'évolution du stock sur une période
     */
    private function getStockEvolution(int $days = 30): array
    {
        $evolution = [];
        $startDate = Carbon::now()->subDays($days);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayEnd = $date->copy()->endOfDay();

            $entries = StockMovement::where('type', 'entry')
                ->where('movement_date', '<=', $dayEnd)
                ->sum('quantity');

            $exits = StockMovement::where('type', 'exit')
                ->where('movement_date', '<=', $dayEnd)
                ->sum('quantity');

            $evolution[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('d/m'),
                'stock' => $entries - $exits,
            ];
        }

        return $evolution;
    }

    /**
     * Calcule le taux de rotation des stocks
     */
    private function calculateRotationRate(): float
    {
        $period = 30; // jours
        $exits = StockMovement::where('type', 'exit')
            ->where('movement_date', '>=', Carbon::now()->subDays($period))
            ->sum('quantity');

        $averageStock = Product::get()->avg(function ($product) {
            return $product->currentStock();
        });

        if ($averageStock == 0) {
            return 0;
        }

        return round(($exits / $averageStock) * (365 / $period), 2);
    }

    /**
     * Récupère les prédictions pour les produits
     */
    private function getPredictions(): array
    {
        $products = Product::take(5)->get();
        $predictions = [];

        foreach ($products as $product) {
            $currentStock = $product->currentStock();
            $stockMin = $product->stock_min;

            if ($currentStock < $stockMin) {
                $suggestedOrder = max(
                    $stockMin - $currentStock,
                    $product->stock_optimal - $currentStock
                );

                $predictions[] = [
                    'product' => $product,
                    'current_stock' => $currentStock,
                    'stock_min' => $stockMin,
                    'suggested_order' => $suggestedOrder,
                    'risk' => 'high',
                ];
            }
        }

        return $predictions;
    }
}