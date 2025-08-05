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

    public function update(Request $request, Action $action)
    {
        $user = Auth::user();
        
        if (!$user->canCreateAction()) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->isOwnerAction() && $action->owner_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'code' => 'required|string|max:10|unique:actions,code,' . $action->id,
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectif_specifique_id' => 'required|exists:objectif_specifiques,id',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $action->update([
            'code' => $request->code,
            'libelle' => $request->libelle,
            'description' => $request->description,
            'objectif_specifique_id' => $request->objectif_specifique_id,
            'owner_id' => $request->owner_id,
        ]);

        return redirect()->route('actions.index')
            ->with('success', 'Action mise à jour avec succès !');
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
