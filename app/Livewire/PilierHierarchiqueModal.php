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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\ObjectifStrategiqueAssigned;
use Illuminate\Support\Facades\Schema;

class PilierHierarchiqueModal extends Component
{
    // Debug: Composant rechargé pour corriger les méthodes manquantes
    public $showModal = false;
    

    
    // Méthodes d'édition avec la même logique que PilierDetailsModalNew
    public function setActionToEditObjectifStrategique($objectifStrategiqueId)
    {
        $this->dispatch('console.log', '🚀 setActionToEditObjectifStrategique appelée pour ID: ' . $objectifStrategiqueId);
        
        try {
            $objectifStrategique = ObjectifStrategique::find($objectifStrategiqueId);
            if ($objectifStrategique) {
                $this->editingObjectifStrategique = [
                    'id' => $objectifStrategique->id,
                    'code' => $objectifStrategique->code,
                    'libelle' => $objectifStrategique->libelle,
                    'description' => $objectifStrategique->description,
                    'owner_id' => $objectifStrategique->owner_id ?: ''
                ];
                $this->showEditObjectifStrategiqueForm = true;
                $this->dispatch('console.log', '✅ Données d\'édition chargées pour objectif stratégique');
            }
        } catch (\Exception $e) {
            $this->dispatch('console.log', '❌ Erreur lors du chargement des données d\'édition: ' . $e->getMessage());
        }
    }
    
    public function setActionToEditObjectifSpecifique($objectifSpecifiqueId)
    {
        $this->dispatch('console.log', '🚀 setActionToEditObjectifSpecifique appelée pour ID: ' . $objectifSpecifiqueId);
        
        try {
            $objectifSpecifique = ObjectifSpecifique::find($objectifSpecifiqueId);
            if ($objectifSpecifique) {
                $this->editingObjectifSpecifique = [
                    'id' => $objectifSpecifique->id,
                    'code' => $objectifSpecifique->code,
                    'libelle' => $objectifSpecifique->libelle,
                    'description' => $objectifSpecifique->description,
                    'owner_id' => $objectifSpecifique->owner_id ?: ''
                ];
                $this->showEditObjectifSpecifiqueForm = true;
                $this->dispatch('console.log', '✅ Données d\'édition chargées pour objectif spécifique');
            }
        } catch (\Exception $e) {
            $this->dispatch('console.log', '❌ Erreur lors du chargement des données d\'édition: ' . $e->getMessage());
        }
    }
    
    public function setActionToEditAction($actionId)
    {
        $this->dispatch('console.log', '🚀 setActionToEditAction appelée pour ID: ' . $actionId);
        
        try {
            $action = Action::find($actionId);
            if ($action) {
                $this->editingAction = [
                    'id' => $action->id,
                    'code' => $action->code,
                    'libelle' => $action->libelle,
                    'description' => $action->description,
                    'owner_id' => $action->owner_id ?: ''
                ];
                $this->showEditActionForm = true;
                $this->dispatch('console.log', '✅ Données d\'édition chargées pour action');
            }
        } catch (\Exception $e) {
            $this->dispatch('console.log', '❌ Erreur lors du chargement des données d\'édition: ' . $e->getMessage());
        }
    }
    
