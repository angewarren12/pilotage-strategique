<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SousAction;
use App\Notifications\EcheanceApprochante;
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
        
        // Récupérer les sous-actions avec échéance approchante
        $sousActions = SousAction::actif()
            ->whereNotNull('date_echeance')
            ->where('date_echeance', '<=', $dateLimite)
            ->where('date_echeance', '>=', Carbon::now())
            ->where('taux_avancement', '<', 100)
            ->with(['owner'])
            ->get();
        
        $count = 0;
        
        foreach ($sousActions as $sousAction) {
            if ($sousAction->owner) {
                $joursRestants = Carbon::now()->diffInDays($sousAction->date_echeance, false);
                
                // Envoyer la notification
                $sousAction->owner->notify(new EcheanceApprochante($sousAction, $joursRestants));
                
                $this->line("Notification envoyée pour {$sousAction->code_complet} - {$sousAction->owner->name}");
                $count++;
            }
        }
        
        $this->info("{$count} notification(s) envoyée(s) avec succès.");
        
        return 0;
    }
}
