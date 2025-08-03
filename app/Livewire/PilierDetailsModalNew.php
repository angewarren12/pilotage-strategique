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

class PilierDetailsModalNew extends Component
{
    public $pilier = null;
    public $objectifsStrategiques = [];
    public $showModal = false;
    public $selectedObjectifStrategique = null;
    public $showObjectifDetails = false;
    public $showPilierMainView = true;
    public $isLoading = false;
    public $currentBreadcrumb = [];
    public $animationDirection = 'next'; // 'next' ou 'prev'
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
    
    public $testProperty = '';
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
    public $newActionDateEcheance = '';
    public $newActionDateRealisation = '';
    
    // Formulaires pour l'édition d'actions
    public $editActionCode = '';
    public $editActionLibelle = '';
    public $editActionDescription = '';
    public $editActionOwnerId = '';
    public $editActionDateEcheance = '';
    public $editActionDateRealisation = '';
    
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
        'openPilierModal' => 'openModal',
        'closeModal' => 'closeModal',
        'refreshData' => 'loadPilierData'
    ];

    public function openModal($pilierId)
    {
        $this->isLoading = true;
        
        $this->pilier = Pilier::with([
            'objectifsStrategiques.owner',
            'objectifsStrategiques.objectifsSpecifiques.owner',
            'objectifsStrategiques.objectifsSpecifiques.actions.owner',
            'objectifsStrategiques.objectifsSpecifiques.actions.sousActions.owner'
        ])->findOrFail($pilierId);
        
        $this->loadPilierData();
        $this->showModal = true;
        $this->showObjectifDetails = false;
        $this->selectedObjectifStrategique = null;
        $this->showCreateForm = false;
        $this->showPilierMainView = true;
        
        // Initialiser le breadcrumb pour le pilier
        $this->updateBreadcrumb('pilier');
        
        // Suggérer un code pour le nouvel objectif
        $this->suggestNewObjectifCode();
        
        // Animation de chargement de 2 secondes
        $this->dispatch('startLoading');
    }

    public function updateBreadcrumb($level, $data = null)
    {
        $this->currentBreadcrumb = [];
        
        // Toujours commencer par le pilier
        $this->currentBreadcrumb[] = [
            'code' => $this->pilier->code,
            'label' => $this->pilier->libelle,
            'type' => 'pilier',
            'action' => 'naviguerVersPilier'
        ];
        
        switch ($level) {
            case 'pilier':
                // Breadcrumb: P1
                break;
                
            case 'objectif_strategique':
                // Breadcrumb: P1 > OS1
                $this->currentBreadcrumb[] = [
                    'code' => $this->selectedObjectifStrategique->code,
                    'label' => $this->selectedObjectifStrategique->libelle,
                    'type' => 'objectif_strategique',
                    'action' => 'naviguerVersObjectifStrategique'
                ];
                break;
                
            case 'objectif_specifique':
                // Breadcrumb: P1 > OS1 > PIL1
                $this->currentBreadcrumb[] = [
                    'code' => $this->selectedObjectifStrategique->code,
                    'label' => $this->selectedObjectifStrategique->libelle,
                    'type' => 'objectif_strategique',
                    'action' => 'naviguerVersObjectifStrategique'
                ];
                $this->currentBreadcrumb[] = [
                    'code' => $this->selectedObjectifSpecifiqueDetails->code,
                    'label' => $this->selectedObjectifSpecifiqueDetails->libelle,
                    'type' => 'objectif_specifique',
                    'action' => 'naviguerVersObjectifSpecifique'
                ];
                break;
                
            case 'action':
                // Breadcrumb: P1 > OS1 > PIL1 > ACT1
                $this->currentBreadcrumb[] = [
                    'code' => $this->selectedObjectifStrategique->code,
                    'label' => $this->selectedObjectifStrategique->libelle,
                    'type' => 'objectif_strategique',
                    'action' => 'naviguerVersObjectifStrategique'
                ];
                $this->currentBreadcrumb[] = [
                    'code' => $this->selectedObjectifSpecifiqueDetails->code,
                    'label' => $this->selectedObjectifSpecifiqueDetails->libelle,
                    'type' => 'objectif_specifique',
                    'action' => 'naviguerVersObjectifSpecifique'
                ];
                $this->currentBreadcrumb[] = [
                    'code' => $this->selectedAction->code,
                    'label' => $this->selectedAction->libelle,
                    'type' => 'action',
                    'action' => 'naviguerVersAction'
                ];
                break;
                
            case 'sous_action':
                // Breadcrumb: P1 > OS1 > PIL1 > ACT1 > SA1
                $this->currentBreadcrumb[] = [
                    'code' => $this->selectedObjectifStrategique->code,
                    'label' => $this->selectedObjectifStrategique->libelle,
                    'type' => 'objectif_strategique',
                    'action' => 'naviguerVersObjectifStrategique'
                ];
                $this->currentBreadcrumb[] = [
                    'code' => $this->selectedObjectifSpecifiqueDetails->code,
                    'label' => $this->selectedObjectifSpecifiqueDetails->libelle,
                    'type' => 'objectif_specifique',
                    'action' => 'naviguerVersObjectifSpecifique'
                ];
                $this->currentBreadcrumb[] = [
                    'code' => $this->selectedAction->code,
                    'label' => $this->selectedAction->libelle,
                    'type' => 'action',
                    'action' => 'naviguerVersAction'
                ];
                $this->currentBreadcrumb[] = [
                    'code' => $this->selectedSousAction->code,
                    'label' => $this->selectedSousAction->libelle,
                    'type' => 'sous_action',
                    'action' => 'retourListeSousActions'
                ];
                break;
        }
    }

    // Nouvelles méthodes pour la navigation via breadcrumb
    public function naviguerVersPilier()
    {
        $this->animationDirection = 'prev';
        $this->isAnimating = true;
        $this->dispatch('startSlideAnimation', ['direction' => 'prev']);
        
        $this->isLoading = true;
        $this->dispatch('startLoading');
        
        // Reset toutes les variables sélectionnées
        $this->selectedObjectifStrategique = null;
        $this->selectedObjectifSpecifiqueDetails = null;
        $this->selectedAction = null;
        $this->selectedSousAction = null;
        
        // Reset tous les états d'affichage
        $this->showObjectifDetails = false;
        $this->showObjectifSpecifiqueDetails = false;
        $this->showActionDetails = false;
        $this->showSousActionDetails = false;
        $this->showCreateForm = false;
        $this->showCreateObjectifSpecifiqueForm = false;
        $this->showCreateActionForm = false;
        $this->showCreateSousActionForm = false;
        
        // Retour à la vue principale du pilier
        $this->showPilierMainView = true;
        
        // Mettre à jour le breadcrumb
        $this->updateBreadcrumb('pilier');
        
        // Arrêter l'animation après le délai
        $this->dispatch('stopSlideAnimation');
        $this->isAnimating = false;
    }

    public function naviguerVersObjectifStrategique()
    {
        $this->animationDirection = 'prev';
        $this->isAnimating = true;
        $this->dispatch('startSlideAnimation', ['direction' => 'prev']);
        
        $this->isLoading = true;
        $this->dispatch('startLoading');
        
        // Reset les variables des niveaux inférieurs
        $this->selectedObjectifSpecifiqueDetails = null;
        $this->selectedAction = null;
        $this->selectedSousAction = null;
        
        // Reset les états d'affichage des niveaux inférieurs
        $this->showObjectifSpecifiqueDetails = false;
        $this->showActionDetails = false;
        $this->showSousActionDetails = false;
        $this->showCreateObjectifSpecifiqueForm = false;
        $this->showCreateActionForm = false;
        $this->showCreateSousActionForm = false;
        
        // Afficher les détails de l'objectif stratégique
        $this->showObjectifDetails = true;
        $this->showPilierMainView = false;
        
        // Mettre à jour le breadcrumb
        $this->updateBreadcrumb('objectif_strategique');
        
        // Arrêter l'animation après le délai
        $this->dispatch('stopSlideAnimation');
        $this->isAnimating = false;
    }

    public function naviguerVersObjectifSpecifique()
    {
        $this->animationDirection = 'prev';
        $this->isAnimating = true;
        $this->dispatch('startSlideAnimation', ['direction' => 'prev']);
        
        $this->isLoading = true;
        $this->dispatch('startLoading');
        
        // Reset les variables des niveaux inférieurs
        $this->selectedAction = null;
        $this->selectedSousAction = null;
        
        // Reset les états d'affichage des niveaux inférieurs
        $this->showActionDetails = false;
        $this->showSousActionDetails = false;
        $this->showCreateActionForm = false;
        $this->showCreateSousActionForm = false;
        
        // Afficher les détails de l'objectif spécifique
        $this->showObjectifSpecifiqueDetails = true;
        $this->showObjectifDetails = false;
        $this->showPilierMainView = false;
        
        // Mettre à jour le breadcrumb
        $this->updateBreadcrumb('objectif_specifique');
        
        // Arrêter l'animation après le délai
        $this->dispatch('stopSlideAnimation');
        $this->isAnimating = false;
    }

    public function naviguerVersAction()
    {
        $this->animationDirection = 'prev';
        $this->isAnimating = true;
        $this->dispatch('startSlideAnimation', ['direction' => 'prev']);
        
        $this->isLoading = true;
        $this->dispatch('startLoading');
        
        // Reset les variables des niveaux inférieurs
        $this->selectedSousAction = null;
        
        // Reset les états d'affichage des niveaux inférieurs
        $this->showSousActionDetails = false;
        $this->showCreateSousActionForm = false;
        
        // Afficher les détails de l'action
        $this->showActionDetails = true;
        $this->showObjectifSpecifiqueDetails = false;
        $this->showObjectifDetails = false;
        $this->showPilierMainView = false;
        
        // Mettre à jour le breadcrumb
        $this->updateBreadcrumb('action');
        
        // Arrêter l'animation après le délai
        $this->dispatch('stopSlideAnimation');
        $this->isAnimating = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->pilier = null;
        $this->objectifsStrategiques = [];
        $this->selectedObjectifStrategique = null;
        $this->showObjectifDetails = false;
        $this->showCreateForm = false;
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

    public function voirObjectifStrategique($objectifId)
    {
        $this->animationDirection = 'next';
        $this->isAnimating = true;
        
        // Déclencher l'animation
        $this->dispatch('startSlideAnimation', ['direction' => 'next']);
        
        $this->isLoading = true;
        $this->dispatch('startLoading');
        
        $this->selectedObjectifStrategique = $this->objectifsStrategiques->find($objectifId);
        $this->showObjectifDetails = true;
        $this->showCreateForm = false;
        $this->showPilierMainView = false;
        
        // Mettre à jour le breadcrumb
        $this->updateBreadcrumb('objectif_strategique');
        
        // Arrêter l'animation après le délai
        $this->dispatch('stopSlideAnimation');
        $this->isAnimating = false;
    }

    public function retourListeObjectifs()
    {
        $this->isLoading = true;
        $this->dispatch('startLoading');
        
        $this->showObjectifDetails = false;
        $this->selectedObjectifStrategique = null;
        $this->selectedObjectifSpecifiqueDetails = null;
        $this->selectedAction = null;
        $this->selectedSousAction = null;
        $this->showCreateForm = false;
        $this->showObjectifSpecifiqueDetails = false;
        $this->showActionDetails = false;
        $this->showSousActionDetails = false;
        $this->showPilierMainView = true;
        
        // Mettre à jour le breadcrumb
        $this->updateBreadcrumb('pilier');
    }

    public function showCreateObjectifForm()
    {
        $this->showCreateForm = true;
        $this->showObjectifDetails = false;
        $this->suggestNewObjectifCode();
    }

    public function cancelCreate()
    {
        $this->showCreateForm = false;
        $this->resetCreateForm();
    }

    public function suggestNewObjectifCode()
    {
        $existingCodes = $this->objectifsStrategiques->pluck('code')->toArray();
        $nextNumber = 1;
        
        while (in_array("OS{$nextNumber}", $existingCodes)) {
            $nextNumber++;
        }
        
        $this->newObjectifCode = "OS{$nextNumber}";
    }

    public function resetCreateForm()
    {
        $this->newObjectifCode = '';
        $this->newObjectifLibelle = '';
        $this->newObjectifDescription = '';
        $this->newObjectifOwnerId = '';
    }

    public function createObjectifStrategique()
    {
        $this->validate([
            'newObjectifCode' => 'required|string|max:10|unique:objectif_strategiques,code',
            'newObjectifLibelle' => 'required|string|max:255',
            'newObjectifDescription' => 'nullable|string',
            'newObjectifOwnerId' => 'nullable|exists:users,id'
        ]);

        try {
            $objectifStrategique = ObjectifStrategique::create([
                'code' => $this->newObjectifCode,
                'libelle' => $this->newObjectifLibelle,
                'description' => $this->newObjectifDescription,
                'pilier_id' => $this->pilier->id,
                'owner_id' => $this->newObjectifOwnerId ?: null,
            ]);

            $this->resetCreateForm();
            $this->showCreateForm = false;
            $this->loadPilierData();
            
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Objectif stratégique créé avec succès !'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Erreur lors de la création : ' . $e->getMessage()
            ]);
        }
    }

    public function deleteObjectifStrategique($objectifId)
    {
        try {
            $objectif = $this->objectifsStrategiques->find($objectifId);
            if ($objectif) {
                $objectif->delete();
                $this->loadPilierData();
                
                $this->dispatch('showToast', [
                    'type' => 'success',
                    'message' => 'Objectif stratégique supprimé avec succès !'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ]);
        }
    }



    public function getUsersProperty()
    {
        return User::whereHas('role', function($query) {
            $query->whereIn('nom', ['admin_general', 'owner_os']);
        })->get();
    }

    // Méthodes pour la création d'objectif spécifique
    public function showCreateObjectifSpecifiqueForm()
    {
        Log::info('🔍 [DEBUG] showCreateObjectifSpecifiqueForm appelée');
        
        try {
            $this->showCreateObjectifSpecifiqueForm = true;
            $this->suggestNewObjectifSpecifiqueCode();
            
            Log::info('✅ [DEBUG] showCreateObjectifSpecifiqueForm terminée avec succès', [
                'showCreateObjectifSpecifiqueForm' => $this->showCreateObjectifSpecifiqueForm,
                'selectedObjectifStrategique' => $this->selectedObjectifStrategique ? $this->selectedObjectifStrategique->id : null
            ]);
            
            $this->dispatch('showToast', [
                'type' => 'info',
                'message' => 'Formulaire de création ouvert !'
            ]);
            
            // Forcer le re-rendu
            $this->render();
            
        } catch (\Exception $e) {
            Log::error('❌ [ERROR] Erreur dans showCreateObjectifSpecifiqueForm', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    public function cancelCreateObjectifSpecifique()
    {
        $this->showCreateObjectifSpecifiqueForm = false;
        $this->resetObjectifSpecifiqueForm();
    }

    public function suggestNewObjectifSpecifiqueCode()
    {
        if (!$this->selectedObjectifStrategique) return;
        
        $existingCodes = $this->selectedObjectifStrategique->objectifsSpecifiques->pluck('code')->toArray();
        $nextNumber = 1;
        
        while (in_array("PIL{$nextNumber}", $existingCodes)) {
            $nextNumber++;
        }
        
        $this->newObjectifSpecifiqueCode = "PIL{$nextNumber}";
    }

    public function resetObjectifSpecifiqueForm()
    {
        $this->newObjectifSpecifiqueCode = '';
        $this->newObjectifSpecifiqueLibelle = '';
        $this->newObjectifSpecifiqueDescription = '';
        $this->newObjectifSpecifiqueOwnerId = '';
    }

    public function createObjectifSpecifique()
    {
        $this->validate([
            'newObjectifSpecifiqueCode' => 'required|string|max:10|unique:objectif_specifiques,code',
            'newObjectifSpecifiqueLibelle' => 'required|string|max:255',
            'newObjectifSpecifiqueDescription' => 'nullable|string',
            'newObjectifSpecifiqueOwnerId' => 'nullable|exists:users,id'
        ]);

        try {
            $objectifSpecifique = ObjectifSpecifique::create([
                'code' => $this->newObjectifSpecifiqueCode,
                'libelle' => $this->newObjectifSpecifiqueLibelle,
                'description' => $this->newObjectifSpecifiqueDescription,
                'objectif_strategique_id' => $this->selectedObjectifStrategique->id,
                'owner_id' => $this->newObjectifSpecifiqueOwnerId ?: null,
            ]);

            $this->resetObjectifSpecifiqueForm();
            $this->showCreateObjectifSpecifiqueForm = false;
            $this->loadPilierData();
            
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Objectif spécifique créé avec succès !'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Erreur lors de la création : ' . $e->getMessage()
            ]);
        }
    }

    // Méthodes pour l'édition d'objectif spécifique
    public function showEditObjectifSpecifiqueForm($objectifSpecifiqueId)
    {
        $this->editingObjectifSpecifique = $this->selectedObjectifStrategique->objectifsSpecifiques->find($objectifSpecifiqueId);
        
        if ($this->editingObjectifSpecifique) {
            $this->editObjectifSpecifiqueCode = $this->editingObjectifSpecifique->code;
            $this->editObjectifSpecifiqueLibelle = $this->editingObjectifSpecifique->libelle;
            $this->editObjectifSpecifiqueDescription = $this->editingObjectifSpecifique->description ?? '';
            $this->editObjectifSpecifiqueOwnerId = $this->editingObjectifSpecifique->owner_id ?? '';
            
            $this->showEditObjectifSpecifiqueForm = true;
            $this->showCreateObjectifSpecifiqueForm = false;
            $this->showObjectifSpecifiqueDetails = false;
        }
    }

    public function cancelEditObjectifSpecifique()
    {
        $this->showEditObjectifSpecifiqueForm = false;
        $this->editingObjectifSpecifique = null;
        $this->resetEditObjectifSpecifiqueForm();
    }

    public function resetEditObjectifSpecifiqueForm()
    {
        $this->editObjectifSpecifiqueCode = '';
        $this->editObjectifSpecifiqueLibelle = '';
        $this->editObjectifSpecifiqueDescription = '';
        $this->editObjectifSpecifiqueOwnerId = '';
    }

    public function updateObjectifSpecifique()
    {
        $this->validate([
            'editObjectifSpecifiqueCode' => 'required|string|max:10|unique:objectif_specifiques,code,' . $this->editingObjectifSpecifique->id,
            'editObjectifSpecifiqueLibelle' => 'required|string|max:255',
            'editObjectifSpecifiqueDescription' => 'nullable|string',
            'editObjectifSpecifiqueOwnerId' => 'nullable|exists:users,id'
        ]);

        try {
            $this->editingObjectifSpecifique->update([
                'code' => $this->editObjectifSpecifiqueCode,
                'libelle' => $this->editObjectifSpecifiqueLibelle,
                'description' => $this->editObjectifSpecifiqueDescription,
                'owner_id' => $this->editObjectifSpecifiqueOwnerId ?: null,
            ]);

            $this->resetEditObjectifSpecifiqueForm();
            $this->showEditObjectifSpecifiqueForm = false;
            $this->editingObjectifSpecifique = null;
            $this->loadPilierData();
            
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Objectif spécifique modifié avec succès !'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Erreur lors de la modification : ' . $e->getMessage()
            ]);
        }
    }

    // Méthodes pour les détails d'objectif spécifique
    public function showObjectifSpecifiqueDetails($objectifSpecifiqueId)
    {
        $this->animationDirection = 'next';
        $this->isAnimating = true;
        $this->dispatch('startSlideAnimation', ['direction' => 'next']);
        
        $this->isLoading = true;
        $this->dispatch('startLoading');
        
        // Si on n'a pas d'objectif stratégique sélectionné, on doit le trouver
        if (!$this->selectedObjectifStrategique) {
            // Chercher l'objectif spécifique dans tous les objectifs stratégiques du pilier
            foreach ($this->objectifsStrategiques as $objectifStrategique) {
                $objectifSpecifique = $objectifStrategique->objectifsSpecifiques->find($objectifSpecifiqueId);
                if ($objectifSpecifique) {
                    $this->selectedObjectifStrategique = $objectifStrategique;
                    $this->selectedObjectifSpecifiqueDetails = $objectifSpecifique;
                    break;
                }
            }
        } else {
            $this->selectedObjectifSpecifiqueDetails = $this->selectedObjectifStrategique->objectifsSpecifiques->find($objectifSpecifiqueId);
        }
        
        if ($this->selectedObjectifSpecifiqueDetails) {
            $this->showObjectifSpecifiqueDetails = true;
            $this->showCreateObjectifSpecifiqueForm = false;
            $this->showEditObjectifSpecifiqueForm = false;
            $this->showCreateActionForm = false;
            $this->showEditActionForm = false;
            $this->showActionDetails = false;
            $this->showCreateSousActionForm = false;
            $this->showEditSousActionForm = false;
            $this->showSousActionDetails = false;
            // Masquer la liste des objectifs spécifiques pour afficher les détails
            $this->showObjectifDetails = false;
            // Masquer aussi les détails du pilier (vue principale)
            $this->showPilierMainView = false;
            
            // Mettre à jour le breadcrumb
            $this->updateBreadcrumb('objectif_specifique');
        }
        
        // Arrêter l'animation après le délai
        $this->dispatch('stopSlideAnimation');
        $this->isAnimating = false;
    }

    public function retourListeObjectifsSpecifiques()
    {
        $this->isLoading = true;
        $this->dispatch('startLoading');
        
        $this->showObjectifSpecifiqueDetails = false;
        $this->selectedObjectifSpecifiqueDetails = null;
        $this->selectedAction = null;
        $this->selectedSousAction = null;
        $this->showCreateActionForm = false;
        $this->showEditActionForm = false;
        $this->showActionDetails = false;
        $this->showCreateSousActionForm = false;
        $this->showEditSousActionForm = false;
        $this->showSousActionDetails = false;
        // Revenir à la liste des objectifs spécifiques
        $this->showObjectifDetails = true;
        // Restaurer la vue principale du pilier
        $this->showPilierMainView = true;
        
        // Mettre à jour le breadcrumb
        $this->updateBreadcrumb('objectif_strategique');
    }

    public function testMethod()
    {
        $this->dispatch('showToast', [
            'type' => 'info',
            'message' => 'Test method works!'
        ]);
    }

    public function setActionToView($objectifSpecifiqueId)
    {
        $this->selectedObjectifSpecifiqueId = $objectifSpecifiqueId;
        $this->actionToPerform = 'view';
        $this->showObjectifSpecifiqueDetails($objectifSpecifiqueId);
    }

    public function setActionToEdit($objectifSpecifiqueId)
    {
        $this->selectedObjectifSpecifiqueId = $objectifSpecifiqueId;
        $this->actionToPerform = 'edit';
        $this->showEditObjectifSpecifiqueForm($objectifSpecifiqueId);
    }

    // Méthodes pour les actions
    public function displayActionDetails($actionId)
    {
        $this->animationDirection = 'next';
        $this->isAnimating = true;
        $this->dispatch('startSlideAnimation', ['direction' => 'next']);
        
        $this->dispatch('showToast', ['type' => 'info', 'message' => 'showActionDetails appelée avec ID: ' . $actionId]);
        
        $this->isLoading = true;
        $this->dispatch('startLoading');
        
        $this->selectedAction = $this->selectedObjectifSpecifiqueDetails->actions->find($actionId);
        
        if ($this->selectedAction) {
            $this->showActionDetails = true;
            $this->showCreateActionForm = false;
            $this->showEditActionForm = false;
            $this->showCreateSousActionForm = false;
            $this->showEditSousActionForm = false;
            $this->showSousActionDetails = false;
            
            // Masquer les autres vues
            $this->showObjectifDetails = false;
            $this->showObjectifSpecifiqueDetails = false;
            $this->showPilierMainView = false;
            
            // Mettre à jour le breadcrumb
            $this->updateBreadcrumb('action');
            
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Détails de l\'action affichés']);
        } else {
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Action non trouvée']);
        }
        
        // Arrêter l'animation après le délai
        $this->dispatch('stopSlideAnimation');
        $this->isAnimating = false;
    }

    public function retourListeActions()
    {
        $this->isLoading = true;
        $this->dispatch('startLoading');
        
        $this->showActionDetails = false;
        $this->selectedAction = null;
        $this->showSousActionDetails = false;
        $this->selectedSousAction = null;
        
        // Restaurer les vues
        $this->showObjectifSpecifiqueDetails = true;
        $this->showPilierMainView = false;
        
        // Mettre à jour le breadcrumb
        $this->updateBreadcrumb('objectif_specifique');
    }

    public function showCreateActionForm()
    {
        $this->showCreateActionForm = true;
        $this->showActionDetails = false;
        $this->showEditActionForm = false;
        $this->suggestNewActionCode();
    }

    public function cancelCreateAction()
    {
        $this->showCreateActionForm = false;
        $this->resetActionForm();
    }

    public function suggestNewActionCode()
    {
        $existingCodes = $this->selectedObjectifSpecifiqueDetails->actions->pluck('code')->toArray();
        $nextNumber = 1;
        
        while (in_array("ACT{$nextNumber}", $existingCodes)) {
            $nextNumber++;
        }
        
        $this->newActionCode = "ACT{$nextNumber}";
    }

    public function resetActionForm()
    {
        $this->newActionCode = '';
        $this->newActionLibelle = '';
        $this->newActionDescription = '';
        $this->newActionOwnerId = '';
        $this->newActionDateEcheance = '';
        $this->newActionDateRealisation = '';
    }

    public function createAction()
    {
        $this->validate([
            'newActionCode' => 'required|string|max:10|unique:actions,code',
            'newActionLibelle' => 'required|string|max:255',
            'newActionDescription' => 'nullable|string',
            'newActionOwnerId' => 'nullable|exists:users,id',
            'newActionDateEcheance' => 'nullable|date',
            'newActionDateRealisation' => 'nullable|date'
        ]);

        try {
            $action = Action::create([
                'code' => $this->newActionCode,
                'libelle' => $this->newActionLibelle,
                'description' => $this->newActionDescription,
                'objectif_specifique_id' => $this->selectedObjectifSpecifiqueDetails->id,
                'owner_id' => $this->newActionOwnerId ?: null,
                'date_echeance' => $this->newActionDateEcheance ?: null,
                'date_realisation' => $this->newActionDateRealisation ?: null,
            ]);

            $this->resetActionForm();
            $this->showCreateActionForm = false;
            $this->loadPilierData();
            
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Action créée avec succès !'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Erreur lors de la création : ' . $e->getMessage()
            ]);
        }
    }

    public function showEditActionForm($actionId)
    {
        $this->editingAction = $this->selectedObjectifSpecifiqueDetails->actions->find($actionId);
        
        if ($this->editingAction) {
            $this->editActionCode = $this->editingAction->code;
            $this->editActionLibelle = $this->editingAction->libelle;
            $this->editActionDescription = $this->editingAction->description ?? '';
            $this->editActionOwnerId = $this->editingAction->owner_id ?? '';
            $this->editActionDateEcheance = $this->editingAction->date_echeance ? $this->editingAction->date_echeance->format('Y-m-d') : '';
            $this->editActionDateRealisation = $this->editingAction->date_realisation ? $this->editingAction->date_realisation->format('Y-m-d') : '';
            
            $this->showEditActionForm = true;
            $this->showCreateActionForm = false;
            $this->showActionDetails = false;
        }
    }

    public function cancelEditAction()
    {
        $this->showEditActionForm = false;
        $this->editingAction = null;
        $this->resetEditActionForm();
    }

    public function resetEditActionForm()
    {
        $this->editActionCode = '';
        $this->editActionLibelle = '';
        $this->editActionDescription = '';
        $this->editActionOwnerId = '';
        $this->editActionDateEcheance = '';
        $this->editActionDateRealisation = '';
    }

    public function updateAction()
    {
        $this->validate([
            'editActionCode' => 'required|string|max:10|unique:actions,code,' . $this->editingAction->id,
            'editActionLibelle' => 'required|string|max:255',
            'editActionDescription' => 'nullable|string',
            'editActionOwnerId' => 'nullable|exists:users,id',
            'editActionDateEcheance' => 'nullable|date',
            'editActionDateRealisation' => 'nullable|date'
        ]);

        try {
            $this->editingAction->update([
                'code' => $this->editActionCode,
                'libelle' => $this->editActionLibelle,
                'description' => $this->editActionDescription,
                'owner_id' => $this->editActionOwnerId ?: null,
                'date_echeance' => $this->editActionDateEcheance ?: null,
                'date_realisation' => $this->editActionDateRealisation ?: null,
            ]);

            $this->resetEditActionForm();
            $this->showEditActionForm = false;
            $this->editingAction = null;
            $this->loadPilierData();
            
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Action modifiée avec succès !'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Erreur lors de la modification : ' . $e->getMessage()
            ]);
        }
    }

    // Méthodes pour les sous-actions
    public function showSousActionDetails($sousActionId)
    {
        $this->isLoading = true;
        $this->dispatch('startLoading');
        
        $this->selectedSousAction = $this->selectedAction->sousActions->find($sousActionId);
        
        if ($this->selectedSousAction) {
            $this->showSousActionDetails = true;
            $this->showCreateSousActionForm = false;
            $this->showEditSousActionForm = false;
            
            // Mettre à jour le breadcrumb
            $this->updateBreadcrumb('sous_action');
        }
    }

    public function retourListeSousActions()
    {
        $this->isLoading = true;
        $this->dispatch('startLoading');
        
        $this->showSousActionDetails = false;
        $this->selectedSousAction = null;
        
        // Mettre à jour le breadcrumb
        $this->updateBreadcrumb('action');
    }

    public function showCreateSousActionForm()
    {
        $this->showCreateSousActionForm = true;
        $this->showSousActionDetails = false;
        $this->showEditSousActionForm = false;
        $this->suggestNewSousActionCode();
    }

    public function cancelCreateSousAction()
    {
        $this->showCreateSousActionForm = false;
        $this->resetSousActionForm();
    }

    public function suggestNewSousActionCode()
    {
        $existingCodes = $this->selectedAction->sousActions->pluck('code')->toArray();
        $nextNumber = 1;
        
        while (in_array("SA{$nextNumber}", $existingCodes)) {
            $nextNumber++;
        }
        
        $this->newSousActionCode = "SA{$nextNumber}";
    }

    public function resetSousActionForm()
    {
        $this->newSousActionCode = '';
        $this->newSousActionLibelle = '';
        $this->newSousActionDescription = '';
        $this->newSousActionOwnerId = '';
        $this->newSousActionDateEcheance = '';
        $this->newSousActionDateRealisation = '';
        $this->newSousActionTauxAvancement = 0;
    }

    public function createSousAction()
    {
        $this->validate([
            'newSousActionCode' => 'required|string|max:10|unique:sous_actions,code',
            'newSousActionLibelle' => 'required|string|max:255',
            'newSousActionDescription' => 'nullable|string',
            'newSousActionOwnerId' => 'nullable|exists:users,id',
            'newSousActionDateEcheance' => 'nullable|date',
            'newSousActionDateRealisation' => 'nullable|date',
            'newSousActionTauxAvancement' => 'required|numeric|min:0|max:100'
        ]);

        try {
            $sousAction = SousAction::create([
                'code' => $this->newSousActionCode,
                'libelle' => $this->newSousActionLibelle,
                'description' => $this->newSousActionDescription,
                'action_id' => $this->selectedAction->id,
                'owner_id' => $this->newSousActionOwnerId ?: null,
                'date_echeance' => $this->newSousActionDateEcheance ?: null,
                'date_realisation' => $this->newSousActionDateRealisation ?: null,
                'taux_avancement' => $this->newSousActionTauxAvancement,
            ]);

            $this->resetSousActionForm();
            $this->showCreateSousActionForm = false;
            $this->loadPilierData();
            
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Sous-action créée avec succès !'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Erreur lors de la création : ' . $e->getMessage()
            ]);
        }
    }

    public function showEditSousActionForm($sousActionId)
    {
        $this->editingSousAction = $this->selectedAction->sousActions->find($sousActionId);
        
        if ($this->editingSousAction) {
            $this->editSousActionCode = $this->editingSousAction->code;
            $this->editSousActionLibelle = $this->editingSousAction->libelle;
            $this->editSousActionDescription = $this->editingSousAction->description ?? '';
            $this->editSousActionOwnerId = $this->editingSousAction->owner_id ?? '';
            $this->editSousActionDateEcheance = $this->editingSousAction->date_echeance ? $this->editingSousAction->date_echeance->format('Y-m-d') : '';
            $this->editSousActionDateRealisation = $this->editingSousAction->date_realisation ? $this->editingSousAction->date_realisation->format('Y-m-d') : '';
            $this->editSousActionTauxAvancement = $this->editingSousAction->taux_avancement;
            
            $this->showEditSousActionForm = true;
            $this->showCreateSousActionForm = false;
            $this->showSousActionDetails = false;
        }
    }

    public function cancelEditSousAction()
    {
        $this->showEditSousActionForm = false;
        $this->editingSousAction = null;
        $this->resetEditSousActionForm();
    }

    public function resetEditSousActionForm()
    {
        $this->editSousActionCode = '';
        $this->editSousActionLibelle = '';
        $this->editSousActionDescription = '';
        $this->editSousActionOwnerId = '';
        $this->editSousActionDateEcheance = '';
        $this->editSousActionDateRealisation = '';
        $this->editSousActionTauxAvancement = 0;
    }

    public function updateSousAction()
    {
        $this->validate([
            'editSousActionCode' => 'required|string|max:10|unique:sous_actions,code,' . $this->editingSousAction->id,
            'editSousActionLibelle' => 'required|string|max:255',
            'editSousActionDescription' => 'nullable|string',
            'editSousActionOwnerId' => 'nullable|exists:users,id',
            'editSousActionDateEcheance' => 'nullable|date',
            'editSousActionDateRealisation' => 'nullable|date',
            'editSousActionTauxAvancement' => 'required|numeric|min:0|max:100'
        ]);

        try {
            $this->editingSousAction->update([
                'code' => $this->editSousActionCode,
                'libelle' => $this->editSousActionLibelle,
                'description' => $this->editSousActionDescription,
                'owner_id' => $this->editSousActionOwnerId ?: null,
                'date_echeance' => $this->editSousActionDateEcheance ?: null,
                'date_realisation' => $this->editSousActionDateRealisation ?: null,
                'taux_avancement' => $this->editSousActionTauxAvancement,
            ]);

            $this->resetEditSousActionForm();
            $this->showEditSousActionForm = false;
            $this->editingSousAction = null;
            $this->loadPilierData();
            
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Sous-action modifiée avec succès !'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Erreur lors de la modification : ' . $e->getMessage()
            ]);
        }
    }

    public function stopLoading()
    {
        $this->isLoading = false;
    }

    // Méthodes pour l'édition d'objectif stratégique
    public function showEditObjectifStrategiqueForm($objectifStrategiqueId)
    {
        $this->dispatch('showToast', ['type' => 'info', 'message' => 'Méthode appelée avec ID: ' . $objectifStrategiqueId]);
        
        $this->editingObjectifStrategique = $this->objectifsStrategiques->find($objectifStrategiqueId);
        
        if ($this->editingObjectifStrategique) {
            $this->editObjectifStrategiqueCode = $this->editingObjectifStrategique->code;
            $this->editObjectifStrategiqueLibelle = $this->editingObjectifStrategique->libelle;
            $this->editObjectifStrategiqueDescription = $this->editingObjectifStrategique->description ?? '';
            $this->editObjectifStrategiqueOwnerId = $this->editingObjectifStrategique->owner_id ?? '';
            $this->showEditObjectifStrategiqueForm = true;
            
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Modal d\'édition ouvert pour: ' . $this->editingObjectifStrategique->libelle]);
        } else {
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Objectif stratégique non trouvé']);
        }
    }

    public function cancelEditObjectifStrategique()
    {
        $this->showEditObjectifStrategiqueForm = false;
        $this->editingObjectifStrategique = null;
        $this->resetEditObjectifStrategiqueForm();
    }

    public function resetEditObjectifStrategiqueForm()
    {
        $this->editObjectifStrategiqueCode = '';
        $this->editObjectifStrategiqueLibelle = '';
        $this->editObjectifStrategiqueDescription = '';
        $this->editObjectifStrategiqueOwnerId = '';
    }

    public function updateObjectifStrategique()
    {
        $this->validate([
            'editObjectifStrategiqueCode' => 'required|string|max:255',
            'editObjectifStrategiqueLibelle' => 'required|string|max:255',
            'editObjectifStrategiqueDescription' => 'nullable|string',
            'editObjectifStrategiqueOwnerId' => 'nullable|exists:users,id',
        ]);

        if ($this->editingObjectifStrategique) {
            $this->editingObjectifStrategique->update([
                'code' => $this->editObjectifStrategiqueCode,
                'libelle' => $this->editObjectifStrategiqueLibelle,
                'description' => $this->editObjectifStrategiqueDescription,
                'owner_id' => $this->editObjectifStrategiqueOwnerId ?: null,
            ]);

            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Objectif stratégique mis à jour avec succès !']);
            $this->cancelEditObjectifStrategique();
            $this->loadPilierData();
        }
    }

    public function updateSousActionTaux($sousActionId, $newTaux, $actionId, $objectifSpecifiqueId, $objectifStrategiqueId, $pilierId)
    {
        try {
            Log::info('Mise à jour du taux sous-action', [
                'sousActionId' => $sousActionId,
                'newTaux' => $newTaux,
                'actionId' => $actionId
            ]);
            
            // Mettre à jour la sous-action
            $sousAction = SousAction::find($sousActionId);
            if (!$sousAction) {
                throw new \Exception('Sous-action non trouvée');
            }

            $sousAction->update(['taux_avancement' => $newTaux]);

            // Recharger les données pour mettre à jour les taux parents
            $this->loadPilierData();

            // Mettre à jour les éléments d'interface en temps réel
            $this->dispatch('updateTauxDisplay', [
                'sousActionId' => $sousActionId,
                'newTaux' => $newTaux,
                'actionId' => $actionId,
                'objectifSpecifiqueId' => $objectifSpecifiqueId,
                'objectifStrategiqueId' => $objectifStrategiqueId,
                'pilierId' => $pilierId
            ]);

            // Rafraîchir la page principale pour synchroniser les taux
            $this->dispatch('refreshPilierList');

            Log::info('Taux sous-action mis à jour avec succès', [
                'sousActionId' => $sousActionId,
                'newTaux' => $newTaux,
                'actionId' => $actionId
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du taux', [
                'error' => $e->getMessage(),
                'sousActionId' => $sousActionId,
                'newTaux' => $newTaux
            ]);
            
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Erreur lors de la mise à jour du taux: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.pilier-details-modal-new');
    }
}
