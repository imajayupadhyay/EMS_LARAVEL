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
        // Run auto-logout every 30 minutes
        $schedule->command('auto:logout-punches')->everyThirtyMinutes()->withoutOverlapping();

        // Run auto punch-out at 11:59 PM every day for employees who forgot to punch out
        $schedule->command('punch:auto-out --today')
            ->dailyAt('23:59')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/auto-punchout.log'));

        // Optional: Run cleanup for previous days' open punches every hour
        $schedule->command('punch:auto-out')
            ->hourly()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/auto-punchout.log'));
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