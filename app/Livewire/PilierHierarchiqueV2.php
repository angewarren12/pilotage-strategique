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
        
        $this->dispatch('toast', 'info', 'Composant V2 fonctionnel !');
        
        Log::info('✅ Test terminé avec succès');
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
            
            // Créer un tableau avec les données pour l'édition
            $this->editingOS = [
                'id' => $objectifStrategique->id,
                'code' => $objectifStrategique->code,
                'libelle' => $objectifStrategique->libelle,
                'description' => $objectifStrategique->description,
                'owner_id' => $objectifStrategique->owner_id
            ];
            
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
            $action = Action::findOrFail($actionId);
            
            Log::info('🔧 Édition Action', [
                'action_id' => $action->id,
                'action_libelle' => $action->libelle,
                'user_id' => Auth::id()
            ]);
            
            $this->editingAction = $action;
            $this->showEditActionModal = true;
            
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
            
            $this->editingSousAction = $sousAction;
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
            $owner = User::find($this->editOSPOwnerId);
            if (!$owner) {
                Log::error('❌ Utilisateur propriétaire non trouvé', ['owner_id' => $this->editOSPOwnerId]);
                $this->dispatch('toast', 'error', 'Utilisateur propriétaire non trouvé');
                return;
            }

            // Sauvegarder l'ancien propriétaire pour la notification
            $oldOwnerId = $this->editingOSP->owner_id;
            $newOwnerId = $this->editOSPOwnerId;

            // Mettre à jour l'objectif spécifique
            $this->editingOSP->update([
                'code' => $this->editOSPCode,
                'libelle' => $this->editOSPLibelle,
                'description' => $this->editOSPDescription,
                'owner_id' => $this->editOSPOwnerId
            ]);

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

            // Mettre à jour le taux d'avancement de l'objectif stratégique parent
            $this->selectedObjectifStrategique->updateTauxAvancement();

            $this->closeEditOSPModal();
            $this->dispatch('toast', 'success', 'Objectif Spécifique modifié avec succès');
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            Log::error('❌ Erreur mise à jour Objectif Spécifique', ['error' => $e->getMessage()]);
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
        $this->showCreateActionModal = true;
        $this->resetNewActionForm();
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
        $this->showCreateSousActionModal = true;
        $this->resetNewSousActionForm();
    }

    public function closeCreateSousActionModal()
    {
        $this->showCreateSousActionModal = false;
        $this->resetNewSousActionForm();
    }

    public function closeEditSousActionModal()
    {
        $this->showEditSousActionModal = false;
        $this->editingSousAction = null;
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
        if (!$this->canCreateAction()) {
            $this->dispatch('toast', 'error', 'Permission refusée');
            return;
        }

        $this->validate([
            'newAction.code' => 'required|string|max:10',
            'newAction.libelle' => 'required|string|max:255',
            'newAction.description' => 'nullable|string',
            'newAction.owner_id' => 'required|exists:users,id',
            'newAction.date_echeance' => 'nullable|date',
            'newAction.taux_avancement' => 'required|numeric|min:0|max:100',
            'newAction.type' => 'required|in:normal,projet'
        ]);

        try {
            $action = new Action();
            $action->objectif_specifique_id = $this->selectedObjectifSpecifique->id;
            $action->code = $this->newAction['code'];
            $action->libelle = $this->newAction['libelle'];
            $action->description = $this->newAction['description'];
            $action->owner_id = $this->newAction['owner_id'];
            $action->date_echeance = $this->newAction['date_echeance'];
            $action->taux_avancement = $this->newAction['taux_avancement'];
            $action->type = $this->newAction['type'];
            $action->save();

            // Envoyer notification au propriétaire
            $this->sendNotification(
                $this->newAction['owner_id'],
                'action_assigned',
                'Nouvelle Action assignée',
                "Vous avez été assigné comme propriétaire de l'action : {$action->libelle}",
                ['action_id' => $action->id, 'osp_id' => $this->selectedObjectifSpecifique->id]
            );

            $this->selectedObjectifSpecifique->updateTauxAvancement();
            $this->closeCreateActionModal();
            $this->dispatch('toast', 'success', 'Action créée avec succès');
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            Log::error('Erreur création Action', ['error' => $e->getMessage()]);
            $this->dispatch('toast', 'error', 'Erreur lors de la création');
        }
    }

    public function createSousAction()
    {
        if (!$this->canCreateSousAction()) {
            $this->dispatch('toast', 'error', 'Permission refusée');
            return;
        }

        $this->validate([
            'newSousAction.code' => 'required|string|max:10',
            'newSousAction.libelle' => 'required|string|max:255',
            'newSousAction.description' => 'nullable|string',
            'newSousAction.owner_id' => 'required|exists:users,id',
            'newSousAction.date_echeance' => 'nullable|date',
            'newSousAction.taux_avancement' => 'required|numeric|min:0|max:100',
            'newSousAction.type' => 'required|in:normal,projet'
        ]);

        try {
            $sousAction = new SousAction();
            $sousAction->action_id = $this->selectedAction->id;
            $sousAction->code = $this->newSousAction['code'];
            $sousAction->libelle = $this->newSousAction['libelle'];
            $sousAction->description = $this->newSousAction['description'];
            $sousAction->owner_id = $this->newSousAction['owner_id'];
            $sousAction->date_echeance = $this->newSousAction['date_echeance'];
            $sousAction->taux_avancement = $this->newSousAction['taux_avancement'];
            $sousAction->type = $this->newSousAction['type'];
            $sousAction->save();

            $this->selectedAction->updateTauxAvancement();
            $this->closeCreateSousActionModal();
            $this->dispatch('toast', 'success', 'Sous-action créée avec succès');
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            Log::error('Erreur création Sous-action', ['error' => $e->getMessage()]);
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
            'editingAction.code' => 'required|string|max:10',
            'editingAction.libelle' => 'required|string|max:255',
            'editingAction.description' => 'nullable|string',
            'editingAction.owner_id' => 'required|exists:users,id',
            'editingAction.date_echeance' => 'nullable|date',
            'editingAction.taux_avancement' => 'required|numeric|min:0|max:100',
            'editingAction.type' => 'required|in:normal,projet'
        ]);

        try {
            // Sauvegarder l'ancien propriétaire pour la notification
            $oldOwnerId = $this->editingAction->getOriginal('owner_id');
            $newOwnerId = $this->editingAction->owner_id;

            // Mettre à jour l'action
            $this->editingAction->save();

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

            // Mettre à jour le taux d'avancement de l'objectif spécifique parent
            $this->selectedObjectifSpecifique->updateTauxAvancement();

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

            $action = Action::findOrFail($actionId);

            if (!$this->canDeleteAction($action)) {
                Log::warning('🚫 Permission refusée pour supprimer Action', ['user_id' => Auth::id(), 'action_id' => $actionId]);
                $this->dispatch('toast', 'error', 'Permission refusée ou action liée');
                return;
            }

            $action->delete();
            $this->selectedObjectifSpecifique->updateTauxAvancement();
            $this->dispatch('toast', 'success', 'Action supprimée avec succès');
            $this->dispatch('refreshComponent');
            Log::info('✅ Action supprimée', ['action_id' => $actionId]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur suppression Action', ['error' => $e->getMessage(), 'action_id' => $actionId]);
            $this->dispatch('error', 'Erreur lors de la suppression');
        }
    }

    public function updateSousAction()
    {
        if (!$this->editingSousAction) {
            $this->dispatch('toast', 'error', 'Aucune sous-action à modifier');
            return;
        }

        if (!$this->canEditSousAction($this->editingSousAction)) {
            $this->dispatch('toast', 'error', 'Permission refusée');
            return;
        }

        $this->validate([
            'editingSousAction.code' => 'required|string|max:10',
            'editingSousAction.libelle' => 'required|string|max:255',
            'editingSousAction.description' => 'nullable|string',
            'editingSousAction.owner_id' => 'required|exists:users,id',
            'editingSousAction.date_echeance' => 'nullable|date',
            'editingSousAction.taux_avancement' => 'required|numeric|min:0|max:100',
            'editingSousAction.type' => 'required|in:normal,projet'
        ]);

        try {
            // Sauvegarder l'ancien propriétaire pour la notification
            $oldOwnerId = $this->editingSousAction->getOriginal('owner_id');
            $newOwnerId = $this->editingSousAction->owner_id;

            // Mettre à jour la sous-action
            $this->editingSousAction->save();

            // Envoyer notification au nouveau propriétaire s'il est différent
            if ($oldOwnerId != $newOwnerId) {
                Log::info('📧 Envoi notification changement responsable Sous-Action', [
                    'old_owner_id' => $oldOwnerId,
                    'new_owner_id' => $newOwnerId,
                    'sous_action_id' => $this->editingSousAction->id,
                    'sous_action_libelle' => $this->editingSousAction->libelle
                ]);

                $notificationSent = $this->sendNotification(
                    $newOwnerId,
                    'sous_action_assigned',
                    'Sous-Action assignée',
                    "Vous avez été assigné comme responsable de la sous-action : {$this->editingSousAction->libelle}",
                    ['sous_action_id' => $this->editingSousAction->id, 'action_id' => $this->selectedAction->id]
                );

                if ($notificationSent) {
                    Log::info('✅ Notification changement responsable Sous-Action envoyée avec succès');
                } else {
                    Log::warning('⚠️ Échec de l\'envoi de la notification changement responsable Sous-Action');
                }
            }

            // Mettre à jour le taux d'avancement de l'action parent
            $this->selectedAction->updateTauxAvancement();

            $this->closeEditSousActionModal();
            $this->dispatch('toast', 'success', 'Sous-action modifiée avec succès');
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            Log::error('❌ Erreur mise à jour Sous-Action', ['error' => $e->getMessage()]);
            $this->dispatch('toast', 'error', 'Erreur lors de la modification de la sous-action');
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

            $sousAction = SousAction::findOrFail($sousActionId);

            if (!$this->canDeleteSousAction($sousAction)) {
                Log::warning('🚫 Permission refusée pour supprimer Sous-Action', ['user_id' => Auth::id(), 'sous_action_id' => $sousActionId]);
                $this->dispatch('toast', 'error', 'Permission refusée ou sous-action liée');
                return;
            }

            $sousAction->delete();
            $this->selectedAction->updateTauxAvancement();
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
        return (method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral()) || 
               ($this->selectedObjectifStrategique && $user->id == $this->selectedObjectifStrategique->owner_id) ||
               ($this->selectedObjectifSpecifique && $user->id == $this->selectedObjectifSpecifique->owner_id);
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
            'taux_avancement' => 0,
            'type' => 'normal'
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
            'users' => $users,
            'canCreateObjectifStrategique' => $this->canCreateObjectifStrategique(),
            'canCreateObjectifSpecifique' => $this->canCreateObjectifSpecifique(),
            'canCreateAction' => $this->canCreateAction(),
            'canCreateSousAction' => $this->canCreateSousAction(),
            // Variables manquantes pour les permissions d'édition
            'canEditObjectifStrategique' => fn($objectifStrategique) => $this->canEditObjectifStrategique($objectifStrategique),
            'canEditObjectifSpecifique' => fn($objectifSpecifique) => $this->canEditObjectifSpecifique($objectifSpecifique),
            'canEditAction' => fn($action) => $this->canEditAction($action),
            'canEditSousAction' => fn($sousAction) => $this->canEditSousAction($sousAction),
            // Variables manquantes pour les permissions de suppression
            'canDeleteObjectifStrategique' => fn($objectifStrategique) => $this->canDeleteObjectifStrategique($objectifStrategique),
            'canDeleteObjectifSpecifique' => fn($objectifSpecifique) => $this->canDeleteObjectifSpecifique($objectifSpecifique),
            'canDeleteAction' => fn($action) => $this->canDeleteAction($action),
            'canDeleteSousAction' => fn($sousAction) => $this->canDeleteSousAction($sousAction)
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
                $osProgress = DB::table('objectifs_specifiques')
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
}
