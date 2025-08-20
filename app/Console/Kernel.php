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
        // Mettre à jour automatiquement le statut des activités chaque jour à 00:01
        $schedule->command('activities:update-status')
                ->dailyAt('00:01')
                ->withoutOverlapping()
                ->runInBackground()
                ->appendOutputTo(storage_path('logs/activities-status-update.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}


