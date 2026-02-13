<?php

namespace App\Http\Controllers;

use App\Models\Forecast;
use Illuminate\Http\Request;

class ForecastController extends Controller
{
    /**
     * List forecasts. Optional query params: product_id, limit
     */
    public function index(Request $request)
    {
        $q = Forecast::with('product')->orderBy('created_at', 'desc');

        if ($request->has('product_id')) {
            $q->where('product_id', (int) $request->query('product_id'));
        }

        $limit = (int) $request->query('limit', 20);
        $forecasts = $q->take($limit)->get();

        return response()->json($forecasts);
    }

    /**
     * Show a single forecast
     */
    public function show(Forecast $forecast)
    {
        $forecast->load('product');
        return response()->json($forecast);
    }

    /**
     * Web view for a forecast (chart + history)
     */
    public function view(Forecast $forecast)
    {
        $forecast->load('product');

        // Load related forecasts for the same product to allow switching runs
        $relatedForecasts = Forecast::where('product_id', $forecast->product_id)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        $methods = $relatedForecasts->pluck('method')->unique()->values();

        return view('forecasts.show', compact('forecast', 'relatedForecasts', 'methods'));
    }
}
