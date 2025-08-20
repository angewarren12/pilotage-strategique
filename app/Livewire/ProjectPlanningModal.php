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

class ProjectPlanningModal extends Component
{
    public $showModal = false;
    public $isLoading = false;
    public $searchTerm = '';
    public $selectedPilier = null;
    public $selectedOwner = null;
    public $selectedStatus = null;
    public $dateRange = 'all'; // all, this_month, this_quarter, this_year
    public $viewMode = 'gantt'; // gantt, timeline, hierarchy
    public $zoomLevel = 1; // 0.5, 1, 1.5, 2
    
    // Données du planning
    public $planningData = [];
    public $timelineData = [];
    public $ganttData = [];
    
    // Filtres
    public $piliers = [];
    public $owners = [];
    public $statuses = [
        'all' => 'Tous',
        'not_started' => 'Non commencé',
        'in_progress' => 'En cours',
        'completed' => 'Terminé',
        'overdue' => 'En retard'
    ];
    
    protected $listeners = [
        'openProjectPlanningModal' => 'openModal',
        'refreshPlanningData' => 'loadPlanningData'
    ];

    public function mount()
    {
        $this->loadFilters();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->loadPlanningData();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetState();
        $this->dispatch('modalClosed');
    }
    
    public function forceCloseModal()
    {
        $this->showModal = false;
        $this->resetState();
        $this->dispatch('modalClosed');
    }

    public function resetState()
    {
        $this->searchTerm = '';
        $this->selectedPilier = null;
        $this->selectedOwner = null;
        $this->selectedStatus = null;
        $this->dateRange = 'all';
        $this->viewMode = 'gantt';
        $this->zoomLevel = 1;
    }

    public function loadFilters()
    {
        $this->piliers = Pilier::orderBy('code')->get();
        $this->owners = User::orderBy('name')->get();
    }

