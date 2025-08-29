<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pilier;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\Action;
use App\Models\SousAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PilierHierarchiqueV2 extends Component
{
    use WithPagination;

    // Propriétés principales
    public $pilier;
    public $currentView = 'pilier';
    public $selectedObjectifStrategique;
    public $selectedObjectifSpecifique;
    public $selectedAction;
    public $selectedSousAction;

    // États des modals
    public $showCreateOSModal = false;
    public $showCreateOSPModal = false;
    public $showCreateActionModal = false;
    public $showCreateSousActionModal = false;
    public $showEditOSModal = false;
    public $showEditOSPModal = false;
    public $showEditActionModal = false;
    public $showEditSousActionModal = false;

    // Formulaires
    public $newOS = [
        'code' => '',
        'libelle' => '',
        'description' => '',
        'owner_id' => ''
    ];

    public $newOSP = [
        'code' => '',
        'libelle' => '',
        'description' => '',
        'owner_id' => ''
    ];

    public $newAction = [
        'code' => '',
        'libelle' => '',
        'description' => '',
        'owner_id' => '',
        'type' => 'normal'
    ];

    public $newSousAction = [
        'code' => '',
        'libelle' => '',
        'description' => '',
        'owner_id' => '',
        'date_echeance' => '',
        'taux_avancement' => 0,
        'type' => 'normal'
    ];

    // Éléments à éditer
    public $editingOS;
    public $editingOSP;
    public $editingAction;
    public $editingSousAction;
    
    // Propriétés d'édition pour OSP
    public $editOSPCode;
    public $editOSPLibelle;
    public $editOSPDescription;
    public $editOSPOwnerId;
    
    // Variable pour l'édition d'objectif spécifique
    public $objectifSpecifiqueToEdit;
    
    // Variables pour les sous-actions
    public $sousActionToEdit;

    // Navigation
    public $breadcrumb = [];

    protected $listeners = [
        'openPilierHierarchique' => 'openModal',
        'refreshComponent' => '$refresh'
    ];

    // Méthode de test pour vérifier que le composant fonctionne
    public function testComponent()
    {
        Log::info('🧪 Test du composant PilierHierarchiqueV2', [
            'user_id' => Auth::id(),
            'pilier_id' => $this->pilier?->id,
            'current_view' => $this->currentView,
            'timestamp' => now()->toISOString()
        ]);
    }
    
    // Méthode pour ouvrir le modal d'édition d'objectif spécifique
    public function editObjectifSpecifique($objectifSpecifiqueId)
    {
        Log::info('🔄 Édition d\'objectif spécifique', ['id' => $objectifSpecifiqueId]);
        
        try {
            // Récupérer l'objectif spécifique
            $this->objectifSpecifiqueToEdit = ObjectifSpecifique::findOrFail($objectifSpecifiqueId);
            
            // Vérifier les permissions
            if (!$this->canEditObjectifSpecifique($this->objectifSpecifiqueToEdit)) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Vous n\'avez pas les permissions pour modifier cet objectif spécifique.'
                ]);
                return;
            }
            
            // Ouvrir le modal
            $this->showEditOSPModal = true;
            
            Log::info('✅ Modal d\'édition OSP ouvert', [
                'osp_id' => $objectifSpecifiqueId,
                'osp_code' => $this->objectifSpecifiqueToEdit->code
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de l\'ouverture du modal d\'édition OSP', [
                'osp_id' => $objectifSpecifiqueId,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'ouverture du formulaire d\'édition.'
            ]);
        }
    }

    public function mount($pilierId = null)
    {
        Log::info('🚀 Montage du composant PilierHierarchiqueV2', [
            'pilier_id' => $pilierId,
            'user_id' => Auth::id(),
            'timestamp' => now()->toISOString()
        ]);
        
        if ($pilierId) {
            try {
                $this->pilier = Pilier::with(['owner', 'objectifsStrategiques'])->findOrFail($pilierId);
                $this->updateBreadcrumb();
                
                Log::info('✅ Pilier chargé lors du montage', [
                    'pilier_id' => $this->pilier->id,
                    'pilier_libelle' => $this->pilier->libelle
                ]);
            } catch (\Exception $e) {
                Log::error('❌ Erreur lors du montage', [
                    'error' => $e->getMessage(),
                    'pilier_id' => $pilierId
                ]);
            }
        } else {
            Log::info('ℹ️ Aucun pilier ID fourni lors du montage');
        }
    }

    public function openModal($pilierId)
    {
        try {
            Log::info('🔄 Début ouverture modal V2', ['pilier_id' => $pilierId, 'user_id' => Auth::id()]);
            
            $this->pilier = Pilier::with(['owner', 'objectifsStrategiques'])->findOrFail($pilierId);
            $this->currentView = 'pilier';
            $this->resetSelections();
            $this->updateBreadcrumb();
            
            Log::info('✅ Pilier chargé avec succès', [
                'pilier_id' => $this->pilier->id,
                'pilier_libelle' => $this->pilier->libelle,
                'pilier_code' => $this->pilier->code,
                'owner_id' => $this->pilier->owner_id,
                'objectifs_count' => $this->pilier->objectifsStrategiques->count()
            ]);
            
            $this->dispatch('open-modal', 'pilier-hierarchique-v2-modal');
            
            Log::info('🚀 Événement modal envoyé', ['modal_id' => 'pilier-hierarchique-v2-modal']);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de l\'ouverture de la modal', [
                'error' => $e->getMessage(),
                'pilier_id' => $pilierId,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('toast', 'error', 'Erreur lors du chargement du pilier');
        }
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'pilier-hierarchique-v2-modal');
        $this->resetSelections();
        $this->resetForms();
    }

    // Navigation
    public function naviguerVersObjectifStrategique($objectifId)
    {
        $this->selectedObjectifStrategique = ObjectifStrategique::with(['owner', 'objectifsSpecifiques'])->findOrFail($objectifId);
        $this->currentView = 'objectifStrategique';
        $this->updateBreadcrumb();
        Log::info('Navigation vers Objectif Stratégique', ['os_id' => $objectifId]);
    }

    public function naviguerVersObjectifSpecifique($objectifId)
    {
        $this->selectedObjectifSpecifique = ObjectifSpecifique::with(['owner', 'actions'])->findOrFail($objectifId);
        $this->currentView = 'objectifSpecifique';
        $this->updateBreadcrumb();
        Log::info('Navigation vers Objectif Spécifique', ['osp_id' => $objectifId]);
    }

    public function naviguerVersAction($actionId)
    {
        $this->selectedAction = Action::with(['owner', 'sousActions'])->findOrFail($actionId);
        $this->currentView = 'action';
        $this->updateBreadcrumb();
        Log::info('Navigation vers Action', ['action_id' => $actionId]);
    }

    public function naviguerVersSousAction($sousActionId)
    {
        $this->selectedSousAction = SousAction::with(['owner'])->findOrFail($sousActionId);
        $this->currentView = 'sousAction';
        $this->updateBreadcrumb();
        Log::info('Navigation vers Sous-Action', ['sous_action_id' => $sousActionId]);
    }

    public function retourVersPilier()
    {
        $this->currentView = 'pilier';
        $this->resetSelections();
        $this->updateBreadcrumb();
    }

    public function retourVersObjectifStrategique()
    {
        $this->currentView = 'objectifStrategique';
        $this->selectedObjectifSpecifique = null;
        $this->updateBreadcrumb();
    }

    public function retourVersObjectifSpecifique()
    {
        $this->currentView = 'objectifSpecifique';
        $this->selectedAction = null;
        $this->updateBreadcrumb();
    }

    public function retourVersAction()
    {
        $this->currentView = 'action';
        $this->selectedSousAction = null;
        $this->updateBreadcrumb();
    }

    // Gestion des formulaires
    public function openCreateOSModal()
    {
        Log::info('🚀 Ouverture modal création OS', [
            'user_id' => Auth::id(),
            'pilier_id' => $this->pilier?->id,
            'can_create' => $this->canCreateObjectifStrategique()
        ]);
        
        $this->showCreateOSModal = true;
        $this->resetNewOSForm();
        
        Log::info('✅ Modal création OS ouverte', ['show_create_os_modal' => $this->showCreateOSModal]);
        
        // Dispatcher l'événement pour ouvrir la modal
        $this->dispatch('show-create-os-modal');
    }

    // Méthodes pour l'édition
    public function setObjectifStrategiqueToEdit($objectifStrategiqueId)
    {
        try {
            Log::info('🔧 Édition Objectif Stratégique demandée', [
                'os_id' => $objectifStrategiqueId,
                'user_id' => Auth::id()
            ]);
            
            // Récupérer l'objet complet depuis l'ID
            $objectifStrategique = ObjectifStrategique::findOrFail($objectifStrategiqueId);
            
            Log::info('✅ Objectif Stratégique récupéré', [
                'os_id' => $objectifStrategique->id,
                'os_libelle' => $objectifStrategique->libelle,
                'os_code' => $objectifStrategique->code,
                'os_description' => $objectifStrategique->description,
                'os_owner_id' => $objectifStrategique->owner_id
            ]);
            
            // Assigner l'objet complet et les propriétés dédiées pour l'édition
            $this->editingObjectifStrategique = $objectifStrategique;
            $this->editOSCode = $objectifStrategique->code;
            $this->editOSLibelle = $objectifStrategique->libelle;
            $this->editOSDescription = $objectifStrategique->description;
            $this->editOSOwnerId = $objectifStrategique->owner_id;
            
            $this->showEditOSModal = true;
            
            Log::info('🚀 Modal d\'édition OS ouverte avec données', [
                'editing_os' => $this->editingOS
            ]);
            
            // Dispatcher l'événement pour ouvrir la modal
            $this->dispatch('show-edit-os-modal');
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de l\'ouverture de la modal d\'édition OS', [
                'error' => $e->getMessage(),
                'objectif_strategique_id' => $objectifStrategiqueId,
                'user_id' => Auth::id()
            ]);
            $this->dispatch('toast', 'error', 'Erreur lors du chargement de l\'objectif stratégique pour édition.');
        }
    }

    public function setObjectifSpecifiqueToEdit($objectifSpecifiqueId)
    {
        try {
            $objectifSpecifique = ObjectifSpecifique::findOrFail($objectifSpecifiqueId);
            
            Log::info('🔧 Édition Objectif Spécifique', [
                'osp_id' => $objectifSpecifique->id,
                'osp_code' => $objectifSpecifique->code,
                'osp_libelle' => $objectifSpecifique->libelle,
                'osp_description' => $objectifSpecifique->description,
                'osp_owner_id' => $objectifSpecifique->owner_id,
                'user_id' => Auth::id()
            ]);
            
            // Réinitialiser les propriétés d'édition
            $this->editingOSP = $objectifSpecifique;
            $this->showEditOSPModal = true;
            
            // Initialiser les propriétés d'édition
            $this->editOSPCode = $objectifSpecifique->code;
            $this->editOSPLibelle = $objectifSpecifique->libelle;
            $this->editOSPDescription = $objectifSpecifique->description;
            $this->editOSPOwnerId = $objectifSpecifique->owner_id;
            
            // Debug : vérifier que les propriétés sont bien définies
            Log::info('🔍 Propriétés d\'édition définies', [
                'editingOSP_id' => $this->editingOSP->id ?? 'null',
                'editOSPCode' => $this->editOSPCode ?? 'null',
                'editOSPLibelle' => $this->editOSPLibelle ?? 'null',
                'editOSPDescription' => $this->editOSPDescription ?? 'null',
                'editOSPOwnerId' => $this->editOSPOwnerId ?? 'null',
                'showEditOSPModal' => $this->showEditOSPModal
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de l\'ouverture de la modal d\'édition OSP', [
                'error' => $e->getMessage(),
                'objectif_specifique_id' => $objectifSpecifiqueId
            ]);
            $this->dispatch('toast', 'error', 'Erreur lors du chargement de l\'objectif spécifique pour édition.');
        }
    }

    public function setActionToEdit($actionId)
    {
        try {
            Log::info('🔧 Ouverture du modal d\'édition d\'action', ['action_id' => $actionId]);
            
            $action = Action::findOrFail($actionId);
            
            Log::info('🔍 Action trouvée:', [
                'action_id' => $action->id,
                'action_code' => $action->code,
                'action_libelle' => $action->libelle,
                'action_description' => $action->description,
                'action_owner_id' => $action->owner_id,
                'user_id' => Auth::id()
            ]);
            
            // Vérifier que l'action est bien un objet Eloquent
            Log::info('🔍 Type de l\'action:', [
                'type' => get_class($action),
                'is_object' => is_object($action),
                'has_attributes' => method_exists($action, 'getAttributes')
            ]);
            
            // Assigner l'action et les propriétés dédiées
            $this->editingAction = $action;
            $this->editActionCode = $action->code;
            $this->editActionLibelle = $action->libelle;
            $this->editActionDescription = $action->description;
            $this->editActionOwnerId = $action->owner_id;
            
            $this->showEditActionModal = true;
            
            Log::info('✅ Modal d\'édition d\'action ouvert:', [
                'editingAction_id' => $this->editingAction ? $this->editingAction->id : 'null',
                'editActionCode' => $this->editActionCode,
                'editActionLibelle' => $this->editActionLibelle,
                'editActionDescription' => $this->editActionDescription,
                'editActionOwnerId' => $this->editActionOwnerId,
                'showEditActionModal' => $this->showEditActionModal
            ]);
            
            // Vérifier que la propriété est bien assignée
            Log::info('🔍 Vérification de la propriété editingAction:', [
                'propriete_existe' => property_exists($this, 'editingAction'),
                'valeur_assignee' => $this->editingAction ? 'OUI' : 'NON',
                'type_valeur' => $this->editingAction ? get_class($this->editingAction) : 'null'
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de l\'ouverture de la modal d\'édition Action', [
                'error' => $e->getMessage(),
                'action_id' => $actionId
            ]);
            $this->dispatch('toast', 'error', 'Erreur lors du chargement de l\'action pour édition.');
        }
    }

    public function setSousActionToEdit($sousActionId)
    {
        try {
            $sousAction = SousAction::findOrFail($sousActionId);
            
            Log::info('🔧 Édition Sous-Action', [
                'sous_action_id' => $sousAction->id,
                'sous_action_libelle' => $sousAction->libelle,
                'user_id' => Auth::id()
            ]);
            
            // Assigner les valeurs aux propriétés dédiées pour le binding
            $this->editingSousAction = $sousAction;
            $this->editSousActionCode = $sousAction->code;
            $this->editSousActionLibelle = $sousAction->libelle;
            $this->editSousActionDescription = $sousAction->description;
            $this->editSousActionOwnerId = $sousAction->owner_id;
            $this->showEditSousActionModal = true;
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de l\'ouverture de la modal d\'édition Sous-Action', [
                'error' => $e->getMessage(),
                'sous_action_id' => $sousActionId
            ]);
            $this->dispatch('toast', 'error', 'Erreur lors du chargement de la sous-action pour édition.');
        }
    }

    public function closeCreateOSModal()
    {
        $this->showCreateOSModal = false;
        $this->resetNewOSForm();
        $this->dispatch('hide-create-os-modal');
    }

    public function closeEditOSModal()
    {
        $this->showEditOSModal = false;
        $this->editingOS = null;
        $this->dispatch('hide-edit-os-modal');
    }

    public function closeEditOSPModal()
    {
        $this->showEditOSPModal = false;
        $this->editingOSP = null;
        
        // Réinitialiser les propriétés d'édition
        $this->editOSPCode = '';
        $this->editOSPLibelle = '';
        $this->editOSPDescription = '';
        $this->editOSPOwnerId = '';
        
        $this->dispatch('hide-edit-osp-modal');
    }

    public function updateObjectifSpecifique()
    {
        if (!$this->editingOSP) {
            $this->dispatch('toast', 'error', 'Aucun objectif spécifique à modifier');
            return;
        }

        if (!$this->canEditObjectifSpecifique($this->editingOSP)) {
            $this->dispatch('toast', 'error', 'Permission refusée');
            return;
        }

        $this->validate([
            'editOSPCode' => 'required|string|max:10',
            'editOSPLibelle' => 'required|string|max:255',
            'editOSPDescription' => 'nullable|string',
            'editOSPOwnerId' => 'required|exists:users,id'
        ]);

        try {
            // Vérifier que l'utilisateur propriétaire existe
            $owner = \DB::table('users')->where('id', $this->editOSPOwnerId)->first();
            if (!$owner) {
                Log::error('❌ Utilisateur propriétaire non trouvé', ['owner_id' => $this->editOSPOwnerId]);
                $this->dispatch('toast', 'error', 'Utilisateur propriétaire non trouvé');
                return;
            }

            // Sauvegarder l'ancien propriétaire pour la notification
            $oldOwnerId = $this->editingOSP->owner_id;
            $newOwnerId = $this->editOSPOwnerId;

            // Mettre à jour l'objectif spécifique avec DB Query Builder
            $updated = \DB::table('objectif_specifiques')
                ->where('id', $this->editingOSP->id)
                ->update([
                    'code' => $this->editOSPCode,
                    'libelle' => $this->editOSPLibelle,
                    'description' => $this->editOSPDescription,
                    'owner_id' => $this->editOSPOwnerId,
                    'updated_at' => now()
                ]);

            if (!$updated) {
                Log::error('❌ Échec de la mise à jour de l\'objectif spécifique', ['osp_id' => $this->editingOSP->id]);
                $this->dispatch('toast', 'error', 'Erreur lors de la mise à jour');
                return;
            }

            // Mettre à jour l'objet local pour la suite
            $this->editingOSP->code = $this->editOSPCode;
            $this->editingOSP->libelle = $this->editOSPLibelle;
            $this->editingOSP->description = $this->editOSPDescription;
            $this->editingOSP->owner_id = $this->editOSPOwnerId;

            // Envoyer notification au nouveau propriétaire s'il est différent
            if ($oldOwnerId != $newOwnerId) {
                Log::info('📧 Envoi notification changement propriétaire OSP', [
                    'old_owner_id' => $oldOwnerId,
                    'new_owner_id' => $newOwnerId,
                    'osp_id' => $this->editingOSP->id,
                    'osp_libelle' => $this->editingOSP->libelle
                ]);

                $notificationSent = $this->sendNotification(
                    $newOwnerId,
                    'objectif_specifique_assigned',
                    'Objectif Spécifique assigné',
                    "Vous avez été assigné comme propriétaire de l'objectif spécifique : {$this->editingOSP->libelle}",
                    ['objectif_id' => $this->editingOSP->id, 'os_id' => $this->selectedObjectifStrategique->id]
                );

                if ($notificationSent) {
                    Log::info('✅ Notification changement propriétaire OSP envoyée avec succès');
                } else {
                    Log::warning('⚠️ Échec de l\'envoi de la notification changement propriétaire OSP');
                }
            }

            // Mettre à jour le taux d'avancement de l'objectif stratégique parent avec DB
            $this->updateOSPProgressWithDB($this->selectedObjectifStrategique->id);

            $this->closeEditOSPModal();
            $this->dispatch('toast', 'success', 'Objectif Spécifique modifié avec succès');
            $this->dispatch('refreshComponent');

            Log::info('✅ Objectif Spécifique mis à jour avec succès', [
                'osp_id' => $this->editingOSP->id,
                'new_code' => $this->editOSPCode,
                'new_libelle' => $this->editOSPLibelle
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur mise à jour Objectif Spécifique', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('toast', 'error', 'Erreur lors de la modification de l\'objectif spécifique');
        }
    }

    public function updateObjectifStrategique()
    {
        if (!$this->editingOS) {
            $this->dispatch('toast', 'error', 'Aucun objectif à modifier');
            return;
        }

        $this->validate([
            'editingOS.code' => 'required|string|max:10',
            'editingOS.libelle' => 'required|string|max:255',
            'editingOS.description' => 'nullable|string',
            'editingOS.owner_id' => 'required|exists:users,id'
        ]);

        try {
            // Récupérer l'objet depuis la base de données et le mettre à jour
            $objectifStrategique = ObjectifStrategique::findOrFail($this->editingOS['id']);
            $objectifStrategique->code = $this->editingOS['code'];
            $objectifStrategique->libelle = $this->editingOS['libelle'];
            $objectifStrategique->description = $this->editingOS['description'];
            $objectifStrategique->owner_id = $this->editingOS['owner_id'];
            $objectifStrategique->save();
            
            $this->pilier->updateTauxAvancement();
            $this->closeEditOSModal();
            $this->dispatch('toast', 'success', 'Objectif Stratégique mis à jour avec succès');
            $this->dispatch('refreshComponent');
            
            Log::info('✅ Objectif Stratégique mis à jour', [
                'os_id' => $this->editingOS['id'],
                'user_id' => Auth::id(),
                'new_data' => $this->editingOS
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur mise à jour Objectif Stratégique', ['error' => $e->getMessage()]);
            $this->dispatch('toast', 'error', 'Erreur lors de la mise à jour');
        }
    }

    public function openCreateOSPModal()
    {
        $this->showCreateOSPModal = true;
        $this->resetNewOSPForm();
    }

    public function closeCreateOSPModal()
    {
        $this->showCreateOSPModal = false;
        $this->resetNewOSPForm();
    }

    public function openCreateActionModal()
    {
        Log::info('🔄 Ouverture du modal de création d\'action');
        Log::info('🔍 État avant ouverture:', [
            'showCreateActionModal' => $this->showCreateActionModal,
            'user_id' => Auth::id(),
            'can_create' => $this->canCreateAction(),
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null'
        ]);
        
        $this->showCreateActionModal = true;
        $this->resetNewActionForm();
        
        Log::info('✅ État après ouverture:', [
            'showCreateActionModal' => $this->showCreateActionModal
        ]);
    }

    public function closeCreateActionModal()
    {
        $this->showCreateActionModal = false;
        $this->resetNewActionForm();
    }

    public function closeEditActionModal()
    {
        $this->showEditActionModal = false;
        $this->editingAction = null;
    }

    public function openCreateSousActionModal()
    {
        Log::info('🔄 Ouverture du modal de création de sous-action');
        $this->resetNewSousAction();
        $this->showCreateSousActionModal = true;
    }
    
    public function closeCreateSousActionModal()
    {
        Log::info('🔄 Fermeture du modal de création de sous-action');
        $this->showCreateSousActionModal = false;
        $this->resetNewSousAction();
    }
    
    public function openEditSousActionModal($sousActionId)
    {
        Log::info('🔄 Ouverture du modal d\'édition de sous-action', ['id' => $sousActionId]);
        
        try {
            $this->editingSousAction = SousAction::findOrFail($sousActionId);
            
            // Vérifier les permissions
            if (!$this->canEditSousAction($this->editingSousAction)) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Vous n\'avez pas les permissions pour modifier cette sous-action.'
                ]);
                return;
            }
            
            // Assigner les valeurs aux propriétés d'édition
            $this->editSousActionCode = $this->editingSousAction->code;
            $this->editSousActionLibelle = $this->editingSousAction->libelle;
            $this->editSousActionDescription = $this->editingSousAction->description;
            $this->editSousActionOwnerId = $this->editingSousAction->owner_id;
            $this->editSousActionType = $this->editingSousAction->type ?? 'normal';
            $this->editSousActionDateEcheance = $this->editingSousAction->date_echeance ? $this->editingSousAction->date_echeance->format('Y-m-d') : '';
            $this->editSousActionTauxAvancement = $this->editingSousAction->taux_avancement ?? 0;
            
            $this->showEditSousActionModal = true;
            
            Log::info('✅ Modal d\'édition de sous-action ouvert', [
                'sous_action_id' => $sousActionId,
                'sous_action_code' => $this->editingSousAction->code,
                'edit_properties_set' => true
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de l\'ouverture du modal d\'édition de sous-action', [
                'sous_action_id' => $sousActionId,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'ouverture du formulaire d\'édition.'
            ]);
        }
    }
    
    public function closeEditSousActionModal()
    {
        Log::info('🔄 Fermeture du modal d\'édition de sous-action');
        $this->showEditSousActionModal = false;
        $this->editingSousAction = null;
        
        // Réinitialiser les propriétés d'édition
        $this->editSousActionCode = '';
        $this->editSousActionLibelle = '';
        $this->editSousActionDescription = '';
        $this->editSousActionOwnerId = '';
        $this->editSousActionType = 'normal';
        $this->editSousActionDateEcheance = '';
        $this->editSousActionTauxAvancement = 0;
    }
    
    public function createSousAction()
    {
        Log::info('💾 Création d\'une nouvelle sous-action', [
            'code' => $this->newSousAction['code'],
            'libelle' => $this->newSousAction['libelle']
        ]);
        
        try {
            // Validation
            $this->validate([
                'newSousAction.code' => 'required|string|max:10',
                'newSousAction.libelle' => 'required|string|max:255',
                'newSousAction.owner_id' => 'required|exists:users,id',
                'newSousAction.date_echeance' => 'nullable|date|after_or_equal:today',
                'newSousAction.type' => 'required|in:normal,projet'
            ]);
            
            // Créer la sous-action avec DB Query Builder
            $sousActionId = DB::table('sous_actions')->insertGetId([
                'code' => $this->newSousAction['code'],
                'libelle' => $this->newSousAction['libelle'],
                'description' => $this->newSousAction['description'],
                'owner_id' => $this->newSousAction['owner_id'],
                'action_id' => $this->selectedAction->id,
                'type' => $this->newSousAction['type'],
                'taux_avancement' => 0, // Taux initial à 0%
                'date_echeance' => $this->newSousAction['date_echeance'] ?: null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Fermer le modal
            $this->closeCreateSousActionModal();
            
            // Rafraîchir les données de l'action sélectionnée
            $this->selectedAction = Action::with(['owner', 'sousActions'])->findOrFail($this->selectedAction->id);
            
            // Mettre à jour le taux d'avancement de l'action parent
            $this->updateActionProgressWithDB($this->selectedAction->id);
            
            // Notification de succès
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Sous-action créée avec succès !'
            ]);
            
            // Rafraîchir le composant pour afficher la nouvelle sous-action
            $this->dispatch('refreshComponent');
            
            Log::info('✅ Sous-action créée et données rafraîchies', [
                'sous_action_id' => $sousActionId,
                'code' => $this->newSousAction['code'],
                'action_id' => $this->selectedAction->id,
                'sous_actions_count' => $this->selectedAction->sousActions->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la création de la sous-action', [
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erreur lors de la création : ' . $e->getMessage()
            ]);
        }
    }
    
    public function updateSousAction()
    {
        Log::info('🚀 MÉTHODE updateSousAction APPELÉE !', [
            'sous_action_id' => $this->editingSousAction?->id,
            'editSousActionCode' => $this->editSousActionCode,
            'editSousActionLibelle' => $this->editSousActionLibelle,
            'editSousActionOwnerId' => $this->editSousActionOwnerId,
            'editSousActionDateEcheance' => $this->editSousActionDateEcheance
        ]);
        
        try {
            // Validation avec les propriétés dédiées
            $this->validate([
                'editSousActionCode' => 'required|string|max:10',
                'editSousActionLibelle' => 'required|string|max:255',
                'editSousActionOwnerId' => 'required|exists:users,id',
                'editSousActionDateEcheance' => 'nullable|date',
                'editSousActionType' => 'required|in:normal,projet'
            ]);
            
            // Vérifier les permissions
            if (!$this->editingSousAction) {
                $this->dispatch('toast', 'error', 'Aucune sous-action à modifier');
                return;
            }
            
            if (!$this->canEditSousAction($this->editingSousAction)) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Vous n\'avez pas les permissions pour modifier cette sous-action.'
                ]);
                return;
            }
            
            // Sauvegarder les modifications avec DB Query Builder
            $updated = DB::table('sous_actions')
                ->where('id', $this->editingSousAction->id)
                ->update([
                    'code' => $this->editSousActionCode,
                    'libelle' => $this->editSousActionLibelle,
                    'description' => $this->editSousActionDescription,
                    'owner_id' => $this->editSousActionOwnerId,
                    'type' => $this->editSousActionType,
                    'date_echeance' => $this->editSousActionDateEcheance ?: null,
                    'updated_at' => now()
                ]);
            
            if (!$updated) {
                Log::error('❌ Échec de la mise à jour de la sous-action', ['sous_action_id' => $this->editingSousAction->id]);
                $this->dispatch('toast', 'error', 'Erreur lors de la mise à jour');
                return;
            }
            
            // Fermer le modal
            $this->closeEditSousActionModal();
            
            // Rafraîchir les données de l'action sélectionnée
            $this->selectedAction = Action::with(['owner', 'sousActions'])->findOrFail($this->selectedAction->id);
            
            // Mettre à jour le taux d'avancement de l'action parent
            $this->updateActionProgressWithDB($this->selectedAction->id);
            
            // Notification de succès
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Sous-action mise à jour avec succès !'
            ]);
            
            // Rafraîchir le composant pour afficher les modifications
            $this->dispatch('refreshComponent');
            
            Log::info('✅ Sous-action mise à jour et données rafraîchies', [
                'sous_action_id' => $this->editingSousAction->id,
                'action_id' => $this->selectedAction->id,
                'sous_actions_count' => $this->selectedAction->sousActions->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la mise à jour de la sous-action', [
                'sous_action_id' => $this->editingSousAction?->id,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
            ]);
        }
    }
    
    private function resetNewSousAction()
    {
        $this->newSousAction = [
            'code' => '',
            'libelle' => '',
            'description' => '',
            'owner_id' => '',
            'date_echeance' => '',
            'taux_avancement' => 0,
            'type' => 'normal'
        ];
    }

    // Création
    public function createObjectifStrategique()
    {
        if (!$this->canCreateObjectifStrategique()) {
            $this->dispatch('toast', 'error', 'Permission refusée');
            return;
        }

        $this->validate([
            'newOS.code' => 'required|string|max:10',
            'newOS.libelle' => 'required|string|max:255',
            'newOS.description' => 'nullable|string',
            'newOS.owner_id' => 'required|exists:users,id'
        ]);

        try {
            // Vérifier que l'utilisateur propriétaire existe
            $owner = User::find($this->newOS['owner_id']);
            if (!$owner) {
                Log::error('❌ Utilisateur propriétaire non trouvé', ['owner_id' => $this->newOS['owner_id']]);
                $this->dispatch('toast', 'error', 'Utilisateur propriétaire non trouvé');
                return;
            }
            
            Log::info('👤 Propriétaire trouvé', [
                'owner_id' => $owner->id,
                'owner_name' => $owner->name,
                'owner_email' => $owner->email
            ]);
            
            $objectifStrategique = new ObjectifStrategique();
            $objectifStrategique->pilier_id = $this->pilier->id;
            $objectifStrategique->code = $this->newOS['code'];
            $objectifStrategique->libelle = $this->newOS['libelle'];
            $objectifStrategique->description = $this->newOS['description'];
            $objectifStrategique->owner_id = $this->newOS['owner_id'];
            $objectifStrategique->taux_avancement = 0; // Valeur par défaut, calculée automatiquement
            $objectifStrategique->save();

            // Envoyer notification au propriétaire
            Log::info('📧 Envoi notification au propriétaire', [
                'owner_id' => $this->newOS['owner_id'],
                'os_id' => $objectifStrategique->id,
                'os_libelle' => $objectifStrategique->libelle,
                'pilier_id' => $this->pilier->id
            ]);
            
            $notificationSent = $this->sendNotification(
                $this->newOS['owner_id'],
                'objectif_strategique_assigned',
                'Nouvel Objectif Stratégique assigné',
                "Vous avez été assigné comme propriétaire de l'objectif stratégique : {$objectifStrategique->libelle}",
                ['objectif_id' => $objectifStrategique->id, 'pilier_id' => $this->pilier->id]
            );
            
            if ($notificationSent) {
                Log::info('✅ Notification envoyée avec succès au propriétaire', [
                    'owner_id' => $this->newOS['owner_id'],
                    'os_id' => $objectifStrategique->id
                ]);
            } else {
                Log::warning('⚠️ Échec de l\'envoi de la notification', [
                    'owner_id' => $this->newOS['owner_id'],
                    'os_id' => $objectifStrategique->id
                ]);
            }

            $this->pilier->updateTauxAvancement();
            $this->closeCreateOSModal();
            $this->dispatch('toast', 'success', 'Objectif Stratégique créé avec succès');
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            Log::error('Erreur création Objectif Stratégique', ['error' => $e->getMessage()]);
            $this->dispatch('toast', 'error', 'Erreur lors de la création');
        }
    }

    public function createObjectifSpecifique()
    {
        if (!$this->canCreateObjectifSpecifique()) {
            $this->dispatch('toast', 'error', 'Permission refusée');
            return;
        }

        $this->validate([
            'newOSP.code' => 'required|string|max:10',
            'newOSP.libelle' => 'required|string|max:255',
            'newOSP.description' => 'nullable|string',
            'newOSP.owner_id' => 'required|exists:users,id'
        ]);

        try {
            // Vérifier que l'utilisateur propriétaire existe
            $owner = User::find($this->newOSP['owner_id']);
            if (!$owner) {
                Log::error('❌ Utilisateur propriétaire non trouvé', ['owner_id' => $this->newOSP['owner_id']]);
                $this->dispatch('toast', 'error', 'Utilisateur propriétaire non trouvé');
                return;
            }
            
            Log::info('👤 Propriétaire OSP trouvé', [
                'owner_id' => $owner->id,
                'owner_name' => $owner->name,
                'owner_email' => $owner->email
            ]);
            
            $objectifSpecifique = new ObjectifSpecifique();
            $objectifSpecifique->objectif_strategique_id = $this->selectedObjectifStrategique->id;
            $objectifSpecifique->code = $this->newOSP['code'];
            $objectifSpecifique->libelle = $this->newOSP['libelle'];
            $objectifSpecifique->description = $this->newOSP['description'];
            $objectifSpecifique->owner_id = $this->newOSP['owner_id'];
            $objectifSpecifique->save();

            // Envoyer notification au propriétaire
            Log::info('📧 Envoi notification OSP au propriétaire', [
                'owner_id' => $this->newOSP['owner_id'],
                'osp_id' => $objectifSpecifique->id,
                'osp_libelle' => $objectifSpecifique->libelle,
                'os_id' => $this->selectedObjectifStrategique->id
            ]);
            
            $notificationSent = $this->sendNotification(
                $this->newOSP['owner_id'],
                'objectif_specifique_assigned',
                'Nouvel Objectif Spécifique assigné',
                "Vous avez été assigné comme propriétaire de l'objectif spécifique : {$objectifSpecifique->libelle}",
                ['objectif_id' => $objectifSpecifique->id, 'os_id' => $this->selectedObjectifStrategique->id]
            );
            
            if ($notificationSent) {
                Log::info('✅ Notification OSP envoyée avec succès au propriétaire', [
                    'owner_id' => $this->newOSP['owner_id'],
                    'osp_id' => $objectifSpecifique->id
                ]);
            } else {
                Log::warning('⚠️ Échec de l\'envoi de la notification OSP', [
                    'owner_id' => $this->newOSP['owner_id'],
                    'osp_id' => $objectifSpecifique->id
                ]);
            }

            $this->selectedObjectifStrategique->updateTauxAvancement();
            $this->closeCreateOSPModal();
            $this->dispatch('toast', 'success', 'Objectif Spécifique créé avec succès');
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            Log::error('❌ Erreur création Objectif Spécifique', ['error' => $e->getMessage()]);
            $this->dispatch('toast', 'error', 'Erreur lors de la création de l\'objectif spécifique');
        }
    }

    public function createAction()
    {
        Log::info('💾 Tentative de création d\'action');
        Log::info('🔍 État avant création:', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'can_create' => $this->canCreateAction(),
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selectedObjectifStrategique' => $this->selectedObjectifStrategique ? $this->selectedObjectifStrategique->id : 'null',
            'newAction_data' => $this->newAction
        ]);
        
        if (!$this->canCreateAction()) {
            Log::warning('🚫 Permission refusée pour créer une action', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name
            ]);
            $this->dispatch('toast', 'error', 'Permission refusée');
            return;
        }

        $this->validate([
            'newAction.code' => 'required|string|max:10',
            'newAction.libelle' => 'required|string|max:255',
            'newAction.description' => 'nullable|string',
            'newAction.owner_id' => 'required|exists:users,id'
        ]);

        try {
            // Créer l'action avec DB Query Builder
            $actionId = DB::table('actions')->insertGetId([
                'objectif_specifique_id' => $this->selectedObjectifSpecifique->id,
                'code' => $this->newAction['code'],
                'libelle' => $this->newAction['libelle'],
                'description' => $this->newAction['description'],
                'owner_id' => $this->newAction['owner_id'],
                'taux_avancement' => 0, // Taux initial à 0%
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Envoyer notification au propriétaire
            $this->sendNotification(
                $this->newAction['owner_id'],
                'action_assigned',
                'Nouvelle Action assignée',
                "Vous avez été assigné comme propriétaire de l'action : {$this->newAction['libelle']}",
                ['action_id' => $actionId, 'osp_id' => $this->selectedObjectifSpecifique->id]
            );

            // Mettre à jour le taux d'avancement de l'objectif spécifique parent avec DB
            $this->updateOSPProgressWithDB($this->selectedObjectifSpecifique->id);
            $this->closeCreateActionModal();
            $this->dispatch('toast', 'success', 'Action créée avec succès');
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            Log::error('Erreur création Action', ['error' => $e->getMessage()]);
            $this->dispatch('toast', 'error', 'Erreur lors de la création');
        }
    }

    // Méthodes de suppression
    public function deleteObjectifStrategique($objectifStrategiqueId)
    {
        try {
            Log::info('🗑️ Demande de suppression Objectif Stratégique', [
                'os_id' => $objectifStrategiqueId,
                'user_id' => Auth::id()
            ]);

            $objectifStrategique = ObjectifStrategique::findOrFail($objectifStrategiqueId);

            if (!$this->canDeleteObjectifStrategique($objectifStrategique)) {
                Log::warning('🚫 Permission refusée pour supprimer OS', ['user_id' => Auth::id(), 'os_id' => $objectifStrategiqueId]);
                $this->dispatch('toast', 'error', 'Permission refusée ou objectif lié');
                return;
            }

            $objectifStrategique->delete();
            $this->pilier->updateTauxAvancement();
            $this->dispatch('toast', 'success', 'Objectif Stratégique supprimé avec succès');
            $this->dispatch('refreshComponent');
            Log::info('✅ Objectif Stratégique supprimé', ['os_id' => $objectifStrategiqueId]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur suppression Objectif Stratégique', ['error' => $e->getMessage(), 'os_id' => $objectifStrategiqueId]);
            $this->dispatch('toast', 'error', 'Erreur lors de la suppression');
        }
    }

    public function deleteObjectifSpecifique($objectifSpecifiqueId)
    {
        try {
            Log::info('🗑️ Demande de suppression Objectif Spécifique', [
                'osp_id' => $objectifSpecifiqueId,
                'user_id' => Auth::id()
            ]);

            $objectifSpecifique = ObjectifSpecifique::findOrFail($objectifSpecifiqueId);

            if (!$this->canDeleteObjectifSpecifique($objectifSpecifique)) {
                Log::warning('🚫 Permission refusée pour supprimer OSP', ['user_id' => Auth::id(), 'osp_id' => $objectifSpecifiqueId]);
                $this->dispatch('toast', 'error', 'Permission refusée ou objectif lié');
                return;
            }

            $objectifSpecifique->delete();
            $this->selectedObjectifStrategique->updateTauxAvancement();
            $this->dispatch('toast', 'success', 'Objectif Spécifique supprimé avec succès');
            $this->dispatch('refreshComponent');
            Log::info('✅ Objectif Spécifique supprimé', ['osp_id' => $objectifSpecifiqueId]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur suppression Objectif Spécifique', ['error' => $e->getMessage(), 'osp_id' => $objectifSpecifiqueId]);
            $this->dispatch('toast', 'error', 'Erreur lors de la suppression');
        }
    }

    public function updateAction()
    {
        if (!$this->editingAction) {
            $this->dispatch('toast', 'error', 'Aucune action à modifier');
            return;
        }

        if (!$this->canEditAction($this->editingAction)) {
            $this->dispatch('toast', 'error', 'Permission refusée');
            return;
        }

        $this->validate([
            'editActionCode' => 'required|string|max:10',
            'editActionLibelle' => 'required|string|max:255',
            'editActionDescription' => 'nullable|string',
            'editActionOwnerId' => 'required|exists:users,id'
        ]);

        try {
            // Sauvegarder l'ancien propriétaire pour la notification
            $oldOwnerId = $this->editingAction->owner_id;
            $newOwnerId = $this->editActionOwnerId;

            // Mettre à jour l'action avec DB Query Builder
            $updated = DB::table('actions')
                ->where('id', $this->editingAction->id)
                ->update([
                    'code' => $this->editActionCode,
                    'libelle' => $this->editActionLibelle,
                    'description' => $this->editActionDescription,
                    'owner_id' => $this->editActionOwnerId,
                    'updated_at' => now()
                ]);

            if (!$updated) {
                Log::error('❌ Échec de la mise à jour de l\'action', ['action_id' => $this->editingAction->id]);
                $this->dispatch('toast', 'error', 'Erreur lors de la mise à jour');
                return;
            }

            // Mettre à jour l'objet local pour la suite
            $this->editingAction->code = $this->editActionCode;
            $this->editingAction->libelle = $this->editActionLibelle;
            $this->editingAction->description = $this->editActionDescription;
            $this->editingAction->owner_id = $this->editActionOwnerId;

            // Envoyer notification au nouveau propriétaire s'il est différent
            if ($oldOwnerId != $newOwnerId) {
                Log::info('📧 Envoi notification changement responsable Action', [
                    'old_owner_id' => $oldOwnerId,
                    'new_owner_id' => $newOwnerId,
                    'action_id' => $this->editingAction->id,
                    'action_libelle' => $this->editingAction->libelle
                ]);

                $notificationSent = $this->sendNotification(
                    $newOwnerId,
                    'action_assigned',
                    'Action assignée',
                    "Vous avez été assigné comme responsable de l'action : {$this->editingAction->libelle}",
                    ['action_id' => $this->editingAction->id, 'osp_id' => $this->selectedObjectifSpecifique->id]
                );

                if ($notificationSent) {
                    Log::info('✅ Notification changement responsable Action envoyée avec succès');
                } else {
                    Log::warning('⚠️ Échec de l\'envoi de la notification changement responsable Action');
                }
            }

            // Mettre à jour le taux d'avancement de l'objectif spécifique parent avec DB
            $this->updateOSPProgressWithDB($this->selectedObjectifSpecifique->id);

            $this->closeEditActionModal();
            $this->dispatch('toast', 'success', 'Action modifiée avec succès');
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            Log::error('❌ Erreur mise à jour Action', ['error' => $e->getMessage()]);
            $this->dispatch('toast', 'error', 'Erreur lors de la modification de l\'action');
        }
    }

    public function deleteAction($actionId)
    {
        try {
            Log::info('🗑️ Demande de suppression Action', [
                'action_id' => $actionId,
                'user_id' => Auth::id()
            ]);

            // Vérifier les permissions avec DB
            $action = DB::table('actions')->where('id', $actionId)->first();
            
            if (!$action) {
                Log::warning('🚫 Action non trouvée', ['action_id' => $actionId]);
                $this->dispatch('toast', 'error', 'Action non trouvée');
                return;
            }

            if (!$this->canDeleteAction((object)$action)) {
                Log::warning('🚫 Permission refusée pour supprimer Action', ['user_id' => Auth::id(), 'action_id' => $actionId]);
                $this->dispatch('toast', 'error', 'Permission refusée ou action liée');
                return;
            }

            // Supprimer l'action avec DB
            $deleted = DB::table('actions')->where('id', $actionId)->delete();
            
            if (!$deleted) {
                Log::error('❌ Échec de la suppression de l\'action', ['action_id' => $actionId]);
                $this->dispatch('toast', 'error', 'Erreur lors de la suppression');
                return;
            }

            // Mettre à jour le taux d'avancement de l'objectif spécifique parent avec DB
            $this->updateOSPProgressWithDB($this->selectedObjectifSpecifique->id);
            $this->dispatch('toast', 'success', 'Action supprimée avec succès');
            $this->dispatch('refreshComponent');
            Log::info('✅ Action supprimée', ['action_id' => $actionId]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur suppression Action', ['error' => $e->getMessage(), 'action_id' => $actionId]);
            $this->dispatch('error', 'Erreur lors de la suppression');
        }
    }



    /**
     * Mise à jour rapide de la progression d'une sous-action via le slider
     */
    
    /**
     * Mise à jour optimisée du taux d'avancement d'une Action
     */
    private function updateActionProgress($action)
    {
        try {
            // Calculer le taux d'avancement basé sur les sous-actions
            $sousActions = $action->sousActions;
            if ($sousActions->count() > 0) {
                $totalProgress = $sousActions->sum('taux_avancement');
                $averageProgress = $totalProgress / $sousActions->count();
                
                $action->taux_avancement = round($averageProgress, 2);
                $action->save();
                
                Log::info('✅ Taux d\'avancement Action mis à jour', [
                    'action_id' => $action->id,
                    'new_progress' => $action->taux_avancement
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('⚠️ Erreur mise à jour Action', ['error' => $e->getMessage(), 'action_id' => $action->id]);
        }
    }

    /**
     * Mise à jour optimisée du taux d'avancement d'un Objectif Spécifique
     */
    private function updateOSPProgress($objectifSpecifique)
    {
        try {
            // Calculer le taux d'avancement basé sur les actions
            $actions = $objectifSpecifique->actions;
            if ($actions->count() > 0) {
                $totalProgress = $actions->sum('taux_avancement');
                $averageProgress = $totalProgress / $actions->count();
                
                $objectifSpecifique->taux_avancement = round($averageProgress, 2);
                $objectifSpecifique->save();
                
                Log::info('✅ Taux d\'avancement OSP mis à jour', [
                    'osp_id' => $objectifSpecifique->id,
                    'new_progress' => $objectifSpecifique->taux_avancement
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('⚠️ Erreur mise à jour OSP', ['error' => $e->getMessage(), 'osp_id' => $objectifSpecifique->id]);
        }
    }

    /**
     * Mise à jour optimisée du taux d'avancement d'un Objectif Stratégique
     */
    private function updateOSProgress($objectifStrategique)
    {
        try {
            // Calculer le taux d'avancement basé sur les objectifs spécifiques
            $objectifsSpecifiques = $objectifStrategique->objectifsSpecifiques;
            if ($objectifsSpecifiques->count() > 0) {
                $totalProgress = $objectifsSpecifiques->sum('taux_avancement');
                $averageProgress = $totalProgress / $objectifsSpecifiques->count();
                
                $objectifStrategique->taux_avancement = round($averageProgress, 2);
                $objectifStrategique->save();
                
                Log::info('✅ Taux d\'avancement OS mis à jour', [
                    'os_id' => $objectifStrategique->id,
                    'new_progress' => $objectifStrategique->taux_avancement
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('⚠️ Erreur mise à jour OS', ['error' => $e->getMessage(), 'os_id' => $objectifStrategique->id]);
        }
    }

    /**
     * Mise à jour optimisée du taux d'avancement d'un Pilier
     */
    private function updatePilierProgress($pilier)
    {
        try {
            // Calculer le taux d'avancement basé sur les objectifs stratégiques
            $objectifsStrategiques = $pilier->objectifsStrategiques;
            if ($objectifsStrategiques->count() > 0) {
                $totalProgress = $objectifsStrategiques->sum('taux_avancement');
                $averageProgress = $totalProgress / $objectifsStrategiques->count();
                
                $pilier->taux_avancement = round($averageProgress, 2);
                $pilier->save();
                
                Log::info('✅ Taux d\'avancement Pilier mis à jour', [
                    'pilier_id' => $pilier->id,
                    'new_progress' => $pilier->taux_avancement
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('⚠️ Erreur mise à jour Pilier', ['error' => $e->getMessage(), 'pilier_id' => $pilier->id]);
        }
    }

    public function deleteSousAction($sousActionId)
    {
        try {
            Log::info('🗑️ Demande de suppression Sous-Action', [
                'sous_action_id' => $sousActionId,
                'user_id' => Auth::id()
            ]);

            // Vérifier les permissions avec DB
            $sousAction = DB::table('sous_actions')->where('id', $sousActionId)->first();
            
            if (!$sousAction) {
                Log::warning('🚫 Sous-action non trouvée', ['sous_action_id' => $sousActionId]);
                $this->dispatch('toast', 'error', 'Sous-action non trouvée');
                return;
            }

            if (!$this->canDeleteSousAction((object)$sousAction)) {
                Log::warning('🚫 Permission refusée pour supprimer Sous-Action', ['user_id' => Auth::id(), 'sous_action_id' => $sousActionId]);
                $this->dispatch('toast', 'error', 'Permission refusée ou sous-action liée');
                return;
            }

            // Supprimer la sous-action avec DB
            $deleted = DB::table('sous_actions')->where('id', $sousActionId)->delete();
            
            if (!$deleted) {
                Log::error('❌ Échec de la suppression de la sous-action', ['sous_action_id' => $sousActionId]);
                $this->dispatch('toast', 'error', 'Erreur lors de la suppression');
                return;
            }

            // Mettre à jour le taux d'avancement de l'action parent avec DB
            $this->updateActionProgressWithDB($this->selectedAction->id);
            $this->dispatch('toast', 'success', 'Sous-action supprimée avec succès');
            $this->dispatch('refreshComponent');
            Log::info('✅ Sous-action supprimée', ['sous_action_id' => $sousActionId]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur suppression Sous-Action', ['error' => $e->getMessage(), 'sous_action_id' => $sousActionId]);
            $this->dispatch('toast', 'error', 'Erreur lors de la suppression');
        }
    }

    // Permissions
    public function canCreateObjectifStrategique()
    {
        $user = Auth::user();
        $isAdmin = method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral();
        
        Log::info('🔐 Vérification permission création OS', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'is_admin' => $isAdmin,
            'method_exists' => method_exists($user, 'isAdminGeneral')
        ]);
        
        return $isAdmin;
    }

    public function canCreateObjectifSpecifique()
    {
        $user = Auth::user();
        return (method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral()) || 
               ($this->selectedObjectifStrategique && $user->id == $this->selectedObjectifStrategique->owner_id);
    }

    public function canCreateAction()
    {
        $user = Auth::user();
        
        Log::info('🔐 Vérification permission création Action', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'has_isAdminGeneral' => method_exists($user, 'isAdminGeneral'),
            'is_admin' => method_exists($user, 'isAdminGeneral') ? $user->isAdminGeneral() : 'Méthode non trouvée',
            'selected_os_id' => $this->selectedObjectifStrategique ? $this->selectedObjectifStrategique->id : 'null',
            'selected_os_owner_id' => $this->selectedObjectifStrategique ? $this->selectedObjectifStrategique->owner_id : 'null',
            'selected_osp_id' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selected_osp_owner_id' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->owner_id : 'null',
            'user_is_os_owner' => $this->selectedObjectifStrategique ? ($user->id == $this->selectedObjectifStrategique->owner_id) : false,
            'user_is_osp_owner' => $this->selectedObjectifSpecifique ? ($user->id == $this->selectedObjectifSpecifique->owner_id) : false
        ]);
        
        $result = (method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral()) || 
               ($this->selectedObjectifStrategique && $user->id == $this->selectedObjectifStrategique->owner_id) ||
               ($this->selectedObjectifSpecifique && $user->id == $this->selectedObjectifSpecifique->owner_id);
               
        Log::info('🔐 Résultat permission création Action', [
            'user_id' => $user->id,
            'permission_granted' => $result
        ]);
        
        return $result;
    }

    public function canCreateSousAction()
    {
        $user = Auth::user();
        return (method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral()) || 
               ($this->selectedObjectifStrategique && $user->id == $this->selectedObjectifStrategique->owner_id) ||
               ($this->selectedObjectifSpecifique && $user->id == $this->selectedObjectifSpecifique->owner_id) ||
               ($this->selectedAction && $user->id == $this->selectedAction->owner_id);
    }

    public function canEditObjectifStrategique($objectifStrategique)
    {
        $user = Auth::user();
        return (method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral()) || $user->id == $objectifStrategique->owner_id;
    }

    public function canEditObjectifSpecifique($objectifSpecifique)
    {
        $user = Auth::user();
        return (method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral()) || 
               ($this->selectedObjectifStrategique && $user->id == $this->selectedObjectifStrategique->owner_id) ||
               $user->id == $objectifSpecifique->owner_id;
    }

    public function canEditAction($action)
    {
        $user = Auth::user();
        return (method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral()) || 
               ($this->selectedObjectifStrategique && $user->id == $this->selectedObjectifStrategique->owner_id) ||
               ($this->selectedObjectifSpecifique && $user->id == $this->selectedObjectifSpecifique->owner_id) ||
               $user->id == $action->owner_id;
    }

    public function canEditSousAction($sousAction)
    {
        $user = Auth::user();
        return (method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral()) || 
               ($this->selectedObjectifStrategique && $user->id == $this->selectedObjectifStrategique->owner_id) ||
               ($this->selectedObjectifSpecifique && $user->id == $this->selectedObjectifSpecifique->owner_id) ||
               ($this->selectedAction && $user->id == $this->selectedAction->owner_id) ||
               $user->id == $sousAction->owner_id;
    }

    public function canDeleteObjectifStrategique($objectifStrategique)
    {
        $user = Auth::user();
        $isAdmin = method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral();
        $isOwner = $user->id == $objectifStrategique->owner_id;
        
        Log::info('🔐 Vérification permission suppression OS', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'is_admin' => $isAdmin,
            'is_owner' => $isOwner,
            'os_id' => $objectifStrategique->id,
            'os_libelle' => $objectifStrategique->libelle
        ]);
        
        // L'admin peut toujours supprimer
        if ($isAdmin) {
            Log::info('✅ Admin autorisé à supprimer');
            return true;
        }
        
        // Le propriétaire peut supprimer s'il n'y a pas d'objectifs spécifiques
        if ($isOwner) {
            $hasChildren = $objectifStrategique->objectifsSpecifiques()->count() > 0;
            Log::info('🔍 Propriétaire - vérification enfants', ['has_children' => $hasChildren]);
            return !$hasChildren;
        }
        
        Log::info('❌ Permission refusée');
        return false;
    }

    public function canDeleteObjectifSpecifique($objectifSpecifique)
    {
        $user = Auth::user();
        $isAdmin = method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral();
        $isOSOwner = $this->selectedObjectifStrategique && $user->id == $this->selectedObjectifStrategique->owner_id;
        $isOSPOwner = $user->id == $objectifSpecifique->owner_id;
        
        Log::info('🔐 Vérification permission suppression OSP', [
            'user_id' => $user->id,
            'is_admin' => $isAdmin,
            'is_os_owner' => $isOSOwner,
            'is_osp_owner' => $isOSPOwner,
            'osp_id' => $objectifSpecifique->id
        ]);
        
        // L'admin peut toujours supprimer
        if ($isAdmin) {
            return true;
        }
        
        // Le propriétaire de l'OS parent ou de l'OSP peut supprimer
        if ($isOSOwner || $isOSPOwner) {
            $hasChildren = $objectifSpecifique->actions()->count() > 0;
            return !$hasChildren;
        }
        
        return false;
    }

    public function canDeleteAction($action)
    {
        $user = Auth::user();
        $isAdmin = method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral();
        $isOSOwner = $this->selectedObjectifStrategique && $user->id == $this->selectedObjectifStrategique->owner_id;
        $isOSPOwner = $this->selectedObjectifSpecifique && $user->id == $this->selectedObjectifSpecifique->owner_id;
        $isActionOwner = $user->id == $action->owner_id;
        
        Log::info('🔐 Vérification permission suppression Action', [
            'user_id' => $user->id,
            'is_admin' => $isAdmin,
            'is_os_owner' => $isOSOwner,
            'is_osp_owner' => $isOSPOwner,
            'is_action_owner' => $isActionOwner,
            'action_id' => $action->id
        ]);
        
        // L'admin peut toujours supprimer
        if ($isAdmin) {
            return true;
        }
        
        // Les propriétaires peuvent supprimer s'il n'y a pas de sous-actions
        if ($isOSOwner || $isOSPOwner || $isActionOwner) {
            $hasChildren = $action->sousActions()->count() > 0;
            return !$hasChildren;
        }
        
        return false;
    }

    public function canDeleteSousAction($sousAction)
    {
        $user = Auth::user();
        $isAdmin = method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral();
        $isOSOwner = $this->selectedObjectifStrategique && $user->id == $this->selectedObjectifStrategique->owner_id;
        $isOSPOwner = $this->selectedObjectifSpecifique && $user->id == $this->selectedObjectifSpecifique->owner_id;
        $isActionOwner = $this->selectedAction && $user->id == $this->selectedAction->owner_id;
        $isSousActionOwner = $user->id == $sousAction->owner_id;
        
        Log::info('🔐 Vérification permission suppression Sous-Action', [
            'user_id' => $user->id,
            'is_admin' => $isAdmin,
            'is_os_owner' => $isOSOwner,
            'is_osp_owner' => $isOSPOwner,
            'is_action_owner' => $isActionOwner,
            'is_sous_action_owner' => $isSousActionOwner
        ]);
        
        // L'admin peut toujours supprimer
        if ($isAdmin) {
            return true;
        }
        
        // Les propriétaires peuvent supprimer
        return $isOSOwner || $isOSPOwner || $isActionOwner || $isSousActionOwner;
    }

    // Utilitaires
    private function updateBreadcrumb()
    {
        $this->breadcrumb = [];
        
        if ($this->pilier) {
            $this->breadcrumb[] = [
                'label' => $this->pilier->code . ' - ' . $this->pilier->libelle,
                'action' => 'retourVersPilier'
            ];
        }
        
        if ($this->selectedObjectifStrategique) {
            $this->breadcrumb[] = [
                'label' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code . ' - ' . $this->selectedObjectifStrategique->libelle,
                'action' => 'retourVersObjectifStrategique'
            ];
        }
        
        if ($this->selectedObjectifSpecifique) {
            $this->breadcrumb[] = [
                'label' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code . '.' . $this->selectedObjectifSpecifique->code . ' - ' . $this->selectedObjectifSpecifique->libelle,
                'action' => 'retourVersObjectifSpecifique'
            ];
        }
        
        if ($this->selectedAction) {
            $this->breadcrumb[] = [
                'label' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code . '.' . $this->selectedObjectifSpecifique->code . '.' . $this->selectedAction->code . ' - ' . $this->selectedAction->libelle,
                'action' => 'retourVersAction'
            ];
        }
        
        if ($this->selectedSousAction) {
            $this->breadcrumb[] = [
                'label' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code . '.' . $this->selectedObjectifSpecifique->code . '.' . $this->selectedAction->code . '.' . $this->selectedSousAction->code . ' - ' . $this->selectedSousAction->libelle,
                'action' => null
            ];
        }
    }

    private function sendNotification($userId, $type, $title, $message, $data = [])
    {
        try {
            $notificationId = DB::table('notifications')->insertGetId([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => json_encode($data),
                'read_at' => null,
                'priority' => 'normal',
                'channel' => 'database',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('✅ Notification envoyée avec succès', [
                'notification_id' => $notificationId,
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message
            ]);
            
            return true;

        } catch (\Exception $e) {
            Log::error('❌ Erreur envoi notification', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'type' => $type,
                'title' => $title
            ]);
            
            return false;
        }
    }

    private function resetSelections()
    {
        $this->selectedObjectifStrategique = null;
        $this->selectedObjectifSpecifique = null;
        $this->selectedAction = null;
        $this->selectedSousAction = null;
    }

    private function resetForms()
    {
        $this->resetNewOSForm();
        $this->resetNewOSPForm();
        $this->resetNewActionForm();
        $this->resetNewSousActionForm();
    }

    private function resetNewOSForm()
    {
        $this->newOS = [
            'code' => '',
            'libelle' => '',
            'description' => '',
            'owner_id' => ''
        ];
    }

    private function resetNewOSPForm()
    {
        $this->newOSP = [
            'code' => '',
            'libelle' => '',
            'description' => '',
            'owner_id' => ''
        ];
    }

    private function resetNewActionForm()
    {
        $this->newAction = [
            'code' => '',
            'libelle' => '',
            'description' => '',
            'owner_id' => '',
            'date_echeance' => '',
            'taux_avancement' => 0,
            'type' => 'normal'
        ];
    }

    private function resetNewSousActionForm()
    {
        $this->newSousAction = [
            'code' => '',
            'libelle' => '',
            'description' => '',
            'owner_id' => '',
            'date_echeance' => '',
            'taux_avancement' => 0
        ];
    }

    public function render()
    {
        $users = User::orderBy('name')->get();
        
        // Log pour déboguer
        $user = Auth::user();
        $isAdmin = method_exists($user, 'isAdminGeneral') ? $user->isAdminGeneral() : false;
        
        Log::info('🎨 Rendu du composant PilierHierarchiqueV2', [
            'pilier_id' => $this->pilier?->id,
            'pilier_libelle' => $this->pilier?->libelle,
            'current_view' => $this->currentView,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'is_admin' => $isAdmin,
            'method_exists' => method_exists($user, 'isAdminGeneral'),
            'permissions' => [
                'can_create_os' => $this->canCreateObjectifStrategique(),
                'can_create_osp' => $this->canCreateObjectifSpecifique(),
                'can_create_action' => $this->canCreateAction(),
                'can_create_sous_action' => $this->canCreateSousAction()
            ]
        ]);
        
        return view('livewire.pilier-hierarchique-v2.index', [
            'pilier' => $this->pilier,
            'objectifsStrategiques' => $this->pilier ? $this->pilier->objectifsStrategiques : collect(),
            'canCreateOS' => fn() => $this->canCreateObjectifStrategique(),
            'canEditOS' => fn($os) => $this->canEditObjectifStrategique($os),
            'canDeleteOS' => fn($os) => $this->canDeleteObjectifStrategique($os),
            'canCreateOSP' => fn() => $this->canCreateObjectifSpecifique(),
            'canEditOSP' => fn($osp) => $this->canEditObjectifSpecifique($osp),
            'canDeleteOSP' => fn($osp) => $this->canDeleteObjectifSpecifique($osp),
            'canCreateAction' => fn() => $this->canCreateAction(),
            'canEditAction' => fn($action) => $this->canEditAction($action),
            'canDeleteAction' => fn($action) => $this->canDeleteAction($action),
            'canCreateSousAction' => fn() => $this->canCreateSousAction(),
            'canEditSousAction' => fn($sousAction) => $this->canEditSousAction($sousAction),
            'canDeleteSousAction' => fn($sousAction) => $this->canDeleteSousAction($sousAction),
            'canEditActivity' => fn($activity) => $this->canEditActivity($activity),
            'canDeleteActivity' => fn($activity) => $this->canDeleteActivity($activity),
            'users' => User::all(), // Ajout de la liste des utilisateurs
        ]);
    }

    /**
     * Mise à jour de la progression d'une sous-action avec DB Query Builder
     */
    public function updateSousActionProgress($sousActionId, $newProgress)
    {
        try {
            // Validation des paramètres
            if (!is_numeric($newProgress) || $newProgress < 0 || $newProgress > 100) {
                $this->dispatch('toast', 'error', 'Valeur de progression invalide (0-100)');
                return;
            }

            // Vérifier les permissions
            $sousAction = SousAction::findOrFail($sousActionId);
            if (!$this->canEditSousAction($sousAction)) {
                $this->dispatch('toast', 'error', 'Permission refusée');
                return;
            }

            // Vérifier le type de sous-action
            if (($sousAction->type ?? 'normal') === 'projet') {
                $this->dispatch('toast', 'error', 'Impossible de modifier manuellement la progression d\'un projet. La progression est calculée automatiquement.');
                return;
            }

            // Préparer les données de mise à jour
            $updateData = ['taux_avancement' => $newProgress];
            
            // Gestion de la date de réalisation et notification
            if ($newProgress == 100) {
                // Si exactement 100%, enregistrer la date de réalisation
                if (!$sousAction->date_realisation) {
                    $updateData['date_realisation'] = now();
                    
                    // Notifier le propriétaire de la sous-action qu'elle est terminée
                    if ($sousAction->owner_id) {
                        $this->sendNotification(
                            $sousAction->owner_id,
                            'sous_action_completed',
                            'Sous-Action terminée ! 🎉',
                            "Félicitations ! Votre sous-action '{$sousAction->libelle}' a été terminée avec succès !",
                            [
                                'sous_action_id' => $sousAction->id,
                                'sous_action_libelle' => $sousAction->libelle,
                                'completion_date' => now()->toISOString()
                            ]
                        );
                        
                        Log::info('✅ Notification de complétion envoyée au propriétaire de la sous-action', [
                            'sous_action_id' => $sousAction->id,
                            'owner_id' => $sousAction->owner_id,
                            'libelle' => $sousAction->libelle
                        ]);
                    }
                }
            } else {
                // Si différent de 100%, masquer la date de réalisation
                $updateData['date_realisation'] = null;
            }

            // Mettre à jour avec DB Query Builder
            $updated = DB::table('sous_actions')
                ->where('id', $sousActionId)
                ->update($updateData);

            if (!$updated) {
                $this->dispatch('toast', 'error', 'Erreur lors de la mise à jour');
                return;
            }

            // Mise à jour des taux parents avec DB Query Builder
            $this->updateParentProgressRates($sousAction);

            // Émettre l'événement pour mettre à jour les cercles de progression en temps réel
            $this->dispatch('progress-updated', [
                'action_progress' => $sousAction->action ? $sousAction->action->taux_avancement : null,
                'osp_progress' => $sousAction->action && $sousAction->action->objectifSpecifique ? $sousAction->action->objectifSpecifique->taux_avancement : null,
                'os_progress' => $sousAction->action && $sousAction->action->objectifSpecifique && $sousAction->action->objectifSpecifique->objectifStrategique ? $sousAction->action->objectifSpecifique->objectifStrategique->taux_avancement : null,
                'pilier_progress' => $sousAction->action && $sousAction->action->objectifSpecifique && $sousAction->action->objectifSpecifique->objectifStrategique && $sousAction->action->objectifSpecifique->objectifStrategique->pilier ? $sousAction->action->objectifSpecifique->objectifStrategique->pilier->taux_avancement : null
            ]);

            $this->dispatch('toast', 'success', 'Progression mise à jour avec succès');
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            Log::error('❌ Erreur mise à jour progression', [
                'error' => $e->getMessage(),
                'sous_action_id' => $sousActionId
            ]);
            
            $this->dispatch('toast', 'error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Calcul automatique de la progression d'un projet
     * Cette méthode sera appelée automatiquement pour les sous-actions de type "projet"
     */
    public function calculateProjectProgress($sousActionId)
    {
        try {
            $sousAction = SousAction::findOrFail($sousActionId);
            
            // Vérifier que c'est bien un projet
            if (($sousAction->type ?? 'normal') !== 'projet') {
                Log::warning('⚠️ Tentative de calcul de progression sur une sous-action non-projet', [
                    'sous_action_id' => $sousActionId,
                    'type' => $sousAction->type
                ]);
                return;
            }

            // Logique de calcul automatique de la progression
            // Pour l'instant, on peut utiliser une logique simple
            // À adapter selon vos besoins métier
            
            $newProgress = $this->calculateAutomaticProgress($sousAction);
            
            if ($newProgress !== null && $newProgress != $sousAction->taux_avancement) {
                // Mettre à jour la progression
                $updateData = ['taux_avancement' => $newProgress];
                
                // Gestion de la date de réalisation et notification
                if ($newProgress == 100) {
                    if (!$sousAction->date_realisation) {
                        $updateData['date_realisation'] = now();
                        
                        // Notifier le propriétaire de la sous-action projet qu'elle est terminée
                        if ($sousAction->owner_id) {
                            $this->sendNotification(
                                $sousAction->owner_id,
                                'projet_completed',
                                'Projet terminé ! 🎯',
                                "Félicitations ! Votre projet '{$sousAction->libelle}' a été terminé avec succès !",
                                [
                                    'sous_action_id' => $sousAction->id,
                                    'sous_action_libelle' => $sousAction->libelle,
                                    'completion_date' => now()->toISOString(),
                                    'type' => 'projet'
                                ]
                            );
                            
                            Log::info('✅ Notification de complétion de projet envoyée au propriétaire', [
                                'sous_action_id' => $sousAction->id,
                                'owner_id' => $sousAction->owner_id,
                                'libelle' => $sousAction->libelle,
                                'type' => 'projet'
                            ]);
                        }
                    }
                } else {
                    $updateData['date_realisation'] = null;
                }

                // Mise à jour avec DB Query Builder
                $updated = DB::table('sous_actions')
                    ->where('id', $sousActionId)
                    ->update($updateData);

                if ($updated) {
                    // Mise à jour des taux parents
                    $this->updateParentProgressRates($sousAction);
                    
                    Log::info('✅ Progression automatique du projet calculée', [
                        'sous_action_id' => $sousActionId,
                        'ancien_taux' => $sousAction->taux_avancement,
                        'nouveau_taux' => $newProgress
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('❌ Erreur lors du calcul automatique de la progression du projet', [
                'sous_action_id' => $sousActionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Logique de calcul automatique de la progression
     * À personnaliser selon vos besoins métier
     */
    private function calculateAutomaticProgress($sousAction)
    {
        // Exemple de logique : progression basée sur la date d'échéance
        if ($sousAction->date_echeance) {
            $now = now();
            $echeance = \Carbon\Carbon::parse($sousAction->date_echeance);
            $creation = \Carbon\Carbon::parse($sousAction->created_at);
            
            $totalDays = $creation->diffInDays($echeance);
            $elapsedDays = $creation->diffInDays($now);
            
            if ($totalDays > 0) {
                $progress = min(100, max(0, ($elapsedDays / $totalDays) * 100));
                return round($progress, 2);
            }
        }
        
        // Si pas de date d'échéance, retourner la progression actuelle
        return $sousAction->taux_avancement;
    }

    /**
     * Ouvrir le modal de gestion des activités pour une sous-action
     */
    public function openActivitiesModal($sousActionId)
    {
        $this->selectedSousActionForActivities = SousAction::with('activities.owner')->findOrFail($sousActionId);
        $this->showActivitiesModal = true;
        $this->resetNewActivity();
        
        Log::info('🔓 Modal des activités ouvert', [
            'sous_action_id' => $sousActionId,
            'type' => $this->selectedSousActionForActivities->type
        ]);
    }

    /**
     * Fermer le modal de gestion des activités
     */
    public function closeActivitiesModal()
    {
        $this->showActivitiesModal = false;
        $this->selectedSousActionForActivities = null;
        $this->resetNewActivity();
        $this->editingActivity = null;
    }

    /**
     * Réinitialiser le formulaire de nouvelle activité
     */
    public function resetNewActivity()
    {
        $this->newActivity = [
            'titre' => '',
            'description' => '',
            'date_debut' => '',
            'date_fin' => '',
            'statut' => 'en_attente',
            'taux_avancement' => 0,
            'owner_id' => auth()->id(),
            'tags' => []
        ];
    }

    /**
     * Créer une nouvelle activité
     */
    public function createActivity()
    {
        $this->validate([
            'newActivity.titre' => 'required|string|max:255',
            'newActivity.description' => 'nullable|string',
            'newActivity.date_debut' => 'required|date',
            'newActivity.date_fin' => 'required|date|after:newActivity.date_debut',
            'newActivity.statut' => 'required|in:en_attente,en_cours,termine,bloque',
            'newActivity.taux_avancement' => 'required|numeric|min:0|max:100',
            'newActivity.owner_id' => 'required|exists:users,id',
        ]);

        // Validation supplémentaire : la date de fin ne doit pas dépasser l'échéance du projet
        if ($this->selectedSousActionForActivities->date_echeance) {
            $this->validate([
                'newActivity.date_fin' => 'before_or_equal:' . $this->selectedSousActionForActivities->date_echeance,
            ], [
                'newActivity.date_fin.before_or_equal' => 'La date de fin de l\'activité ne peut pas dépasser l\'échéance du projet (' . \Carbon\Carbon::parse($this->selectedSousActionForActivities->date_echeance)->format('d/m/Y') . ')'
            ]);
        }

        try {
            $activityData = $this->newActivity;
            $activityData['sous_action_id'] = $this->selectedSousActionForActivities->id;
            $activityData['tags'] = json_encode($this->newActivity['tags']);

            $activityId = DB::table('activities')->insertGetId($activityData);

            if ($activityId) {
                // Recalculer la progression de la sous-action projet
                if ($this->selectedSousActionForActivities->type === 'projet') {
                    $this->recalculateProjectProgress($this->selectedSousActionForActivities->id);
                }

                $this->dispatch('toast', 'success', 'Activité créée avec succès !');
                $this->resetNewActivity();
                
                // Rafraîchir les données
                $this->selectedSousActionForActivities = SousAction::with('activities.owner')->find($this->selectedSousActionForActivities->id);
                
                Log::info('✅ Activité créée avec succès', [
                    'activity_id' => $activityId,
                    'sous_action_id' => $this->selectedSousActionForActivities->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la création de l\'activité', [
                'error' => $e->getMessage(),
                'sous_action_id' => $this->selectedSousActionForActivities->id
            ]);
            
            $this->dispatch('toast', 'error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    /**
     * Ouvrir le modal d'édition d'une activité
     */
    public function openEditActivityModal($activityId)
    {
        $activity = DB::table('activities')->find($activityId);
        if ($activity) {
            $this->editingActivity = $activity;
            $this->editActivityData = [
                'titre' => $activity->titre,
                'description' => $activity->description,
                'date_debut' => $activity->date_debut,
                'date_fin' => $activity->date_fin,
                'statut' => $activity->statut,
                'taux_avancement' => $activity->taux_avancement,
                'owner_id' => $activity->owner_id,
                'tags' => json_decode($activity->tags ?? '[]', true) ?: []
            ];
            
            Log::info('🔓 Modal d\'édition d\'activité ouvert', [
                'activity_id' => $activityId,
                'titre' => $activity->titre
            ]);
        }
    }

    /**
     * Fermer le modal d'édition d'activité
     */
    public function closeEditActivityModal()
    {
        $this->editingActivity = null;
        $this->editActivityData = [];
    }

    /**
     * Mettre à jour une activité
     */
    public function updateActivity()
    {
        // Vérifier que l'activité à éditer est bien définie
        if (!$this->editingActivity) {
            Log::error('❌ Tentative de mise à jour d\'une activité non définie', [
                'editingActivity' => $this->editingActivity,
                'editActivityData' => $this->editActivityData
            ]);
            $this->dispatch('toast', 'error', 'Aucune activité sélectionnée pour édition');
            return;
        }

        $this->validate([
            'editActivityData.titre' => 'required|string|max:255',
            'editActivityData.description' => 'nullable|string',
            'editActivityData.date_debut' => 'required|date',
            'editActivityData.date_fin' => 'required|date|after:editActivityData.date_debut',
            'editActivityData.statut' => 'required|in:en_attente,en_cours,termine,bloque',
            'editActivityData.taux_avancement' => 'required|numeric|min:0|max:100',
            'editActivityData.owner_id' => 'required|exists:users,id',
        ]);

        // Validation supplémentaire : la date de fin ne doit pas dépasser l'échéance du projet
        if ($this->selectedSousActionForActivities->date_echeance) {
            $this->validate([
                'editActivityData.date_fin' => 'before_or_equal:' . $this->selectedSousActionForActivities->date_echeance,
            ], [
                'editActivityData.date_fin.before_or_equal' => 'La date de fin de l\'activité ne peut pas dépasser l\'échéance du projet (' . \Carbon\Carbon::parse($this->selectedSousActionForActivities->date_echeance)->format('d/m/Y') . ')'
            ]);
        }

        try {
            $activityId = $this->editingActivity->id;
            $updateData = $this->editActivityData;
            $updateData['tags'] = json_encode($this->editActivityData['tags']);

            Log::info('🔄 Début de mise à jour de l\'activité', [
                'activity_id' => $activityId,
                'update_data' => $updateData,
                'sous_action_id' => $this->selectedSousActionForActivities->id
            ]);

            $updated = DB::table('activities')
                ->where('id', $activityId)
                ->update($updateData);

            if ($updated) {
                // Recalculer la progression de la sous-action projet
                if ($this->selectedSousActionForActivities->type === 'projet') {
                    $this->recalculateProjectProgress($this->selectedSousActionForActivities->id);
                }

                $this->dispatch('toast', 'success', 'Activité mise à jour avec succès !');
                $this->closeEditActivityModal();
                
                // Rafraîchir les données
                $this->selectedSousActionForActivities = SousAction::with('activities.owner')->find($this->selectedSousActionForActivities->id);
                
                Log::info('✅ Activité mise à jour avec succès', [
                    'activity_id' => $activityId,
                    'sous_action_id' => $this->selectedSousActionForActivities->id
                ]);
            } else {
                Log::warning('⚠️ Aucune modification effectuée lors de la mise à jour de l\'activité', [
                    'activity_id' => $activityId
                ]);
                $this->dispatch('toast', 'warning', 'Aucune modification effectuée');
            }

        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la mise à jour de l\'activité', [
                'error' => $e->getMessage(),
                'activity_id' => $this->editingActivity ? $this->editingActivity->id : 'null',
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('toast', 'error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une activité
     */
    public function deleteActivity($activityId)
    {
        try {
            $deleted = DB::table('activities')->where('id', $activityId)->delete();

            if ($deleted) {
                // Recalculer la progression de la sous-action projet
                if ($this->selectedSousActionForActivities->type === 'projet') {
                    $this->recalculateProjectProgress($this->selectedSousActionForActivities->id);
                }

                $this->dispatch('toast', 'success', 'Activité supprimée avec succès !');
                
                // Rafraîchir les données
                $this->selectedSousActionForActivities = SousAction::with('activities.owner')->find($this->selectedSousActionForActivities->id);
                
                Log::info('✅ Activité supprimée avec succès', [
                    'activity_id' => $activityId,
                    'sous_action_id' => $this->selectedSousActionForActivities->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la suppression de l\'activité', [
                'error' => $e->getMessage(),
                'activity_id' => $activityId
            ]);
            
            $this->dispatch('toast', 'error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour la progression d'une activité
     */
    public function updateActivityProgress($activityId, $newProgress)
    {
        try {
            Log::info('🔄 Début de mise à jour de la progression d\'activité', [
                'activity_id' => $activityId,
                'nouvelle_progression' => $newProgress,
                'user_id' => Auth::id(),
                'timestamp' => now()->toISOString()
            ]);

            if (!is_numeric($newProgress) || $newProgress < 0 || $newProgress > 100) {
                Log::warning('⚠️ Valeur de progression invalide', [
                    'activity_id' => $activityId,
                    'nouvelle_progression' => $newProgress
                ]);
                $this->dispatch('toast', 'error', 'Valeur de progression invalide (0-100)');
                return;
            }

            // Récupérer l'activité avant mise à jour pour comparer
            $activityBefore = DB::table('activities')->where('id', $activityId)->first();
            
            if (!$activityBefore) {
                Log::error('❌ Activité non trouvée lors de la mise à jour de progression', [
                    'activity_id' => $activityId
                ]);
                $this->dispatch('toast', 'error', 'Activité non trouvée');
                return;
            }

            Log::info('📊 Activité trouvée avant mise à jour', [
                'activity_id' => $activityId,
                'ancienne_progression' => $activityBefore->taux_avancement,
                'nouvelle_progression' => $newProgress,
                'titre' => $activityBefore->titre,
                'sous_action_id' => $activityBefore->sous_action_id,
                'owner_id' => $activityBefore->owner_id,
                'ancien_statut' => $activityBefore->statut
            ]);

            // Vérifier si l'activité passe à 100%
            $passeA100 = ($activityBefore->taux_avancement < 100 && $newProgress == 100);
            
            if ($passeA100) {
                Log::info('🎯 ACTIVITÉ PASSE À 100% - Préparation de la notification', [
                    'activity_id' => $activityId,
                    'ancienne_progression' => $activityBefore->taux_avancement,
                    'nouvelle_progression' => $newProgress,
                    'titre' => $activityBefore->titre,
                    'owner_id' => $activityBefore->owner_id
                ]);
            }

            // Déterminer le nouveau statut
            $nouveauStatut = $newProgress >= 100 ? 'termine' : ($newProgress > 0 ? 'en_cours' : 'en_attente');
            
            Log::info('🔄 Mise à jour du statut de l\'activité', [
                'activity_id' => $activityId,
                'ancien_statut' => $activityBefore->statut,
                'nouveau_statut' => $nouveauStatut,
                'progression' => $newProgress
            ]);

            $updated = DB::table('activities')
                ->where('id', $activityId)
                ->update([
                    'taux_avancement' => $newProgress,
                    'statut' => $nouveauStatut
                ]);

            if (!$updated) {
                Log::error('❌ Échec de la mise à jour de la progression de l\'activité', [
                    'activity_id' => $activityId,
                    'nouvelle_progression' => $newProgress
                ]);
                $this->dispatch('toast', 'error', 'Erreur lors de la mise à jour de la progression');
                return;
            }

            Log::info('✅ Progression de l\'activité mise à jour avec succès', [
                'activity_id' => $activityId,
                'ancienne_progression' => $activityBefore->taux_avancement,
                'nouvelle_progression' => $newProgress,
                'ancien_statut' => $activityBefore->statut,
                'nouveau_statut' => $nouveauStatut,
                'updated_at' => now()->toISOString()
            ]);

            // Récupérer l'activité mise à jour
            $activity = DB::table('activities')->where('id', $activityId)->first();
            
            if ($activity) {
                // Si l'activité passe à 100%, envoyer une notification au propriétaire
                if ($passeA100 && $activity->owner_id) {
                    Log::info('🔔 ENVOI DE NOTIFICATION - Activité terminée à 100%', [
                        'activity_id' => $activityId,
                        'owner_id' => $activity->owner_id,
                        'titre' => $activity->titre,
                        'progression' => $newProgress,
                        'sous_action_id' => $activity->sous_action_id
                    ]);

                    try {
                        $notificationSent = $this->sendNotification(
                            $activity->owner_id,
                            'activity_completed',
                            'Activité terminée ! 🎯',
                            "Félicitations ! Votre activité '{$activity->titre}' a été terminée avec succès !",
                            [
                                'activity_id' => $activity->id,
                                'activity_titre' => $activity->titre,
                                'completion_date' => now()->toISOString(),
                                'sous_action_id' => $activity->sous_action_id
                            ]
                        );

                        if ($notificationSent) {
                            Log::info('✅ NOTIFICATION ENVOYÉE avec succès pour l\'activité terminée', [
                                'activity_id' => $activityId,
                                'owner_id' => $activity->owner_id,
                                'notification_type' => 'activity_completed',
                                'timestamp' => now()->toISOString()
                            ]);
                        } else {
                            Log::warning('⚠️ Échec de l\'envoi de la notification pour l\'activité terminée', [
                                'activity_id' => $activityId,
                                'owner_id' => $activity->owner_id
                            ]);
                        }
                    } catch (\Exception $notificationError) {
                        Log::error('❌ Erreur lors de l\'envoi de la notification pour l\'activité terminée', [
                            'activity_id' => $activityId,
                            'owner_id' => $activity->owner_id,
                            'error' => $notificationError->getMessage(),
                            'trace' => $notificationError->getTraceAsString()
                        ]);
                    }
                } else {
                    Log::info('ℹ️ Pas de notification nécessaire', [
                        'activity_id' => $activityId,
                        'passe_a_100' => $passeA100,
                        'owner_id' => $activity->owner_id,
                        'raison' => $passeA100 ? 'Pas de propriétaire' : 'Progression différente de 100%'
                    ]);
                }

                // Recalculer la progression du projet
                if ($this->selectedSousActionForActivities->type === 'projet') {
                    Log::info('🔄 Début du recalcul de la progression du projet', [
                        'sous_action_id' => $this->selectedSousActionForActivities->id,
                        'activity_id' => $activityId,
                        'type_sous_action' => $this->selectedSousActionForActivities->type
                    ]);

                    $this->recalculateProjectProgress($this->selectedSousActionForActivities->id);
                    
                    Log::info('✅ Recalcul de la progression du projet terminé', [
                        'sous_action_id' => $this->selectedSousActionForActivities->id,
                        'activity_id' => $activityId
                    ]);
                } else {
                    Log::info('ℹ️ Pas de recalcul nécessaire - Sous-action de type normal', [
                        'sous_action_id' => $this->selectedSousActionForActivities->id,
                        'type' => $this->selectedSousActionForActivities->type
                    ]);
                }

                $this->dispatch('toast', 'success', 'Progression de l\'activité mise à jour !');
                
                // Rafraîchir les données
                $this->selectedSousActionForActivities = SousAction::with('activities.owner')->find($this->selectedSousActionForActivities->id);
                
                Log::info('🔄 Données rafraîchies après mise à jour', [
                    'activity_id' => $activityId,
                    'sous_action_id' => $this->selectedSousActionForActivities->id,
                    'timestamp' => now()->toISOString()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la mise à jour de la progression de l\'activité', [
                'activity_id' => $activityId,
                'nouvelle_progression' => $newProgress,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);
            
            $this->dispatch('toast', 'error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Vérifier si l'utilisateur peut éditer une activité
     */
    public function canEditActivity($activity)
    {
        $user = auth()->user();
        
        // L'admin général peut tout faire
        if ($user->hasRole('admin_general')) {
            return true;
        }
        
        // Le propriétaire de l'activité peut l'éditer
        if ($activity->owner_id == $user->id) {
            return true;
        }
        
        // Le propriétaire de la sous-action peut éditer ses activités
        $sousAction = SousAction::find($activity->sous_action_id);
        if ($sousAction && $sousAction->owner_id == $user->id) {
            return true;
        }
        
        // Le propriétaire de l'action parent peut éditer
        if ($sousAction && $sousAction->action) {
            $action = $sousAction->action;
            if ($action->owner_id == $user->id) {
                return true;
            }
            
            // Vérifier la chaîne hiérarchique
            if ($action->objectifSpecifique && $action->objectifSpecifique->owner_id == $user->id) {
                return true;
            }
            
            if ($action->objectifSpecifique && $action->objectifSpecifique->objectifStrategique && 
                $action->objectifSpecifique->objectifStrategique->owner_id == $user->id) {
                return true;
            }
            
            if ($action->objectifSpecifique && $action->objectifSpecifique->objectifStrategique && 
                $action->objectifSpecifique->objectifStrategique->pilier && 
                $action->objectifSpecifique->objectifStrategique->pilier->owner_id == $user->id) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut supprimer une activité
     */
    public function canDeleteActivity($activity)
    {
        $user = auth()->user();
        
        // L'admin général peut tout faire
        if ($user->hasRole('admin_general')) {
            return true;
        }
        
        // Le propriétaire de l'activité peut la supprimer
        if ($activity->owner_id == $user->id) {
            return true;
        }
        
        // Le propriétaire de la sous-action peut supprimer ses activités
        $sousAction = SousAction::find($activity->sous_action_id);
        if ($sousAction && $sousAction->owner_id == $user->id) {
            return true;
        }
        
        // Le propriétaire de l'action parent peut supprimer
        if ($sousAction && $sousAction->action) {
            $action = $sousAction->action;
            if ($action->owner_id == $user->id) {
                return true;
            }
            
            // Vérifier la chaîne hiérarchique
            if ($action->objectifSpecifique && $action->objectifSpecifique->owner_id == $user->id) {
                return true;
            }
            
            if ($action->objectifSpecifique && $action->objectifSpecifique->objectifStrategique && 
                $action->objectifSpecifique->objectifStrategique->owner_id == $user->id) {
                return true;
            }
            
            if ($action->objectifSpecifique && $action->objectifSpecifique->objectifStrategique && 
                $action->objectifSpecifique->objectifStrategique->pilier && 
                $action->objectifSpecifique->objectifStrategique->pilier->owner_id == $user->id) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Recalculer la progression d'un projet basée sur ses activités
     */
    private function recalculateProjectProgress($sousActionId)
    {
        try {
            Log::info('🔄 Début du recalcul de la progression du projet', [
                'sous_action_id' => $sousActionId,
                'timestamp' => now()->toISOString()
            ]);

            // Récupérer la sous-action pour vérifier son type
            $sousAction = DB::table('sous_actions')->where('id', $sousActionId)->first();
            
            if (!$sousAction) {
                Log::error('❌ Sous-action non trouvée lors du recalcul de progression', [
                    'sous_action_id' => $sousActionId
                ]);
                return;
            }

            Log::info('📊 Informations de la sous-action projet', [
                'sous_action_id' => $sousActionId,
                'type' => $sousAction->type,
                'libelle' => $sousAction->libelle,
                'ancienne_progression' => $sousAction->taux_avancement,
                'owner_id' => $sousAction->owner_id
            ]);

            $activities = DB::table('activities')
                ->where('sous_action_id', $sousActionId)
                ->get();

            Log::info('📋 Activités trouvées pour le projet', [
                'sous_action_id' => $sousActionId,
                'nombre_activites' => $activities->count(),
                'activites_details' => $activities->map(function($activity) {
                    return [
                        'id' => $activity->id,
                        'titre' => $activity->titre,
                        'taux_avancement' => $activity->taux_avancement,
                        'statut' => $activity->statut
                    ];
                })
            ]);

            if ($activities->count() > 0) {
                $totalProgress = $activities->sum('taux_avancement');
                $averageProgress = $totalProgress / $activities->count();
                $newProgress = round($averageProgress, 2);
                
                Log::info('🧮 Calcul de la nouvelle progression du projet', [
                    'sous_action_id' => $sousActionId,
                    'total_progression_activites' => $totalProgress,
                    'nombre_activites' => $activities->count(),
                    'progression_moyenne' => $averageProgress,
                    'progression_arrondie' => $newProgress,
                    'ancienne_progression' => $sousAction->taux_avancement
                ]);

                // Vérifier si le projet passe à 100%
                $passeA100 = ($sousAction->taux_avancement < 100 && $newProgress == 100);
                
                if ($passeA100) {
                    Log::info('🎯 PROJET PASSE À 100% - Préparation de la notification', [
                        'sous_action_id' => $sousActionId,
                        'ancienne_progression' => $sousAction->taux_avancement,
                        'nouvelle_progression' => $newProgress,
                        'libelle' => $sousAction->libelle,
                        'owner_id' => $sousAction->owner_id
                    ]);
                }
                
                // Préparer les données de mise à jour
                $updateData = ['taux_avancement' => $newProgress];
                
                // Gestion de la date de réalisation pour les projets
                if ($newProgress == 100) {
                    // Si exactement 100%, enregistrer la date de réalisation
                    if (!$sousAction->date_realisation) {
                        $updateData['date_realisation'] = now();
                        
                        Log::info('📅 Date de réalisation ajoutée au projet terminé', [
                            'sous_action_id' => $sousActionId,
                            'date_realisation' => now()->toISOString(),
                            'libelle' => $sousAction->libelle
                        ]);
                    }
                } else {
                    // Si différent de 100%, masquer la date de réalisation
                    $updateData['date_realisation'] = null;
                    
                    if ($sousAction->date_realisation) {
                        Log::info('📅 Date de réalisation supprimée du projet (progression < 100%)', [
                            'sous_action_id' => $sousActionId,
                            'ancienne_date_realisation' => $sousAction->date_realisation,
                            'nouvelle_progression' => $newProgress
                        ]);
                    }
                }
                
                // Mettre à jour la progression de la sous-action
                $updated = DB::table('sous_actions')
                    ->where('id', $sousActionId)
                    ->update($updateData);

                if ($updated) {
                    Log::info('✅ Progression de la sous-action projet mise à jour', [
                        'sous_action_id' => $sousActionId,
                        'ancienne_progression' => $sousAction->taux_avancement,
                        'nouvelle_progression' => $newProgress,
                        'timestamp' => now()->toISOString()
                    ]);

                    // Mettre à jour les taux parents
                    $sousActionObj = SousAction::find($sousActionId);
                    if ($sousActionObj) {
                        Log::info('🔄 Début de la mise à jour des taux parents', [
                            'sous_action_id' => $sousActionId
                        ]);
                        
                        $this->updateParentProgressRates($sousActionObj);
                        
                        Log::info('✅ Mise à jour des taux parents terminée', [
                            'sous_action_id' => $sousActionId
                        ]);
                    } else {
                        Log::warning('⚠️ Impossible de récupérer l\'objet SousAction pour la mise à jour des parents', [
                            'sous_action_id' => $sousActionId
                        ]);
                    }
                } else {
                    Log::error('❌ Échec de la mise à jour de la progression de la sous-action projet', [
                        'sous_action_id' => $sousActionId,
                        'nouvelle_progression' => $newProgress
                    ]);
                }

                Log::info('✅ Progression du projet recalculée avec succès', [
                    'sous_action_id' => $sousActionId,
                    'nombre_activites' => $activities->count(),
                    'ancienne_progression' => $sousAction->taux_avancement,
                    'nouvelle_progression' => $newProgress,
                    'timestamp' => now()->toISOString()
                ]);
            } else {
                Log::info('ℹ️ Aucune activité trouvée pour le projet', [
                    'sous_action_id' => $sousActionId
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ Erreur lors du recalcul de la progression du projet', [
                'sous_action_id' => $sousActionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);
        }
    }

    /**
     * Mise à jour des taux parents avec DB Query Builder
     */
    private function updateParentProgressRates($sousAction)
    {
        try {
            // 1. Mettre à jour l'Action parente
            if ($sousAction->action_id) {
                $actionProgress = DB::table('sous_actions')
                    ->where('action_id', $sousAction->action_id)
                    ->avg('taux_avancement');
                
                $newActionProgress = round($actionProgress, 2);
                
                // Récupérer l'ancien taux pour vérifier si on passe à 100%
                $oldActionProgress = DB::table('actions')
                    ->where('id', $sousAction->action_id)
                    ->value('taux_avancement');
                
                DB::table('actions')
                    ->where('id', $sousAction->action_id)
                    ->update(['taux_avancement' => $newActionProgress]);
                
                // Notifier si l'Action passe à 100%
                if ($newActionProgress == 100 && $oldActionProgress != 100) {
                    $this->notifyParentCompletion('Action', $sousAction->action_id, $sousAction->action->owner_id ?? null);
                }
            }

            // 2. Mettre à jour l'Objectif Spécifique parent
            if ($sousAction->action && $sousAction->action->objectif_specifique_id) {
                $ospProgress = DB::table('actions')
                    ->where('objectif_specifique_id', $sousAction->action->objectif_specifique_id)
                    ->avg('taux_avancement');
                
                $newOSPProgress = round($ospProgress, 2);
                
                // Récupérer l'ancien taux
                $oldOSPProgress = DB::table('objectif_specifiques')
                    ->where('id', $sousAction->action->objectif_specifique_id)
                    ->value('taux_avancement');
                
                DB::table('objectif_specifiques')
                    ->where('id', $sousAction->action->objectif_specifique_id)
                    ->update(['taux_avancement' => $newOSPProgress]);
                
                // Notifier si l'OSP passe à 100%
                if ($newOSPProgress == 100 && $oldOSPProgress != 100) {
                    $this->notifyParentCompletion('Objectif Spécifique', $sousAction->action->objectif_specifique_id, $sousAction->action->objectifSpecifique->owner_id ?? null);
                }
            }

            // 3. Mettre à jour l'Objectif Stratégique parent
            if ($sousAction->action && $sousAction->action->objectifSpecifique && $sousAction->action->objectifSpecifique->objectif_strategique_id) {
                $osProgress = DB::table('objectif_specifiques')
                    ->where('objectif_strategique_id', $sousAction->action->objectifSpecifique->objectif_strategique_id)
                    ->avg('taux_avancement');
                
                $newOSProgress = round($osProgress, 2);
                
                // Récupérer l'ancien taux
                $oldOSProgress = DB::table('objectif_strategiques')
                    ->where('id', $sousAction->action->objectifSpecifique->objectif_strategique_id)
                    ->value('taux_avancement');
                
                DB::table('objectif_strategiques')
                    ->where('id', $sousAction->action->objectifSpecifique->objectif_strategique_id)
                    ->update(['taux_avancement' => $newOSProgress]);
                
                // Notifier si l'OS passe à 100%
                if ($newOSProgress == 100 && $oldOSProgress != 100) {
                    $this->notifyParentCompletion('Objectif Stratégique', $sousAction->action->objectifSpecifique->objectif_strategique_id, $sousAction->action->objectifSpecifique->objectifStrategique->owner_id ?? null);
                }
            }

            // 4. Mettre à jour le Pilier parent
            if ($sousAction->action && $sousAction->action->objectifSpecifique && $sousAction->action->objectifSpecifique->objectifStrategique && $sousAction->action->objectifSpecifique->objectifStrategique->pilier_id) {
                $pilierProgress = DB::table('objectif_strategiques')
                    ->where('pilier_id', $sousAction->action->objectifSpecifique->objectifStrategique->pilier_id)
                    ->avg('taux_avancement');
                
                $newPilierProgress = round($pilierProgress, 2);
                
                // Récupérer l'ancien taux
                $oldPilierProgress = DB::table('piliers')
                    ->where('id', $sousAction->action->objectifSpecifique->objectifStrategique->pilier_id)
                    ->value('taux_avancement');
                
                DB::table('piliers')
                    ->where('id', $sousAction->action->objectifSpecifique->objectifStrategique->pilier_id)
                    ->update(['taux_avancement' => $newPilierProgress]);
                
                // Notifier si le Pilier passe à 100%
                if ($newPilierProgress == 100 && $oldPilierProgress != 100) {
                    $this->notifyParentCompletion('Pilier', $sousAction->action->objectifSpecifique->objectifStrategique->pilier_id, $sousAction->action->objectifSpecifique->objectifStrategique->pilier->owner_id ?? null);
                }
            }

        } catch (\Exception $e) {
            Log::warning('⚠️ Erreur mise à jour taux parents', [
                'error' => $e->getMessage(),
                'sous_action_id' => $sousAction->id
            ]);
        }
    }

    /**
     * Notifier le propriétaire quand un parent passe à 100%
     */
    private function notifyParentCompletion($parentType, $parentId, $ownerId)
    {
        if (!$ownerId) {
            Log::warning('⚠️ Pas de propriétaire pour notifier', [
                'parent_type' => $parentType,
                'parent_id' => $parentId
            ]);
            return;
        }

        try {
            $notificationSent = $this->sendNotification(
                $ownerId,
                'parent_completion',
                'Objectif atteint ! 🎉',
                "Félicitations ! Votre {$parentType} a atteint 100% de progression !",
                [
                    'parent_type' => $parentType,
                    'parent_id' => $parentId,
                    'completion_date' => now()->toISOString()
                ]
            );

            if ($notificationSent) {
                Log::info('✅ Notification de complétion envoyée', [
                    'parent_type' => $parentType,
                    'parent_id' => $parentId,
                    'owner_id' => $ownerId
                ]);
            } else {
                Log::warning('⚠️ Échec envoi notification de complétion', [
                    'parent_type' => $parentType,
                    'parent_id' => $parentId,
                    'owner_id' => $ownerId
                ]);
            }

        } catch (\Exception $e) {
            Log::error('❌ Erreur notification de complétion', [
                'error' => $e->getMessage(),
                'parent_type' => $parentType,
                'parent_id' => $parentId,
                'owner_id' => $ownerId
            ]);
        }
    }

    // Propriétés dédiées pour l'édition d'action (pour résoudre le binding)
    public $editActionCode = '';
    public $editActionLibelle = '';
    public $editActionDescription = '';
    public $editActionOwnerId = '';
    
    // Propriétés dédiées pour l'édition de sous-action (pour résoudre le binding)
    public $editSousActionCode = '';
    public $editSousActionLibelle = '';
    public $editSousActionDescription = '';
    public $editSousActionOwnerId = '';
    public $editSousActionDateEcheance = '';
    public $editSousActionTauxAvancement = 0;
    public $editSousActionType = 'normal';
    
    // Propriétés pour la gestion des activités
    public $selectedSousActionForActivities = null;
    public $showActivitiesModal = false;
    public $newActivity = [
        'titre' => '',
        'description' => '',
        'date_debut' => '',
        'date_fin' => '',
        'statut' => 'en_attente',
        'taux_avancement' => 0,
        'owner_id' => null,
        'tags' => []
    ];
    public $editingActivity = null;
    public $editActivityData = [
        'titre' => '',
        'description' => '',
        'date_debut' => '',
        'date_fin' => '',
        'statut' => 'en_attente',
        'taux_avancement' => 0,
        'owner_id' => null,
        'tags' => []
    ];
    
    // Nouvelle propriété pour contrôler la visibilité du formulaire de création d'activité
    public $showCreateActivityForm = false; // Masqué par défaut
    
    // Propriétés pour le calendrier des activités
    public $showActivityCalendarModal = false;
    public $calendarMonth;
    public $calendarYear;
    public $calendarDays = [];
    
    // Propriétés pour le diagramme de Gantt
    public $showGanttChartModal = false;
    public $ganttMonth;
    public $ganttYear;
    
    // Propriétés pour l'édition d'objectif stratégique
    public $editingObjectifStrategique = null;
    public $editOSCode = '';
    public $editOSLibelle = '';
    public $editOSDescription = '';
    public $editOSOwnerId = '';

    /**
     * Mise à jour optimisée du taux d'avancement d'une Action avec DB
     */
    private function updateActionProgressWithDB($actionId)
    {
        try {
            // Calculer le taux d'avancement basé sur les sous-actions avec DB
            $sousActions = DB::table('sous_actions')
                ->where('action_id', $actionId)
                ->get();
            
            if ($sousActions->count() > 0) {
                $totalProgress = $sousActions->sum('taux_avancement');
                $averageProgress = $totalProgress / $sousActions->count();
                $newProgress = round($averageProgress, 2);
                
                // Récupérer l'action pour vérifier si elle atteint 100%
                $action = DB::table('actions')->where('id', $actionId)->first();
                
                // Mettre à jour avec DB
                DB::table('actions')
                    ->where('id', $actionId)
                    ->update([
                        'taux_avancement' => $newProgress,
                        'updated_at' => now()
                    ]);
                
                // Notification si l'action atteint 100%
                if ($newProgress == 100 && $action && $action->taux_avancement < 100) {
                    if ($action->owner_id) {
                        $this->sendNotification(
                            $action->owner_id,
                            'action_completed',
                            'Action terminée ! 🎯',
                            "Félicitations ! Votre action '{$action->libelle}' a été terminée avec succès !",
                            [
                                'action_id' => $action->id,
                                'action_libelle' => $action->libelle,
                                'completion_date' => now()->toISOString()
                            ]
                        );
                        
                        Log::info('✅ Notification de complétion d\'action envoyée au propriétaire', [
                            'action_id' => $action->id,
                            'owner_id' => $action->owner_id,
                            'libelle' => $action->libelle
                        ]);
                    }
                }
                
                Log::info('✅ Taux d\'avancement Action mis à jour avec DB', [
                    'action_id' => $actionId,
                    'new_progress' => $newProgress
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('⚠️ Erreur mise à jour Action avec DB', [
                'error' => $e->getMessage(), 
                'action_id' => $actionId
            ]);
        }
    }

    /**
     * Mise à jour optimisée du taux d'avancement d'un Objectif Spécifique avec DB
     */
    private function updateOSPProgressWithDB($objectifSpecifiqueId)
    {
        try {
            // Calculer le taux d'avancement basé sur les actions avec DB
            $actions = DB::table('actions')
                ->where('objectif_specifique_id', $objectifSpecifiqueId)
                ->get();
            
            if ($actions->count() > 0) {
                $totalProgress = $actions->sum('taux_avancement');
                $averageProgress = $totalProgress / $actions->count();
                $newProgress = round($averageProgress, 2);
                
                // Récupérer l'objectif spécifique pour vérifier s'il atteint 100%
                $objectifSpecifique = DB::table('objectif_specifiques')->where('id', $objectifSpecifiqueId)->first();
                
                // Mettre à jour avec DB
                DB::table('objectif_specifiques')
                    ->where('id', $objectifSpecifiqueId)
                    ->update([
                        'taux_avancement' => $newProgress,
                        'updated_at' => now()
                    ]);
                
                // Notification si l'objectif spécifique atteint 100%
                if ($newProgress == 100 && $objectifSpecifique && $objectifSpecifique->taux_avancement < 100) {
                    if ($objectifSpecifique->owner_id) {
                        $this->sendNotification(
                            $objectifSpecifique->owner_id,
                            'objectif_specifique_completed',
                            'Objectif Spécifique terminé ! 🎯',
                            "Félicitations ! Votre objectif spécifique '{$objectifSpecifique->libelle}' a été terminé avec succès !",
                            [
                                'objectif_specifique_id' => $objectifSpecifique->id,
                                'objectif_specifique_libelle' => $objectifSpecifique->libelle,
                                'completion_date' => now()->toISOString()
                            ]
                        );
                        
                        Log::info('✅ Notification de complétion d\'objectif spécifique envoyée au propriétaire', [
                            'objectif_specifique_id' => $objectifSpecifique->id,
                            'owner_id' => $objectifSpecifique->owner_id,
                            'libelle' => $objectifSpecifique->libelle
                        ]);
                    }
                }
                
                Log::info('✅ Taux d\'avancement OSP mis à jour avec DB', [
                    'osp_id' => $objectifSpecifiqueId,
                    'new_progress' => $newProgress
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('⚠️ Erreur mise à jour OSP avec DB', [
                'error' => $e->getMessage(), 
                'osp_id' => $objectifSpecifiqueId
            ]);
        }
    }

    /**
     * Ouvrir le modal du calendrier des activités
     */
    public function openActivityCalendarModal()
    {
        $this->calendarMonth = now()->month;
        $this->calendarYear = now()->year;
        $this->generateCalendarDays();
        $this->showActivityCalendarModal = true;
        
        Log::info('📅 Modal du calendrier des activités ouvert', [
            'sous_action_id' => $this->selectedSousActionForActivities->id,
            'month' => $this->calendarMonth,
            'year' => $this->calendarYear
        ]);
    }

    /**
     * Fermer le modal du calendrier des activités
     */
    public function closeActivityCalendarModal()
    {
        $this->showActivityCalendarModal = false;
        $this->calendarDays = [];
    }

    /**
     * Aller au mois précédent
     */
    public function previousMonth()
    {
        if ($this->calendarMonth == 1) {
            $this->calendarMonth = 12;
            $this->calendarYear--;
        } else {
            $this->calendarMonth--;
        }
        
        $this->generateCalendarDays();
        
        Log::info('📅 Navigation vers le mois précédent', [
            'month' => $this->calendarMonth,
            'year' => $this->calendarYear
        ]);
    }

    /**
     * Aller au mois suivant
     */
    public function nextMonth()
    {
        if ($this->calendarMonth == 12) {
            $this->calendarMonth = 1;
            $this->calendarYear++;
        } else {
            $this->calendarMonth++;
        }
        
        $this->generateCalendarDays();
        
        Log::info('📅 Navigation vers le mois suivant', [
            'month' => $this->calendarMonth,
            'year' => $this->calendarYear
        ]);
    }

    /**
     * Aller au mois actuel
     */
    public function goToCurrentMonth()
    {
        $this->calendarMonth = now()->month;
        $this->calendarYear = now()->year;
        $this->generateCalendarDays();
        
        Log::info('📅 Retour au mois actuel', [
            'month' => $this->calendarMonth,
            'year' => $this->calendarYear
        ]);
    }

    /**
     * Afficher toutes les activités d'un jour spécifique
     */
    public function showDayActivities($date)
    {
        $formattedDate = \Carbon\Carbon::parse($date)->format('d/m/Y');
        $activities = $this->selectedSousActionForActivities->activities
            ->filter(function ($activity) use ($date) {
                $startDate = \Carbon\Carbon::parse($activity->date_debut);
                $endDate = \Carbon\Carbon::parse($activity->date_fin);
                $targetDate = \Carbon\Carbon::parse($date);
                
                return $targetDate->between($startDate, $endDate, true);
            });
        
        $this->dispatch('toast', 'info', "📅 {$activities->count()} activité(s) pour le {$formattedDate}");
        
        Log::info('📅 Affichage des activités d\'un jour', [
            'date' => $date,
            'formatted_date' => $formattedDate,
            'activity_count' => $activities->count(),
            'sous_action_id' => $this->selectedSousActionForActivities->id
        ]);
    }

    /**
     * Générer les jours du calendrier pour le mois et l'année donnés
     */
    private function generateCalendarDays()
    {
        $this->calendarDays = [];
        
        // Premier jour du mois
        $firstDayOfMonth = \Carbon\Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1);
        
        // Dernier jour du mois
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
        
        // Premier jour de la semaine (lundi = 1)
        $firstDayOfWeek = $firstDayOfMonth->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        
        // Dernier jour de la semaine (dimanche = 0)
        $lastDayOfWeek = $lastDayOfMonth->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
        
        $currentDate = $firstDayOfWeek->copy();
        
        // Récupérer toutes les activités du projet pour ce mois
        $allActivities = $this->selectedSousActionForActivities->activities
            ->filter(function ($activity) use ($firstDayOfWeek, $lastDayOfWeek) {
                $startDate = \Carbon\Carbon::parse($activity->date_debut);
                $endDate = \Carbon\Carbon::parse($activity->date_fin);
                
                // Une activité est visible si elle chevauche la période du mois
                return $startDate->lte($lastDayOfWeek) && $endDate->gte($firstDayOfWeek);
            });
        
        // Créer un index des activités par position
        $activityPositions = $this->createActivityPositions($allActivities, $firstDayOfWeek, $lastDayOfWeek);
        
        while ($currentDate->lte($lastDayOfWeek)) {
            $day = [
                'date' => $currentDate->copy(),
                'day' => $currentDate->day,
                'isCurrentMonth' => $currentDate->month == $this->calendarMonth,
                'isToday' => $currentDate->isToday(),
                'activities' => collect(),
                'activityPositions' => []
            ];
            
            // Si c'est un jour du mois actuel, récupérer les activités
            if ($day['isCurrentMonth']) {
                $day['activities'] = $allActivities
                    ->filter(function ($activity) use ($currentDate) {
                        $startDate = \Carbon\Carbon::parse($activity->date_debut);
                        $endDate = \Carbon\Carbon::parse($activity->date_fin);
                        
                        // Une activité est visible si elle commence, se termine ou est en cours ce jour-là
                        return $currentDate->between($startDate, $endDate, true);
                    });
                
                // Créer les positions d'activités pour ce jour
                $day['activityPositions'] = $this->getActivityPositionsForDay($currentDate, $activityPositions);
            }
            
            $this->calendarDays[] = $day;
            $currentDate->addDay();
        }
        
        Log::info('📅 Jours du calendrier générés avec alignement', [
            'month' => $this->calendarMonth,
            'year' => $this->calendarYear,
            'total_days' => count($this->calendarDays),
            'days_with_activities' => collect($this->calendarDays)->filter(fn($day) => $day['activities']->count() > 0)->count(),
            'max_activity_positions' => count($activityPositions)
        ]);
    }

    /**
     * Créer un index des positions d'activités pour le mois
     */
    private function createActivityPositions($activities, $startDate, $endDate)
    {
        $positions = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dayActivities = $activities->filter(function ($activity) use ($currentDate) {
                $start = \Carbon\Carbon::parse($activity->date_debut);
                $end = \Carbon\Carbon::parse($activity->date_fin);
                return $currentDate->between($start, $end, true);
            });
            
            if ($dayActivities->count() > 0) {
                $positions[$currentDate->format('Y-m-d')] = $dayActivities->values();
            }
            
            $currentDate->addDay();
        }
        
        return $positions;
    }

    /**
     * Obtenir les positions d'activités pour un jour spécifique
     */
    private function getActivityPositionsForDay($date, $activityPositions)
    {
        $dateKey = $date->format('Y-m-d');
        $dayActivities = $activityPositions[$dateKey] ?? collect();
        
        // Créer un tableau de positions avec des "slots" vides
        $maxPositions = $this->getMaxActivityPositions();
        $positions = array_fill(0, $maxPositions, null);
        
        foreach ($dayActivities as $index => $activity) {
            $positions[$index] = $activity;
        }
        
        return $positions;
    }

    /**
     * Obtenir le nombre maximum de positions d'activités
     */
    private function getMaxActivityPositions()
    {
        $maxCount = 0;
        
        foreach ($this->calendarDays as $day) {
            if ($day['activities']->count() > $maxCount) {
                $maxCount = $day['activities']->count();
            }
        }
        
        return max($maxCount, 1); // Au moins 1 position
    }

    /**
     * Ouvrir le modal du diagramme de Gantt
     */
    public function openGanttChartModal()
    {
        $this->ganttMonth = now()->month;
        $this->ganttYear = now()->year;
        $this->showGanttChartModal = true;
        
        Log::info('📊 Modal du diagramme de Gantt ouvert', [
            'sous_action_id' => $this->selectedSousActionForActivities->id,
            'month' => $this->ganttMonth,
            'year' => $this->ganttYear
        ]);
    }

    /**
     * Fermer le modal du diagramme de Gantt
     */
    public function closeGanttChartModal()
    {
        $this->showGanttChartModal = false;
    }

    /**
     * Aller à la période précédente du Gantt
     */
    public function previousGanttPeriod()
    {
        if ($this->ganttMonth == 1) {
            $this->ganttMonth = 12;
            $this->ganttYear--;
        } else {
            $this->ganttMonth--;
        }
        
        Log::info('📊 Navigation vers la période précédente du Gantt', [
            'month' => $this->ganttMonth,
            'year' => $this->ganttYear
        ]);
    }

    /**
     * Aller à la période suivante du Gantt
     */
    public function nextGanttPeriod()
    {
        if ($this->ganttMonth == 12) {
            $this->ganttMonth = 1;
            $this->ganttYear++;
        } else {
            $this->ganttMonth++;
        }
        
        Log::info('📊 Navigation vers la période suivante du Gantt', [
            'month' => $this->ganttMonth,
            'year' => $this->ganttYear
        ]);
    }

    /**
     * Aller à la période actuelle du Gantt
     */
    public function goToCurrentGanttPeriod()
    {
        $this->ganttMonth = now()->month;
        $this->ganttYear = now()->year;
        
        Log::info('📊 Retour à la période actuelle du Gantt', [
            'month' => $this->ganttMonth,
            'year' => $this->ganttYear
        ]);
    }

    /**
     * Calculer la position de la barre Gantt
     */
    public function calculateGanttBarPosition($activity)
    {
        $startDate = \Carbon\Carbon::parse($activity->date_debut);
        $monthStart = \Carbon\Carbon::createFromDate($this->ganttYear, $this->ganttMonth, 1);
        $monthEnd = $monthStart->copy()->endOfMonth();
        
        if ($startDate->lt($monthStart)) {
            return 0;
        }
        
        $daysFromStart = $startDate->diffInDays($monthStart);
        $totalDays = $monthStart->diffInDays($monthEnd) + 1;
        
        return ($daysFromStart / $totalDays) * 100;
    }

    /**
     * Calculer la largeur de la barre Gantt
     */
    public function calculateGanttBarWidth($activity)
    {
        $startDate = \Carbon\Carbon::parse($activity->date_debut);
        $endDate = \Carbon\Carbon::parse($activity->date_fin);
        $monthStart = \Carbon\Carbon::createFromDate($this->ganttYear, $this->ganttMonth, 1);
        $monthEnd = $monthStart->copy()->endOfMonth();
        
        // Ajuster les dates pour le mois affiché
        $effectiveStart = $startDate->lt($monthStart) ? $monthStart : $startDate;
        $effectiveEnd = $endDate->gt($monthEnd) ? $monthEnd : $endDate;
        
        $duration = $effectiveEnd->diffInDays($effectiveStart) + 1;
        $totalDays = $monthStart->diffInDays($monthEnd) + 1;
        
        return min(($duration / $totalDays) * 100, 100);
    }

}
