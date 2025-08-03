<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilier;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\Action;
use App\Models\SousAction;
use App\Models\User;
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

        // Données selon le rôle de l'utilisateur
        if ($user->isAdminGeneral()) {
            // Admin général : voir tous les piliers
            $piliers = Pilier::with(['owner', 'objectifsStrategiques'])->get();
            $objectifsStrategiques = ObjectifStrategique::with(['owner', 'pilier'])->get();
            $objectifsSpecifiques = ObjectifSpecifique::with(['owner', 'objectifStrategique'])->get();
            $actions = Action::with(['owner', 'objectifSpecifique'])->get();
            $sousActions = SousAction::with(['owner', 'action'])->get();
        } elseif ($user->isOwnerOS()) {
            // Owner OS : voir ses objectifs stratégiques et niveaux inférieurs
            $piliers = collect(); // Pas d'accès aux piliers
            $objectifsStrategiques = ObjectifStrategique::byOwner($user->id)->with(['owner', 'pilier'])->get();
            $objectifsSpecifiques = ObjectifSpecifique::with(['owner', 'objectifStrategique'])
                ->whereHas('objectifStrategique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
            $actions = Action::with(['owner', 'objectifSpecifique'])
                ->whereHas('objectifSpecifique.objectifStrategique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
            $sousActions = SousAction::with(['owner', 'action'])
                ->whereHas('action.objectifSpecifique.objectifStrategique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerPIL()) {
            // Owner PIL : voir ses objectifs spécifiques et niveaux inférieurs
            $piliers = collect();
            $objectifsStrategiques = collect();
            $objectifsSpecifiques = ObjectifSpecifique::byOwner($user->id)->with(['owner', 'objectifStrategique'])->get();
            $actions = Action::with(['owner', 'objectifSpecifique'])
                ->whereHas('objectifSpecifique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
            $sousActions = SousAction::with(['owner', 'action'])
                ->whereHas('action.objectifSpecifique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerAction()) {
            // Owner Action : voir ses actions et sous-actions
            $piliers = collect();
            $objectifsStrategiques = collect();
            $objectifsSpecifiques = collect();
            $actions = Action::byOwner($user->id)->with(['owner', 'objectifSpecifique'])->get();
            $sousActions = SousAction::with(['owner', 'action'])
                ->whereHas('action', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerSA()) {
            // Owner SA : voir uniquement ses sous-actions
            $piliers = collect();
            $objectifsStrategiques = collect();
            $objectifsSpecifiques = collect();
            $actions = collect();
            $sousActions = SousAction::byOwner($user->id)->with(['owner', 'action'])->get();
        } else {
            // Utilisateur sans rôle : pas d'accès
            $piliers = collect();
            $objectifsStrategiques = collect();
            $objectifsSpecifiques = collect();
            $actions = collect();
            $sousActions = collect();
        }

        return view('dashboard.index', compact('stats', 'piliers', 'objectifsStrategiques', 'objectifsSpecifiques', 'actions', 'sousActions'));
    }

    public function reporting()
    {
        $user = Auth::user();
        
        // Filtres
        $filters = request()->only(['owner', 'statut', 'periode', 'objectif']);
        
        // Sous-actions avec filtres
        $sousActionsQuery = SousAction::with(['owner', 'action.objectifSpecifique.objectifStrategique.pilier']);
        
        if ($user->isAdminGeneral()) {
            // Admin peut voir toutes les sous-actions
        } elseif ($user->isOwnerOS()) {
            $sousActionsQuery->whereHas('action.objectifSpecifique.objectifStrategique', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            });
        } elseif ($user->isOwnerPIL()) {
            $sousActionsQuery->whereHas('action.objectifSpecifique', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            });
        } elseif ($user->isOwnerAction()) {
            $sousActionsQuery->whereHas('action', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            });
        } elseif ($user->isOwnerSA()) {
            $sousActionsQuery->where('owner_id', $user->id);
        }

        // Appliquer les filtres
        if (isset($filters['owner']) && $filters['owner']) {
            $sousActionsQuery->where('owner_id', $filters['owner']);
        }
        
        if (isset($filters['statut']) && $filters['statut']) {
            $sousActionsQuery->where('statut', $filters['statut']);
        }

        $sousActions = $sousActionsQuery->orderBy('date_echeance')->get();

        return view('reporting', compact('sousActions', 'filters'));
    }
}