    public function setActionToEditSousAction($sousActionId)
    {
        $this->dispatch('console.log', '🚀 setActionToEditSousAction appelée pour ID: ' . $sousActionId);
        
        try {
            $sousAction = SousAction::find($sousActionId);
            if ($sousAction) {
                $this->editingSousAction = [
                    'id' => $sousAction->id,
                    'code' => $sousAction->code,
                    'libelle' => $sousAction->libelle,
                    'description' => $sousAction->description,
                    'owner_id' => $sousAction->owner_id ?: '',
                    'date_echeance' => $sousAction->date_echeance ?: '',
                    'taux_avancement' => $sousAction->taux_avancement
                ];
                $this->showEditSousActionForm = true;
                $this->dispatch('console.log', '✅ Données d\'édition chargées pour sous-action');
            }
        } catch (\Exception $e) {
            $this->dispatch('console.log', '❌ Erreur lors du chargement des données d\'édition: ' . $e->getMessage());
        }
    }
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
        'type' => '',
        'owner_id' => '',
        'date_echeance' => '',
        'taux_avancement' => 0
    ];
    

    
    // Données d'édition
    public $editObjectifStrategique = [];
    public $editObjectifSpecifique = [];
    public $editAction = [];
    public $editSousAction = [];
    
    // Données d'édition avec wire:model
    public $editingObjectifStrategique = [
        'code' => '',
        'libelle' => '',
        'description' => '',
        'owner_id' => ''
    ];
    
    public $editingObjectifSpecifique = [
        'code' => '',
        'libelle' => '',
        'description' => '',
        'owner_id' => ''
    ];
    
    public $editingAction = [
        'code' => '',
        'libelle' => '',
        'description' => '',
        'owner_id' => ''
    ];
    
    public $editingSousAction = [
        'code' => '',
        'libelle' => '',
        'description' => '',
        'type' => '',
        'owner_id' => '',
        'date_echeance' => '',
        'date_realisation' => '',
        'taux_avancement' => 0
    ];
    

    
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
    
    // États des modals
    
    // Propriété pour identifier le composant
    public $componentType = 'hierarchique';
    
    // Propriété users comme fallback
    public $users = [];
    
    // Propriété de débogage pour le slider
    public $debugSliderValue = 0;

    protected $listeners = [
        'openPilierHierarchiqueModal' => 'openModal',
        'refreshHierarchique' => 'loadPilierData'
    ];

    public function mount()
    {
        $this->resetFormData();
        $this->users = $this->getUsersProperty();
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
                $this->dispatch('showToast', ['type' => 'error', 'message' => 'Pilier non trouvé']);
                return;
            }
            
            Log::info('Vue Hiérarchique - Données du pilier chargées', ['pilier_id' => $this->pilierId]);
            
            // Mettre à jour la liste des users
            $this->users = $this->getUsersProperty();
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des données du pilier', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors du chargement des données']);
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

        
        $this->editObjectifStrategique = [];
        $this->editObjectifSpecifique = [];
        $this->editAction = [];
        $this->editSousAction = [];
        
        // Réinitialiser les propriétés d'affichage des formulaires
        $this->showCreateObjectifForm = false;
        $this->showCreateObjectifSpecifiqueForm = false;
        $this->showCreateActionForm = false;
        $this->showCreateSousActionForm = false;
        $this->showEditObjectifStrategiqueForm = false;
        $this->showEditObjectifSpecifiqueForm = false;
        $this->showEditActionForm = false;
    }

    // Navigation
    public function naviguerVersObjectifStrategique($objectifStrategiqueId)
    {
        $this->dispatch('console.log', 'DEBUG: naviguerVersObjectifStrategique - AVANT navigation', [
            'currentView' => $this->currentView,
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selectedAction' => $this->selectedAction ? $this->selectedAction->id : 'null',
            'selectedSousAction' => $this->selectedSousAction ? $this->selectedSousAction->id : 'null',
        ]);

        $this->selectedObjectifStrategique = ObjectifStrategique::with(['objectifsSpecifiques.actions.sousActions'])->find($objectifStrategiqueId);
        $this->currentView = 'objectifStrategique';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->code, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code, 'id' => $objectifStrategiqueId]
        ];

        $this->dispatch('console.log', 'DEBUG: naviguerVersObjectifStrategique - APRÈS navigation', [
            'currentView' => $this->currentView,
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selectedAction' => $this->selectedAction ? $this->selectedAction->id : 'null',
            'selectedSousAction' => $this->selectedSousAction ? $this->selectedSousAction->id : 'null',
        ]);
    }

    public function naviguerVersObjectifSpecifique($objectifSpecifiqueId)
    {
        $this->dispatch('console.log', 'DEBUG: naviguerVersObjectifSpecifique - AVANT navigation', [
            'currentView' => $this->currentView,
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selectedAction' => $this->selectedAction ? $this->selectedAction->id : 'null',
            'selectedSousAction' => $this->selectedSousAction ? $this->selectedSousAction->id : 'null',
        ]);

        $this->selectedObjectifSpecifique = ObjectifSpecifique::with(['actions.sousActions'])->find($objectifSpecifiqueId);
        $this->currentView = 'objectifSpecifique';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->code, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code, 'id' => $this->selectedObjectifStrategique->id],
            ['type' => 'objectifSpecifique', 'name' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code . '.' . $this->selectedObjectifSpecifique->code, 'id' => $objectifSpecifiqueId]
        ];

        $this->dispatch('console.log', 'DEBUG: naviguerVersObjectifSpecifique - APRÈS navigation', [
            'currentView' => $this->currentView,
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selectedAction' => $this->selectedAction ? $this->selectedAction->id : 'null',
            'selectedSousAction' => $this->selectedSousAction ? $this->selectedSousAction->id : 'null',
        ]);
    }

    public function naviguerVersAction($actionId)
    {
        $this->dispatch('console.log', 'DEBUG: naviguerVersAction - AVANT navigation', [
            'currentView' => $this->currentView,
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selectedAction' => $this->selectedAction ? $this->selectedAction->id : 'null',
            'selectedSousAction' => $this->selectedSousAction ? $this->selectedSousAction->id : 'null',
        ]);

        $this->selectedAction = Action::with(['sousActions'])->find($actionId);
        $this->currentView = 'action';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->code, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code, 'id' => $this->selectedObjectifStrategique->id],
            ['type' => 'objectifSpecifique', 'name' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code . '.' . $this->selectedObjectifSpecifique->code, 'id' => $this->selectedObjectifSpecifique->id],
            ['type' => 'action', 'name' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code . '.' . $this->selectedObjectifSpecifique->code . '.' . $this->selectedAction->code, 'id' => $actionId]
        ];

        $this->dispatch('console.log', 'DEBUG: naviguerVersAction - APRÈS navigation', [
            'currentView' => $this->currentView,
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selectedAction' => $this->selectedAction ? $this->selectedAction->id : 'null',
            'selectedSousAction' => $this->selectedSousAction ? $this->selectedSousAction->id : 'null',
        ]);
    }

    public function naviguerVersSousAction($sousActionId)
    {
        $this->selectedSousAction = SousAction::find($sousActionId);
        $this->currentView = 'sousAction';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->code, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code, 'id' => $this->selectedObjectifStrategique->id],
            ['type' => 'objectifSpecifique', 'name' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code . '.' . $this->selectedObjectifSpecifique->code, 'id' => $this->selectedObjectifSpecifique->id],
            ['type' => 'action', 'name' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code . '.' . $this->selectedObjectifSpecifique->code . '.' . $this->selectedAction->code, 'id' => $this->selectedAction->id],
            ['type' => 'sousAction', 'name' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code . '.' . $this->selectedObjectifSpecifique->code . '.' . $this->selectedAction->code . '.' . $this->selectedSousAction->code, 'id' => $sousActionId]
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
        $this->dispatch('console.log', 'DEBUG: retourListeObjectifsSpecifiques - AVANT réinitialisation', [
            'currentView' => $this->currentView,
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selectedAction' => $this->selectedAction ? $this->selectedAction->id : 'null',
            'selectedSousAction' => $this->selectedSousAction ? $this->selectedSousAction->id : 'null',
        ]);

        $this->currentView = 'objectifStrategique';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->code, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code, 'id' => $this->selectedObjectifStrategique->id]
        ];
        $this->selectedObjectifSpecifique = null;
        $this->selectedObjectifSpecifiqueDetails = null;
        $this->selectedAction = null;
        $this->selectedActionDetails = null;
        $this->selectedSousAction = null;
        $this->selectedSousActionDetails = null;

        $this->dispatch('console.log', 'DEBUG: retourListeObjectifsSpecifiques - APRÈS réinitialisation', [
            'currentView' => $this->currentView,
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selectedAction' => $this->selectedAction ? $this->selectedAction->id : 'null',
            'selectedSousAction' => $this->selectedSousAction ? $this->selectedSousAction->id : 'null',
        ]);
    }

    public function retourListeActions()
    {
        $this->dispatch('console.log', 'DEBUG: retourListeActions - AVANT réinitialisation', [
            'currentView' => $this->currentView,
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selectedAction' => $this->selectedAction ? $this->selectedAction->id : 'null',
            'selectedSousAction' => $this->selectedSousAction ? $this->selectedSousAction->id : 'null',
        ]);

        $this->currentView = 'objectifSpecifique';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->code, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code, 'id' => $this->selectedObjectifStrategique->id],
            ['type' => 'objectifSpecifique', 'name' => $this->pilier->code . '.' . $this->selectedObjectifStrategique->code . '.' . $this->selectedObjectifSpecifique->code, 'id' => $this->selectedObjectifSpecifique->id]
        ];
        $this->selectedAction = null;
        $this->selectedActionDetails = null;
        $this->selectedSousAction = null;
        $this->selectedSousActionDetails = null;

        $this->dispatch('console.log', 'DEBUG: retourListeActions - APRÈS réinitialisation', [
            'currentView' => $this->currentView,
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selectedAction' => $this->selectedAction ? $this->selectedAction->id : 'null',
            'selectedSousAction' => $this->selectedSousAction ? $this->selectedSousAction->id : 'null',
        ]);
    }

    public function retourListeSousActions()
    {
        $this->dispatch('console.log', 'DEBUG: retourListeSousActions - AVANT réinitialisation', [
            'currentView' => $this->currentView,
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selectedAction' => $this->selectedAction ? $this->selectedAction->id : 'null',
            'selectedSousAction' => $this->selectedSousAction ? $this->selectedSousAction->id : 'null',
        ]);

        $this->currentView = 'action';
        $this->breadcrumb = [
            ['type' => 'pilier', 'name' => $this->pilier->libelle, 'id' => $this->pilier->id],
            ['type' => 'objectifStrategique', 'name' => $this->selectedObjectifStrategique->libelle, 'id' => $this->selectedObjectifStrategique->id],
            ['type' => 'objectifSpecifique', 'name' => $this->selectedObjectifSpecifique->libelle, 'id' => $this->selectedObjectifSpecifique->id],
            ['type' => 'action', 'name' => $this->selectedAction->libelle, 'id' => $this->selectedAction->id]
        ];
        $this->selectedSousAction = null;
        $this->selectedSousActionDetails = null;

        $this->dispatch('console.log', 'DEBUG: retourListeSousActions - APRÈS réinitialisation', [
            'currentView' => $this->currentView,
            'selectedObjectifSpecifique' => $this->selectedObjectifSpecifique ? $this->selectedObjectifSpecifique->id : 'null',
            'selectedAction' => $this->selectedAction ? $this->selectedAction->id : 'null',
            'selectedSousAction' => $this->selectedSousAction ? $this->selectedSousAction->id : 'null',
        ]);
    }

    // NOUVELLES MÉTHODES DE CRÉATION - NOMS DIFFÉRENTS
    public function openCreateOSModal()
    {
        Log::info('🚀 [MODAL] openCreateOSModal appelée');
        Log::info('📊 [MODAL] État avant ouverture', [
            'showCreateObjectifForm' => $this->showCreateObjectifForm,
            'pilier_id' => $this->pilier->id ?? 'null'
        ]);
        
        $this->showCreateObjectifForm = true;
        
        Log::info('✅ [MODAL] showCreateObjectifForm mis à true');
        Log::info('📊 [MODAL] État après ouverture', [
            'showCreateObjectifForm' => $this->showCreateObjectifForm
        ]);
        
        // Dispatch un événement pour forcer l'affichage CSS du modal
        $this->dispatch('force-modal-css', [
            'type' => 'create-os',
            'timestamp' => now()->timestamp
        ]);
        
        // Dispatch un événement pour le debug
        $this->dispatch('console.log', '🚀 [MODAL] openCreateOSModal - showCreateObjectifForm = true');
        
        // Forcer le re-rendu du composant de plusieurs façons
        $this->dispatch('$refresh');
        
        // Attendre un peu puis forcer à nouveau
        $this->dispatch('console.log', '🔄 [MODAL] Forçage du re-rendu...');
        
        // Vérifier que la propriété est bien à true
        Log::info('🔍 [MODAL] Vérification finale - showCreateObjectifForm:', ['value' => $this->showCreateObjectifForm]);
    }

    public function closeCreateOSModal()
    {
        $this->showCreateObjectifForm = false;
    }

    public function saveNewOS($formData = null)
    {
        $this->dispatch('console.log', '🚀 [DEBUG] saveNewOS() appelée');
        
        try {
            // Vérifier que l'utilisateur est admin général
            /** @var User $user */
            $user = Auth::user();
            $this->dispatch('console.log', '👤 [DEBUG] Utilisateur connecté:', $user->email);
            $this->dispatch('console.log', '🔐 [DEBUG] isAdminGeneral:', $user->isAdminGeneral());
            
            if (!$user->isAdminGeneral()) {
                $this->dispatch('console.log', '❌ [DEBUG] Accès refusé - Utilisateur non admin');
                $this->dispatch('showToast', ['type' => 'error', 'message' => 'Accès non autorisé. Seuls les administrateurs généraux peuvent créer des objectifs stratégiques.']);
                return;
            }

            $this->dispatch('console.log', '✅ [DEBUG] Permissions OK, validation en cours...');
            
            // Utiliser les données du formulaire Bootstrap ou les données Livewire
            $data = $formData ?: $this->newObjectifStrategique;
            $this->dispatch('console.log', '📋 [DEBUG] Données à valider:', $data);
            
            // Validation des données
            $validatedData = $this->validate([
                'data.code' => 'required|string|max:50',
                'data.libelle' => 'required|string|max:255',
                'data.description' => 'nullable|string',
                'data.owner_id' => 'nullable|exists:users,id'
            ]);

            $this->dispatch('console.log', '✅ [DEBUG] Validation OK, création en cours...');
            $this->dispatch('console.log', '🏗️ [DEBUG] Pilier ID:', $this->pilier->id);

            $objectifStrategique = new ObjectifStrategique($validatedData['data']);
            $objectifStrategique->pilier_id = $this->pilier->id;
            $objectifStrategique->save();

            $this->dispatch('console.log', '✅ [DEBUG] Objectif stratégique créé avec succès, ID:', $objectifStrategique->id);

            // Envoyer une notification à l'utilisateur assigné si un owner est spécifié
            if ($validatedData['data']['owner_id']) {
                $this->dispatch('console.log', '📧 [DEBUG] Envoi de notification à l\'utilisateur ID:', $validatedData['data']['owner_id']);
                $owner = User::find($validatedData['data']['owner_id']);
                if ($owner) {
                    $this->dispatch('console.log', '👤 [DEBUG] Owner trouvé:', $owner->email);
                    $this->dispatch('console.log', '📧 [DEBUG] Envoi de la notification ObjectifStrategiqueAssigned...');
                    
                    $this->dispatch('console.log', '📧 [DEBUG] Création de la notification ObjectifStrategiqueAssigned...');
                    // Créer la notification dans la table personnalisée
                    $notificationId = DB::table('notifications')->insertGetId([
                        'user_id' => $owner->id,
                        'type' => 'objectif_strategique_assigned',
                        'title' => 'Nouvel objectif stratégique assigné',
                        'message' => "Un nouvel objectif stratégique vous a été assigné : {$objectifStrategique->code} - {$objectifStrategique->libelle}",
                        'data' => json_encode([
                            'objectif_strategique_id' => $objectifStrategique->id,
                            'objectif_strategique_code' => $objectifStrategique->code,
                            'objectif_strategique_libelle' => $objectifStrategique->libelle,
                            'pilier_id' => $objectifStrategique->pilier_id,
                            'pilier_code' => $this->pilier->code,
                            'createur_id' => Auth::user()->id,
                        ]),
                        'priority' => 'normal',
                        'channel' => 'database',
                        'is_sent' => true,
                        'sent_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $this->dispatch('console.log', '✅ [DEBUG] Notification personnalisée créée avec ID:', $notificationId);
                    
                    Log::info('Notification personnalisée créée pour l\'owner:', [
                        'notification_id' => $notificationId,
                        'owner_id' => $owner->id, 
                        'owner_email' => $owner->email,
                        'owner_name' => $owner->name,
                        'objectif_strategique_id' => $objectifStrategique->id,
                        'objectif_strategique_code' => $objectifStrategique->code,
                        'createur_id' => Auth::user()->id,
                        'createur_email' => Auth::user()->email
                    ]);
                    
                    $this->dispatch('console.log', '✅ [DEBUG] Notification personnalisée envoyée à l\'owner:', $owner->email);
                } else {
                    $this->dispatch('console.log', '⚠️ [DEBUG] Utilisateur owner non trouvé avec ID:', $validatedData['data']['owner_id']);
                    Log::warning('Owner non trouvé lors de la création d\'objectif stratégique', [
                        'owner_id_demande' => $validatedData['data']['owner_id'],
                        'objectif_strategique_id' => $objectifStrategique->id
                    ]);
                }
            }

            // Recharger les données du pilier
            $this->chargerDonneesPilier($this->pilier->id);

            // Réinitialiser le formulaire
            $this->newObjectifStrategique = [
                'code' => '',
                'libelle' => '',
                'description' => '',
                'owner_id' => ''
            ];

            // Afficher un message de succès
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Objectif stratégique créé avec succès !']);

            $this->dispatch('console.log', '🎉 [DEBUG] Création terminée avec succès !');
            
        } catch (\Exception $e) {
            $this->dispatch('console.log', '❌ [DEBUG] Erreur lors de la création:', $e->getMessage());
            Log::error('Erreur lors de la création d\'objectif stratégique', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la création : ' . $e->getMessage()]);
        }
    }

    public function deleteObjectifStrategique($objectifStrategiqueId)
    {
        try {
            $objectifStrategique = ObjectifStrategique::find($objectifStrategiqueId);
            if ($objectifStrategique) {
                // Vérifier les permissions : seul l'admin ou le owner peut supprimer
                /** @var User $user */
                $user = Auth::user();
                if (!$user->canDeleteObjectifStrategique($objectifStrategique)) {
                    $this->dispatch('showToast', ['type' => 'error', 'message' => 'Accès non autorisé. Seuls l\'administrateur général et le propriétaire de cet objectif stratégique peuvent le supprimer.']);
                    return;
                }

                // Vérifier s'il y a des objectifs spécifiques liés
                if ($objectifStrategique->objectifsSpecifiques()->count() > 0) {
                    $this->dispatch('showToast', ['type' => 'error', 'message' => 'Impossible de supprimer cet objectif stratégique car il contient des objectifs spécifiques.']);
                    return;
                }

                $objectifStrategique->delete();
                $this->loadPilierData();
                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Objectif stratégique supprimé']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'objectif stratégique', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la suppression']);
        }
    }
    
    public function showEditObjectifStrategiqueForm($objectifStrategiqueId)
    {
        $this->dispatch('showToast', ['type' => 'info', 'message' => 'Méthode appelée avec ID: ' . $objectifStrategiqueId]);
        
        $objectifStrategique = ObjectifStrategique::find($objectifStrategiqueId);
        
        if ($objectifStrategique) {
            // Vérifier les permissions : seul l'admin ou le owner peut éditer
            /** @var User $user */
            $user = Auth::user();
            if (!$user->canEditObjectifStrategique($objectifStrategique)) {
                $this->dispatch('showToast', ['type' => 'error', 'message' => 'Accès non autorisé. Seuls l\'administrateur général et le propriétaire de cet objectif stratégique peuvent le modifier.']);
                return;
            }

            $this->editingObjectifStrategique = [
                'id' => $objectifStrategique->id,
                'code' => $objectifStrategique->code,
                'libelle' => $objectifStrategique->libelle,
                'description' => $objectifStrategique->description,
                'owner_id' => $objectifStrategique->owner_id
            ];
            $this->showEditObjectifStrategiqueForm = true;
            
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Modal d\'édition ouvert pour: ' . $objectifStrategique->libelle]);
        } else {
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Objectif stratégique non trouvé']);
        }
    }
    
    public function showEditObjectifSpecifiqueForm($objectifSpecifiqueId)
    {
        $objectifSpecifique = ObjectifSpecifique::find($objectifSpecifiqueId);
        
        if ($objectifSpecifique) {
            $this->editingObjectifSpecifique = [
                'id' => $objectifSpecifique->id,
                'code' => $objectifSpecifique->code,
                'libelle' => $objectifSpecifique->libelle,
                'description' => $objectifSpecifique->description,
                'owner_id' => $objectifSpecifique->owner_id
            ];
            $this->showEditObjectifSpecifiqueForm = true;
        }
    }
    
    public function showEditActionForm($actionId)
    {
        $action = Action::find($actionId);
        
        if ($action) {
            $this->editingAction = [
                'id' => $action->id,
                'code' => $action->code,
                'libelle' => $action->libelle,
                'description' => $action->description,
                'owner_id' => $action->owner_id,
                'date_echeance' => $action->date_echeance,
                'date_realisation' => $action->date_realisation
            ];
            $this->showEditActionForm = true;
        }
    }
    
    public function showEditSousActionForm($sousActionId)
    {
        $sousAction = SousAction::find($sousActionId);
        
        if ($sousAction) {
            $this->editingSousAction = [
                'id' => $sousAction->id,
                'code' => $sousAction->code,
                'libelle' => $sousAction->libelle,
                'description' => $sousAction->description,
                'owner_id' => $sousAction->owner_id,
                'date_echeance' => $sousAction->date_echeance,
                'date_realisation' => $sousAction->date_realisation,
                'taux_avancement' => $sousAction->taux_avancement
            ];
            $this->showEditSousActionForm = true;
        }
    }

    // NOUVELLES MÉTHODES POUR OBJECTIFS SPÉCIFIQUES
    public function openCreateOSPModal()
    {
        $this->showCreateObjectifSpecifiqueForm = true;
    }

    public function closeCreateOSPModal()
    {
        $this->showCreateObjectifSpecifiqueForm = false;
    }

    public function saveNewOSP()
    {
        // Vérification des permissions: Admin général OU owner de l'objectif stratégique parent
        /** @var User $user */
        $user = Auth::user();
        if (!$user || !($user->isAdminGeneral() || ($this->selectedObjectifStrategique && $user->id === ($this->selectedObjectifStrategique->owner_id)))) {
            $this->dispatch('showToast', ['type' => 'error', 'message' => "Accès non autorisé. Seul l'admin général ou l'owner de l'objectif stratégique peut créer un objectif spécifique."]);
            return;
        }

        $this->validate([
            'newObjectifSpecifique.code' => 'required|string|max:50',
            'newObjectifSpecifique.libelle' => 'required|string|max:255',
            'newObjectifSpecifique.description' => 'nullable|string',
            'newObjectifSpecifique.owner_id' => 'nullable|exists:users,id'
        ]);

        try {
            $this->dispatch('console.log', '🚀 [DEBUG] saveNewOSP() appelée');
            $objectifSpecifique = new ObjectifSpecifique($this->newObjectifSpecifique);
            $objectifSpecifique->objectif_strategique_id = $this->selectedObjectifStrategique->id;
            $objectifSpecifique->save();

            // Notification au owner de l'objectif spécifique (si défini)
            if (!empty($this->newObjectifSpecifique['owner_id'])) {
                $owner = User::find($this->newObjectifSpecifique['owner_id']);
                if ($owner) {
                    $notificationId = DB::table('notifications')->insertGetId([
                        'user_id' => $owner->id,
                        'type' => 'objectif_specifique_assigned',
                        'title' => 'Nouvel objectif spécifique assigné',
                        'message' => "Un nouvel objectif spécifique vous a été assigné : " . $this->selectedObjectifStrategique->code . '.' . $objectifSpecifique->code . ' - ' . $objectifSpecifique->libelle,
                        'data' => json_encode([
                            'objectif_specifique_id' => $objectifSpecifique->id,
                            'objectif_specifique_code' => $objectifSpecifique->code,
                            'objectif_specifique_libelle' => $objectifSpecifique->libelle,
                            'objectif_strategique_id' => $this->selectedObjectifStrategique->id,
                            'objectif_strategique_code' => $this->selectedObjectifStrategique->code,
                            'pilier_id' => $this->pilier->id,
                            'pilier_code' => $this->pilier->code,
                            'createur_id' => $user->id,
                        ]),
                        'priority' => 'normal',
                        'channel' => 'database',
                        'is_sent' => true,
                        'sent_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    Log::info('Notification (objectif spécifique) créée', ['notification_id' => $notificationId, 'owner_id' => $owner->id]);
                    // Rafraîchir le centre de notifications
                    $this->dispatch('refreshNotifications');
                    $this->dispatch('notificationReceived');
                }
            }

            $this->showCreateObjectifSpecifiqueForm = false;
            $this->newObjectifSpecifique = ['code' => '', 'libelle' => '', 'description' => '', 'owner_id' => ''];
            $this->loadPilierData();
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Objectif spécifique créé avec succès !']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'objectif spécifique', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la création']);
        }
    }
    
    public function editObjectifSpecifique($objectifSpecifiqueId)
    {
        // Debug: Méthode d'édition objectif spécifique
        $objectifSpecifique = ObjectifSpecifique::find($objectifSpecifiqueId);
        if ($objectifSpecifique) {
            $this->editingObjectifSpecifique = [
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
                // Vérifier s'il y a des actions liées
                if ($objectifSpecifique->actions()->count() > 0) {
                    $this->dispatch('showToast', ['type' => 'error', 'message' => 'Impossible de supprimer cet objectif spécifique car il contient des actions.']);
                    return;
                }
                
                $objectifSpecifique->delete();
                $this->loadPilierData();
                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Objectif spécifique supprimé']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'objectif spécifique', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la suppression']);
        }
    }

    // Gestion des actions
    public function openCreateActionModal()
    {
        $this->showCreateActionForm = true;
    }

    public function closeCreateActionModal()
    {
        $this->showCreateActionForm = false;
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
        $this->newAction = [
            'code' => '',
            'libelle' => '',
            'description' => '',
            'owner_id' => ''
        ];
    }
    
    public function resetSousActionForm()
    {
        $this->newSousAction = [
            'code' => '',
            'libelle' => '',
            'description' => '',
            'type' => '',
            'owner_id' => '',
            'date_echeance' => '',
            'taux_avancement' => 0
        ];
    }

    public function saveNewAction()
    {
        // Vérification des permissions: Admin général OU owner de l'objectif stratégique parent OU owner de l'objectif spécifique parent
        /** @var User $user */
        $user = Auth::user();
        if (!$user || !($user->isAdminGeneral() || ($this->selectedObjectifStrategique && $user->id === ($this->selectedObjectifStrategique->owner_id)) || ($this->selectedObjectifSpecifique && $user->id === ($this->selectedObjectifSpecifique->owner_id)))) {
            $this->dispatch('showToast', ['type' => 'error', 'message' => "Accès non autorisé. Seul l'admin général, l'owner de l'objectif stratégique ou l'owner de l'objectif spécifique peut créer une action."]);
            return;
        }

        $this->validate([
            'newAction.code' => 'required|string|max:50',
            'newAction.libelle' => 'required|string|max:255',
            'newAction.description' => 'nullable|string',
            'newAction.owner_id' => 'nullable|exists:users,id'
        ]);

        try {
            $this->dispatch('console.log', '🚀 [DEBUG] saveNewAction() appelée');
            $action = new Action($this->newAction);
            $action->objectif_specifique_id = $this->selectedObjectifSpecifique->id;
            $action->save();

            // Notification au owner de l'action (si défini)
            if (!empty($this->newAction['owner_id'])) {
                $owner = User::find($this->newAction['owner_id']);
                if ($owner) {
                    $notificationId = DB::table('notifications')->insertGetId([
                        'user_id' => $owner->id,
                        'type' => 'action_assigned',
                        'title' => 'Nouvelle action assignée',
                        'message' => "Une nouvelle action vous a été assignée : " . $this->selectedObjectifStrategique->code . '.' . $this->selectedObjectifSpecifique->code . '.' . $action->code . ' - ' . $action->libelle,
                        'data' => json_encode([
                            'action_id' => $action->id,
                            'action_code' => $action->code,
                            'action_libelle' => $action->libelle,
                            'objectif_specifique_id' => $this->selectedObjectifSpecifique->id,
                            'objectif_specifique_code' => $this->selectedObjectifSpecifique->code,
                            'objectif_strategique_id' => $this->selectedObjectifStrategique->id,
                            'objectif_strategique_code' => $this->selectedObjectifStrategique->code,
                            'pilier_id' => $this->pilier->id,
                            'pilier_code' => $this->pilier->code,
                            'createur_id' => $user->id,
                        ]),
                        'priority' => 'normal',
                        'channel' => 'database',
                        'is_sent' => true,
                        'sent_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    Log::info('Notification (action) créée', ['notification_id' => $notificationId, 'owner_id' => $owner->id]);
                    // Rafraîchir le centre de notifications
                    $this->dispatch('refreshNotifications');
                    $this->dispatch('notificationReceived');
                }
            }

            $this->showCreateActionForm = false;
            $this->newAction = ['code' => '', 'libelle' => '', 'description' => '', 'owner_id' => ''];
            $this->loadPilierData();
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Action créée avec succès !']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'action', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la création']);
        }
    }
    
    public function editAction($actionId)
    {
        // Debug: Méthode d'édition action
        $action = Action::find($actionId);
        if ($action) {
            $this->editingAction = [
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
                // Vérifier s'il y a des sous-actions liées
                if ($action->sousActions()->count() > 0) {
                    $this->dispatch('showToast', ['type' => 'error', 'message' => 'Impossible de supprimer cette action car elle contient des sous-actions.']);
                    return;
                }
                
                $action->delete();
                $this->loadPilierData();
                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Action supprimée']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'action', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la suppression']);
        }
    }

    public function saveNewSousAction()
    {
        $this->validate([
            'newSousAction.code' => 'required|string|max:50',
            'newSousAction.libelle' => 'required|string|max:255',
            'newSousAction.description' => 'nullable|string',
            'newSousAction.type' => 'required|in:normal,projet',
            'newSousAction.owner_id' => 'nullable|exists:users,id',
            'newSousAction.date_echeance' => 'nullable|date',
            'newSousAction.taux_avancement' => 'required|numeric|min:0|max:100'
        ]);

        try {
            $sousAction = new SousAction($this->newSousAction);
            $sousAction->action_id = $this->selectedAction->id;
            $sousAction->save();

            $this->showCreateSousActionForm = false;
            $this->newSousAction = [
                'code' => '', 
                'libelle' => '', 
                'description' => '', 
                'type' => '', 
                'owner_id' => '', 
                'date_echeance' => '', 
                'taux_avancement' => 0
            ];
            $this->loadPilierData();
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Sous-action créée avec succès !']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la sous-action', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la création']);
        }
    }

    public function deleteSousAction($sousActionId)
    {
        try {
            $sousAction = SousAction::find($sousActionId);
            if ($sousAction) {
                $sousAction->delete();
                $this->loadPilierData();
                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Sous-action supprimée']);
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la sous-action', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la suppression']);
        }
    }






    

    


    // Méthode de test pour vérifier que Livewire fonctionne
    public function testSlider()
    {
        $this->dispatch('console.log', 'DEBUG: testSlider appelé', [
            'debugSliderValue' => $this->debugSliderValue
        ]);
        
        $this->dispatch('showToast', ['type' => 'info', 'message' => 'Test slider: ' . $this->debugSliderValue]);
    }

    // MISE À JOUR SIMPLIFIÉE POUR DÉBOGAGE
    public function updateTauxSousAction($nouveauTaux, $sousActionId = null)
    {
        try {
            $this->dispatch('console.log', '🚀 DÉBUT - Mise à jour simplifiée', [
                'nouveauTaux' => $nouveauTaux,
                'sousActionId' => $sousActionId,
                'timestamp' => now()->format('H:i:s')
            ]);

            // 1. VÉRIFICATION SIMPLE
            if (!$sousActionId) {
                $this->dispatch('console.log', '❌ ID de sous-action manquant');
                return;
            }

            // 2. RECHERCHE SOUS-ACTION
            $sousAction = SousAction::find($sousActionId);
            if (!$sousAction) {
                $this->dispatch('console.log', '❌ Sous-action non trouvée');
                return;
            }

            $this->dispatch('console.log', '✅ Sous-action trouvée', [
                'id' => $sousAction->id,
                'ancienTaux' => $sousAction->taux_avancement
            ]);

            // 3. SAUVEGARDE SIMPLE
            $ancienTaux = $sousAction->taux_avancement;
            $sousAction->taux_avancement = (int)$nouveauTaux;
            $sousAction->save();

            $this->dispatch('console.log', '💾 Sauvegarde réussie', [
                'ancien' => $ancienTaux,
                'nouveau' => $nouveauTaux
            ]);

            // 4. NOTIFICATION SIMPLE
            $this->dispatch('showToast', ['type' => 'success', 'message' => "✅ Taux mis à jour : {$ancienTaux}% → {$nouveauTaux}%"]);
            
            $this->dispatch('console.log', '🎉 SUCCÈS - Mise à jour simple terminée');
            
        } catch (\Exception $e) {
            $this->dispatch('console.log', '💥 ERREUR CRITIQUE', [
                'message' => $e->getMessage(),
                'fichier' => $e->getFile(),
                'ligne' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('showToast', ['type' => 'error', 'message' => '❌ Erreur: ' . $e->getMessage()]);
        }
    }

    // MÉTHODE DE TEST POUR VÉRIFIER LA CONNEXION LIVEWIRE
    public function testLivewireConnection()
    {
        $this->dispatch('console.log', '🧪 Test Livewire - Connexion OK', [
            'timestamp' => now()->format('H:i:s'),
            'currentView' => $this->currentView,
            'pilierId' => $this->pilierId
        ]);
        
        $this->dispatch('showToast', ['type' => 'success', 'message' => '🧪 Test Livewire - Connexion OK !']);
    }

    // MÉTHODE DE TEST SPÉCIFIQUE POUR LE BOUTON 75%
    public function testButton75($sousActionId)
    {
        $this->dispatch('console.log', '🧪 Test bouton 75% - Méthode appelée', [
            'sousActionId' => $sousActionId,
            'timestamp' => now()->format('H:i:s')
        ]);
        
        $this->dispatch('showToast', ['type' => 'info', 'message' => "🧪 Test bouton 75% - ID: {$sousActionId}"]);
    }

    // MÉTHODE DE TEST AVEC MÊMES PARAMÈTRES QUE updateSousActionTauxSimple
    public function testUpdateMethod($nouveauTaux, $sousActionId)
    {
        $this->dispatch('console.log', '🧪 Test updateSousActionTauxSimple - Méthode appelée', [
            'nouveauTaux' => $nouveauTaux,
            'sousActionId' => $sousActionId,
            'timestamp' => now()->format('H:i:s')
        ]);
        
        $this->dispatch('showToast', ['type' => 'success', 'message' => "🧪 Test réussi - {$nouveauTaux}% pour sous-action {$sousActionId}"]);
    }

    // MÉTHODE DE TEST TRÈS SIMPLE
    public function testSimple()
    {
        $this->dispatch('console.log', '🧪 Test simple - Méthode appelée');
        $this->dispatch('showToast', ['type' => 'info', 'message' => '🧪 Test simple OK !']);
    }

    public function updateParentRates($sousAction)
    {
        $this->dispatch('console.log', '🔄 updateParentRates - DÉBUT', [
            'sousActionId' => $sousAction->id,
            'sousActionTaux' => $sousAction->taux_avancement,
            'timestamp' => now()->format('H:i:s')
        ]);

        try {
        // Mettre à jour le taux de l'action
        $action = $sousAction->action;
        if ($action) {
                $ancienTauxAction = $action->taux_avancement;
            $sousActions = $action->sousActions;
            if ($sousActions->count() > 0) {
                    $nouveauTauxAction = round($sousActions->avg('taux_avancement'), 1);
                    $action->taux_avancement = $nouveauTauxAction;
                $action->save();
                    
                    $this->dispatch('console.log', 'DEBUG: updateParentRates - Action mise à jour', [
                        'actionId' => $action->id,
                        'ancienTaux' => $ancienTauxAction,
                        'nouveauTaux' => $nouveauTauxAction
                    ]);
                
                // Mettre à jour le taux de l'objectif spécifique
                $objectifSpecifique = $action->objectifSpecifique;
                if ($objectifSpecifique) {
                        $ancienTauxOS = $objectifSpecifique->taux_avancement;
                    $actions = $objectifSpecifique->actions;
                    if ($actions->count() > 0) {
                            $nouveauTauxOS = round($actions->avg('taux_avancement'), 1);
                            $objectifSpecifique->taux_avancement = $nouveauTauxOS;
                        $objectifSpecifique->save();
                            
                            $this->dispatch('console.log', 'DEBUG: updateParentRates - Objectif Spécifique mis à jour', [
                                'objectifSpecifiqueId' => $objectifSpecifique->id,
                                'ancienTaux' => $ancienTauxOS,
                                'nouveauTaux' => $nouveauTauxOS
                            ]);
                        
                        // Mettre à jour le taux de l'objectif stratégique
                        $objectifStrategique = $objectifSpecifique->objectifStrategique;
                        if ($objectifStrategique) {
                                $ancienTauxOS = $objectifStrategique->taux_avancement;
                            $objectifsSpecifiques = $objectifStrategique->objectifsSpecifiques;
                            if ($objectifsSpecifiques->count() > 0) {
                                    $nouveauTauxOS = round($objectifsSpecifiques->avg('taux_avancement'), 1);
                                    $objectifStrategique->taux_avancement = $nouveauTauxOS;
                                $objectifStrategique->save();
                                    
                                    $this->dispatch('console.log', 'DEBUG: updateParentRates - Objectif Stratégique mis à jour', [
                                        'objectifStrategiqueId' => $objectifStrategique->id,
                                        'ancienTaux' => $ancienTauxOS,
                                        'nouveauTaux' => $nouveauTauxOS
                                    ]);
                                
                                // Mettre à jour le taux du pilier
                                $pilier = $objectifStrategique->pilier;
                                if ($pilier) {
                                        $ancienTauxPilier = $pilier->taux_avancement;
                                    $objectifsStrategiques = $pilier->objectifsStrategiques;
                                    if ($objectifsStrategiques->count() > 0) {
                                            $nouveauTauxPilier = round($objectifsStrategiques->avg('taux_avancement'), 1);
                                            $pilier->taux_avancement = $nouveauTauxPilier;
                                        $pilier->save();
                                            
                                            $this->dispatch('console.log', 'DEBUG: updateParentRates - Pilier mis à jour', [
                                                'pilierId' => $pilier->id,
                                                'ancienTaux' => $ancienTauxPilier,
                                                'nouveauTaux' => $nouveauTauxPilier
                                            ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            }
            
            $this->dispatch('console.log', 'DEBUG: updateParentRates - SUCCÈS', [
                'message' => 'Tous les taux parents ont été mis à jour avec succès'
            ]);
            
            // Mettre à jour l'interface avec les nouveaux taux calculés
            $this->dispatch('parent-taux-updated', [
                'action_taux' => $nouveauTauxAction ?? 0,
                'osp_taux' => $nouveauTauxOSP ?? 0,
                'os_taux' => $nouveauTauxOS ?? 0,
                'pilier_taux' => $nouveauTauxPilier ?? 0
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des taux parents', ['error' => $e->getMessage()]);
            $this->dispatch('console.log', 'DEBUG: updateParentRates - ERREUR', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // ===== NOUVELLES MÉTHODES DE CALCUL EN CASCADE =====

    /**
     * Calcule le taux d'une action = MOYENNE des taux des sous-actions
     */
    public function calculerTauxAction($actionId)
    {
        try {
            $action = Action::with('sousActions')->find($actionId);
            if (!$action || $action->sousActions->count() == 0) {
                return 0;
            }

            $tauxTotal = $action->sousActions->sum('taux_avancement');
            $nombreSousActions = $action->sousActions->count();
            $tauxMoyen = round($tauxTotal / $nombreSousActions, 1);

            $this->dispatch('console.log', '📊 Calcul taux Action', [
                'actionId' => $actionId,
                'sousActions' => $nombreSousActions,
                'tauxTotal' => $tauxTotal,
                'tauxMoyen' => $tauxMoyen
            ]);

            return $tauxMoyen;

        } catch (\Exception $e) {
            $this->dispatch('console.log', '❌ Erreur calcul taux Action', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Calcule le taux d'un objectif spécifique = MOYENNE des taux des actions
     */
    public function calculerTauxObjectifSpecifique($objectifSpecifiqueId)
    {
        try {
            $objectifSpecifique = ObjectifSpecifique::with('actions')->find($objectifSpecifiqueId);
            if (!$objectifSpecifique || $objectifSpecifique->actions->count() == 0) {
                return 0;
            }

            $tauxTotal = $objectifSpecifique->actions->sum('taux_avancement');
            $nombreActions = $objectifSpecifique->actions->count();
            $tauxMoyen = round($tauxTotal / $nombreActions, 1);

            $this->dispatch('console.log', '📊 Calcul taux Objectif Spécifique', [
                'osId' => $objectifSpecifiqueId,
                'actions' => $nombreActions,
                'tauxTotal' => $tauxTotal,
                'tauxMoyen' => $tauxMoyen
            ]);

            return $tauxMoyen;

        } catch (\Exception $e) {
            $this->dispatch('console.log', '❌ Erreur calcul taux Objectif Spécifique', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Calcule le taux d'un objectif stratégique = MOYENNE des taux des objectifs spécifiques
     */
    public function calculerTauxObjectifStrategique($objectifStrategiqueId)
    {
        try {
            $objectifStrategique = ObjectifStrategique::with('objectifsSpecifiques')->find($objectifStrategiqueId);
            if (!$objectifStrategique || $objectifStrategique->objectifsSpecifiques->count() == 0) {
                return 0;
            }

            $tauxTotal = $objectifStrategique->objectifsSpecifiques->sum('taux_avancement');
            $nombreOS = $objectifStrategique->objectifsSpecifiques->count();
            $tauxMoyen = round($tauxTotal / $nombreOS, 1);

            $this->dispatch('console.log', '📊 Calcul taux Objectif Stratégique', [
                'ostId' => $objectifStrategiqueId,
                'objectifsSpecifiques' => $nombreOS,
                'tauxTotal' => $tauxTotal,
                'tauxMoyen' => $tauxMoyen
            ]);

            return $tauxMoyen;

        } catch (\Exception $e) {
            $this->dispatch('console.log', '❌ Erreur calcul taux Objectif Stratégique', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Calcule le taux d'un pilier = MOYENNE des taux des objectifs stratégiques
     */
    public function calculerTauxPilier($pilierId)
    {
        try {
            $pilier = Pilier::with('objectifsStrategiques')->find($pilierId);
            if (!$pilier || $pilier->objectifsStrategiques->count() == 0) {
                return 0;
            }

            $tauxTotal = $pilier->objectifsStrategiques->sum('taux_avancement');
            $nombreOST = $pilier->objectifsStrategiques->count();
            $tauxMoyen = round($tauxTotal / $nombreOST, 1);

            $this->dispatch('console.log', '📊 Calcul taux Pilier', [
                'pilierId' => $pilierId,
                'objectifsStrategiques' => $nombreOST,
                'tauxTotal' => $tauxTotal,
                'tauxMoyen' => $tauxMoyen
            ]);

            return $tauxMoyen;

        } catch (\Exception $e) {
            $this->dispatch('console.log', '❌ Erreur calcul taux Pilier', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    // Propriétés calculées
    public function getUsersProperty()
    {
        try {
            return User::whereHas('role', function($query) {
                $query->whereIn('nom', ['admin_general', 'owner_pil', 'owner_action']);
            })->get();
        } catch (\Exception $e) {
            // Fallback : retourner tous les users si la relation échoue
            Log::warning('Erreur lors du chargement des users avec rôles, fallback vers tous les users', ['error' => $e->getMessage()]);
            return User::all();
        }
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

    // Méthodes pour les formulaires de création
    public function resetCreateForm()
    {
        $this->newObjectifStrategique = [
            'code' => '',
            'libelle' => '',
            'description' => '',
            'owner_id' => ''
        ];
    }
    
    public function resetObjectifSpecifiqueForm()
    {
        $this->newObjectifSpecifique = [
            'code' => '',
            'libelle' => '',
            'description' => '',
            'owner_id' => ''
        ];
    }
    
    public function hideCreateObjectifForm()
    {
        $this->showCreateObjectifForm = false;
        $this->resetCreateForm();
    }
    
    public function hideCreateObjectifSpecifiqueForm()
    {
        $this->showCreateObjectifSpecifiqueForm = false;
        $this->resetObjectifSpecifiqueForm();
    }
    
    public function hideCreateActionForm()
    {
        $this->showCreateActionForm = false;
        $this->resetActionForm();
    }
    
    public function hideCreateSousActionForm()
    {
        $this->showCreateSousActionForm = false;
        $this->resetSousActionForm();
    }
    
    // Méthodes pour les formulaires d'édition
    public function hideEditObjectifForm()
    {
        $this->showEditObjectifStrategiqueForm = false;
        $this->editingObjectifStrategique = [
            'code' => '',
            'libelle' => '',
            'description' => '',
            'owner_id' => ''
        ];
    }
    
    public function hideEditObjectifSpecifiqueForm()
    {
        $this->showEditObjectifSpecifiqueForm = false;
        $this->editingObjectifSpecifique = [
            'code' => '',
            'libelle' => '',
            'description' => '',
            'owner_id' => ''
        ];
    }
    
    public function hideEditActionForm()
    {
        $this->showEditActionForm = false;
        $this->editingAction = [
            'code' => '',
            'libelle' => '',
            'description' => '',
            'owner_id' => ''
        ];
    }
    
    public function hideEditSousActionForm()
    {
        $this->showEditSousActionForm = false;
        $this->editingSousAction = [
            'code' => '',
            'libelle' => '',
            'description' => '',
            'type' => '',
            'owner_id' => '',
            'date_echeance' => '',
            'date_realisation' => '',
            'taux_avancement' => 0
        ];
    }
    
    // Méthodes de mise à jour
    public function updateObjectifStrategique()
    {
        $this->dispatch('console.log', '🚀 [DEBUG] updateObjectifStrategique() appelée');
        
        try {
            $this->dispatch('console.log', '📋 [DEBUG] Données d\'édition:', $this->editingObjectifStrategique);
        
        $this->validate([
            'editingObjectifStrategique.code' => 'required|string|max:10',
            'editingObjectifStrategique.libelle' => 'required|string|max:255',
            'editingObjectifStrategique.description' => 'nullable|string',
            'editingObjectifStrategique.owner_id' => 'nullable|exists:users,id',
        ]);

            $this->dispatch('console.log', '✅ [DEBUG] Validation OK, recherche de l\'objectif...');

            $objectifStrategique = ObjectifStrategique::find($this->editingObjectifStrategique['id'] ?? null);
            if ($objectifStrategique) {
                $this->dispatch('console.log', '✅ [DEBUG] Objectif stratégique trouvé, mise à jour en cours...');
                
                $objectifStrategique->update([
                    'code' => $this->editingObjectifStrategique['code'],
                    'libelle' => $this->editingObjectifStrategique['libelle'],
                    'description' => $this->editingObjectifStrategique['description'],
                    'owner_id' => $this->editingObjectifStrategique['owner_id'] ?: null,
                ]);
                
                $this->dispatch('console.log', '✅ [DEBUG] Objectif stratégique mis à jour avec succès');
                
                $this->hideEditObjectifForm();
                $this->loadPilierData();
                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Objectif stratégique mis à jour avec succès !']);
            } else {
                $this->dispatch('console.log', '❌ [DEBUG] Objectif stratégique non trouvé avec ID:', $this->editingObjectifStrategique['id'] ?? 'null');
                $this->dispatch('showToast', ['type' => 'error', 'message' => 'Objectif stratégique non trouvé']);
            }
        } catch (\Exception $e) {
            $this->dispatch('console.log', '❌ [DEBUG] Exception dans updateObjectifStrategique:', $e->getMessage());
            $this->dispatch('console.log', '❌ [DEBUG] Stack trace:', $e->getTraceAsString());
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }
    
    public function updateObjectifSpecifique()
    {
        $this->dispatch('console.log', '🚀 updateObjectifSpecifique appelée');
        
        $this->validate([
            'editingObjectifSpecifique.code' => 'required|string|max:10',
            'editingObjectifSpecifique.libelle' => 'required|string|max:255',
            'editingObjectifSpecifique.description' => 'nullable|string',
            'editingObjectifSpecifique.owner_id' => 'nullable|exists:users,id',
        ]);

        try {
            $objectifSpecifique = ObjectifSpecifique::find($this->editingObjectifSpecifique['id'] ?? null);
            if ($objectifSpecifique) {
                $objectifSpecifique->update([
                    'code' => $this->editingObjectifSpecifique['code'],
                    'libelle' => $this->editingObjectifSpecifique['libelle'],
                    'description' => $this->editingObjectifSpecifique['description'],
                    'owner_id' => $this->editingObjectifSpecifique['owner_id'] ?: null,
                ]);
                
                $this->dispatch('console.log', '✅ Objectif spécifique mis à jour avec succès');
                $this->hideEditObjectifSpecifiqueForm();
                $this->loadPilierData();
                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Objectif spécifique mis à jour avec succès !']);
            }
        } catch (\Exception $e) {
            $this->dispatch('console.log', '❌ Erreur updateObjectifSpecifique: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la mise à jour']);
        }
    }
    
    public function updateAction()
    {
        $this->dispatch('console.log', '🚀 updateAction appelée');
        
        $this->validate([
            'editingAction.code' => 'required|string|max:10',
            'editingAction.libelle' => 'required|string|max:255',
            'editingAction.description' => 'nullable|string',
            'editingAction.owner_id' => 'nullable|exists:users,id',
        ]);

        try {
            $action = Action::find($this->editingAction['id'] ?? null);
            if ($action) {
                $action->update([
                    'code' => $this->editingAction['code'],
                    'libelle' => $this->editingAction['libelle'],
                    'description' => $this->editingAction['description'],
                    'owner_id' => $this->editingAction['owner_id'] ?: null,
                ]);
                
                $this->dispatch('console.log', '✅ Action mise à jour avec succès');
                $this->hideEditActionForm();
                $this->loadPilierData();
                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Action mise à jour avec succès !']);
            }
        } catch (\Exception $e) {
            $this->dispatch('console.log', '❌ Erreur updateAction: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la mise à jour']);
        }
    }
    
    public function updateSousAction()
    {
        $this->dispatch('console.log', '🚀 updateSousAction appelée');
        
        $this->validate([
            'editingSousAction.code' => 'required|string|max:10',
            'editingSousAction.libelle' => 'required|string|max:255',
            'editingSousAction.description' => 'nullable|string',
            'editingSousAction.type' => 'required|in:normal,projet',
            'editingSousAction.owner_id' => 'nullable|exists:users,id',
            'editingSousAction.date_echeance' => 'nullable|date',
            'editingSousAction.taux_avancement' => 'required|numeric|min:0|max:100'
        ]);

        try {
            $sousAction = SousAction::find($this->editingSousAction['id'] ?? null);
            if ($sousAction) {
                $updateData = [
                    'code' => $this->editingSousAction['code'],
                    'libelle' => $this->editingSousAction['libelle'],
                    'description' => $this->editingSousAction['description'],
                    'type' => $this->editingSousAction['type'],
                    'owner_id' => $this->editingSousAction['owner_id'] ?: null,
                    'date_echeance' => $this->editingSousAction['date_echeance'] ?: null,
                ];
                
                // Gestion spéciale pour les actions normales
                if ($this->editingSousAction['type'] === 'normal') {
                    $updateData['taux_avancement'] = $this->editingSousAction['taux_avancement'];
                    
                    // Si le taux est 100%, définir la date de réalisation
                    if ($this->editingSousAction['taux_avancement'] == 100) {
                        $updateData['date_realisation'] = now();
                    }
                }
                // Pour les projets, le taux reste inchangé (calculé automatiquement)
                
                $sousAction->update($updateData);
                
                $this->dispatch('console.log', '✅ Sous-action mise à jour avec succès');
                $this->hideEditSousActionForm();
                $this->loadPilierData();
                $this->dispatch('showToast', ['type' => 'success', 'message' => 'Sous-action mise à jour avec succès !']);
            }
        } catch (\Exception $e) {
            $this->dispatch('console.log', '❌ Erreur updateSousAction: ' . $e->getMessage());
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la mise à jour']);
        }
    }
    
    // MÉTHODES MANQUANTES POUR LA CRÉATION ET L'ÉDITION
    
    // === CRÉATION OBJECTIF STRATÉGIQUE ===
    public function showCreateObjectifForm()
    {
        $this->dispatch('console.log', '🚀 showCreateObjectifForm appelée');
        $this->showCreateObjectifForm = true;
        $this->resetFormData();
    }
    
    // === CRÉATION OBJECTIF SPÉCIFIQUE ===
    public function showCreateObjectifSpecifiqueForm()
    {
        $this->dispatch('console.log', '🚀 showCreateObjectifSpecifiqueForm appelée');
        $this->showCreateObjectifSpecifiqueForm = true;
        $this->resetFormData();
    }
    
    // === CRÉATION ACTION ===
    public function showCreateActionForm()
    {
        $this->dispatch('console.log', '🚀 showCreateActionForm appelée');
        $this->showCreateActionForm = true;
        $this->resetFormData();
    }
    
    // === ÉDITION OBJECTIF STRATÉGIQUE ===
    public function showEditObjectifForm()
    {
        $this->dispatch('console.log', '🚀 showEditObjectifForm appelée');
        $this->showEditObjectifStrategiqueForm = true;
    }
    
    // === MÉTHODES DE CRÉATION MANQUANTES ===
    
    public function createObjectifStrategique()
    {
        $this->dispatch('console.log', '🚀 createObjectifStrategique appelée');
        return $this->saveNewOS();
    }
    
    public function createObjectifSpecifique()
    {
        $this->dispatch('console.log', '🚀 createObjectifSpecifique appelée');
        return $this->saveNewOSP();
    }
    
    public function createAction()
    {
        $this->dispatch('console.log', '🚀 createAction appelée');
        return $this->saveNewAction();
    }
    
    // === CRÉATION SOUS-ACTION ===
    public function openModalCreateSousAction()
    {
        $this->dispatch('console.log', '🚀 openModalCreateSousAction appelée');
        $this->showCreateSousActionForm = true;
        $this->dispatch('console.log', '✅ Modal de création de sous-action ouvert');
    }
    
    public function closeModalCreateSousAction()
    {
        $this->dispatch('console.log', '🚀 closeModalCreateSousAction appelée');
        $this->showCreateSousActionForm = false;
        $this->dispatch('console.log', '✅ Modal de création de sous-action fermé');
    }

    // === MISE À JOUR TAUX D'AVANCEMENT DES ACTIVITÉS - COMMENTé TEMPORAIREMENT ===
    // TODO: Réactiver quand on implémente la gestion des projets
    
    // === MISE À JOUR TAUX D'AVANCEMENT DES SOUS-ACTIONS DE TYPE NORMAL ===
    public function updateSousActionTaux($sousActionId, $newTaux)
    {
        Log::info('🚀 [UPDATE] updateSousActionTaux appelée', [
            'sousActionId' => $sousActionId,
            'newTaux' => $newTaux,
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'user_id' => Auth::user()->id
        ]);
        
        try {
            // Rechercher la sous-action
            Log::info('🔍 [UPDATE] Recherche de la sous-action', ['sousActionId' => $sousActionId]);
            $sousAction = SousAction::find($sousActionId);
            
            if (!$sousAction) {
                Log::error('❌ [UPDATE] Sous-action non trouvée', ['sousActionId' => $sousActionId]);
                $this->dispatch('showToast', ['type' => 'error', 'message' => 'Sous-action non trouvée']);
                return;
            }
            
            Log::info('✅ [UPDATE] Sous-action trouvée', [
                'sousActionId' => $sousAction->id,
                'libelle' => $sousAction->libelle,
                'taux_actuel' => $sousAction->taux_avancement,
                'owner_id' => $sousAction->owner_id
            ]);
            
            // Toutes les sous-actions sont maintenant de type "normal"
            // Pas besoin de vérifier le type
            
            // Vérifier les permissions
            Log::info('🔐 [UPDATE] Vérification des permissions');
            /** @var User $user */
            $user = Auth::user();
            Log::info('👤 [UPDATE] Utilisateur connecté', [
                'user_id' => $user->id,
                'is_admin' => $user->isAdminGeneral(),
                'sous_action_owner' => $sousAction->owner_id
            ]);
            
            if (!$user->isAdminGeneral() && 
                $user->id !== $sousAction->owner_id &&
                $user->id !== $sousAction->objectifSpecifique->owner_id &&
                $user->id !== $sousAction->objectifSpecifique->objectifStrategique->owner_id) {
                
                Log::error('❌ [UPDATE] Permissions insuffisantes', [
                    'user_id' => $user->id,
                    'sous_action_owner' => $sousAction->owner_id,
                    'objectif_specifique_owner' => $sousAction->objectifSpecifique->owner_id ?? 'null',
                    'objectif_strategique_owner' => $sousAction->objectifSpecifique->objectifStrategique->owner_id ?? 'null'
                ]);
                $this->dispatch('showToast', ['type' => 'error', 'message' => 'Permissions insuffisantes']);
                return;
            }
            
            Log::info('✅ [UPDATE] Permissions vérifiées avec succès');
            
            // Mettre à jour le taux d'avancement
            Log::info('💾 [UPDATE] Mise à jour de la sous-action', [
                'ancien_taux' => $sousAction->taux_avancement,
                'nouveau_taux' => $newTaux,
                'date_realisation' => $newTaux == 100 ? now() : 'null'
            ]);
            
            // Vérifier que la sous-action est bien un modèle Eloquent
            Log::info('🔍 [UPDATE] Vérification du modèle', [
                'class' => get_class($sousAction),
                'exists' => $sousAction->exists,
                'id' => $sousAction->id,
                'fillable' => $sousAction->getFillable(),
                'guarded' => $sousAction->getGuarded()
            ]);
            
            // Activer le log des requêtes SQL
            DB::enableQueryLog();
            
            try {
                Log::info('💾 [UPDATE] Début de la mise à jour des propriétés');
                
                // Mettre à jour les propriétés une par une
                $sousAction->taux_avancement = $newTaux;
                Log::info('✅ [UPDATE] Propriété taux_avancement mise à jour', ['valeur' => $newTaux]);
                
                $sousAction->date_realisation = $newTaux == 100 ? now() : null;
                Log::info('✅ [UPDATE] Propriété date_realisation mise à jour', ['valeur' => $sousAction->date_realisation]);
                
                // Vérifier les valeurs avant save()
                Log::info('🔍 [UPDATE] Valeurs du modèle avant save()', [
                    'taux_avancement' => $sousAction->taux_avancement,
                    'date_realisation' => $sousAction->date_realisation,
                    'isDirty' => $sousAction->isDirty(),
                    'getDirty' => $sousAction->getDirty()
                ]);
                
                Log::info('💾 [UPDATE] Appel de save()');
                
                try {
                    // Vérifier la connexion à la base de données
                    Log::info('🔌 [UPDATE] Test de connexion DB');
                    DB::connection()->getPdo();
                    Log::info('✅ [UPDATE] Connexion DB OK');
                    
                    // Tentative de save() avec gestion d'erreur maximale
                    Log::info('💾 [UPDATE] Tentative de save()...');
                    $result = $sousAction->save();
                    Log::info('✅ [UPDATE] save() exécuté avec succès', ['resultat' => $result]);
                    
                } catch (\PDOException $e) {
                    Log::error('❌ [UPDATE] Erreur PDO lors de save()', [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                    throw $e;
                } catch (\Exception $e) {
                    Log::error('❌ [UPDATE] Erreur générale lors de save()', [
                        'message' => $e->getMessage(),
                        'class' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                    throw $e;
                }
                
                // Vérifier les valeurs après save()
                Log::info('🔍 [UPDATE] Valeurs du modèle après save()', [
                    'taux_avancement' => $sousAction->taux_avancement,
                    'date_realisation' => $sousAction->date_realisation,
                    'isDirty' => $sousAction->isDirty()
                ]);
                
                Log::info('✅ [UPDATE] Sous-action mise à jour avec succès');
                
            } catch (\Exception $e) {
                Log::error('❌ [UPDATE] Erreur lors de la mise à jour', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e; // Relancer l'exception pour la gestion globale
            }
            
            // Mettre à jour les taux des parents (objectif spécifique, objectif stratégique, pilier)
            Log::info('🔄 [UPDATE] Début de la mise à jour des taux parents');
            $this->updateParentTaux($sousAction);
            
            Log::info('✅ [UPDATE] Taux d\'avancement de la sous-action mis à jour avec succès');
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Taux d\'avancement mis à jour !']);
            
            // Recharger les données
            Log::info('🔄 [UPDATE] Rechargement des données');
            $this->loadPilierData();
            
            Log::info('✅ [UPDATE] Processus terminé avec succès');
            
            // Mettre à jour l'interface en temps réel
            $this->dispatch('sous-action-updated', [
                'sousActionId' => $sousAction->id,
                'nouveauTaux' => $newTaux,
                'nouvelleDateRealisation' => $sousAction->date_realisation
            ]);
            
            // Rafraîchir les données pour l'affichage
            $this->dispatch('refresh-hierarchical-data');
            
            Log::info('✅ [UPDATE] Sous-action mise à jour avec succès');
            
        } catch (\Exception $e) {
            Log::error('❌ [UPDATE] Erreur dans updateSousActionTaux', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'sousActionId' => $sousActionId,
                'newTaux' => $newTaux
            ]);
            
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.pilier-hierarchique-modal');
    }

    // Méthode pour mettre à jour les taux des éléments parents
    private function updateParentTaux($sousAction)
    {
        try {
            Log::info('🔄 [PARENT] Début updateParentTaux', [
                'sousActionId' => $sousAction->id,
                'taux_sous_action' => $sousAction->taux_avancement
            ]);
            
            // OPTIMISATION : Utiliser les relations déjà chargées au lieu de refaire des requêtes
            $action = $sousAction->action;
            if (!$action) {
                Log::warning('⚠️ [PARENT] Action parent non trouvée', ['sousActionId' => $sousAction->id]);
                return;
            }
            
            // Calculer le taux de l'action basé sur ses sous-actions (POUR AFFICHAGE SEULEMENT)
            $sousActions = $action->sousActions;
            if ($sousActions->count() > 0) {
                $totalTaux = $sousActions->sum('taux_avancement');
                $nouveauTauxAction = round($totalTaux / $sousActions->count(), 2);
                
                Log::info('📊 [PARENT] Calcul du taux de l\'action (affichage)', [
                    'action_id' => $action->id,
                    'nombre_sous_actions' => $sousActions->count(),
                    'total_taux' => $totalTaux,
                    'nouveau_taux' => $nouveauTauxAction
                ]);
                
                // NE PAS SAUVEGARDER - juste calculer pour l'affichage
                // $action->taux_avancement = $nouveauTauxAction;
                // $action->save();
            }
            
            // Cascade vers l'Objectif Spécifique (POUR AFFICHAGE SEULEMENT)
            $objectifSpecifique = $action->objectifSpecifique;
            if ($objectifSpecifique) {
                $actions = $objectifSpecifique->actions;
                if ($actions->count() > 0) {
                    $totalTaux = $actions->sum(function($action) {
                        // Calculer le taux de chaque action basé sur ses sous-actions
                        $sousActions = $action->sousActions;
                        if ($sousActions->count() > 0) {
                            return $sousActions->sum('taux_avancement') / $sousActions->count();
                        }
                        return 0;
                    });
                    $nouveauTauxOSP = round($totalTaux / $actions->count(), 2);
                    
                    Log::info('📊 [PARENT] Calcul du taux de l\'OSP (affichage)', [
                        'osp_id' => $objectifSpecifique->id,
                        'nombre_actions' => $actions->count(),
                        'total_taux' => $totalTaux,
                        'nouveau_taux' => $nouveauTauxOSP
                    ]);
                    
                    // NE PAS SAUVEGARDER - juste calculer pour l'affichage
                    // $objectifSpecifique->taux_avancement = $nouveauTauxOSP;
                    // $objectifSpecifique->save();
                }
                
                // Cascade vers l'Objectif Stratégique (POUR AFFICHAGE SEULEMENT)
                $objectifStrategique = $objectifSpecifique->objectifStrategique;
                if ($objectifStrategique) {
                    $objectifsSpecifiques = $objectifStrategique->objectifsSpecifiques;
                    if ($objectifsSpecifiques->count() > 0) {
                        $totalTaux = $objectifsSpecifiques->sum(function($osp) {
                            // Calculer le taux de chaque OSP basé sur ses actions
                            $actions = $osp->actions;
                            if ($actions->count() > 0) {
                                return $actions->sum(function($action) {
                                    $sousActions = $action->sousActions;
                                    if ($sousActions->count() > 0) {
                                        return $sousActions->sum('taux_avancement') / $sousActions->count();
                                    }
                                    return 0;
                                }) / $actions->count();
                            }
                            return 0;
                        });
                        $nouveauTauxOS = round($totalTaux / $objectifsSpecifiques->count(), 2);
                        
                        Log::info('📊 [PARENT] Calcul du taux de l\'OS (affichage)', [
                            'os_id' => $objectifStrategique->id,
                            'nombre_osp' => $objectifsSpecifiques->count(),
                            'total_taux' => $totalTaux,
                            'nouveau_taux' => $nouveauTauxOS
                        ]);
                        
                        // NE PAS SAUVEGARDER - juste calculer pour l'affichage
                        // $objectifStrategique->taux_avancement = $nouveauTauxOS;
                        // $objectifStrategique->save();
                    }
                    
                    // Cascade vers le Pilier (POUR AFFICHAGE SEULEMENT)
                    $pilier = $objectifStrategique->pilier;
                    if ($pilier) {
                        $objectifsStrategiques = $pilier->objectifsStrategiques;
                        if ($objectifsStrategiques->count() > 0) {
                            $totalTaux = $objectifsStrategiques->sum(function($os) {
                                // Calculer le taux de chaque OS basé sur ses OSP
                                $objectifsSpecifiques = $os->objectifsSpecifiques;
                                if ($objectifsSpecifiques->count() > 0) {
                                    return $objectifsSpecifiques->sum(function($osp) {
                                        $actions = $osp->actions;
                                        if ($actions->count() > 0) {
                                            return $actions->sum(function($action) {
                                                $sousActions = $action->sousActions;
                                                if ($sousActions->count() > 0) {
                                                    return $sousActions->sum('taux_avancement') / $sousActions->count();
                                                }
                                                return 0;
                                            }) / $actions->count();
                                        }
                                        return 0;
                                    }) / $objectifsSpecifiques->count();
                                }
                                return 0;
                            });
                            $nouveauTauxPilier = round($totalTaux / $objectifsStrategiques->count(), 2);
                            
                            Log::info('📊 [PARENT] Calcul du taux du pilier (affichage)', [
                                'pilier_id' => $pilier->id,
                                'nombre_os' => $objectifsStrategiques->count(),
                                'total_taux' => $totalTaux,
                                'nouveau_taux' => $nouveauTauxPilier
                            ]);
                            
                            // NE PAS SAUVEGARDER - juste calculer pour l'affichage
                            // $pilier->taux_avancement = $nouveauTauxPilier;
                            // $pilier->save();
                        }
                    }
                }
            }
            
            Log::info('✅ [PARENT] updateParentTaux terminé avec succès (taux calculés pour affichage)');
            
            // Mettre à jour l'interface avec les nouveaux taux calculés
            $this->dispatch('parent-taux-updated', [
                'action_taux' => $nouveauTauxAction ?? 0,
                'osp_taux' => $nouveauTauxOSP ?? 0,
                'os_taux' => $nouveauTauxOS ?? 0,
                'pilier_taux' => $nouveauTauxPilier ?? 0
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ [PARENT] Erreur dans updateParentTaux', [
                'sousActionId' => $sousAction->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    // === MÉTHODE SIMPLE POUR LE SLIDER ===
    public function updateSousActionTauxSimple($nouveauTaux)
    {
        Log::info('🚀 [SLIDER] updateSousActionTauxSimple appelée', [
            'nouveauTaux' => $nouveauTaux,
            'selectedSousAction' => $this->selectedSousAction ? $this->selectedSousAction->id : 'null',
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'user_id' => Auth::user()->id,
            'memory_usage' => memory_get_usage(true)
        ]);
        
        try {
            // Vérifier que selectedSousAction existe
            if (!$this->selectedSousAction) {
                Log::error('❌ [SLIDER] selectedSousAction est null');
                $this->dispatch('showToast', ['type' => 'error', 'message' => 'Aucune sous-action sélectionnée']);
                return;
            }
            
            Log::info('✅ [SLIDER] selectedSousAction trouvé', [
                'sousActionId' => $this->selectedSousAction->id,
                'libelle' => $this->selectedSousAction->libelle,
                'taux_actuel' => $this->selectedSousAction->taux_avancement,
                'nouveau_taux' => $nouveauTaux
            ]);
            
            // Appeler updateSousActionTaux
            $this->updateSousActionTaux($this->selectedSousAction->id, $nouveauTaux);
            
            Log::info('✅ [SLIDER] updateSousActionTaux appelé avec succès');
            
        } catch (\Exception $e) {
            Log::error('❌ [SLIDER] Erreur dans updateSousActionTauxSimple', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    public function chargerDonneesPilier()
    {
        try {
            Log::info('Vue Hiérarchique - Début du chargement des données', ['pilier_id' => $this->pilierId]);
            
            // OPTIMISATION : Eager Loading de toutes les relations en une seule requête
            $this->pilier = Pilier::with([
                'objectifsStrategiques.objectifsSpecifiques.actions.sousActions' => function ($query) {
                    $query->orderBy('code');
                },
                'objectifsStrategiques.objectifsSpecifiques.actions' => function ($query) {
                    $query->orderBy('code');
                },
                'objectifsStrategiques.objectifsSpecifiques' => function ($query) {
                    $query->orderBy('code');
                },
                'objectifsStrategiques' => function ($query) {
                    $query->orderBy('code');
                }
            ])->findOrFail($this->pilierId);
            
            // CALCUL DYNAMIQUE DES TAUX pour l'affichage
            $this->calculerTauxDynamiques();
            
            Log::info('Vue Hiérarchique - Données du pilier chargées', ['pilier_id' => $this->pilierId]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des données du pilier', [
                'pilier_id' => $this->pilierId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erreur lors du chargement des données du pilier'
            ]);
        }
    }
    
    /**
     * Calcule dynamiquement tous les taux pour l'affichage
     */
    private function calculerTauxDynamiques()
    {
        if (!$this->pilier) return;
        
        foreach ($this->pilier->objectifsStrategiques as $os) {
            // Calculer le taux de l'OS basé sur ses OSP
            $totalTauxOS = 0;
            $nombreOSP = $os->objectifsSpecifiques->count();
            
            if ($nombreOSP > 0) {
                foreach ($os->objectifsSpecifiques as $osp) {
                    // Calculer le taux de l'OSP basé sur ses actions
                    $totalTauxOSP = 0;
                    $nombreActions = $osp->actions->count();
                    
                    if ($nombreActions > 0) {
                        foreach ($osp->actions as $action) {
                            // Calculer le taux de l'action basé sur ses sous-actions
                            $totalTauxAction = 0;
                            $nombreSousActions = $action->sousActions->count();
                            
                            if ($nombreSousActions > 0) {
                                $totalTauxAction = $action->sousActions->sum('taux_avancement') / $nombreSousActions;
                            }
                            
                            // Ajouter le taux calculé à l'action (pour l'affichage)
                            $action->taux_avancement_calcule = round($totalTauxAction, 2);
                            $totalTauxOSP += $totalTauxAction;
                        }
                        
                        $osp->taux_avancement_calcule = round($totalTauxOSP / $nombreActions, 2);
                    }
                    
                    $totalTauxOS += $osp->taux_avancement_calcule ?? 0;
                }
                
                $os->taux_avancement_calcule = round($totalTauxOS / $nombreOSP, 2);
            }
        }
        
        // Calculer le taux du pilier basé sur ses OS
        $totalTauxPilier = 0;
        $nombreOS = $this->pilier->objectifsStrategiques->count();
        
        if ($nombreOS > 0) {
            $totalTauxPilier = $this->pilier->objectifsStrategiques->sum('taux_avancement_calcule') / $nombreOS;
            $this->pilier->taux_avancement_calcule = round($totalTauxPilier, 2);
        }
    }

    /**
     * Met à jour la progression d'une sous-action via les boutons
     */
    public function updateProgressionBouton($sousActionId, $nouveauTaux)
    {
        try {
            Log::info('🚀 [BOUTON] updateProgressionBouton appelée', [
                'sousActionId' => $sousActionId,
                'nouveauTaux' => $nouveauTaux,
                'timestamp' => now(),
                'user_id' => Auth::id(),
                'memory_usage' => memory_get_usage()
            ]);
            
            // Appeler la méthode existante
            $this->updateSousActionTaux($sousActionId, $nouveauTaux);
            
            // LOGS DÉTAILLÉS DE TOUTE LA HIÉRARCHIE
            $this->loggerHierarchieComplete($sousActionId);
            
            // Mettre à jour l'interface en temps réel
            $this->dispatch('progression-updated', [
                'sousActionId' => $sousActionId,
                'nouveauTaux' => $nouveauTaux
            ]);
            
            // Toast de confirmation
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => "Progression mise à jour à {$nouveauTaux}% !"
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ [BOUTON] Erreur dans updateProgressionBouton', [
                'sousActionId' => $sousActionId,
                'nouveauTaux' => $nouveauTaux,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erreur lors de la mise à jour de la progression'
            ]);
        }
    }
    
    /**
     * Log détaillé de toute la hiérarchie après mise à jour
     */
    private function loggerHierarchieComplete($sousActionId)
    {
        try {
            // Récupérer la sous-action avec toutes ses relations
            $sousAction = SousAction::with([
                'action.objectifSpecifique.objectifStrategique.pilier'
            ])->find($sousActionId);
            
            if (!$sousAction) {
                Log::warning('⚠️ [HIERARCHIE] Sous-action non trouvée', ['sousActionId' => $sousActionId]);
                return;
            }
            
            // Calculer les taux actuels de toute la hiérarchie
            $tauxHierarchie = $this->calculerTauxHierarchieComplete($sousAction);
            
            // LOGS DÉTAILLÉS
            Log::info('📊 [HIERARCHIE] ===== MISE À JOUR COMPLÈTE ====', [
                'timestamp' => now(),
                'sous_action_id' => $sousActionId
            ]);
            
            // 1. SOUS-ACTION
            Log::info('🎯 [HIERARCHIE] SOUS-ACTION', [
                'code' => $sousAction->code,
                'libelle' => $sousAction->libelle,
                'pourcentage' => $sousAction->taux_avancement . '%',
                'taux_actuel' => $sousAction->taux_avancement
            ]);
            
            // 2. ACTION PARENT
            if ($sousAction->action) {
                Log::info('⚡ [HIERARCHIE] ACTION PARENT', [
                    'code' => $sousAction->action->code,
                    'libelle' => $sousAction->action->libelle,
                    'pourcentage' => $tauxHierarchie['action'] . '%',
                    'taux_calcule' => $tauxHierarchie['action']
                ]);
            }
            
            // 3. OSP PARENT
            if ($sousAction->action && $sousAction->action->objectifSpecifique) {
                Log::info('🎯 [HIERARCHIE] OSP PARENT', [
                    'code' => $sousAction->action->objectifSpecifique->code,
                    'libelle' => $sousAction->action->objectifSpecifique->libelle,
                    'pourcentage' => $tauxHierarchie['osp'] . '%',
                    'taux_calcule' => $tauxHierarchie['osp']
                ]);
            }
            
            // 4. OS PARENT
            if ($sousAction->action && $sousAction->action->objectifSpecifique && $sousAction->action->objectifSpecifique->objectifStrategique) {
                Log::info('🌟 [HIERARCHIE] OS PARENT', [
                    'code' => $sousAction->action->objectifSpecifique->objectifStrategique->code,
                    'libelle' => $sousAction->action->objectifSpecifique->objectifStrategique->libelle,
                    'pourcentage' => $tauxHierarchie['os'] . '%',
                    'taux_calcule' => $tauxHierarchie['os']
                ]);
            }
            
            // 5. PILIER PARENT
            if ($sousAction->action && $sousAction->action->objectifSpecifique && $sousAction->action->objectifSpecifique->objectifStrategique && $sousAction->action->objectifSpecifique->objectifStrategique->pilier) {
                Log::info('🏗️ [HIERARCHIE] PILIER PARENT', [
                    'code' => $sousAction->action->objectifSpecifique->objectifStrategique->pilier->code,
                    'libelle' => $sousAction->action->objectifSpecifique->objectifStrategique->pilier->libelle,
                    'pourcentage' => $tauxHierarchie['pilier'] . '%',
                    'taux_calcule' => $tauxHierarchie['pilier']
                ]);
            }
            
            Log::info('📊 [HIERARCHIE] ===== FIN DES LOGS ====');
            
        } catch (\Exception $e) {
            Log::error('❌ [HIERARCHIE] Erreur dans loggerHierarchieComplete', [
                'sousActionId' => $sousActionId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
    
    /**
     * Calcule les taux de toute la hiérarchie
     */
    private function calculerTauxHierarchieComplete($sousAction)
    {
        $taux = [
            'action' => 0,
            'osp' => 0,
            'os' => 0,
            'pilier' => 0
        ];
        
        try {
            // TAUX DE L'ACTION (moyenne des sous-actions)
            if ($sousAction->action && $sousAction->action->sousActions) {
                $sousActions = $sousAction->action->sousActions;
                if ($sousActions->count() > 0) {
                    $totalTaux = $sousActions->sum('taux_avancement');
                    $taux['action'] = round($totalTaux / $sousActions->count(), 2);
                }
            }
            
            // TAUX DE L'OSP (moyenne des actions)
            if ($sousAction->action && $sousAction->action->objectifSpecifique && $sousAction->action->objectifSpecifique->actions) {
                $actions = $sousAction->action->objectifSpecifique->actions;
                if ($actions->count() > 0) {
                    $totalTaux = 0;
                    foreach ($actions as $action) {
                        $sousActions = $action->sousActions;
                        if ($sousActions->count() > 0) {
                            $totalTaux += $sousActions->sum('taux_avancement') / $sousActions->count();
                        }
                    }
                    $taux['osp'] = round($totalTaux / $actions->count(), 2);
                }
            }
            
            // TAUX DE L'OS (moyenne des OSP)
            if ($sousAction->action && $sousAction->action->objectifSpecifique && $sousAction->action->objectifSpecifique->objectifStrategique && $sousAction->action->objectifSpecifique->objectifStrategique->objectifsSpecifiques) {
                $objectifsSpecifiques = $sousAction->action->objectifSpecifique->objectifStrategique->objectifsSpecifiques;
                if ($objectifsSpecifiques->count() > 0) {
                    $totalTaux = 0;
                    foreach ($objectifsSpecifiques as $osp) {
                        $actions = $osp->actions;
                        if ($actions->count() > 0) {
                            $tauxOSP = 0;
                            foreach ($actions as $action) {
                                $sousActions = $action->sousActions;
                                if ($sousActions->count() > 0) {
                                    $tauxOSP += $sousActions->sum('taux_avancement') / $sousActions->count();
                                }
                            }
                            $totalTaux += $tauxOSP / $actions->count();
                        }
                    }
                    $taux['os'] = round($totalTaux / $objectifsSpecifiques->count(), 2);
                }
            }
            
            // TAUX DU PILIER (moyenne des OS)
            if ($sousAction->action && $sousAction->action->objectifSpecifique && $sousAction->action->objectifSpecifique->objectifStrategique && $sousAction->action->objectifSpecifique->objectifStrategique->pilier && $sousAction->action->objectifSpecifique->objectifStrategique->pilier->objectifsStrategiques) {
                $objectifsStrategiques = $sousAction->action->objectifSpecifique->objectifStrategique->pilier->objectifsStrategiques;
                if ($objectifsStrategiques->count() > 0) {
                    $totalTaux = 0;
                    foreach ($objectifsStrategiques as $os) {
                        $objectifsSpecifiques = $os->objectifsSpecifiques;
                        if ($objectifsSpecifiques->count() > 0) {
                            $tauxOS = 0;
                            foreach ($objectifsSpecifiques as $osp) {
                                $actions = $osp->actions;
                                if ($actions->count() > 0) {
                                    $tauxOSP = 0;
                                    foreach ($actions as $action) {
                                        $sousActions = $action->sousActions;
                                        if ($sousActions->count() > 0) {
                                            $tauxOSP += $sousActions->sum('taux_avancement') / $sousActions->count();
                                        }
                                    }
                                    $tauxOS += $tauxOSP / $actions->count();
                                }
                            }
                            $totalTaux += $tauxOS / $objectifsSpecifiques->count();
                        }
                    }
                    $taux['pilier'] = round($totalTaux / $objectifsStrategiques->count(), 2);
                }
            }
            
        } catch (\Exception $e) {
            Log::error('❌ [HIERARCHIE] Erreur dans calculerTauxHierarchieComplete', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
        
        return $taux;
    }
    
    // Méthode de test pour forcer l'affichage
    public function testModalDisplay()
    {
        Log::info('🧪 [TEST] testModalDisplay appelée');
        
        // Forcer la propriété à true
        $this->showCreateObjectifForm = true;
        
        // Dispatch un événement personnalisé
        $this->dispatch('modal-test-opened');
        
        // Forcer le re-rendu
        $this->dispatch('$refresh');
        
        Log::info('🧪 [TEST] Modal forcé à true et re-rendu demandé');
    }
}
