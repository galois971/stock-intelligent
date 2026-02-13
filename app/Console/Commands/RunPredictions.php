<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ForecastService;
use App\Models\Product;
use App\Models\Forecast;
use App\Models\StockMovement;
use Carbon\Carbon;

class RunPredictions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'predictions:run {--days=30} {--forecast_days=7} {--method=ma}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run batch forecasts for products and persist results';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $forecastDays = (int) $this->option('forecast_days');
        $method = $this->option('method');

        $service = app(ForecastService::class);

        $this->info("Running predictions: days={$days} forecast_days={$forecastDays} method={$method}");

        Product::chunk(50, function ($products) use ($service, $days, $forecastDays, $method) {
            foreach ($products as $product) {
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

                $y = array_column($daily, 'quantity');

                if ($method === 'lr' && count($y) >= 2) {
                    $forecast = $service->linearRegressionForecast($y, $forecastDays);
                } else {
                    $forecast = $service->movingAverageForecast($y, $forecastDays);
                }

                // Simple holdout evaluation: if we have more points than forecastDays,
                // train on the series without the last forecastDays and predict them,
                // then compute RMSE and MAPE against the holdout.
                $rmse = null;
                $mape = null;
                $n = count($y);
                if ($n > $forecastDays) {
                    $train = array_slice($y, 0, $n - $forecastDays);
                    $holdout = array_slice($y, $n - $forecastDays, $forecastDays);

                    if ($method === 'lr' && count($train) >= 2) {
                        $predHoldout = $service->linearRegressionForecast($train, $forecastDays);
                    } else {
                        $predHoldout = $service->movingAverageForecast($train, $forecastDays);
                    }

                    // RMSE
                    $sumSq = 0.0;
                    $count = 0;
                    $mapes = [];
                    for ($i = 0; $i < count($holdout); $i++) {
                        $actual = (float) $holdout[$i];
                        $pred = isset($predHoldout[$i]) ? (float) $predHoldout[$i] : 0.0;
                        $sumSq += ($actual - $pred) ** 2;
                        $count++;

                        if ($actual != 0.0) {
                            $mapes[] = abs(($actual - $pred) / $actual) * 100.0;
                        }
                    }

                    if ($count > 0) {
                        $rmse = sqrt($sumSq / $count);
                    }

                    if (count($mapes) > 0) {
                        $mape = array_sum($mapes) / count($mapes);
                    }
                }

                Forecast::create([
                    'product_id' => $product->id,
                    'method' => $method,
                    'history_days' => $days,
                    'forecast_days' => $forecastDays,
                    'history' => $daily,
                    'forecast' => $forecast,
                    'rmse' => $rmse,
                    'mape' => $mape,
                ]);
            }
        });

        $this->info('Predictions finished.');
        return 0;
    }
}
