<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\ForecastService;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    /**
     * Return historical daily consumption and a simple forecast.
     *
     * Query params:
     * - days: int (history length, default 30)
     * - forecast_days: int (default 7)
     * - method: 'ma'|'lr' (moving average or linear regression)
     */
    public function predict(Request $request, Product $product)
    {
        $days = (int) $request->query('days', 30);
        $forecastDays = (int) $request->query('forecast_days', 7);
        $method = $request->query('method', 'ma');

        // Gather daily consumption (exits) for the period
        $from = now()->subDays($days - 1)->startOfDay();

        $daily = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $from->copy()->addDays($i);
            $q = StockMovement::where('product_id', $product->id)
                ->where('type', 'exit')
                ->whereBetween('movement_date', [$date->startOfDay(), $date->endOfDay()])
                ->sum('quantity');
            $daily[] = ['date' => $date->toDateString(), 'quantity' => (int) $q];
        }

        // Prepare series for computation
        $y = array_column($daily, 'quantity');

        $service = app(ForecastService::class);
        if ($method === 'lr' && count($y) >= 2) {
            $forecast = $service->linearRegressionForecast($y, $forecastDays);
        } else {
            $forecast = $service->movingAverageForecast($y, $forecastDays);
        }

        return response()->json([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'history' => $daily,
            'forecast' => $forecast,
        ]);
    }

    // Forecast logic moved to App\Services\ForecastService
}
