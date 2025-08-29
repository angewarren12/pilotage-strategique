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

class VueGeneraleModal extends Component
{
    public $showModal = false;
    public $piliers;
    public $isLoading = true;
    public $searchTerm = '';
    public $selectedPilier = null;
    public $selectedObjectifStrategique = null;
    public $selectedObjectifSpecifique = null;
    public $selectedAction = null;

    protected $listeners = [
        'openVueGeneraleModal' => 'openModal',
        'refreshVueGenerale' => 'loadData'
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function openModal()
    {
        Log::info('🔄 [VUE-GENERALE] Méthode openModal appelée');
        $this->showModal = true;
        $this->loadData();
        Log::info('✅ [VUE-GENERALE] Modal ouvert, showModal = ' . ($this->showModal ? 'true' : 'false'));
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFilters();
    }

    public function loadData()
    {
        try {
            $this->isLoading = true;
            
            $query = Pilier::with([
                'objectifsStrategiques.objectifsSpecifiques.actions.sousActions',
                'objectifsStrategiques.objectifsSpecifiques.actions.owner',
                'objectifsStrategiques.objectifsSpecifiques.owner',
                'objectifsStrategiques.owner',
                'owner'
            ]);

            if ($this->searchTerm) {
                $query->where(function($q) {
                    $q->where('libelle', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('code', 'like', '%' . $this->searchTerm . '%')
                      ->orWhereHas('objectifsStrategiques', function($os) {
                          $os->where('libelle', 'like', '%' . $this->searchTerm . '%')
                             ->orWhere('code', 'like', '%' . $this->searchTerm . '%');
                      });
                });
            }

            $this->piliers = $query->get();
            
            Log::info('Vue Générale - Données chargées', [
                'count' => $this->piliers->count(),
                'piliers_with_os' => $this->piliers->filter(function($p) {
                    return $p->objectifsStrategiques->count() > 0;
                })->count(),
                'piliers_without_os' => $this->piliers->filter(function($p) {
                    return $p->objectifsStrategiques->count() === 0;
                })->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de la Vue Générale', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', (object)['type' => 'error', 'message' => 'Erreur lors du chargement des données']);
        } finally {
            $this->isLoading = false;
        }
    }

    public function resetFilters()
    {
        $this->searchTerm = '';
        $this->selectedPilier = null;
        $this->selectedObjectifStrategique = null;
        $this->selectedObjectifSpecifique = null;
        $this->selectedAction = null;
    }

    public function filterByPilier($pilierId)
    {
        $this->selectedPilier = $pilierId;
        $this->selectedObjectifStrategique = null;
        $this->selectedObjectifSpecifique = null;
        $this->selectedAction = null;
    }

    public function filterByObjectifStrategique($objectifStrategiqueId)
    {
        $this->selectedObjectifStrategique = $objectifStrategiqueId;
        $this->selectedObjectifSpecifique = null;
        $this->selectedAction = null;
    }

    public function filterByObjectifSpecifique($objectifSpecifiqueId)
    {
        $this->selectedObjectifSpecifique = $objectifSpecifiqueId;
        $this->selectedAction = null;
    }

    public function filterByAction($actionId)
    {
        $this->selectedAction = $actionId;
    }

    public function clearFilters()
    {
        $this->resetFilters();
        $this->loadData();
    }

    public function calculateEcart($dateEcheance, $dateRealisation)
    {
        if (!$dateEcheance) return null;
        
        // Si pas de date de réalisation, ne pas calculer d'écart
        if (!$dateRealisation) {
            $echeance = Carbon::parse($dateEcheance);
            $today = Carbon::now();
            $daysUntilDeadline = $today->diffInDays($echeance, false);
            $daysUntilDeadline = (int)$daysUntilDeadline; // 🔒 force entier
            
            if ($daysUntilDeadline < 0) {
                return abs($daysUntilDeadline) . 'J de retard';
            } elseif ($daysUntilDeadline > 0) {
                return 'Reste ' . $daysUntilDeadline . 'J';
            } else {
                return 'Échéance aujourd\'hui';
            }
        }
        
        // Si date de réalisation existe, calculer l'écart réel
        $echeance = Carbon::parse($dateEcheance);
        $realisation = Carbon::parse($dateRealisation);
        
        $diff = $echeance->diffInDays($realisation, false);
        $diff = (int)$diff; // 🔒 force entier
        
        if ($diff < 0) {
            return abs($diff) . 'J en avance';
        } elseif ($diff > 0) {
            return $diff . 'J de retard';
        } else {
            return 'À jour';
        }
    }
    



    public function getProgressStatus($taux)
    {
        if ($taux >= 100) return 'success';
        if ($taux >= 75) return 'info';
        if ($taux >= 50) return 'warning';
        return 'danger';
    }

    public function render()
    {
        return view('livewire.vue-generale-modal');
    }
} 