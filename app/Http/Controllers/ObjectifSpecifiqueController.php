<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ObjectifSpecifique;
use App\Models\ObjectifStrategique;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ObjectifSpecifiqueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->canCreateObjectifSpecifique()) {
                abort(403, 'Accès non autorisé');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdminGeneral()) {
            $objectifsSpecifiques = ObjectifSpecifique::with(['owner', 'objectifStrategique.pilier'])->get();
        } elseif ($user->isOwnerOS()) {
            $objectifsSpecifiques = ObjectifSpecifique::with(['owner', 'objectifStrategique.pilier'])
                ->whereHas('objectifStrategique', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })->get();
        } elseif ($user->isOwnerPIL()) {
            $objectifsSpecifiques = ObjectifSpecifique::where('owner_id', $user->id)->with(['owner', 'objectifStrategique.pilier'])->get();
        } else {
            $objectifsSpecifiques = collect();
        }

        return view('objectifs-specifiques.index', compact('objectifsSpecifiques'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if (!$user->canCreateObjectifSpecifique()) {
            abort(403, 'Accès non autorisé');
    }

        // Déterminer les objectifs stratégiques disponibles selon le rôle
        if ($user->isAdminGeneral()) {
            $objectifsStrategiques = ObjectifStrategique::with('pilier')->get();
        } elseif ($user->isOwnerOS()) {
            $objectifsStrategiques = ObjectifStrategique::where('owner_id', $user->id)->with('pilier')->get();
        } else {
            $objectifsStrategiques = collect();
        }

        $users = User::whereHas('role', function($query) {
            $query->whereIn('nom', ['owner_os', 'owner_pil']);
        })->get();

        return view('objectifs-specifiques.create', compact('objectifsStrategiques', 'users'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->canCreateObjectifSpecifique()) {
            abort(403, 'Accès non autorisé');
    }

        // Log pour débogage
        Log::info('Données reçues ObjectifSpecifique:', $request->all());
        Log::info('Début de la méthode store ObjectifSpecifique');

        $request->validate([
            'code' => 'required|string|max:10',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectif_strategique_id' => 'required|exists:objectif_strategiques,id',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        Log::info('Validation réussie ObjectifSpecifique');

        try {
            Log::info('Début de la transaction ObjectifSpecifique');
            DB::beginTransaction();

            Log::info('Création de l\'objectif spécifique avec les données:', [
                'code' => $request->code,
                'libelle' => $request->libelle,
                'description' => $request->description,
                'objectif_strategique_id' => $request->objectif_strategique_id,
                'owner_id' => $request->owner_id,
            ]);

            $objectifSpecifique = ObjectifSpecifique::create([
                'code' => $request->code,
                'libelle' => $request->libelle,
                'description' => $request->description,
                'objectif_strategique_id' => $request->objectif_strategique_id,
                'owner_id' => $request->owner_id,
                'taux_avancement' => 0,
                'actif' => true,
            ]);

            Log::info('Objectif spécifique créé avec succès, ID:', ['id' => $objectifSpecifique->id]);

            Log::info('Avant commit de la transaction ObjectifSpecifique');
            DB::commit();
            Log::info('Transaction commitée avec succès ObjectifSpecifique');

            // Vérifier si c'est une requête AJAX ou si le header Accept contient JSON
            if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
                Log::info('Retour de réponse JSON ObjectifSpecifique');
                return response()->json([
                    'success' => true,
                    'message' => 'Objectif spécifique créé avec succès !',
                    'objectif_specifique' => $objectifSpecifique
                ]);
            }

            return redirect()->route('objectifs-specifiques.index')
                ->with('success', 'Objectif spécifique créé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur dans la méthode store ObjectifSpecifique:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            DB::rollback();
            
            // Vérifier si c'est une requête AJAX ou si le header Accept contient JSON
            if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création : ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    public function show(ObjectifSpecifique $objectifSpecifique)
    {
        $user = Auth::user();
        
        // Vérifier les permissions
        if (!$user->isAdminGeneral() && !$user->isOwnerOS() && !$user->isOwnerPIL()) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->isOwnerPIL() && $objectifSpecifique->owner_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        $objectifSpecifique->load(['owner', 'objectifStrategique.pilier', 'actions.owner', 'actions.sousActions.owner']);

        return view('objectifs-specifiques.show', compact('objectifSpecifique'));
    }

    public function edit(ObjectifSpecifique $objectifSpecifique)
    {
        $user = Auth::user();
        
        if (!$user->canCreateObjectifSpecifique()) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->isOwnerPIL() && $objectifSpecifique->owner_id !== $user->id) {
            abort(403, 'Accès non autorisé');
    }

        // Déterminer les objectifs stratégiques disponibles selon le rôle
        if ($user->isAdminGeneral()) {
            $objectifsStrategiques = ObjectifStrategique::actif()->with('pilier')->get();
        } elseif ($user->isOwnerOS()) {
            $objectifsStrategiques = ObjectifStrategique::actif()->byOwner($user->id)->with('pilier')->get();
        } else {
            $objectifsStrategiques = collect();
        }

        $users = User::whereHas('role', function($query) {
            $query->whereIn('nom', ['owner_os', 'owner_pil']);
        })->get();

        return view('objectifs-specifiques.edit', compact('objectifSpecifique', 'objectifsStrategiques', 'users'));
    }

    public function update(Request $request, ObjectifSpecifique $objectifSpecifique)
    {
        $user = Auth::user();
        
        if (!$user->canCreateObjectifSpecifique()) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->isOwnerPIL() && $objectifSpecifique->owner_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'code' => 'required|string|max:10|unique:objectif_specifiques,code,' . $objectifSpecifique->id,
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectif_strategique_id' => 'required|exists:objectif_strategiques,id',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $objectifSpecifique->update([
            'code' => $request->code,
            'libelle' => $request->libelle,
            'description' => $request->description,
            'objectif_strategique_id' => $request->objectif_strategique_id,
            'owner_id' => $request->owner_id,
        ]);

        return redirect()->route('objectifs-specifiques.index')
            ->with('success', 'Objectif spécifique mis à jour avec succès !');
    }

    public function destroy(ObjectifSpecifique $objectifSpecifique)
    {
        $user = Auth::user();
        
        if (!$user->canCreateObjectifSpecifique()) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->isOwnerPIL() && $objectifSpecifique->owner_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier s'il y a des actions liées
        if ($objectifSpecifique->actions()->count() > 0) {
            return redirect()->route('objectifs-specifiques.index')
                ->with('error', 'Impossible de supprimer cet objectif spécifique car il contient des actions.');
        }

        $objectifSpecifique->update(['actif' => false]);

        return redirect()->route('objectifs-specifiques.index')
            ->with('success', 'Objectif spécifique supprimé avec succès !');
    }
}
