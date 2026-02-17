<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run predictions daily at 02:00 (server time)
        $schedule->command('predictions:run --method=ma --days=30 --forecast_days=7')->dailyAt('02:00');

        // Check product expirations daily at 07:00 (server time)
        $schedule->command('products:check-expirations')->dailyAt('07:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
