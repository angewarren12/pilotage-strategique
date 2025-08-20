<?php

namespace App\Services;

use App\Models\Validation;
use App\Models\User;
use App\Models\Pilier;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\Action;
use App\Models\SousAction;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ValidationService
{
    /**
     * Créer une demande de validation
     */
    public function createValidationRequest(
        string $elementType,
        int $elementId,
        User $requester,
        array $validationData = [],
        int $expirationDays = 7
    ): Validation {
        // Vérifier si une validation est déjà en cours
        $existingValidation = Validation::byElement($elementType, $elementId)
            ->pending()
            ->notExpired()
            ->first();

        if ($existingValidation) {
            throw new \Exception('Une demande de validation est déjà en cours pour cet élément.');
        }

        // Créer la demande de validation
        $validation = Validation::createValidationRequest(
            $elementType,
            $elementId,
            $requester,
            $validationData,
            $expirationDays
        );

        // Notifier les validateurs potentiels
        $this->notifyValidators($validation);

        return $validation;
    }

    /**
     * Approuver une validation
     */
    public function approveValidation(Validation $validation, User $validator, string $comments = null): bool
    {
        if (!$validation->canBeValidatedBy($validator)) {
            throw new \Exception('Vous n\'êtes pas autorisé à valider cette demande.');
        }

        $success = $validation->approve($validator, $comments);

        if ($success) {
            // Appliquer les changements validés
            $this->applyValidatedChanges($validation);
            
            // Notifier le demandeur
            $this->notifyRequester($validation, 'approved');
        }

        return $success;
    }

    /**
     * Rejeter une validation
     */
    public function rejectValidation(Validation $validation, User $validator, string $reason): bool
    {
        if (!$validation->canBeValidatedBy($validator)) {
            throw new \Exception('Vous n\'êtes pas autorisé à valider cette demande.');
        }

        $success = $validation->reject($validator, $reason);

        if ($success) {
            // Notifier le demandeur
            $this->notifyRequester($validation, 'rejected');
        }

        return $success;
    }

    /**
     * Obtenir les validations en attente pour un utilisateur
     */
    public function getPendingValidationsForUser(User $user): \Illuminate\Support\Collection
    {
        return Validation::pending()
            ->notExpired()
            ->where(function($query) use ($user) {
                $query->where('requested_by', $user->id)
                      ->orWhereIn('id', $this->getValidationIdsUserCanValidate($user));
            })
            ->with(['requestedBy', 'validatedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtenir les validations expirées
     */
    public function getExpiredValidations(): \Illuminate\Support\Collection
    {
        return Validation::pending()
            ->expired()
            ->with(['requestedBy'])
            ->get();
    }

    /**
     * Nettoyer les validations expirées
     */
    public function cleanExpiredValidations(): int
    {
        $expiredValidations = $this->getExpiredValidations();
        
        foreach ($expiredValidations as $validation) {
            // Notifier le demandeur de l'expiration
            $this->notifyRequester($validation, 'expired');
            
            // Marquer comme rejetée
            $validation->update([
                'status' => 'rejected',
                'rejection_reason' => 'Demande expirée automatiquement'
            ]);
        }

        return $expiredValidations->count();
    }

    /**
     * Vérifier si un élément nécessite une validation
     * 
     * Actions critiques nécessitant une validation :
     * 
     * 1. CHANGE_OWNER - Changement de propriétaire
     *    - Impact : Affecte la responsabilité et les permissions
     *    - Validation : Propriétaire parent + Admin général
     * 
     * 2. CHANGE_BUDGET - Modification budgétaire importante
     *    - Impact : Affecte les ressources financières
     *    - Validation : Propriétaire parent + Admin général
     * 
     * 3. CHANGE_DEADLINE - Modification d'échéance
     *    - Impact : Affecte la planification et les délais
     *    - Validation : Propriétaire parent + Admin général
     * 
     * 4. CHANGE_STATUS - Changement de statut critique
     *    - Impact : Affecte l'état d'avancement
     *    - Validation : Propriétaire parent + Admin général
     * 
     * 5. DELETE_ELEMENT - Suppression d'élément
     *    - Impact : Perte de données et impact sur la hiérarchie
     *    - Validation : Propriétaire parent + Admin général
     * 
     * 6. CHANGE_PRIORITY - Changement de priorité
     *    - Impact : Affecte l'ordre d'exécution
     *    - Validation : Propriétaire parent
     * 
     * 7. CHANGE_DESCRIPTION - Modification de description majeure
     *    - Impact : Affecte la compréhension du projet
     *    - Validation : Propriétaire parent
     * 
     * 8. CHANGE_CODE - Modification du code hiérarchique
     *    - Impact : Affecte la structure et les références
     *    - Validation : Admin général uniquement
     * 
     * 9. CHANGE_COLOR - Modification de la couleur du pilier
     *    - Impact : Affecte l'identité visuelle
     *    - Validation : Propriétaire du pilier + Admin général
     * 
     * 10. BULK_UPDATE - Mise à jour en lot
     *     - Impact : Modifications multiples simultanées
     *     - Validation : Admin général uniquement
     */
    public function requiresValidation(string $elementType, int $elementId, string $action = 'update'): bool
    {
        // Logique pour déterminer si une validation est nécessaire
        $element = $this->getElementByType($elementType, $elementId);
        
        if (!$element) {
            return false;
        }

        // Actions critiques nécessitant une validation
        $criticalActions = [
            // Actions de gestion des responsabilités
            'change_owner',           // Changement de propriétaire
            'change_responsibility',  // Changement de responsabilité
            
            // Actions budgétaires
            'change_budget',          // Modification budgétaire importante
            'change_budget_allocation', // Réallocation budgétaire
            'increase_budget',        // Augmentation de budget
            'decrease_budget',        // Diminution de budget
            
            // Actions temporelles
            'change_deadline',        // Modification d'échéance
            'extend_deadline',        // Extension d'échéance
            'advance_deadline',       // Avancement d'échéance
            'change_start_date',      // Modification de date de début
            
            // Actions de statut
            'change_status',          // Changement de statut critique
            'mark_completed',         // Marquer comme terminé
            'mark_cancelled',         // Marquer comme annulé
            'mark_on_hold',           // Mettre en attente
            
            // Actions de suppression
            'delete_element',         // Suppression d'élément
            'archive_element',        // Archivage d'élément
            'bulk_delete',            // Suppression en lot
            
            // Actions de priorité et organisation
            'change_priority',        // Changement de priorité
            'change_order',           // Changement d'ordre
            'reorganize_hierarchy',   // Réorganisation hiérarchique
            
            // Actions de contenu
            'change_description',     // Modification de description majeure
            'change_objectives',      // Modification d'objectifs
            'change_scope',           // Modification de périmètre
            
            // Actions de structure
            'change_code',            // Modification du code hiérarchique
            'change_structure',       // Modification de structure
            'merge_elements',         // Fusion d'éléments
            'split_element',          // Division d'élément
            
            // Actions d'identité
            'change_color',           // Modification de la couleur du pilier
            'change_name',            // Modification du nom
            'change_identity',        // Modification d'identité
            
            // Actions en lot
            'bulk_update',            // Mise à jour en lot
            'bulk_status_change',     // Changement de statut en lot
            'bulk_owner_change',      // Changement de propriétaire en lot
            
            // Actions de sécurité
            'change_permissions',     // Modification de permissions
            'change_access_rights',   // Modification de droits d'accès
            'change_visibility',      // Modification de visibilité
        ];

        return in_array($action, $criticalActions);
    }

    /**
     * Notifier les validateurs potentiels
     */
    private function notifyValidators(Validation $validation): void
    {
        $validators = Validation::getValidatorsForElement(
            $validation->element_type,
            $validation->element_id
        );

        foreach ($validators as $validator) {
            app(NotificationService::class)->notifyValidationRequired(
                $validation->element_type,
                $validation->element_id,
                $validation->requestedBy->name ?? 'Utilisateur inconnu',
                $validation->getElement()
            );
        }
    }

    /**
     * Notifier le demandeur du résultat
     */
    private function notifyRequester(Validation $validation, string $status): void
    {
        $message = match($status) {
            'approved' => 'Votre demande de validation a été approuvée.',
            'rejected' => 'Votre demande de validation a été rejetée.',
            'expired' => 'Votre demande de validation a expiré.',
            default => 'Le statut de votre demande de validation a changé.'
        };

        // Créer une notification pour le demandeur
        app(NotificationService::class)->notifyValidationRequired(
            $validation->element_type,
            $validation->element_id,
            $validation->validatedBy->name ?? 'Système',
            $validation->getElement()
        );
    }

    /**
     * Appliquer les changements validés
     */
    private function applyValidatedChanges(Validation $validation): void
    {
        $validationData = $validation->validation_data ?? [];
        
        if (empty($validationData)) {
            return;
        }

        $element = $validation->getElement();
        
        if (!$element) {
            return;
        }

        // Appliquer les changements selon le type de validation
        switch ($validationData['action'] ?? '') {
            case 'change_owner':
                if (isset($validationData['new_owner_id'])) {
                    $element->update(['owner_id' => $validationData['new_owner_id']]);
                }
                break;
                
            case 'change_budget':
                // Logique pour les changements budgétaires
                break;
                
            case 'change_deadline':
                if (isset($validationData['new_deadline'])) {
                    $element->update(['date_echeance' => $validationData['new_deadline']]);
                }
                break;
                
            case 'change_status':
                if (isset($validationData['new_status'])) {
                    $element->update(['statut' => $validationData['new_status']]);
                }
                break;
        }
    }

    /**
     * Obtenir les IDs des validations qu'un utilisateur peut valider
     */
    private function getValidationIdsUserCanValidate(User $user): array
    {
        $validations = Validation::pending()
            ->notExpired()
            ->with(['requestedBy'])
            ->get();

        $validatableIds = [];

        foreach ($validations as $validation) {
            if ($validation->canBeValidatedBy($user)) {
                $validatableIds[] = $validation->id;
            }
        }

        return $validatableIds;
    }

    /**
     * Obtenir un élément par type et ID
     */
    private function getElementByType(string $elementType, int $elementId)
    {
        switch ($elementType) {
            case 'pilier':
                return Pilier::find($elementId);
            case 'objectif_strategique':
                return ObjectifStrategique::find($elementId);
            case 'objectif_specifique':
                return ObjectifSpecifique::find($elementId);
            case 'action':
                return Action::find($elementId);
            case 'sous_action':
                return SousAction::find($elementId);
            default:
                return null;
        }
    }

    /**
     * Obtenir les statistiques de validation
     */
    public function getValidationStats(): array
    {
        $total = Validation::count();
        $pending = Validation::pending()->count();
        $approved = Validation::approved()->count();
        $rejected = Validation::rejected()->count();
        $expired = Validation::expired()->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'expired' => $expired,
            'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0
        ];
    }

    /**
     * Exemples d'utilisation des actions critiques
     */
    
    /**
     * Exemple : Demande de changement de propriétaire
     */
    public function requestOwnerChange($elementType, $elementId, $newOwnerId, $reason = null): Validation
    {
        $element = $this->getElementByType($elementType, $elementId);
        
        if (!$element) {
            throw new \Exception('Élément non trouvé');
        }

        $validationData = [
            'action' => 'change_owner',
            'old_owner_id' => $element->owner_id,
            'new_owner_id' => $newOwnerId,
            'reason' => $reason ?? 'Changement de propriétaire demandé'
        ];

        return $this->createValidationRequest(
            $elementType,
            $elementId,
            Auth::user(),
            $validationData
        );
    }

    /**
     * Exemple : Demande de modification d'échéance
     */
    public function requestDeadlineChange($elementType, $elementId, $newDeadline, $reason = null): Validation
    {
        $element = $this->getElementByType($elementType, $elementId);
        
        if (!$element) {
            throw new \Exception('Élément non trouvé');
        }

        $validationData = [
            'action' => 'change_deadline',
            'old_deadline' => $element->date_echeance ?? 'Non définie',
            'new_deadline' => $newDeadline,
            'reason' => $reason ?? 'Modification d\'échéance demandée'
        ];

        return $this->createValidationRequest(
            $elementType,
            $elementId,
            Auth::user(),
            $validationData
        );
    }

    /**
     * Exemple : Demande de changement de statut
     */
    public function requestStatusChange($elementType, $elementId, $newStatus, $reason = null): Validation
    {
        $element = $this->getElementByType($elementType, $elementId);
        
        if (!$element) {
            throw new \Exception('Élément non trouvé');
        }

        $validationData = [
            'action' => 'change_status',
            'old_status' => $element->statut ?? 'Non défini',
            'new_status' => $newStatus,
            'reason' => $reason ?? 'Changement de statut demandé'
        ];

        return $this->createValidationRequest(
            $elementType,
            $elementId,
            Auth::user(),
            $validationData
        );
    }

    /**
     * Exemple : Demande de suppression d'élément
     */
    public function requestElementDeletion($elementType, $elementId, $reason = null): Validation
    {
        $element = $this->getElementByType($elementType, $elementId);
        
        if (!$element) {
            throw new \Exception('Élément non trouvé');
        }

        $validationData = [
            'action' => 'delete_element',
            'element_name' => $element->libelle ?? $element->name ?? 'Élément',
            'element_code' => $element->code ?? 'N/A',
            'reason' => $reason ?? 'Suppression demandée'
        ];

        return $this->createValidationRequest(
            $elementType,
            $elementId,
            Auth::user(),
            $validationData
        );
    }

    /**
     * Exemple : Demande de modification de budget
     */
    public function requestBudgetChange($elementType, $elementId, $newBudget, $reason = null): Validation
    {
        $element = $this->getElementByType($elementType, $elementId);
        
        if (!$element) {
            throw new \Exception('Élément non trouvé');
        }

        $validationData = [
            'action' => 'change_budget',
            'old_budget' => $element->budget ?? 0,
            'new_budget' => $newBudget,
            'budget_change' => $newBudget - ($element->budget ?? 0),
            'reason' => $reason ?? 'Modification budgétaire demandée'
        ];

        return $this->createValidationRequest(
            $elementType,
            $elementId,
            Auth::user(),
            $validationData
        );
    }
} 