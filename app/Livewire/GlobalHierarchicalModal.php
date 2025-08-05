<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pilier;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\User;
use App\Models\Action;
use App\Models\SousAction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GlobalHierarchicalModal extends Component
{
    public $pilier = null;
    public $objectifsStrategiques = [];
    public $showModal = false;
    public $selectedObjectifStrategique = null;
    public $showObjectifDetails = false;
    public $showPilierMainView = true;
    public $isLoading = false;
    public $currentBreadcrumb = [];
    public $animationDirection = 'next';
    public $isAnimating = false;
    
    // Propriétés pour les statistiques
    public $totalObjectifsSpecifiques = 0;
    public $totalActions = 0;
    public $totalSousActions = 0;
    public $objectifsTermines = 0;
    
    // Propriétés pour la création d'objectif stratégique
    public $newObjectifCode = '';
    public $newObjectifLibelle = '';
    public $newObjectifDescription = '';
    public $newObjectifOwnerId = '';
    public $showCreateForm = false;

    // Propriétés pour la création d'objectif spécifique
    public $newObjectifSpecifiqueCode = '';
    public $newObjectifSpecifiqueLibelle = '';
    public $newObjectifSpecifiqueDescription = '';
    public $newObjectifSpecifiqueOwnerId = '';
    public $showCreateObjectifSpecifiqueForm = false;
    
    // Propriétés pour l'édition d'objectif spécifique
    public $editingObjectifSpecifique = null;
    public $editObjectifSpecifiqueCode = '';
    public $editObjectifSpecifiqueLibelle = '';
    public $editObjectifSpecifiqueDescription = '';
    public $editObjectifSpecifiqueOwnerId = '';
    public $showEditObjectifSpecifiqueForm = false;
    
    // Propriétés pour l'édition d'objectif stratégique
    public $showEditObjectifStrategiqueForm = false;
    public $editingObjectifStrategique = null;
    public $editObjectifStrategiqueCode = '';
    public $editObjectifStrategiqueLibelle = '';
    public $editObjectifStrategiqueDescription = '';
    public $editObjectifStrategiqueOwnerId = '';

    // Propriétés pour les détails d'objectif spécifique
    public $selectedObjectifSpecifiqueDetails = null;
    public $showObjectifSpecifiqueDetails = false;
    
    public $selectedObjectifSpecifiqueId = null;
    public $actionToPerform = '';
    
    // Propriétés pour les actions
    public $selectedAction = null;
    public $showActionDetails = false;
    public $showCreateActionForm = false;
    public $showEditActionForm = false;
    public $editingAction = null;
    
    // Propriétés pour les sous-actions
    public $selectedSousAction = null;
    public $showSousActionDetails = false;
    public $showCreateSousActionForm = false;
    public $showEditSousActionForm = false;
    public $editingSousAction = null;
    
    // Formulaires pour les actions
    public $newActionCode = '';
    public $newActionLibelle = '';
    public $newActionDescription = '';
    public $newActionOwnerId = '';
    
    // Formulaires pour l'édition d'actions
    public $editActionCode = '';
    public $editActionLibelle = '';
    public $editActionDescription = '';
    public $editActionOwnerId = '';
    
    // Formulaires pour les sous-actions
    public $newSousActionCode = '';
    public $newSousActionLibelle = '';
    public $newSousActionDescription = '';
    public $newSousActionOwnerId = '';
    public $newSousActionDateEcheance = '';
    public $newSousActionDateRealisation = '';
    public $newSousActionTauxAvancement = 0;
    
    // Formulaires pour l'édition de sous-actions
    public $editSousActionCode = '';
    public $editSousActionLibelle = '';
    public $editSousActionDescription = '';
    public $editSousActionOwnerId = '';
    public $editSousActionDateEcheance = '';
    public $editSousActionDateRealisation = '';
    public $editSousActionTauxAvancement = 0;

    protected $listeners = [
        'openHierarchicalModal' => 'openModal',
        'refreshPage' => 'refreshPage'
    ];

    public function openModal($pilierId)
    {
        $user = Auth::user();
        
        // Vérifier les permissions selon le rôle
        if (!$this->canAccessPilier($user, $pilierId)) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Vous n\'avez pas les permissions pour accéder à ce pilier'
            ]);
            return;
        }

        $this->pilier = Pilier::find($pilierId);
        
        if (!$this->pilier) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Pilier non trouvé'
            ]);
            return;
        }

        $this->showModal = true;
        $this->showPilierMainView = true;
        $this->showObjectifDetails = false;
        $this->showCreateForm = false;
        $this->selectedObjectifStrategique = null;
        $this->selectedObjectifSpecifiqueDetails = null;
        $this->selectedAction = null;
        $this->selectedSousAction = null;
        
        $this->loadPilierData();
        $this->updateBreadcrumb('pilier');
    }

    private function canAccessPilier($user, $pilierId)
    {
        // Admin général peut accéder à tout
        if ($user->isAdminGeneral()) {
            return true;
        }

        // Owner du pilier peut accéder à son pilier
        if ($user->isOwnerPIL()) {
            $pilier = Pilier::find($pilierId);
            return $pilier && $pilier->owner_id === $user->id;
        }

        // Owner d'action peut accéder aux piliers qui contiennent ses actions
        if ($user->isOwnerAction()) {
            return Action::where('owner_id', $user->id)
                ->whereHas('objectifSpecifique.objectifStrategique.pilier', function($query) use ($pilierId) {
                    $query->where('id', $pilierId);
                })->exists();
        }

        // Owner d'objectif spécifique peut accéder aux piliers qui contiennent ses objectifs
        if ($user->isOwnerOS()) {
            return ObjectifSpecifique::where('owner_id', $user->id)
                ->whereHas('objectifStrategique.pilier', function($query) use ($pilierId) {
                    $query->where('id', $pilierId);
                })->exists();
        }

        return false;
    }

    public function updateBreadcrumb($level, $data = null)
    {
        $this->currentBreadcrumb = [];
        
        if ($level === 'pilier' && $this->pilier) {
            $this->currentBreadcrumb = [
                ['level' => 'pilier', 'label' => $this->pilier->code, 'data' => $this->pilier]
            ];
        } elseif ($level === 'objectif_strategique' && $this->selectedObjectifStrategique) {
            $this->currentBreadcrumb = [
                ['level' => 'pilier', 'label' => $this->pilier->code, 'data' => $this->pilier],
                ['level' => 'objectif_strategique', 'label' => $this->selectedObjectifStrategique->code, 'data' => $this->selectedObjectifStrategique]
            ];
        } elseif ($level === 'objectif_specifique' && $this->selectedObjectifSpecifiqueDetails) {
            $this->currentBreadcrumb = [
                ['level' => 'pilier', 'label' => $this->pilier->code, 'data' => $this->pilier],
                ['level' => 'objectif_strategique', 'label' => $this->selectedObjectifStrategique->code, 'data' => $this->selectedObjectifStrategique],
                ['level' => 'objectif_specifique', 'label' => $this->selectedObjectifSpecifiqueDetails->code, 'data' => $this->selectedObjectifSpecifiqueDetails]
            ];
        } elseif ($level === 'action' && $this->selectedAction) {
            $this->currentBreadcrumb = [
                ['level' => 'pilier', 'label' => $this->pilier->code, 'data' => $this->pilier],
                ['level' => 'objectif_strategique', 'label' => $this->selectedObjectifStrategique->code, 'data' => $this->selectedObjectifStrategique],
                ['level' => 'objectif_specifique', 'label' => $this->selectedObjectifSpecifiqueDetails->code, 'data' => $this->selectedObjectifSpecifiqueDetails],
                ['level' => 'action', 'label' => $this->selectedAction->code, 'data' => $this->selectedAction]
            ];
        } elseif ($level === 'sous_action' && $this->selectedSousAction) {
            $this->currentBreadcrumb = [
                ['level' => 'pilier', 'label' => $this->pilier->code, 'data' => $this->pilier],
                ['level' => 'objectif_strategique', 'label' => $this->selectedObjectifStrategique->code, 'data' => $this->selectedObjectifStrategique],
                ['level' => 'objectif_specifique', 'label' => $this->selectedObjectifSpecifiqueDetails->code, 'data' => $this->selectedObjectifSpecifiqueDetails],
                ['level' => 'action', 'label' => $this->selectedAction->code, 'data' => $this->selectedAction],
                ['level' => 'sous_action', 'label' => $this->selectedSousAction->code, 'data' => $this->selectedSousAction]
            ];
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->pilier = null;
        $this->objectifsStrategiques = [];
        $this->selectedObjectifStrategique = null;
        $this->showObjectifDetails = false;
        $this->showCreateForm = false;
        
        // Actualiser la page après avoir fermé le modal
        $this->dispatch('refreshPage');
    }

    public function loadPilierData()
    {
        if (!$this->pilier) return;
        
        $this->objectifsStrategiques = $this->pilier->objectifsStrategiques;
        
        // Calculer les statistiques
        $this->totalObjectifsSpecifiques = $this->objectifsStrategiques->sum(function($os) {
            return $os->objectifsSpecifiques->count();
        });
        
        $this->totalActions = $this->objectifsStrategiques->sum(function($os) {
            return $os->objectifsSpecifiques->sum(function($ospec) {
                return $ospec->actions->count();
            });
        });
        
        $this->totalSousActions = $this->objectifsStrategiques->sum(function($os) {
            return $os->objectifsSpecifiques->sum(function($ospec) {
                return $ospec->actions->sum(function($action) {
                    return $action->sousActions->count();
                });
            });
        });
        
        $this->objectifsTermines = $this->objectifsStrategiques->filter(function($os) {
            return $os->taux_avancement == 100;
        })->count();
    }

    public function getUsersProperty()
    {
        $user = Auth::user();
        
        // Filtrer les utilisateurs selon le rôle de l'utilisateur connecté
        if ($user->isAdminGeneral()) {
            return User::whereHas('role', function($query) {
                $query->whereIn('nom', ['admin_general', 'owner_os', 'owner_pil', 'owner_action']);
            })->get();
        }
        
        if ($user->isOwnerPIL()) {
            return User::whereHas('role', function($query) {
                $query->whereIn('nom', ['owner_action']);
            })->get();
        }
        
        if ($user->isOwnerOS()) {
            return User::whereHas('role', function($query) {
                $query->whereIn('nom', ['owner_action']);
            })->get();
        }
        
        if ($user->isOwnerAction()) {
            return collect([$user]); // Peut seulement s'assigner à lui-même
        }
        
        return collect();
    }

    public function refreshPage()
    {
        $this->dispatch('refreshPage');
    }

    public function render()
    {
        return view('livewire.global-hierarchical-modal');
    }
}
