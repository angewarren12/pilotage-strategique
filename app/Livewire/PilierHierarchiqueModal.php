<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pilier;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\Action;
use App\Models\SousAction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PilierHierarchiqueModal extends Component
{
    public $showModal = false;
    public $pilierId = null;
    public $pilier = null;
    public $isLoading = true;
    public $searchTerm = '';
    
    // Navigation et états
    public $currentView = 'pilier'; // pilier, objectifStrategique, objectifSpecifique, action, sousAction
    public $breadcrumb = [];
    
    // Données sélectionnées
    public $selectedObjectifStrategique = null;
    public $selectedObjectifSpecifique = null;
    public $selectedAction = null;
    public $selectedSousAction = null;
    
    // Formulaires de création
    public $showCreateObjectifForm = false;
    public $showCreateObjectifSpecifiqueForm = false;
    public $showCreateActionForm = false;
    public $showCreateSousActionForm = false;
    
    // Formulaires d'édition
    public $showEditObjectifStrategiqueForm = false;
    public $showEditObjectifSpecifiqueForm = false;
    public $showEditActionForm = false;
    public $showEditSousActionForm = false;
    
    // Données des formulaires
    public $newObjectifStrategique = [
        'code' => '',
        'libelle' => '',
        'description' => '',
        'owner_id' => ''
    ];
    
    public $newObjectifSpecifique = [
        'code' => '',
        'libelle' => '',
        'description' => '',
        'owner_id' => ''
    ];
    
    public $newAction = [
        'code' => '',
        'libelle' => '',
        'description' => '',
        'owner_id' => ''
    ];
    
    public $newSousAction = [
        'code' => '',
        'libelle' => '',
        'description' => '',
        'owner_id' => '',
        'date_echeance' => '',
        'taux_avancement' => 0
    ];
    
    // Données d'édition
    public $editObjectifStrategique = [];
    public $editObjectifSpecifique = [];
    public $editAction = [];
    public $editSousAction = [];
    
    // Détails sélectionnés
    public $selectedObjectifStrategiqueDetails = null;
    public $selectedObjectifSpecifiqueDetails = null;
    public $selectedActionDetails = null;
    public $selectedSousActionDetails = null;
    
    // États de chargement
    public $isLoadingObjectifs = false;
    public $isLoadingObjectifsSpecifiques = false;
    public $isLoadingActions = false;
    public $isLoadingSousActions = false;
    
    // Propriété pour identifier le composant
    public $componentType = 'hierarchique';

    protected $listeners = [
        'openPilierHierarchiqueModal' => 'openModal',
        'refreshHierarchique' => 'loadPilierData'
    ];

    public function mount()
    {
        $this->resetFormData();
    }

    public function openModal($data = null)
    {
        if ($data === null) {
            Log::info('🔄 [HIERARCHIQUE] Appel automatique ignoré');
            return; // Éviter l'appel automatique par Livewire
        }
        
        $pilierId = is_array($data) ? $data['pilierId'] : $data;
        Log::info('🔄 [HIERARCHIQUE] Méthode openModal appelée avec pilierId: ' . $pilierId);
        $this->pilierId = $pilierId;
        $this->showModal = true;
        $this->loadPilierData();
        $this->resetNavigation();
        Log::info('✅ [HIERARCHIQUE] Modal ouvert, showModal = ' . ($this->showModal ? 'true' : 'false'));
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFormData();
        $this->resetNavigation();
    }

    public function loadPilierData()
    {
        try {
            $this->isLoading = true;
            
            $this->pilier = Pilier::with([
                'objectifsStrategiques.objectifsSpecifiques.actions.sousActions',
                'objectifsStrategiques.objectifsSpecifiques.actions.owner',
                'objectifsStrategiques.objectifsSpecifiques.owner',
                'objectifsStrategiques.owner',
                'owner'
            ])->find($this->pilierId);
            
            if (!$this->pilier) {
                $this->dispatch('showToast', (object)['type' => 'error', 'message' => 'Pilier non trouvé']);
                return;
            }
            
            Log::info('Vue Hiérarchique - Données du pilier chargées', ['pilier_id' => $this->pilierId]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des données du pilier', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', (object)['type' => 'error', 'message' => 'Erreur lors du chargement des données']);
        } finally {
            $this->isLoading = false;
        }
    }

    public function resetNavigation()
    {
        $this->currentView = 'pilier';
        $this->breadcrumb = [];
        $this->selectedObjectifStrategique = null;
        $this->selectedObjectifSpecifique = null;
        $this->selectedAction = null;
        $this->selectedSousAction = null;
        $this->selectedObjectifStrategiqueDetails = null;
        $this->selectedObjectifSpecifiqueDetails = null;
        $this->selectedActionDetails = null;
        $this->selectedSousActionDetails = null;
    }

    public function resetFormData()
    {
        $this->newObjectifStrategique = ['code' => '', 'libelle' => '', 'description' => '', 'owner_id' => ''];
        $this->newObjectifSpecifique = ['code' => '', 'libelle' => '', 'description' => '', 'owner_id' => ''];
        $this->newAction = ['code' => '', 'libelle' => '', 'description' => '', 'owner_id' => ''];
        $this->newSousAction = ['code' => '', 'libelle' => '', 'description' => '', 'owner_id' => '', 'date_echeance' => '', 'taux_avancement' => 0];
        
        $this->editObjectifStrategique = [];
        $this->editObjectifSpecifique = [];
        $this->editAction = [];
        $this->editSousAction = [];
    }

    // Navigation
    public function naviguerVersObjectifStrategique($objectifStrategiqueId)
    {
        $this->selectedObjectifStrategique = ObjectifStrategique::with(['objectifsSpecifiques.actions.sousActions'])->find($objectifStrategiqueId);
        $this->currentView = 'objectifStrategique';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->libelle, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->selectedObjectifStrategique->libelle, 'id' => $objectifStrategiqueId]
        ];
    }

    public function naviguerVersObjectifSpecifique($objectifSpecifiqueId)
    {
        $this->selectedObjectifSpecifique = ObjectifSpecifique::with(['actions.sousActions'])->find($objectifSpecifiqueId);
        $this->currentView = 'objectifSpecifique';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->libelle, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->selectedObjectifStrategique->libelle, 'id' => $this->selectedObjectifStrategique->id],
            ['type' => 'objectifSpecifique', 'name' => $this->selectedObjectifSpecifique->libelle, 'id' => $objectifSpecifiqueId]
        ];
    }

    public function naviguerVersAction($actionId)
    {
        $this->selectedAction = Action::with(['sousActions'])->find($actionId);
        $this->currentView = 'action';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->libelle, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->selectedObjectifStrategique->libelle, 'id' => $this->selectedObjectifStrategique->id],
            ['type' => 'objectifSpecifique', 'name' => $this->selectedObjectifSpecifique->libelle, 'id' => $this->selectedObjectifSpecifique->id],
            ['type' => 'action', 'name' => $this->selectedAction->libelle, 'id' => $actionId]
        ];
    }

    public function naviguerVersSousAction($sousActionId)
    {
        $this->selectedSousAction = SousAction::find($sousActionId);
        $this->currentView = 'sousAction';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->libelle, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->selectedObjectifStrategique->libelle, 'id' => $this->selectedObjectifStrategique->id],
            ['type' => 'objectifSpecifique', 'name' => $this->selectedObjectifSpecifique->libelle, 'id' => $this->selectedObjectifSpecifique->id],
            ['type' => 'action', 'name' => $this->selectedAction->libelle, 'id' => $this->selectedAction->id],
            ['type' => 'sousAction', 'name' => $this->selectedSousAction->libelle, 'id' => $sousActionId]
        ];
    }

    public function retourListeObjectifs()
    {
        $this->currentView = 'pilier';
        $this->breadcrumb = [];
        $this->selectedObjectifStrategique = null;
        $this->selectedObjectifStrategiqueDetails = null;
    }
    
    public function naviguerVersPilier()
    {
        $this->currentView = 'pilier';
        $this->breadcrumb = [];
        $this->selectedObjectifStrategique = null;
        $this->selectedObjectifSpecifique = null;
        $this->selectedAction = null;
        $this->selectedSousAction = null;
        $this->selectedObjectifStrategiqueDetails = null;
        $this->selectedObjectifSpecifiqueDetails = null;
        $this->selectedActionDetails = null;
        $this->selectedSousActionDetails = null;
    }

    public function retourListeObjectifsSpecifiques()
    {
        $this->currentView = 'objectifStrategique';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->libelle, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->selectedObjectifStrategique->libelle, 'id' => $this->selectedObjectifStrategique->id]
        ];
        $this->selectedObjectifSpecifique = null;
        $this->selectedObjectifSpecifiqueDetails = null;
    }

    public function retourListeActions()
    {
        $this->currentView = 'objectifSpecifique';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->libelle, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->selectedObjectifStrategique->libelle, 'id' => $this->selectedObjectifStrategique->id],
            ['type' => 'objectifSpecifique', 'name' => $this->selectedObjectifSpecifique->libelle, 'id' => $this->selectedObjectifSpecifique->id]
        ];
        $this->selectedAction = null;
        $this->selectedActionDetails = null;
    }

    public function retourListeSousActions()
    {
        $this->currentView = 'action';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->libelle, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->selectedObjectifStrategique->libelle, 'id' => $this->selectedObjectifStrategique->id],
            ['type' => 'objectifSpecifique', 'name' => $this->selectedObjectifSpecifique->libelle, 'id' => $this->selectedObjectifSpecifique->id],
            ['type' => 'action', 'name' => $this->selectedAction->libelle, 'id' => $this->selectedAction->id]
        ];
        $this->selectedSousAction = null;
        $this->selectedSousActionDetails = null;
    }

    // Gestion des objectifs stratégiques
    public function showCreateObjectifForm()
    {
        $this->showCreateObjectifForm = true;
        $this->suggestNewObjectifCode();
    }

    public function cancelCreate()
    {
        $this->showCreateObjectifForm = false;
        $this->resetCreateForm();
    }

    public function suggestNewObjectifCode()
    {
        $lastObjectif = $this->pilier->objectifsStrategiques()->orderBy('code', 'desc')->first();
        if ($lastObjectif) {
            $lastNumber = (int) substr($lastObjectif->code, -1);
            $this->newObjectifStrategique['code'] = $this->pilier->code . '.OS' . ($lastNumber + 1);
        } else {
            $this->newObjectifStrategique['code'] = $this->pilier->code . '.OS1';
        }
    }

    public function resetCreateForm()
    {
        $this->newObjectifStrategique = ['code' => '', 'libelle' => '', 'description' => '', 'owner_id' => ''];
    }

    public function createObjectifStrategique()
    {
        $this->validate([
            'newObjectifStrategique.code' => 'required|string|max:50',
            'newObjectifStrategique.libelle' => 'required|string|max:255',
            'newObjectifStrategique.description' => 'nullable|string',
            'newObjectifStrategique.owner_id' => 'nullable|exists:users,id'
        ]);

        try {
            $objectifStrategique = new ObjectifStrategique($this->newObjectifStrategique);
            $objectifStrategique->pilier_id = $this->pilier->id;
            $objectifStrategique->save();

            $this->showCreateObjectifForm = false;
            $this->resetCreateForm();
            $this->loadPilierData();
            
            $this->dispatch('showToast', (object)['type' => 'success', 'message' => 'Objectif stratégique créé avec succès']);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'objectif stratégique', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', (object)['type' => 'error', 'message' => 'Erreur lors de la création']);
        }
    }

    public function deleteObjectifStrategique($objectifStrategiqueId)
    {
        try {
            $objectifStrategique = ObjectifStrategique::find($objectifStrategiqueId);
            if ($objectifStrategique) {
                $objectifStrategique->delete();
                $this->loadPilierData();
                $this->dispatch('showToast', (object)['type' => 'success', 'message' => 'Objectif stratégique supprimé']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', (object)['type' => 'error', 'message' => 'Erreur lors de la suppression']);
        }
    }
    
    public function editObjectifStrategique($objectifStrategiqueId)
    {
        $objectifStrategique = ObjectifStrategique::find($objectifStrategiqueId);
        if ($objectifStrategique) {
            $this->editObjectifStrategique = [
                'id' => $objectifStrategique->id,
                'code' => $objectifStrategique->code,
                'libelle' => $objectifStrategique->libelle,
                'description' => $objectifStrategique->description,
                'owner_id' => $objectifStrategique->owner_id
            ];
            $this->showEditObjectifStrategiqueForm = true;
        }
    }

    // Gestion des objectifs spécifiques
    public function showCreateObjectifSpecifiqueForm()
    {
        $this->showCreateObjectifSpecifiqueForm = true;
        $this->suggestNewObjectifSpecifiqueCode();
    }

    public function cancelCreateObjectifSpecifique()
    {
        $this->showCreateObjectifSpecifiqueForm = false;
        $this->resetObjectifSpecifiqueForm();
    }

    public function suggestNewObjectifSpecifiqueCode()
    {
        $lastObjectifSpecifique = $this->selectedObjectifStrategique->objectifsSpecifiques()->orderBy('code', 'desc')->first();
        if ($lastObjectifSpecifique) {
            $lastNumber = (int) substr($lastObjectifSpecifique->code, -1);
            $this->newObjectifSpecifique['code'] = $this->selectedObjectifStrategique->code . '.PIL' . ($lastNumber + 1);
        } else {
            $this->newObjectifSpecifique['code'] = $this->selectedObjectifStrategique->code . '.PIL1';
        }
    }

    public function resetObjectifSpecifiqueForm()
    {
        $this->newObjectifSpecifique = ['code' => '', 'libelle' => '', 'description' => '', 'owner_id' => ''];
    }

    public function createObjectifSpecifique()
    {
        $this->validate([
            'newObjectifSpecifique.code' => 'required|string|max:50',
            'newObjectifSpecifique.libelle' => 'required|string|max:255',
            'newObjectifSpecifique.description' => 'nullable|string',
            'newObjectifSpecifique.owner_id' => 'nullable|exists:users,id'
        ]);

        try {
            $objectifSpecifique = new ObjectifSpecifique($this->newObjectifSpecifique);
            $objectifSpecifique->objectif_strategique_id = $this->selectedObjectifStrategique->id;
            $objectifSpecifique->save();

            $this->showCreateObjectifSpecifiqueForm = false;
            $this->resetObjectifSpecifiqueForm();
            $this->loadPilierData();
            
            $this->dispatch('showToast', (object)['type' => 'success', 'message' => 'Objectif spécifique créé avec succès']);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'objectif spécifique', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', (object)['type' => 'error', 'message' => 'Erreur lors de la création']);
        }
    }
    
    public function editObjectifSpecifique($objectifSpecifiqueId)
    {
        $objectifSpecifique = ObjectifSpecifique::find($objectifSpecifiqueId);
        if ($objectifSpecifique) {
            $this->editObjectifSpecifique = [
                'id' => $objectifSpecifique->id,
                'code' => $objectifSpecifique->code,
                'libelle' => $objectifSpecifique->libelle,
                'description' => $objectifSpecifique->description,
                'owner_id' => $objectifSpecifique->owner_id
            ];
            $this->showEditObjectifSpecifiqueForm = true;
        }
    }
    
    public function deleteObjectifSpecifique($objectifSpecifiqueId)
    {
        try {
            $objectifSpecifique = ObjectifSpecifique::find($objectifSpecifiqueId);
            if ($objectifSpecifique) {
                $objectifSpecifique->delete();
                $this->loadPilierData();
                $this->dispatch('showToast', (object)['type' => 'success', 'message' => 'Objectif spécifique supprimé']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', (object)['type' => 'error', 'message' => 'Erreur lors de la suppression']);
        }
    }

    // Gestion des actions
    public function showCreateActionForm()
    {
        $this->showCreateActionForm = true;
        $this->suggestNewActionCode();
    }

    public function cancelCreateAction()
    {
        $this->showCreateActionForm = false;
        $this->resetActionForm();
    }

    public function suggestNewActionCode()
    {
        $lastAction = $this->selectedObjectifSpecifique->actions()->orderBy('code', 'desc')->first();
        if ($lastAction) {
            $lastNumber = (int) substr($lastAction->code, -1);
            $this->newAction['code'] = $this->selectedObjectifSpecifique->code . '.A' . ($lastNumber + 1);
        } else {
            $this->newAction['code'] = $this->selectedObjectifSpecifique->code . '.A1';
        }
    }

    public function resetActionForm()
    {
        $this->newAction = ['code' => '', 'libelle' => '', 'description' => '', 'owner_id' => ''];
    }

    public function createAction()
    {
        $this->validate([
            'newAction.code' => 'required|string|max:50',
            'newAction.libelle' => 'required|string|max:255',
            'newAction.description' => 'nullable|string',
            'newAction.owner_id' => 'nullable|exists:users,id'
        ]);

        try {
            $action = new Action($this->newAction);
            $action->objectif_specifique_id = $this->selectedObjectifSpecifique->id;
            $action->save();

            $this->showCreateActionForm = false;
            $this->resetActionForm();
            $this->loadPilierData();
            
            $this->dispatch('showToast', (object)['type' => 'success', 'message' => 'Action créée avec succès']);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'action', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', (object)['type' => 'error', 'message' => 'Erreur lors de la création']);
        }
    }
    
    public function editAction($actionId)
    {
        $action = Action::find($actionId);
        if ($action) {
            $this->editAction = [
                'id' => $action->id,
                'code' => $action->code,
                'libelle' => $action->libelle,
                'description' => $action->description,
                'owner_id' => $action->owner_id
            ];
            $this->showEditActionForm = true;
        }
    }
    
    public function deleteAction($actionId)
    {
        try {
            $action = Action::find($actionId);
            if ($action) {
                $action->delete();
                $this->loadPilierData();
                $this->dispatch('showToast', (object)['type' => 'success', 'message' => 'Action supprimée']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', (object)['type' => 'error', 'message' => 'Erreur lors de la suppression']);
        }
    }

    // Gestion des sous-actions
    public function showCreateSousActionForm()
    {
        $this->showCreateSousActionForm = true;
        $this->suggestNewSousActionCode();
    }

    public function cancelCreateSousAction()
    {
        $this->showCreateSousActionForm = false;
        $this->resetSousActionForm();
    }

    public function suggestNewSousActionCode()
    {
        $lastSousAction = $this->selectedAction->sousActions()->orderBy('code', 'desc')->first();
        if ($lastSousAction) {
            $lastNumber = (int) substr($lastSousAction->code, -1);
            $this->newSousAction['code'] = $this->selectedAction->code . '.SA' . ($lastNumber + 1);
        } else {
            $this->newSousAction['code'] = $this->selectedAction->code . '.SA1';
        }
    }

    public function resetSousActionForm()
    {
        $this->newSousAction = ['code' => '', 'libelle' => '', 'description' => '', 'owner_id' => '', 'date_echeance' => '', 'taux_avancement' => 0];
    }

    public function createSousAction()
    {
        $this->validate([
            'newSousAction.code' => 'required|string|max:50',
            'newSousAction.libelle' => 'required|string|max:255',
            'newSousAction.description' => 'nullable|string',
            'newSousAction.owner_id' => 'nullable|exists:users,id',
            'newSousAction.date_echeance' => 'nullable|date',
            'newSousAction.taux_avancement' => 'required|numeric|min:0|max:100'
        ]);

        try {
            $sousAction = new SousAction($this->newSousAction);
            $sousAction->action_id = $this->selectedAction->id;
            
            // Si le taux est 100%, définir la date de réalisation
            if ($sousAction->taux_avancement == 100) {
                $sousAction->date_realisation = now();
            }
            
            $sousAction->save();

            $this->showCreateSousActionForm = false;
            $this->resetSousActionForm();
            $this->loadPilierData();
            
            $this->dispatch('showToast', (object)['type' => 'success', 'message' => 'Sous-action créée avec succès']);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la sous-action', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', (object)['type' => 'error', 'message' => 'Erreur lors de la création']);
        }
    }
    
    public function editSousAction($sousActionId)
    {
        $sousAction = SousAction::find($sousActionId);
        if ($sousAction) {
            $this->editSousAction = [
                'id' => $sousAction->id,
                'code' => $sousAction->code,
                'libelle' => $sousAction->libelle,
                'description' => $sousAction->description,
                'owner_id' => $sousAction->owner_id,
                'date_echeance' => $sousAction->date_echeance ? $sousAction->date_echeance->format('Y-m-d') : '',
                'taux_avancement' => $sousAction->taux_avancement
            ];
            $this->showEditSousActionForm = true;
        }
    }
    
    public function deleteSousAction($sousActionId)
    {
        try {
            $sousAction = SousAction::find($sousActionId);
            if ($sousAction) {
                $sousAction->delete();
                $this->loadPilierData();
                $this->dispatch('showToast', (object)['type' => 'success', 'message' => 'Sous-action supprimée']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', (object)['type' => 'error', 'message' => 'Erreur lors de la suppression']);
        }
    }

    // Mise à jour des taux en temps réel
    public function updateSousActionTaux($sousActionId, $taux)
    {
        try {
            $sousAction = SousAction::find($sousActionId);
            if ($sousAction) {
                $sousAction->taux_avancement = $taux;
                
                // Si le taux atteint 100%, définir la date de réalisation
                if ($taux == 100 && !$sousAction->date_realisation) {
                    $sousAction->date_realisation = now();
                }
                
                $sousAction->save();
                
                // Mettre à jour les taux parents
                $this->updateParentRates($sousAction);
                
                $this->loadPilierData();
                
                $this->dispatch('showToast', (object)['type' => 'success', 'message' => 'Taux mis à jour']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du taux', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', (object)['type' => 'error', 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    public function updateParentRates($sousAction)
    {
        // Mettre à jour le taux de l'action
        $action = $sousAction->action;
        if ($action) {
            $sousActions = $action->sousActions;
            if ($sousActions->count() > 0) {
                $action->taux_avancement = $sousActions->avg('taux_avancement');
                $action->save();
                
                // Mettre à jour le taux de l'objectif spécifique
                $objectifSpecifique = $action->objectifSpecifique;
                if ($objectifSpecifique) {
                    $actions = $objectifSpecifique->actions;
                    if ($actions->count() > 0) {
                        $objectifSpecifique->taux_avancement = $actions->avg('taux_avancement');
                        $objectifSpecifique->save();
                        
                        // Mettre à jour le taux de l'objectif stratégique
                        $objectifStrategique = $objectifSpecifique->objectifStrategique;
                        if ($objectifStrategique) {
                            $objectifsSpecifiques = $objectifStrategique->objectifsSpecifiques;
                            if ($objectifsSpecifiques->count() > 0) {
                                $objectifStrategique->taux_avancement = $objectifsSpecifiques->avg('taux_avancement');
                                $objectifStrategique->save();
                                
                                // Mettre à jour le taux du pilier
                                $pilier = $objectifStrategique->pilier;
                                if ($pilier) {
                                    $objectifsStrategiques = $pilier->objectifsStrategiques;
                                    if ($objectifsStrategiques->count() > 0) {
                                        $pilier->taux_avancement = $objectifsStrategiques->avg('taux_avancement');
                                        $pilier->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // Propriétés calculées
    public function getUsersProperty()
    {
        return User::whereHas('role', function($query) {
            $query->whereIn('name', ['admin', 'owner_pil', 'owner_action']);
        })->get();
    }

    public function getProgressStatus($taux)
    {
        if ($taux >= 100) return 'success';
        if ($taux >= 75) return 'info';
        if ($taux >= 50) return 'warning';
        return 'danger';
    }

    public function calculateEcart($dateEcheance, $dateRealisation)
    {
        if (!$dateEcheance) return null;
        
        $echeance = Carbon::parse($dateEcheance);
        $realisation = $dateRealisation ? Carbon::parse($dateRealisation) : Carbon::now();
        
        $diff = $echeance->diffInDays($realisation, false);
        
        if ($diff < 0) {
            return abs($diff) . 'J en avance';
        } elseif ($diff > 0) {
            return $diff . 'J de retard';
        } else {
            return 'À jour';
        }
    }

    public function render()
    {
        return view('livewire.pilier-hierarchique-modal');
    }
} 