<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Action;
use App\Models\ObjectifSpecifique;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->canCreateAction()) {
                abort(403, 'Accès non autorisé');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdminGeneral()) {
            $actions = Action::with(['owner', 'objectifSpecifique.objectifStrategique.pilier'])->get();
        } elseif ($user->isOwnerOS()) {
            $actions = Action::with(['owner', 'objectifSpecifique.objectifStrategique.pilier'])
                ->whereHas('objectifSpecifique.objectifStrategique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerPIL()) {
            $actions = Action::with(['owner', 'objectifSpecifique.objectifStrategique.pilier'])
                ->whereHas('objectifSpecifique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerAction()) {
            $actions = Action::where('owner_id', $user->id)->with(['owner', 'objectifSpecifique.objectifStrategique.pilier'])->get();
        } else {
            $actions = collect();
        }

        return view('actions.index', compact('actions'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->canCreateAction()) {
            abort(403, 'Accès non autorisé');
        }

        // Récupérer l'objectif spécifique pré-sélectionné si fourni
        $selectedObjectifSpecifique = null;
        if ($request->has('objectif_specifique_id')) {
            $selectedObjectifSpecifique = ObjectifSpecifique::find($request->objectif_specifique_id);
        }

        // Déterminer les objectifs spécifiques disponibles selon le rôle
        if ($user->isAdminGeneral()) {
            $objectifsSpecifiques = ObjectifSpecifique::with(['objectifStrategique.pilier'])->get();
        } elseif ($user->isOwnerOS()) {
            $objectifsSpecifiques = ObjectifSpecifique::with(['objectifStrategique.pilier'])
                ->whereHas('objectifStrategique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerPIL()) {
            $objectifsSpecifiques = ObjectifSpecifique::where('owner_id', $user->id)->with(['objectifStrategique.pilier'])->get();
        } else {
            $objectifsSpecifiques = collect();
        }

        $users = User::whereHas('role', function($query) {
            $query->whereIn('nom', ['owner_pil', 'owner_action']);
        })->get();

        return view('actions.create', compact('objectifsSpecifiques', 'users', 'selectedObjectifSpecifique'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->canCreateAction()) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'code' => 'required|string|max:10|unique:actions',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectif_specifique_id' => 'required|exists:objectif_specifiques,id',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $action = Action::create([
            'code' => $request->code,
            'libelle' => $request->libelle,
            'description' => $request->description,
            'objectif_specifique_id' => $request->objectif_specifique_id,
            'owner_id' => $request->owner_id,
            'taux_avancement' => 0,
            'actif' => true,
        ]);

        return redirect()->route('actions.index')
            ->with('success', 'Action créée avec succès !');
    }

    public function show(Action $action)
    {
        $user = Auth::user();
        
        // Vérifier les permissions
        if (!$user->isAdminGeneral() && !$user->isOwnerOS() && !$user->isOwnerPIL() && !$user->isOwnerAction()) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->isOwnerAction() && $action->owner_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        $action->load(['owner', 'objectifSpecifique.objectifStrategique.pilier', 'sousActions.owner']);

        return view('actions.show', compact('action'));
    }

    public function edit(Action $action)
    {
        $user = Auth::user();
        
        if (!$user->canCreateAction()) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->isOwnerAction() && $action->owner_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        // Déterminer les objectifs spécifiques disponibles selon le rôle
        if ($user->isAdminGeneral()) {
            $objectifsSpecifiques = ObjectifSpecifique::actif()->with(['objectifStrategique.pilier'])->get();
        } elseif ($user->isOwnerOS()) {
            $objectifsSpecifiques = ObjectifSpecifique::actif()->with(['objectifStrategique.pilier'])
                ->whereHas('objectifStrategique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerPIL()) {
            $objectifsSpecifiques = ObjectifSpecifique::actif()->byOwner($user->id)->with(['objectifStrategique.pilier'])->get();
        } else {
            $objectifsSpecifiques = collect();
        }

        $users = User::whereHas('role', function($query) {
            $query->whereIn('nom', ['owner_pil', 'owner_action']);
        })->get();

        return view('actions.edit', compact('action', 'objectifsSpecifiques', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Action $action)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'owner_id' => 'required|exists:users,id',
            'date_echeance' => 'nullable|date',
            'statut' => 'nullable|string|in:En cours,Terminé,En attente,Annulé',
        ]);

        $user = Auth::user();
        $validationService = app(\App\Services\ValidationService::class);

        // Vérifier si des changements critiques nécessitent une validation
        $requiresValidation = false;
        $validationData = [];

        // Changement de propriétaire
        if ($action->owner_id != $request->owner_id) {
            $requiresValidation = true;
            $validationData = [
                'action' => 'change_owner',
                'old_owner_id' => $action->owner_id,
                'new_owner_id' => $request->owner_id,
                'reason' => 'Changement de propriétaire de l\'action'
            ];
        }

        // Changement d'échéance
        if ($action->date_echeance != $request->date_echeance) {
            $requiresValidation = true;
            $validationData = [
                'action' => 'change_deadline',
                'old_deadline' => $action->date_echeance ?? 'Non définie',
                'new_deadline' => $request->date_echeance,
                'reason' => 'Modification de l\'échéance de l\'action'
            ];
        }

        // Changement de statut critique
        if ($action->statut != $request->statut && in_array($request->statut, ['Terminé', 'Annulé'])) {
            $requiresValidation = true;
            $validationData = [
                'action' => 'change_status',
                'old_status' => $action->statut ?? 'Non défini',
                'new_status' => $request->statut,
                'reason' => 'Changement de statut critique de l\'action'
            ];
        }

        // Si validation requise
        if ($requiresValidation) {
            try {
                $validation = $validationService->createValidationRequest(
                    'action',
                    $action->id,
                    $user,
                    $validationData
                );

                return redirect()->back()->with('success', 
                    'Demande de validation créée. Vous recevrez une notification une fois approuvée.'
                );
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        // Mise à jour directe si pas de validation requise
        $action->update($request->all());

        return redirect()->route('actions.index')->with('success', 'Action mise à jour avec succès.');
    }

    public function destroy(Action $action)
    {
        $user = Auth::user();
        
        if (!$user->canCreateAction()) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->isOwnerAction() && $action->owner_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier s'il y a des sous-actions liées
        if ($action->sousActions()->count() > 0) {
            return redirect()->route('actions.index')
                ->with('error', 'Impossible de supprimer cette action car elle contient des sous-actions.');
        }

        $action->update(['actif' => false]);

        return redirect()->route('actions.index')
            ->with('success', 'Action supprimée avec succès !');
    }
}
