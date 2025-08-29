<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilier;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\Action;
use App\Models\SousAction;
use App\Models\User;
use App\Services\AdvancedAnalyticsService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Statistiques générales
        $stats = [
            'total_piliers' => Pilier::count(),
            'total_objectifs_strategiques' => ObjectifStrategique::count(),
            'total_objectifs_specifiques' => ObjectifSpecifique::count(),
            'total_actions' => Action::count(),
            'total_sous_actions' => SousAction::count(),
            'sous_actions_terminees' => 0,
            'sous_actions_en_retard' => 0,
            'sous_actions_en_cours' => 0,
        ];

        // Pour l'instant, tous les utilisateurs voient toutes les données
        // TODO: Implémenter la logique de rôles selon les besoins
        $piliers = Pilier::with(['owner', 'objectifsStrategiques'])->get();
        $objectifsStrategiques = ObjectifStrategique::with(['owner', 'pilier'])->get();
        $objectifsSpecifiques = ObjectifSpecifique::with(['owner', 'objectifStrategique'])->get();
        $actions = Action::with(['owner', 'objectifSpecifique'])->get();
        $sousActions = SousAction::with(['owner', 'action'])->get();

        return view('dashboard.index', compact('stats', 'piliers', 'objectifsStrategiques', 'objectifsSpecifiques', 'actions', 'sousActions'));
    }

    public function reporting(AdvancedAnalyticsService $analyticsService)
    {
        $user = Auth::user();
        
        // Filtres
        $filters = request()->only(['owner', 'statut', 'periode', 'objectif']);
        
        // Obtenir les analyses avancées
        $advancedKPIs = $analyticsService->getAdvancedKPIs();
        
        // Sous-actions avec filtres
        $sousActionsQuery = SousAction::with(['owner', 'action.objectifSpecifique.objectifStrategique.pilier']);
        
        // Pour l'instant, admin peut voir toutes les sous-actions
        // TODO: Implémenter la logique de rôles selon les besoins
        if (method_exists($user, 'isAdminGeneral') && $user->isAdminGeneral()) {
            // Admin peut voir toutes les sous-actions
        } else {
            // Pour l'instant, tous les utilisateurs voient toutes les sous-actions
            // À ajuster selon la logique métier
        }

        // Appliquer les filtres
        if (isset($filters['owner']) && $filters['owner']) {
            $sousActionsQuery->where('owner_id', $filters['owner']);
        }
        
        if (isset($filters['statut']) && $filters['statut']) {
            $sousActionsQuery->where('statut', $filters['statut']);
        }

        $sousActions = $sousActionsQuery->orderBy('date_echeance')->get();

        return view('reporting.advanced', compact('sousActions', 'filters', 'advancedKPIs'));
    }
}
