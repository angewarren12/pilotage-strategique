<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckSousActionDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sous-actions:check-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les échéances des sous-actions et envoyer des notifications de relance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Vérification des échéances des sous-actions...');
        
        try {
            // Récupérer toutes les sous-actions avec échéance et taux < 100%
            $sousActions = DB::table('sous_actions')
                ->whereNotNull('date_echeance')
                ->where('taux_avancement', '<', 100)
                ->where('date_echeance', '>', now()) // Pas encore en retard
                ->get();

            $this->info("📋 {$sousActions->count()} sous-actions à vérifier");

            $notificationsEnvoyees = 0;

            foreach ($sousActions as $sousAction) {
                $dateEcheance = Carbon::parse($sousAction->date_echeance);
                $joursRestants = now()->diffInDays($dateEcheance, false); // false = pas de valeur absolue
                $taux = $sousAction->taux_avancement;

                // Vérifier les différents seuils de relance
                if ($this->shouldSendNotification($joursRestants, $taux, $sousAction->id)) {
                    $this->sendReminderNotification($sousAction, $joursRestants);
                    $notificationsEnvoyees++;
                }
            }

            // Vérifier les sous-actions en retard
            $sousActionsEnRetard = DB::table('sous_actions')
                ->whereNotNull('date_echeance')
                ->where('taux_avancement', '<', 100)
                ->where('date_echeance', '<', now())
                ->get();

            foreach ($sousActionsEnRetard as $sousAction) {
                $this->sendDelayNotification($sousAction);
                $notificationsEnvoyees++;
            }

            $this->info("✅ {$notificationsEnvoyees} notifications de relance envoyées");
            
            Log::info('🔔 Vérification des échéances terminée', [
                'sous_actions_verifiees' => $sousActions->count(),
                'notifications_envoyees' => $notificationsEnvoyees,
                'sous_actions_en_retard' => $sousActionsEnRetard->count()
            ]);

        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la vérification des échéances: " . $e->getMessage());
            Log::error('❌ Erreur vérification échéances', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Détermine si une notification de relance doit être envoyée
     */
    private function shouldSendNotification($joursRestants, $taux, $sousActionId): bool
    {
        // Vérifier si une notification a déjà été envoyée récemment
        $derniereNotification = DB::table('notifications')
            ->where('data->sous_action_id', $sousActionId)
            ->whereIn('type', ['reminder_1_month', 'reminder_1_week', 'reminder_3_days'])
            ->where('created_at', '>', now()->subDays(1)) // Pas plus d'une notification par jour
            ->first();

        if ($derniereNotification) {
            return false;
        }

        // Seuils de relance
        return ($joursRestants == 30) || ($joursRestants == 7) || ($joursRestants == 3);
    }

    /**
     * Envoie une notification de relance
     */
    private function sendReminderNotification($sousAction, $joursRestants)
    {
        $type = $this->getReminderType($joursRestants);
        $titre = $this->getReminderTitle($joursRestants);
        $message = $this->getReminderMessage($sousAction, $joursRestants);

        try {
            $notificationId = DB::table('notifications')->insertGetId([
                'user_id' => $sousAction->owner_id,
                'type' => $type,
                'title' => $titre,
                'message' => $message,
                'data' => json_encode([
                    'sous_action_id' => $sousAction->id,
                    'sous_action_libelle' => $sousAction->libelle,
                    'date_echeance' => $sousAction->date_echeance,
                    'taux_avancement' => $sousAction->taux_avancement,
                    'jours_restants' => $joursRestants,
                    'reminder_type' => $type
                ]),
                'read_at' => null,
                'priority' => $this->getReminderPriority($joursRestants),
                'channel' => 'database',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('✅ Notification de relance envoyée', [
                'sous_action_id' => $sousAction->id,
                'owner_id' => $sousAction->owner_id,
                'type' => $type,
                'jours_restants' => $joursRestants,
                'notification_id' => $notificationId
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur envoi notification de relance', [
                'error' => $e->getMessage(),
                'sous_action_id' => $sousAction->id
            ]);
        }
    }

    /**
     * Envoie une notification de retard
     */
    private function sendDelayNotification($sousAction)
    {
        try {
            $notificationId = DB::table('notifications')->insertGetId([
                'user_id' => $sousAction->owner_id,
                'type' => 'delay_notification',
                'title' => '🚨 SOUS-ACTION EN RETARD !',
                'message' => "Votre sous-action '{$sousAction->libelle}' est en retard depuis le " . Carbon::parse($sousAction->date_echeance)->format('d/m/Y') . ". Progression actuelle : {$sousAction->taux_avancement}%",
                'data' => json_encode([
                    'sous_action_id' => $sousAction->id,
                    'sous_action_libelle' => $sousAction->libelle,
                    'date_echeance' => $sousAction->date_echeance,
                    'taux_avancement' => $sousAction->taux_avancement,
                    'jours_de_retard' => now()->diffInDays($sousAction->date_echeance),
                    'is_delayed' => true
                ]),
                'read_at' => null,
                'priority' => 'high',
                'channel' => 'database',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('🚨 Notification de retard envoyée', [
                'sous_action_id' => $sousAction->id,
                'owner_id' => $sousAction->owner_id,
                'notification_id' => $notificationId
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur envoi notification de retard', [
                'error' => $e->getMessage(),
                'sous_action_id' => $sousAction->id
            ]);
        }
    }

    /**
     * Détermine le type de relance selon les jours restants
     */
    private function getReminderType($joursRestants): string
    {
        return match($joursRestants) {
            30 => 'reminder_1_month',
            7 => 'reminder_1_week',
            3 => 'reminder_3_days',
            default => 'reminder_general'
        };
    }

    /**
     * Détermine le titre de la relance
     */
    private function getReminderTitle($joursRestants): string
    {
        return match($joursRestants) {
            30 => '🟡 Relance - 1 mois avant échéance',
            7 => '🟠 Relance - 1 semaine avant échéance',
            3 => '🔴 Relance - 3 jours avant échéance',
            default => '📅 Relance échéance'
        };
    }

    /**
     * Détermine le message de la relance
     */
    private function getReminderMessage($sousAction, $joursRestants): string
    {
        $libelle = $sousAction->libelle;
        $taux = $sousAction->taux_avancement;
        $dateEcheance = Carbon::parse($sousAction->date_echeance)->format('d/m/Y');

        return match($joursRestants) {
            30 => "Rappel : Votre sous-action '{$libelle}' arrive à échéance dans 1 mois (le {$dateEcheance}). Progression actuelle : {$taux}%. Pensez à accélérer le travail !",
            7 => "URGENT : Votre sous-action '{$libelle}' arrive à échéance dans 1 semaine (le {$dateEcheance}). Progression actuelle : {$taux}%. Action immédiate requise !",
            3 => "CRITIQUE : Votre sous-action '{$libelle}' arrive à échéance dans 3 jours (le {$dateEcheance}). Progression actuelle : {$taux}%. Mobilisation immédiate !",
            default => "Rappel échéance pour '{$libelle}' le {$dateEcheance}. Progression : {$taux}%"
        };
    }

    /**
     * Détermine la priorité de la notification
     */
    private function getReminderPriority($joursRestants): string
    {
        return match($joursRestants) {
            30 => 'normal',
            7 => 'high',
            3 => 'urgent',
            default => 'normal'
        };
    }
}
