<?php

namespace App\Services;

class ForecastService
{
    /**
     * Moving average iterative forecast
     *
     * @param array<int, int|float> $y
     * @param int $k
     * @return array<int,int>
     */
    public function movingAverageForecast(array $y, int $k): array
    {
        $n = count($y);
        $window = max(3, (int) round($n / 4));
        $forecast = [];

        for ($i = 1; $i <= $k; $i++) {
            $slice = array_slice($y, -$window);
            $avg = $slice ? array_sum($slice) / count($slice) : 0;
            $forecast[] = (int) round($avg);
            $y[] = $avg; // iterative
        }

        return $forecast;
    }

    /**
     * Linear regression forecast
     *
     * @param array<int,int|float> $y
     * @param int $k
     * @return array<int,int>
     */
    public function linearRegressionForecast(array $y, int $k): array
    {
        $n = count($y);
        $x = range(1, $n);
        $x_mean = array_sum($x) / $n;
        $y_mean = array_sum($y) / $n;

        $num = 0; $den = 0;
        for ($i = 0; $i < $n; $i++) {
            $num += ($x[$i] - $x_mean) * ($y[$i] - $y_mean);
            $den += ($x[$i] - $x_mean) ** 2;
        }
        $slope = $den != 0 ? $num / $den : 0;
        $intercept = $y_mean - $slope * $x_mean;

        $forecast = [];
        for ($i = 1; $i <= $k; $i++) {
            $xi = $n + $i;
            $yi = $intercept + $slope * $xi;
            $forecast[] = max(0, (int) round($yi));
        }

        return $forecast;
    }
}
