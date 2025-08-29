<?php

namespace App\Services;

use App\Models\Pilier;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\Action;
use App\Models\SousAction;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdvancedAnalyticsService
{
    /**
     * Calcule les KPI avancés pour le reporting
     */
    public function getAdvancedKPIs()
    {
        return [
            'global_health_score' => $this->calculateGlobalHealthScore(),
            'velocity_trend' => $this->calculateVelocityTrend(),
            'completion_prediction' => $this->predictCompletionDate(),
            'critical_alerts' => $this->getCriticalAlerts(),
            'performance_by_pilier' => $this->getPerformanceByPilier(),
            'bottlenecks' => $this->identifyBottlenecks(),
            'team_performance' => $this->getTeamPerformance(),
            'milestone_tracking' => $this->getMilestoneTracking()
        ];
    }

    /**
     * Score de santé global (0-100)
     */
    public function calculateGlobalHealthScore()
    {
        $totalSousActions = SousAction::count();
        if ($totalSousActions === 0) return 0;

        // Facteurs de calcul
        $progressionMoyenne = SousAction::avg('taux_avancement') ?? 0;
        $respectEcheances = $this->calculateDeadlineRespectRate();
        $activiteRecente = $this->calculateRecentActivityRate();
        $qualiteCommentaires = $this->calculateCommentQuality();

        // Pondération
        $score = (
            $progressionMoyenne * 0.4 +           // 40% progression
            $respectEcheances * 0.3 +             // 30% respect échéances
            $activiteRecente * 0.2 +              // 20% activité récente
            $qualiteCommentaires * 0.1            // 10% qualité suivi
        );

        return round($score, 1);
    }

    /**
     * Tendance de vélocité (progression par semaine)
     */
    public function calculateVelocityTrend()
    {
        $weeks = [];
        for ($i = 4; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i + 1)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();
            
            // Simulation - dans un vrai système, on aurait un historique
            $avgProgress = SousAction::whereBetween('updated_at', [$startDate, $endDate])
                ->avg('taux_avancement') ?? 0;
                
            $weeks[] = [
                'week' => $startDate->format('W/Y'),
                'date' => $startDate->format('d/m'),
                'progress' => round($avgProgress, 1),
                'velocity' => $i > 0 ? round($avgProgress - ($weeks[$i-1]['progress'] ?? 0), 1) : 0
            ];
        }

        return $weeks;
    }

    /**
     * Prédiction de date d'achèvement
     */
    public function predictCompletionDate()
    {
        $sousActionsEnCours = SousAction::where('taux_avancement', '<', 100)->get();
        $predictions = [];

        foreach ($sousActionsEnCours as $sousAction) {
            $progressionRestante = 100 - $sousAction->taux_avancement;
            $velociteMoyenne = $this->calculateVelocityForSousAction($sousAction->id);
            
            if ($velociteMoyenne > 0) {
                $joursRestants = $progressionRestante / $velociteMoyenne;
                $dateFinPrevue = Carbon::now()->addDays($joursRestants);
                
                $predictions[] = [
                    'sous_action_id' => $sousAction->id,
                    'code' => $this->getSousActionFullCode($sousAction),
                    'libelle' => $sousAction->libelle,
                    'progression_actuelle' => $sousAction->taux_avancement,
                    'date_echeance' => $sousAction->date_echeance,
                    'date_fin_prevue' => $dateFinPrevue,
                    'jours_restants' => round($joursRestants),
                    'risque' => $this->calculateRiskLevel($sousAction, $dateFinPrevue)
                ];
            }
        }

        return collect($predictions)->sortBy('risque')->reverse()->take(10)->values();
    }

    /**
     * Alertes critiques
     */
    public function getCriticalAlerts()
    {
        $alerts = [];

        // Sous-actions en retard
        $sousActionsRetard = SousAction::where('date_echeance', '<', Carbon::now())
            ->where('taux_avancement', '<', 100)
            ->get();

        foreach ($sousActionsRetard as $sousAction) {
            $alerts[] = [
                'type' => 'retard',
                'severity' => 'critical',
                'title' => 'Sous-action en retard',
                'message' => $this->getSousActionFullCode($sousAction) . ' - ' . $sousAction->libelle,
                'days_overdue' => Carbon::now()->diffInDays($sousAction->date_echeance),
                'progress' => $sousAction->taux_avancement
            ];
        }

        // Échéances approchantes (7 jours)
        $echeancesProches = SousAction::whereBetween('date_echeance', [
            Carbon::now(),
            Carbon::now()->addDays(7)
        ])->where('taux_avancement', '<', 90)->get();

        foreach ($echeancesProches as $sousAction) {
            $alerts[] = [
                'type' => 'echeance_proche',
                'severity' => 'warning',
                'title' => 'Échéance approchante',
                'message' => $this->getSousActionFullCode($sousAction) . ' - ' . $sousAction->libelle,
                'days_remaining' => Carbon::now()->diffInDays($sousAction->date_echeance, false),
                'progress' => $sousAction->taux_avancement
            ];
        }

        // Sous-actions sans progression (30 jours)
        $sousActionsSansProgression = SousAction::where('updated_at', '<', Carbon::now()->subDays(30))
            ->where('taux_avancement', '<', 100)
            ->get();

        foreach ($sousActionsSansProgression as $sousAction) {
            $alerts[] = [
                'type' => 'sans_progression',
                'severity' => 'info',
                'title' => 'Aucune progression récente',
                'message' => $this->getSousActionFullCode($sousAction) . ' - ' . $sousAction->libelle,
                'days_inactive' => Carbon::now()->diffInDays($sousAction->updated_at),
                'progress' => $sousAction->taux_avancement
            ];
        }

        return collect($alerts)->sortBy([
            ['severity', 'desc'],
            ['days_overdue', 'desc']
        ])->take(15)->values();
    }

    /**
     * Performance par pilier
     */
    public function getPerformanceByPilier()
    {
        return Pilier::with(['objectifsStrategiques.objectifsSpecifiques.actions.sousActions'])
            ->get()
            ->map(function ($pilier) {
                $sousActions = $pilier->objectifsStrategiques
                    ->flatMap->objectifsSpecifiques
                    ->flatMap->actions
                    ->flatMap->sousActions;

                return [
                    'pilier_id' => $pilier->id,
                    'code' => $pilier->code,
                    'libelle' => $pilier->libelle,
                    'color' => $pilier->couleur,
                    'progression_moyenne' => round($sousActions->avg('taux_avancement') ?? 0, 1),
                    'total_sous_actions' => $sousActions->count(),
                    'terminees' => $sousActions->where('taux_avancement', 100)->count(),
                    'en_retard' => $sousActions->where('date_echeance', '<', Carbon::now())
                        ->where('taux_avancement', '<', 100)->count(),
                    'score_sante' => $this->calculatePilierHealthScore($pilier),
                    'tendance' => $this->calculatePilierTrend($pilier)
                ];
            });
    }

    /**
     * Identification des goulots d'étranglement
     */
    public function identifyBottlenecks()
    {
        $bottlenecks = [];

        // Actions avec le plus de sous-actions en retard
        $actions = Action::with(['sousActions' => function($query) {
            $query->where('date_echeance', '<', Carbon::now())
                  ->where('taux_avancement', '<', 100);
        }])->get();

        foreach ($actions as $action) {
            if ($action->sousActions->count() >= 2) {
                $bottlenecks[] = [
                    'type' => 'action',
                    'entity_id' => $action->id,
                    'code' => $this->getActionFullCode($action),
                    'libelle' => $action->libelle,
                    'problemes' => $action->sousActions->count(),
                    'impact_score' => $action->sousActions->count() * 10
                ];
            }
        }

        // Responsables avec le plus de retards
        $responsables = DB::table('sous_actions')
            ->join('users', 'sous_actions.owner_id', '=', 'users.id')
            ->where('sous_actions.date_echeance', '<', Carbon::now())
            ->where('sous_actions.taux_avancement', '<', 100)
            ->groupBy('users.id', 'users.name')
            ->selectRaw('users.id, users.name, COUNT(*) as retards')
            ->having('retards', '>=', 2)
            ->get();

        foreach ($responsables as $responsable) {
            $bottlenecks[] = [
                'type' => 'responsable',
                'entity_id' => $responsable->id,
                'nom' => $responsable->name,
                'problemes' => $responsable->retards,
                'impact_score' => $responsable->retards * 5
            ];
        }

        return collect($bottlenecks)->sortByDesc('impact_score')->take(10)->values();
    }

    /**
     * Performance des équipes
     */
    public function getTeamPerformance()
    {
        return DB::table('sous_actions')
            ->join('users', 'sous_actions.owner_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name')
            ->selectRaw('
                users.id,
                users.name,
                COUNT(*) as total_sous_actions,
                AVG(sous_actions.taux_avancement) as progression_moyenne,
                SUM(CASE WHEN sous_actions.taux_avancement = 100 THEN 1 ELSE 0 END) as terminees,
                SUM(CASE WHEN sous_actions.date_echeance < NOW() AND sous_actions.taux_avancement < 100 THEN 1 ELSE 0 END) as en_retard
            ')
            ->get()
            ->map(function ($user) {
                return [
                    'user_id' => $user->id,
                    'nom' => $user->name,
                    'total_sous_actions' => $user->total_sous_actions,
                    'progression_moyenne' => round($user->progression_moyenne, 1),
                    'taux_completion' => round(($user->terminees / $user->total_sous_actions) * 100, 1),
                    'taux_retard' => round(($user->en_retard / $user->total_sous_actions) * 100, 1),
                    'score_performance' => $this->calculateUserPerformanceScore($user)
                ];
            })
            ->sortByDesc('score_performance')
            ->values();
    }

    /**
     * Suivi des jalons
     */
    public function getMilestoneTracking()
    {
        // Jalons = sous-actions avec échéances importantes
        $milestones = SousAction::whereNotNull('date_echeance')
            ->where('taux_avancement', '<', 100)
            ->orderBy('date_echeance')
            ->take(20)
            ->get()
            ->map(function ($sousAction) {
                return [
                    'id' => $sousAction->id,
                    'code' => $this->getSousActionFullCode($sousAction),
                    'libelle' => $sousAction->libelle,
                    'date_echeance' => $sousAction->date_echeance,
                    'progression' => $sousAction->taux_avancement,
                    'jours_restants' => Carbon::now()->diffInDays($sousAction->date_echeance, false),
                    'statut' => $this->getMilestoneStatus($sousAction),
                    'priorite' => $this->calculateMilestonePriority($sousAction)
                ];
            });

        return $milestones->sortByDesc('priorite')->values();
    }

    // ===== MÉTHODES UTILITAIRES =====

    private function calculateDeadlineRespectRate()
    {
        $totalAvecEcheance = SousAction::whereNotNull('date_echeance')->count();
        if ($totalAvecEcheance === 0) return 100;

        $respectees = SousAction::whereNotNull('date_echeance')
            ->where(function($query) {
                $query->where('taux_avancement', 100)
                      ->orWhere('date_echeance', '>=', Carbon::now());
            })->count();

        return round(($respectees / $totalAvecEcheance) * 100, 1);
    }

    private function calculateRecentActivityRate()
    {
        $total = SousAction::count();
        if ($total === 0) return 0;

        $activesRecemment = SousAction::where('updated_at', '>=', Carbon::now()->subDays(7))->count();
        return round(($activesRecemment / $total) * 100, 1);
    }

    private function calculateCommentQuality()
    {
        // Simulation - dans un vrai système, analyser les commentaires
        return 75; // Score fixe pour l'instant
    }

    private function calculateVelocityForSousAction($sousActionId)
    {
        // Simulation - calculer la vélocité basée sur l'historique
        return 2.5; // Progression moyenne par jour
    }

    private function getSousActionFullCode($sousAction)
    {
        $action = $sousAction->action;
        if (!$action) return $sousAction->code;

        $osp = $action->objectifSpecifique;
        if (!$osp) return $action->code . '.' . $sousAction->code;

        $os = $osp->objectifStrategique;
        if (!$os) return $osp->code . '.' . $action->code . '.' . $sousAction->code;

        $pilier = $os->pilier;
        if (!$pilier) return $os->code . '.' . $osp->code . '.' . $action->code . '.' . $sousAction->code;

        return $pilier->code . '.' . $os->code . '.' . $osp->code . '.' . $action->code . '.' . $sousAction->code;
    }

    private function getActionFullCode($action)
    {
        $osp = $action->objectifSpecifique;
        if (!$osp) return $action->code;

        $os = $osp->objectifStrategique;
        if (!$os) return $osp->code . '.' . $action->code;

        $pilier = $os->pilier;
        if (!$pilier) return $os->code . '.' . $osp->code . '.' . $action->code;

        return $pilier->code . '.' . $os->code . '.' . $osp->code . '.' . $action->code;
    }

    private function calculateRiskLevel($sousAction, $dateFinPrevue)
    {
        if (!$sousAction->date_echeance) return 'low';
        
        $daysDiff = $dateFinPrevue->diffInDays($sousAction->date_echeance, false);
        
        if ($daysDiff < -7) return 'critical';
        if ($daysDiff < 0) return 'high';
        if ($daysDiff < 7) return 'medium';
        return 'low';
    }

    private function calculatePilierHealthScore($pilier)
    {
        // Simulation du calcul de santé du pilier
        return rand(60, 95);
    }

    private function calculatePilierTrend($pilier)
    {
        // Simulation de la tendance
        return ['up', 'down', 'stable'][rand(0, 2)];
    }

    private function calculateUserPerformanceScore($user)
    {
        $completionScore = ($user->terminees / $user->total_sous_actions) * 50;
        $progressScore = ($user->progression_moyenne / 100) * 30;
        $timelinessScore = (1 - ($user->en_retard / $user->total_sous_actions)) * 20;
        
        return round($completionScore + $progressScore + $timelinessScore, 1);
    }

    private function getMilestoneStatus($sousAction)
    {
        if ($sousAction->taux_avancement >= 100) return 'complete';
        if ($sousAction->date_echeance < Carbon::now()) return 'overdue';
        if ($sousAction->date_echeance <= Carbon::now()->addDays(7)) return 'urgent';
        return 'on_track';
    }

    private function calculateMilestonePriority($sousAction)
    {
        $score = 0;
        
        // Plus l'échéance est proche, plus la priorité est haute
        $daysToDeadline = Carbon::now()->diffInDays($sousAction->date_echeance, false);
        $score += max(0, 30 - $daysToDeadline);
        
        // Moins il y a de progression, plus la priorité est haute
        $score += (100 - $sousAction->taux_avancement) / 10;
        
        // Bonus si déjà en retard
        if ($sousAction->date_echeance < Carbon::now()) {
            $score += 50;
        }
        
        return round($score, 1);
    }
}

