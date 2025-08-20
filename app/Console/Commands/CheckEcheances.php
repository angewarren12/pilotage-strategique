<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SousAction;
use App\Services\NotificationService;
use Carbon\Carbon;

class CheckEcheances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'echeances:check {--days=7 : Nombre de jours avant l\'échéance pour alerter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les échéances approchantes et envoyer des notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $dateLimite = Carbon::now()->addDays($days);
        
        $this->info("Vérification des échéances dans les {$days} prochains jours...");
        
        // Utiliser le service de notifications
        $notificationService = app(NotificationService::class);
        
        // Vérifier les échéances approchantes
        $notificationService->checkEcheancesApprochantes();
        
        // Vérifier les délais dépassés
        $notificationService->checkDelaisDepasses();
        
        $this->info("Vérification des échéances terminée.");
        
        return 0;
    }
}