    public function loadPlanningData()
    {
        $this->isLoading = true;
        
        try {
            // Charger les données selon les filtres
            $query = Pilier::with([
                'objectifsStrategiques.objectifsSpecifiques.actions.sousActions',
                'objectifsStrategiques.objectifsSpecifiques.actions.owner',
                'objectifsStrategiques.objectifsSpecifiques.owner',
                'objectifsStrategiques.owner',
                'owner'
            ]);

            // Appliquer les filtres
            if ($this->selectedPilier) {
                $query->where('id', $this->selectedPilier);
            }

            if ($this->searchTerm) {
                $query->where(function($q) {
                    $q->where('libelle', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('code', 'like', '%' . $this->searchTerm . '%');
                });
            }

            $piliers = $query->get();
            
            // Préparer les données pour les différentes vues
            $this->prepareHierarchyData($piliers);
            $this->prepareTimelineData($piliers);
            $this->prepareGanttData($piliers);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des données de planning', ['error' => $e->getMessage()]);
            $this->dispatch('showToast', (object)['type' => 'error', 'message' => 'Erreur lors du chargement des données']);
        }
        
        $this->isLoading = false;
    }

    private function prepareHierarchyData($piliers)
    {
        $this->planningData = [];
        
        foreach ($piliers as $pilier) {
            $pilierData = [
                'id' => $pilier->id,
                'code' => $pilier->code,
                'libelle' => $pilier->libelle,
                'taux_avancement' => $pilier->taux_avancement,
                'color' => $pilier->color,
                'objectifs_strategiques' => []
            ];
            
            foreach ($pilier->objectifsStrategiques as $os) {
                $osData = [
                    'id' => $os->id,
                    'code' => $os->code,
                    'libelle' => $os->libelle,
                    'taux_avancement' => $os->taux_avancement,
                    'objectifs_specifiques' => []
                ];
                
                foreach ($os->objectifsSpecifiques as $osp) {
                    $ospData = [
                        'id' => $osp->id,
                        'code' => $osp->code,
                        'libelle' => $osp->libelle,
                        'taux_avancement' => $osp->taux_avancement,
                        'actions' => []
                    ];
                    
                    foreach ($osp->actions as $action) {
                        $actionData = [
                            'id' => $action->id,
                            'code' => $action->code,
                            'libelle' => $action->libelle,
                            'taux_avancement' => $action->taux_avancement,
                            'date_echeance' => $action->date_echeance,
                            'date_realisation' => $action->date_realisation,
                            'sous_actions' => []
                        ];
                        
                        foreach ($action->sousActions as $sousAction) {
                            $actionData['sous_actions'][] = [
                                'id' => $sousAction->id,
                                'code' => $sousAction->code,
                                'libelle' => $sousAction->libelle,
                                'taux_avancement' => $sousAction->taux_avancement,
                                'date_echeance' => $sousAction->date_echeance,
                                'date_realisation' => $sousAction->date_realisation
                            ];
                        }
                        
                        $ospData['actions'][] = $actionData;
                    }
                    
                    $osData['objectifs_specifiques'][] = $ospData;
                }
                
                $pilierData['objectifs_strategiques'][] = $osData;
            }
            
            $this->planningData[] = $pilierData;
        }
    }

    private function prepareTimelineData($piliers)
    {
        $this->timelineData = [];
        
        foreach ($piliers as $pilier) {
            foreach ($pilier->objectifsStrategiques as $os) {
                foreach ($os->objectifsSpecifiques as $osp) {
                    foreach ($osp->actions as $action) {
                        if ($action->date_echeance) {
                            $this->timelineData[] = [
                                'id' => $action->id,
                                'title' => $action->libelle,
                                'start' => $action->date_echeance,
                                'end' => $action->date_realisation ?: $action->date_echeance,
                                'color' => $pilier->color,
                                'pilier' => $pilier->code,
                                'os' => $os->code,
                                'osp' => $osp->code,
                                'taux' => $action->taux_avancement,
                                'type' => 'action'
                            ];
                        }
                        
                        foreach ($action->sousActions as $sousAction) {
                            if ($sousAction->date_echeance) {
                                $this->timelineData[] = [
                                    'id' => $sousAction->id,
                                    'title' => $sousAction->libelle,
                                    'start' => $sousAction->date_echeance,
                                    'end' => $sousAction->date_realisation ?: $sousAction->date_echeance,
                                    'color' => $pilier->color,
                                    'pilier' => $pilier->code,
                                    'os' => $os->code,
                                    'osp' => $osp->code,
                                    'action' => $action->code,
                                    'taux' => $sousAction->taux_avancement,
                                    'type' => 'sous_action'
                                ];
                            }
                        }
                    }
                }
            }
        }
    }

    private function prepareGanttData($piliers)
    {
        $this->ganttData = [];
        
        foreach ($piliers as $pilier) {
            $pilierGantt = [
                'id' => 'pilier_' . $pilier->id,
                'text' => $pilier->code . ' - ' . $pilier->libelle,
                'start_date' => $this->getEarliestDate($pilier),
                'end_date' => $this->getLatestDate($pilier),
                'progress' => $pilier->taux_avancement / 100,
                'color' => $pilier->color,
                'type' => 'pilier',
                'children' => []
            ];
            
            foreach ($pilier->objectifsStrategiques as $os) {
                $osGantt = [
                    'id' => 'os_' . $os->id,
                    'text' => $os->code . ' - ' . $os->libelle,
                    'start_date' => $this->getEarliestDate($os),
                    'end_date' => $this->getLatestDate($os),
                    'progress' => $os->taux_avancement / 100,
                    'color' => $pilier->color,
                    'type' => 'objectif_strategique',
                    'children' => []
                ];
                
                foreach ($os->objectifsSpecifiques as $osp) {
                    $ospGantt = [
                        'id' => 'osp_' . $osp->id,
                        'text' => $osp->code . ' - ' . $osp->libelle,
                        'start_date' => $this->getEarliestDate($osp),
                        'end_date' => $this->getLatestDate($osp),
                        'progress' => $osp->taux_avancement / 100,
                        'color' => $pilier->color,
                        'type' => 'objectif_specifique',
                        'children' => []
                    ];
                    
                    foreach ($osp->actions as $action) {
                        $actionGantt = [
                            'id' => 'action_' . $action->id,
                            'text' => $action->code . ' - ' . $action->libelle,
                            'start_date' => $action->date_echeance ?: now()->format('Y-m-d'),
                            'end_date' => $action->date_realisation ?: $action->date_echeance ?: now()->format('Y-m-d'),
                            'progress' => $action->taux_avancement / 100,
                            'color' => $pilier->color,
                            'type' => 'action',
                            'children' => []
                        ];
                        
                        foreach ($action->sousActions as $sousAction) {
                            $actionGantt['children'][] = [
                                'id' => 'sous_action_' . $sousAction->id,
                                'text' => $sousAction->code . ' - ' . $sousAction->libelle,
                                'start_date' => $sousAction->date_echeance ?: now()->format('Y-m-d'),
                                'end_date' => $sousAction->date_realisation ?: $sousAction->date_echeance ?: now()->format('Y-m-d'),
                                'progress' => $sousAction->taux_avancement / 100,
                                'color' => $pilier->color,
                                'type' => 'sous_action'
                            ];
                        }
                        
                        $ospGantt['children'][] = $actionGantt;
                    }
                    
                    $osGantt['children'][] = $ospGantt;
                }
                
                $pilierGantt['children'][] = $osGantt;
            }
            
            $this->ganttData[] = $pilierGantt;
        }
    }

    private function getEarliestDate($item)
    {
        $dates = [];
        
        if (method_exists($item, 'objectifsStrategiques')) {
            foreach ($item->objectifsStrategiques as $os) {
                foreach ($os->objectifsSpecifiques as $osp) {
                    foreach ($osp->actions as $action) {
                        if ($action->date_echeance) {
                            $dates[] = $action->date_echeance;
                        }
                        foreach ($action->sousActions as $sousAction) {
                            if ($sousAction->date_echeance) {
                                $dates[] = $sousAction->date_echeance;
                            }
                        }
                    }
                }
            }
        }
        
        return !empty($dates) ? min($dates) : now()->format('Y-m-d');
    }

    private function getLatestDate($item)
    {
        $dates = [];
        
        if (method_exists($item, 'objectifsStrategiques')) {
            foreach ($item->objectifsStrategiques as $os) {
                foreach ($os->objectifsSpecifiques as $osp) {
                    foreach ($osp->actions as $action) {
                        if ($action->date_realisation) {
                            $dates[] = $action->date_realisation;
                        } elseif ($action->date_echeance) {
                            $dates[] = $action->date_echeance;
                        }
                        foreach ($action->sousActions as $sousAction) {
                            if ($sousAction->date_realisation) {
                                $dates[] = $sousAction->date_realisation;
                            } elseif ($sousAction->date_echeance) {
                                $dates[] = $sousAction->date_echeance;
                            }
                        }
                    }
                }
            }
        }
        
        return !empty($dates) ? max($dates) : now()->format('Y-m-d');
    }

    public function updatedSearchTerm()
    {
        $this->loadPlanningData();
    }

    public function updatedSelectedPilier()
    {
        $this->loadPlanningData();
    }

    public function updatedSelectedOwner()
    {
        $this->loadPlanningData();
    }

    public function updatedSelectedStatus()
    {
        $this->loadPlanningData();
    }

    public function updatedDateRange()
    {
        $this->loadPlanningData();
    }

    public function updatedViewMode()
    {
        // Recharger les données selon le mode de vue
        $this->loadPlanningData();
    }

    public function zoomIn()
    {
        if ($this->zoomLevel < 2) {
            $this->zoomLevel += 0.25;
        }
    }

    public function zoomOut()
    {
        if ($this->zoomLevel > 0.5) {
            $this->zoomLevel -= 0.25;
        }
    }

    public function resetZoom()
    {
        $this->zoomLevel = 1;
    }

    public function exportPDF()
    {
        // Logique d'export PDF
        $this->dispatch('showToast', (object)['type' => 'info', 'message' => 'Export PDF en cours...']);
    }

    public function exportExcel()
    {
        // Logique d'export Excel
        $this->dispatch('showToast', (object)['type' => 'info', 'message' => 'Export Excel en cours...']);
    }

    public function exportImage()
    {
        // Logique d'export image
        $this->dispatch('showToast', (object)['type' => 'info', 'message' => 'Export image en cours...']);
    }
    
    // Méthodes pour calculer les positions des barres Gantt
    public function calculateBarPosition($startDate)
    {
        if (!$startDate) return 0;
        
        $start = Carbon::parse($startDate);
        $earliestDate = $this->getEarliestProjectDate();
        
        if (!$earliestDate) return 0;
        
        $earliest = Carbon::parse($earliestDate);
        $totalDays = $this->getTotalProjectDays();
        
        if ($totalDays == 0) return 0;
        
        $daysFromStart = $start->diffInDays($earliest);
        return ($daysFromStart / $totalDays) * 100;
    }
    
    public function calculateBarWidth($startDate, $endDate)
    {
        if (!$startDate || !$endDate) return 20;
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $totalDays = $this->getTotalProjectDays();
        
        if ($totalDays == 0) return 20;
        
        $duration = $start->diffInDays($end) + 1;
        return ($duration / $totalDays) * 100;
    }
    
    private function getEarliestProjectDate()
    {
        $dates = [];
        
        foreach ($this->ganttData as $pilierData) {
            if ($pilierData['start_date']) {
                $dates[] = $pilierData['start_date'];
            }
            foreach ($pilierData['children'] as $osData) {
                if ($osData['start_date']) {
                    $dates[] = $osData['start_date'];
                }
                foreach ($osData['children'] as $ospData) {
                    if ($ospData['start_date']) {
                        $dates[] = $ospData['start_date'];
                    }
                    foreach ($ospData['children'] as $actionData) {
                        if ($actionData['start_date']) {
                            $dates[] = $actionData['start_date'];
                        }
                    }
                }
            }
        }
        
        return !empty($dates) ? min($dates) : null;
    }
    
    private function getTotalProjectDays()
    {
        $earliest = $this->getEarliestProjectDate();
        $latest = $this->getLatestProjectDate();
        
        if (!$earliest || !$latest) return 365; // Par défaut 1 an
        
        $earliestDate = Carbon::parse($earliest);
        $latestDate = Carbon::parse($latest);
        
        return $earliestDate->diffInDays($latestDate) + 1;
    }
    
    private function getLatestProjectDate()
    {
        $dates = [];
        
        foreach ($this->ganttData as $pilierData) {
            if ($pilierData['end_date']) {
                $dates[] = $pilierData['end_date'];
            }
            foreach ($pilierData['children'] as $osData) {
                if ($osData['end_date']) {
                    $dates[] = $osData['end_date'];
                }
                foreach ($osData['children'] as $ospData) {
                    if ($ospData['end_date']) {
                        $dates[] = $ospData['end_date'];
                    }
                    foreach ($ospData['children'] as $actionData) {
                        if ($actionData['end_date']) {
                            $dates[] = $actionData['end_date'];
                        }
                    }
                }
            }
        }
        
        return !empty($dates) ? max($dates) : null;
    }

    public function render()
    {
        return view('livewire.project-planning-modal');
    }
} 