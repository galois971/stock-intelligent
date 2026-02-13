<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ForecastService;

class ForecastServiceTest extends TestCase
{
    public function test_moving_average_forecast()
    {
        $service = new ForecastService();
        $history = [10, 12, 11, 13, 12, 14, 13];
        $forecast = $service->movingAverageForecast($history, 3);

        $this->assertIsArray($forecast);
        $this->assertCount(3, $forecast);
        foreach ($forecast as $f) {
            $this->assertIsInt($f);
            $this->assertGreaterThanOrEqual(0, $f);
        }
    }

    public function test_linear_regression_forecast()
    {
        $service = new ForecastService();
        // simple increasing series
        $history = [5, 6, 7, 8, 9];
        $forecast = $service->linearRegressionForecast($history, 2);

        $this->assertIsArray($forecast);
        $this->assertCount(2, $forecast);
        // expect positive increasing forecast
        $this->assertGreaterThanOrEqual($history[count($history)-1], $forecast[0]);
    }
}
