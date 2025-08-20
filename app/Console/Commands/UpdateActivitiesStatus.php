<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UpdateActivitiesStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:update-status {--dry-run : Afficher les changements sans les appliquer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mettre à jour automatiquement le statut des activités selon leur date de début';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Mise à jour automatique du statut des activités...');
        
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('⚠️ Mode simulation activé - aucun changement ne sera appliqué');
        }
        
        $today = Carbon::today();
        $this->info("📅 Date de référence: {$today->format('d/m/Y')}");
        
        // Récupérer toutes les activités
        $activities = Activity::with('sousAction')->get();
        $this->info("📊 Nombre total d'activités: {$activities->count()}");
        
        $updatedCount = 0;
        $errors = [];
        
        foreach ($activities as $activity) {
            try {
                $oldStatus = $activity->statut;
                $newStatus = $activity->determinerStatutParDate();
                
                if ($oldStatus !== $newStatus) {
                    $this->line("🔄 Activité {$activity->id} ({$activity->titre}):");
                    $this->line("   📅 Date de début: {$activity->date_debut->format('d/m/Y')}");
                    $this->line("   🔄 Statut: {$oldStatus} → {$newStatus}");
                    
                    if (!$dryRun) {
                        // Mettre à jour le statut
                        DB::table('activities')
                            ->where('id', $activity->id)
                            ->update(['statut' => $newStatus]);
                        
                        // Mettre à jour l'instance locale
                        $activity->statut = $newStatus;
                        
                        // Recalculer le taux d'avancement de la sous-action parent
                        if ($activity->sousAction) {
                            try {
                                $activity->sousAction->recalculerTauxAvancement();
                                $this->line("   ✅ Sous-action {$activity->sousAction->id} mise à jour");
                            } catch (\Exception $e) {
                                $this->warn("   ⚠️ Erreur lors de la mise à jour de la sous-action: {$e->getMessage()}");
                                $errors[] = "Sous-action {$activity->sousAction->id}: {$e->getMessage()}";
                            }
                        }
                        
                        $updatedCount++;
                        $this->line("   ✅ Activité mise à jour");
                    } else {
                        $updatedCount++;
                        $this->line("   📝 Simulation: serait mise à jour");
                    }
                    
                    $this->line('');
                }
                
            } catch (\Exception $e) {
                $errorMsg = "Erreur lors de la mise à jour de l'activité {$activity->id}: {$e->getMessage()}";
                $this->error($errorMsg);
                $errors[] = $errorMsg;
            }
        }
        
        // Résumé
        $this->newLine();
        $this->info('📊 Résumé de l\'opération:');
        $this->info("   • Activités traitées: {$activities->count()}");
        $this->info("   • Activités mises à jour: {$updatedCount}");
        
        if (count($errors) > 0) {
            $this->warn("   • Erreurs: " . count($errors));
            foreach ($errors as $error) {
                $this->warn("     - {$error}");
            }
        }
        
        if ($dryRun) {
            $this->warn('⚠️ Mode simulation - aucun changement réel effectué');
            $this->info('Pour appliquer les changements, exécutez la commande sans l\'option --dry-run');
        } else {
            $this->info('✅ Mise à jour terminée avec succès!');
            
            // Log de l'opération
            Log::info('🔄 [COMMAND] Mise à jour automatique du statut des activités terminée', [
                'activities_processed' => $activities->count(),
                'activities_updated' => $updatedCount,
                'errors_count' => count($errors),
                'executed_at' => now()
            ]);
        }
        
        return 0;
    }
}
