<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pilier;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PilierDetailsModal extends Component
{
    public $pilier = null;
    public $objectifsStrategiques = [];
    public $showModal = false;
    public $selectedObjectifStrategique = null;
    public $showObjectifDetails = false;
    
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

    // Propriétés pour les détails d'objectif spécifique
    public $selectedObjectifSpecifiqueDetails = null;
    public $showObjectifSpecifiqueDetails = false;

    protected $listeners = [
        'openPilierModal' => 'openModal',
        'closeModal' => 'closeModal',
        'refreshData' => 'loadPilierData'
    ];

    public function openModal($pilierId)
    {
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
        
        // Suggérer un code pour le nouvel objectif
        $this->suggestNewObjectifCode();
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
        $this->selectedObjectifStrategique = $this->objectifsStrategiques->find($objectifId);
        $this->showObjectifDetails = true;
        $this->showCreateForm = false;
    }

    public function retourListeObjectifs()
    {
        $this->showObjectifDetails = false;
        $this->selectedObjectifStrategique = null;
        $this->showCreateForm = false;
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

    public function showCreateObjectifSpecifiqueForm()
    {
        $this->showCreateObjectifSpecifiqueForm = true;
        $this->showEditObjectifSpecifiqueForm = false;
        $this->showObjectifSpecifiqueDetails = false;
        $this->suggestNewObjectifSpecifiqueCode();
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

    public function showObjectifSpecifiqueDetails($objectifSpecifiqueId)
    {
        $this->selectedObjectifSpecifiqueDetails = $this->selectedObjectifStrategique->objectifsSpecifiques->find($objectifSpecifiqueId);
        
        if ($this->selectedObjectifSpecifiqueDetails) {
            $this->showObjectifSpecifiqueDetails = true;
            $this->showCreateObjectifSpecifiqueForm = false;
            $this->showEditObjectifSpecifiqueForm = false;
        }
    }

    public function retourListeObjectifsSpecifiques()
    {
        $this->showObjectifSpecifiqueDetails = false;
        $this->selectedObjectifSpecifiqueDetails = null;
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

    public function getUsersProperty()
    {
        return User::whereHas('role', function($query) {
            $query->whereIn('nom', ['admin_general', 'owner_os']);
        })->get();
    }

    public function testMethod()
    {
        $this->dispatch('showToast', [
            'type' => 'info',
            'message' => 'Test method works!'
        ]);
    }

    public function render()
    {
        return view('livewire.pilier-details-modal');
    }
}
