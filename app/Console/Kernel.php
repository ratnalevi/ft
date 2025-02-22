<?php

namespace App\Console;

use App\Console\Commands\MoveUnacknowledgedAlerts;
use App\Console\Commands\SendAlerts;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        MoveUnacknowledgedAlerts::class,
        SendAlerts::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('alerts:move-unacknowledged')->dailyAt('00:00');
        $schedule->command('sync:pos')->dailyAt('15:00');
        //        $schedule->command('alerts:abnormalities')->everyFifteenMinutes()->emailWrittenOutputTo(['alerts@flotequsa.com', 'levi@flotequsa.com']);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
