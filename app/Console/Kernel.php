<?php

namespace App\Console;

use App\Jobs\ConvertImageJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->job(new ConvertImageJob())
        //     ->daily()
        //     ->at('08:00');

        //$schedule->job(new ConvertImageJob())->everyMinute();
        //$schedule->job(new ConvertImageJob())->everyTwoMinutes();
        //$schedule->job(new ConvertImageJob())->everyFiveMinutes();
        $schedule->job(new ConvertImageJob())->everyTenMinutes();
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
