<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SousAction;
use App\Models\Action;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SousActionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->canCreateSousAction()) {
                abort(403, 'Accès non autorisé');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdminGeneral()) {
            $sousActions = SousAction::actif()->with(['owner', 'action.objectifSpecifique.objectifStrategique.pilier'])->get();
        } elseif ($user->isOwnerOS()) {
            $sousActions = SousAction::actif()->with(['owner', 'action.objectifSpecifique.objectifStrategique.pilier'])
                ->whereHas('action.objectifSpecifique.objectifStrategique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerPIL()) {
            $sousActions = SousAction::actif()->with(['owner', 'action.objectifSpecifique.objectifStrategique.pilier'])
                ->whereHas('action.objectifSpecifique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerAction()) {
            $sousActions = SousAction::actif()->with(['owner', 'action.objectifSpecifique.objectifStrategique.pilier'])
                ->whereHas('action', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerSA()) {
            $sousActions = SousAction::actif()->byOwner($user->id)->with(['owner', 'action.objectifSpecifique.objectifStrategique.pilier'])->get();
        } else {
            $sousActions = collect();
        }

        return view('sous-actions.index', compact('sousActions'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Déterminer les actions disponibles selon le rôle
        if ($user->isAdminGeneral()) {
            $actions = Action::actif()->with(['objectifSpecifique.objectifStrategique.pilier'])->get();
        } elseif ($user->isOwnerOS()) {
            $actions = Action::actif()->with(['objectifSpecifique.objectifStrategique.pilier'])
                ->whereHas('objectifSpecifique.objectifStrategique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerPIL()) {
            $actions = Action::actif()->with(['objectifSpecifique.objectifStrategique.pilier'])
                ->whereHas('objectifSpecifique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerAction()) {
            $actions = Action::actif()->byOwner($user->id)->with(['objectifSpecifique.objectifStrategique.pilier'])->get();
        } else {
            $actions = collect();
        }

        $users = User::whereHas('role', function($query) {
            $query->whereIn('nom', ['owner_action', 'owner_sa']);
        })->get();

        return view('sous-actions.create', compact('actions', 'users'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->canCreateSousAction()) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'code' => 'required|string|max:10',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'action_id' => 'required|exists:actions,id',
            'owner_id' => 'nullable|exists:users,id',
            'taux_avancement' => 'required|numeric|min:0|max:100',
            'date_echeance' => 'nullable|date',
            'date_realisation' => 'nullable|date',
        ]);

        $sousAction = SousAction::create([
            'code' => $request->code,
            'libelle' => $request->libelle,
            'description' => $request->description,
            'action_id' => $request->action_id,
            'owner_id' => $request->owner_id,
            'taux_avancement' => $request->taux_avancement,
            'date_echeance' => $request->date_echeance,
            'date_realisation' => $request->date_realisation,
            'actif' => true,
        ]);

        return redirect()->route('sous-actions.index')
            ->with('success', 'Sous-action créée avec succès !');
    }

    public function show(SousAction $sousAction)
    {
        $user = Auth::user();
        
        // Vérifier les permissions
        if (!$user->isAdminGeneral() && !$user->isOwnerOS() && !$user->isOwnerPIL() && !$user->isOwnerAction() && !$user->isOwnerSA()) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->isOwnerSA() && $sousAction->owner_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        $sousAction->load(['owner', 'action.objectifSpecifique.objectifStrategique.pilier']);

        return view('sous-actions.show', compact('sousAction'));
    }

    public function edit(SousAction $sousAction)
    {
        $user = Auth::user();
        
        if (!$user->canUpdateSousAction()) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->isOwnerSA() && $sousAction->owner_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        // Déterminer les actions disponibles selon le rôle
        if ($user->isAdminGeneral()) {
            $actions = Action::actif()->with(['objectifSpecifique.objectifStrategique.pilier'])->get();
        } elseif ($user->isOwnerOS()) {
            $actions = Action::actif()->with(['objectifSpecifique.objectifStrategique.pilier'])
                ->whereHas('objectifSpecifique.objectifStrategique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerPIL()) {
            $actions = Action::actif()->with(['objectifSpecifique.objectifStrategique.pilier'])
                ->whereHas('objectifSpecifique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerAction()) {
            $actions = Action::actif()->byOwner($user->id)->with(['objectifSpecifique.objectifStrategique.pilier'])->get();
        } else {
            $actions = collect();
        }

        $users = User::whereHas('role', function($query) {
            $query->whereIn('nom', ['owner_action', 'owner_sa']);
        })->get();

        return view('sous-actions.edit', compact('sousAction', 'actions', 'users'));
    }

    public function update(Request $request, SousAction $sousAction)
    {
        $user = Auth::user();
        
        if (!$user->canUpdateSousAction()) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->isOwnerSA() && $sousAction->owner_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'code' => 'required|string|max:10',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'action_id' => 'required|exists:actions,id',
            'owner_id' => 'nullable|exists:users,id',
            'taux_avancement' => 'required|numeric|min:0|max:100',
            'date_echeance' => 'nullable|date',
            'date_realisation' => 'nullable|date',
        ]);

        $validationService = app(\App\Services\ValidationService::class);

        // Vérifier si des changements critiques nécessitent une validation
        $requiresValidation = false;
        $validationData = [];

        // Changement de propriétaire
        if ($sousAction->owner_id != $request->owner_id) {
            $requiresValidation = true;
            $validationData = [
                'action' => 'change_owner',
                'old_owner_id' => $sousAction->owner_id,
                'new_owner_id' => $request->owner_id,
                'reason' => 'Changement de propriétaire de la sous-action'
            ];
        }

        // Changement d'échéance
        if ($sousAction->date_echeance != $request->date_echeance) {
            $requiresValidation = true;
            $validationData = [
                'action' => 'change_deadline',
                'old_deadline' => $sousAction->date_echeance ?? 'Non définie',
                'new_deadline' => $request->date_echeance,
                'reason' => 'Modification de l\'échéance de la sous-action'
            ];
        }

        // Changement de statut critique (marquage comme terminé)
        if ($request->date_realisation && !$sousAction->date_realisation) {
            $requiresValidation = true;
            $validationData = [
                'action' => 'mark_completed',
                'old_status' => $sousAction->statut ?? 'En cours',
                'new_status' => 'Terminé',
                'reason' => 'Marquage de la sous-action comme terminée'
            ];
        }

        // Si validation requise
        if ($requiresValidation) {
            try {
                $validation = $validationService->createValidationRequest(
                    'sous_action',
                    $sousAction->id,
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
        $sousAction->update([
            'code' => $request->code,
            'libelle' => $request->libelle,
            'description' => $request->description,
            'action_id' => $request->action_id,
            'owner_id' => $request->owner_id,
            'taux_avancement' => $request->taux_avancement,
            'date_echeance' => $request->date_echeance,
            'date_realisation' => $request->date_realisation,
        ]);

        return redirect()->route('sous-actions.index')
            ->with('success', 'Sous-action mise à jour avec succès !');
    }

    public function destroy(SousAction $sousAction)
    {
        $user = Auth::user();
        
        if (!$user->canUpdateSousAction()) {
            abort(403, 'Accès non autorisé');
        }

        // Créer une demande de validation pour la suppression
        $validationService = app(\App\Services\ValidationService::class);
        
        try {
            $validation = $validationService->createValidationRequest(
                'sous_action',
                $sousAction->id,
                $user,
                [
                    'action' => 'delete_element',
                    'element_name' => $sousAction->libelle,
                    'element_code' => $sousAction->code,
                    'reason' => 'Suppression de la sous-action demandée'
                ]
            );

            return redirect()->route('sous-actions.index')->with('success', 
                'Demande de validation pour la suppression créée. Vous recevrez une notification une fois approuvée.'
            );
        } catch (\Exception $e) {
            return redirect()->route('sous-actions.index')->with('error', $e->getMessage());
        }
    }

    // Méthode pour mettre à jour uniquement le taux d'avancement (AJAX)
    public function updateTauxAvancement(Request $request, SousAction $sousAction)
    {
        $user = Auth::user();
        
        if (!$user->canUpdateSousAction()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        if ($user->isOwnerSA() && $sousAction->owner_id !== $user->id) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'taux_avancement' => 'required|numeric|min:0|max:100',
        ]);

        $sousAction->update([
            'taux_avancement' => $request->taux_avancement,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Taux d\'avancement mis à jour',
            'taux_avancement' => $sousAction->taux_avancement,
            'statut' => $sousAction->statut_libelle,
            'statut_color' => $sousAction->statut_color,
        ]);
    }
}
