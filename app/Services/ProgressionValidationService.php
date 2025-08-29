<?php

namespace App\Services;

use App\Models\Validation;
use App\Models\SousAction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProgressionValidationService
{
    /**
     * Vérifie si un utilisateur peut modifier la progression d'une sous-action
     */
    public function canModifyProgression(User $user, SousAction $sousAction, int $newProgress): array
    {
        $currentProgress = $sousAction->taux_avancement;
        $isDecrease = $newProgress < $currentProgress;
        $isAdmin = $user->isAdminGeneral();
        
        Log::info('🔐 Vérification permission modification progression', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'sous_action_id' => $sousAction->id,
            'sous_action_libelle' => $sousAction->libelle,
            'current_progress' => $currentProgress,
            'new_progress' => $newProgress,
            'is_decrease' => $isDecrease,
            'is_admin' => $isAdmin
        ]);
        
        // L'admin général peut tout faire
        if ($isAdmin) {
            Log::info('✅ Admin général - Modification autorisée', [
                'user_id' => $user->id,
                'reason' => 'admin_general'
            ]);
            return [
                'can_modify' => true,
                'requires_validation' => false,
                'reason' => 'admin_general'
            ];
        }
        
        // Si c'est une augmentation, autoriser
        if (!$isDecrease) {
            Log::info('✅ Augmentation de progression autorisée', [
                'user_id' => $user->id,
                'reason' => 'progression_increase'
            ]);
            return [
                'can_modify' => true,
                'requires_validation' => false,
                'reason' => 'progression_increase'
            ];
        }
        
        // Si c'est une diminution, bloquer et demander validation
        Log::info('❌ Diminution de progression bloquée - Demande de validation requise', [
            'user_id' => $user->id,
            'reason' => 'progression_decrease_requires_validation'
        ]);
        
        return [
            'can_modify' => false,
            'requires_validation' => true,
            'reason' => 'progression_decrease_requires_validation'
        ];
    }
    
    /**
     * Crée une demande de validation pour diminution de progression
     */
    public function createProgressionDecreaseValidation(SousAction $sousAction, User $requester, int $newProgress, string $reason = null): Validation
    {
        Log::info('🔐 Création demande de validation diminution progression', [
            'sous_action_id' => $sousAction->id,
            'sous_action_libelle' => $sousAction->libelle,
            'requester_id' => $requester->id,
            'requester_name' => $requester->name,
            'current_progress' => $sousAction->taux_avancement,
            'requested_progress' => $newProgress,
            'reason' => $reason
        ]);
        
        // Récupérer le responsable de l'action parente
        $actionOwner = $sousAction->action->owner ?? null;
        
        if (!$actionOwner) {
            Log::error('❌ Impossible de créer la validation - Pas de propriétaire d\'action trouvé', [
                'sous_action_id' => $sousAction->id,
                'action_id' => $sousAction->action_id ?? null
            ]);
            throw new \Exception('Propriétaire de l\'action parente non trouvé');
        }
        
        // Créer la validation
        $validation = Validation::create([
            'type' => 'progression_decrease',
            'element_type' => 'sous_action',
            'element_id' => $sousAction->id,
            'requested_by' => $requester->id,
            'validated_by' => $actionOwner->id,
            'status' => 'pending',
            'current_value' => $sousAction->taux_avancement,
            'requested_value' => $newProgress,
            'reason' => $reason,
            'requested_at' => now(),
            'expires_at' => now()->addDays(7)
        ]);
        
        Log::info('✅ Demande de validation créée avec succès', [
            'validation_id' => $validation->id,
            'sous_action_id' => $sousAction->id,
            'action_owner_id' => $actionOwner->id,
            'action_owner_name' => $actionOwner->name
        ]);
        
        // Envoyer notification au propriétaire de l'action
        $this->notifyActionOwner($sousAction, $actionOwner, $requester, $newProgress);
        
        return $validation;
    }
    
    /**
     * Applique une diminution de progression après validation
     */
    public function applyProgressionDecrease(Validation $validation): bool
    {
        try {
            DB::beginTransaction();
            
            $sousAction = SousAction::find($validation->element_id);
            if (!$sousAction) {
                Log::error('❌ Sous-action non trouvée pour la validation', [
                    'validation_id' => $validation->id,
                    'element_id' => $validation->element_id
                ]);
                return false;
            }
            
            $oldProgress = $sousAction->taux_avancement;
            $newProgress = $validation->requested_value;
            
            // Appliquer la nouvelle progression
            $sousAction->update([
                'taux_avancement' => $newProgress
            ]);
            
            // Mettre à jour les taux des parents
            $this->updateParentProgressRates($sousAction);
            
            Log::info('✅ Diminution de progression appliquée avec succès', [
                'validation_id' => $validation->id,
                'sous_action_id' => $sousAction->id,
                'old_progress' => $oldProgress,
                'new_progress' => $newProgress
            ]);
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Erreur lors de l\'application de la diminution de progression', [
                'validation_id' => $validation->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Gère la progression qui atteint 100% (la fige à 99%)
     */
    public function handleProgressionCompletion(SousAction $sousAction, User $user): bool
    {
        $currentProgress = $sousAction->taux_avancement;
        
        // Si la progression atteint ou dépasse 100%
        if ($currentProgress >= 100) {
            Log::info('🔐 Progression à 100% détectée - Figement à 99% en attente de validation', [
                'sous_action_id' => $sousAction->id,
                'sous_action_libelle' => $sousAction->libelle,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'current_progress' => $currentProgress
            ]);
            
            // Figer à 99%
            $sousAction->update([
                'taux_avancement' => 99
            ]);
            
            // Créer une demande de validation d'achèvement
            $this->createCompletionValidation($sousAction, $user);
            
            // Mettre à jour les taux des parents
            $this->updateParentProgressRates($sousAction);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Crée une validation d'achèvement (100%)
     */
    private function createCompletionValidation(SousAction $sousAction, User $requester): ?Validation
    {
        $actionOwner = $sousAction->action->owner ?? null;
        
        if (!$actionOwner) {
            Log::error('❌ Impossible de créer la validation d\'achèvement - Pas de propriétaire d\'action trouvé');
            return null;
        }
        
        $validation = Validation::create([
            'type' => 'completion',
            'element_type' => 'sous_action',
            'element_id' => $sousAction->id,
            'requested_by' => $requester->id,
            'validated_by' => $actionOwner->id,
            'status' => 'pending',
            'current_value' => 99,
            'requested_value' => 100,
            'reason' => 'Demande d\'achèvement de la sous-action',
            'requested_at' => now(),
            'expires_at' => now()->addDays(7)
        ]);
        
        Log::info('✅ Validation d\'achèvement créée', [
            'validation_id' => $validation->id,
            'sous_action_id' => $sousAction->id,
            'action_owner_id' => $actionOwner->id
        ]);
        
        return $validation;
    }
    
    /**
     * Met à jour les taux de progression des éléments parents
     */
    private function updateParentProgressRates(SousAction $sousAction): void
    {
        try {
            // Mettre à jour l'action parente
            if ($sousAction->action) {
                $actionProgress = DB::table('sous_actions')
                    ->where('action_id', $sousAction->action_id)
                    ->avg('taux_avancement');
                
                DB::table('actions')
                    ->where('id', $sousAction->action_id)
                    ->update(['taux_avancement' => round($actionProgress, 2)]);
                
                Log::info('✅ Taux de progression de l\'action parente mis à jour', [
                    'action_id' => $sousAction->action_id,
                    'new_progress' => round($actionProgress, 2)
                ]);
            }
            
            // Mettre à jour l'objectif spécifique parent
            if ($sousAction->action && $sousAction->action->objectifSpecifique) {
                $ospProgress = DB::table('actions')
                    ->where('objectif_specifique_id', $sousAction->action->objectif_specifique_id)
                    ->avg('taux_avancement');
                
                DB::table('objectif_specifiques')
                    ->where('id', $sousAction->action->objectif_specifique_id)
                    ->update(['taux_avancement' => round($ospProgress, 2)]);
                
                Log::info('✅ Taux de progression de l\'OSP parente mis à jour', [
                    'osp_id' => $sousAction->action->objectif_specifique_id,
                    'new_progress' => round($ospProgress, 2)
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la mise à jour des taux parents', [
                'error' => $e->getMessage(),
                'sous_action_id' => $sousAction->id
            ]);
        }
    }
    
    /**
     * Envoie une notification au propriétaire de l'action
     */
    private function notifyActionOwner(SousAction $sousAction, User $actionOwner, User $requester, int $newProgress): void
    {
        try {
            app(NotificationService::class)->notifyValidationRequired(
                'sous_action',
                $sousAction->id,
                $requester->name,
                $sousAction
            );
            
            Log::info('✅ Notification envoyée au propriétaire de l\'action', [
                'action_owner_id' => $actionOwner->id,
                'requester_id' => $requester->id,
                'sous_action_id' => $sousAction->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de l\'envoi de la notification', [
                'error' => $e->getMessage(),
                'action_owner_id' => $actionOwner->id
            ]);
        }
    }
}
