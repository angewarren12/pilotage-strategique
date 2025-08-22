<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Schedule as ScheduleFacade;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configuration du planificateur de tâches
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            
            // Vérification quotidienne des échéances des sous-actions
            $schedule->command('sous-actions:check-deadlines')
                    ->daily()
                    ->at('09:00') // Exécution à 9h00 du matin
                    ->withoutOverlapping() // Éviter les exécutions simultanées
                    ->runInBackground() // Exécution en arrière-plan
                    ->appendOutputTo(storage_path('logs/sous-actions-deadlines.log')); // Log des exécutions
        });
    }
}
