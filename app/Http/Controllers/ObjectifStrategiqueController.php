<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ObjectifStrategique;
use App\Models\Pilier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Added missing import
use Illuminate\Support\Facades\Log; // Added missing import
use App\Models\ObjectifSpecifique; // Added missing import

class ObjectifStrategiqueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->canCreateObjectifStrategique()) {
                abort(403, 'Accès non autorisé');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdminGeneral()) {
            $objectifsStrategiques = ObjectifStrategique::with(['owner', 'pilier'])->get();
        } elseif ($user->isOwnerOS()) {
            $objectifsStrategiques = ObjectifStrategique::byOwner($user->id)->with(['owner', 'pilier'])->get();
        } else {
            $objectifsStrategiques = collect();
        }

        return view('objectifs-strategiques.index', compact('objectifsStrategiques'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isAdminGeneral()) {
            abort(403, 'Accès non autorisé');
        }

        $piliers = Pilier::get();
        $users = User::whereHas('role', function($query) {
            $query->whereIn('nom', ['admin_general', 'owner_os']);
        })->get();

        return view('objectifs-strategiques.create', compact('piliers', 'users'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGeneral()) {
            abort(403, 'Accès non autorisé');
        }

        // Log pour débogage
        \Log::info('Données reçues:', $request->all());
        \Log::info('Début de la méthode store');

        $request->validate([
            'code' => 'required|string|max:10',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pilier_id' => 'required|exists:piliers,id',
            'owner_id' => 'nullable|exists:users,id',
            'objectifs_specifiques' => 'nullable|array',
            'objectifs_specifiques.*.code' => 'nullable|string|max:10',
            'objectifs_specifiques.*.libelle' => 'nullable|string|max:255',
            'objectifs_specifiques.*.description' => 'nullable|string',
            'objectifs_specifiques.*.owner_id' => 'nullable|exists:users,id',
        ]);

        \Log::info('Validation réussie');

        try {
            \Log::info('Début de la transaction');
            \DB::beginTransaction();

            \Log::info('Création de l\'objectif stratégique avec les données:', [
                'code' => $request->code,
                'libelle' => $request->libelle,
                'description' => $request->description,
                'pilier_id' => $request->pilier_id,
                'owner_id' => $request->owner_id,
            ]);

            $objectifStrategique = ObjectifStrategique::create([
                'code' => $request->code,
                'libelle' => $request->libelle,
                'description' => $request->description,
                'pilier_id' => $request->pilier_id,
                'owner_id' => $request->owner_id,
            ]);

            \Log::info('Objectif stratégique créé avec succès, ID:', ['id' => $objectifStrategique->id]);

            // Créer les objectifs spécifiques si fournis
            if ($request->has('objectifs_specifiques') && is_array($request->objectifs_specifiques)) {
                \Log::info('Création des objectifs spécifiques');
                foreach ($request->objectifs_specifiques as $objectifSpecifiqueData) {
                    if (!empty($objectifSpecifiqueData['libelle'])) {
                        ObjectifSpecifique::create([
                            'code' => $objectifSpecifiqueData['code'] ?? '',
                            'libelle' => $objectifSpecifiqueData['libelle'],
                            'description' => $objectifSpecifiqueData['description'] ?? null,
                            'objectif_strategique_id' => $objectifStrategique->id,
                            'owner_id' => $objectifSpecifiqueData['owner_id'] ?? null,
                        ]);
                    }
                }
            }

            \Log::info('Avant commit de la transaction');
            \DB::commit();
            \Log::info('Transaction commitée avec succès');

            // Vérifier si c'est une requête AJAX ou si le header Accept contient JSON
            if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
                \Log::info('Retour de réponse JSON');
                return response()->json([
                    'success' => true,
                    'message' => 'Objectif stratégique créé avec succès !',
                    'objectif_strategique' => $objectifStrategique
                ]);
            }

            return redirect()->route('objectifs-strategiques.index')
                ->with('success', 'Objectif stratégique créé avec succès !');

        } catch (\Exception $e) {
            \Log::error('Erreur dans la méthode store:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            \DB::rollback();
            
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

    public function show(ObjectifStrategique $objectifStrategique)
    {
        $user = Auth::user();
        
        // Log pour débogage
        \Log::info('Méthode show ObjectifStrategique appelée', [
            'objectif_id' => $objectifStrategique->id,
            'objectif_code' => $objectifStrategique->code,
            'objectif_libelle' => $objectifStrategique->libelle,
            'pilier_id' => $objectifStrategique->pilier_id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role ? $user->role->nom : 'Aucun rôle'
        ]);
        
        // Charger les relations
        $objectifStrategique->load(['owner', 'pilier', 'objectifsSpecifiques.owner', 'objectifsSpecifiques.actions.owner']);

        \Log::info('Relations chargées pour ObjectifStrategique', [
            'objectif_id' => $objectifStrategique->id,
            'has_pilier' => $objectifStrategique->pilier ? true : false,
            'pilier_id' => $objectifStrategique->pilier ? $objectifStrategique->pilier->id : null,
            'objectifs_specifiques_count' => $objectifStrategique->objectifsSpecifiques->count()
        ]);

        // TEMPORAIRE : Permissions simplifiées pour débogage
        // if (!$user->isAdminGeneral() && !$user->isOwnerOS()) {
        //     abort(403, 'Accès non autorisé');
        // }

        // if ($user->isOwnerOS() && $objectifStrategique->owner_id !== $user->id) {
        //     abort(403, 'Accès non autorisé');
        // }

        return view('objectifs-strategiques.show', compact('objectifStrategique'));
    }

    public function edit(ObjectifStrategique $objectifStrategique)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGeneral()) {
            abort(403, 'Accès non autorisé');
        }

        $piliers = Pilier::actif()->get();
        $users = User::whereHas('role', function($query) {
            $query->whereIn('nom', ['admin_general', 'owner_os']);
        })->get();

        return view('objectifs-strategiques.edit', compact('objectifStrategique', 'piliers', 'users'));
    }

    public function update(Request $request, ObjectifStrategique $objectifStrategique)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGeneral()) {
            abort(403, 'Accès non autorisé');
        }

        // Log pour débogage
        \Log::info('Méthode update ObjectifStrategique appelée', [
            'objectif_id' => $objectifStrategique->id,
            'request_data' => $request->all()
        ]);

        $request->validate([
            'code' => 'required|string|max:10',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pilier_id' => 'required|exists:piliers,id',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        try {
            $objectifStrategique->update([
                'code' => $request->code,
                'libelle' => $request->libelle,
                'description' => $request->description,
                'pilier_id' => $request->pilier_id,
                'owner_id' => $request->owner_id,
            ]);

            \Log::info('Objectif stratégique mis à jour avec succès', [
                'objectif_id' => $objectifStrategique->id
            ]);

            // Vérifier si c'est une requête AJAX ou si le header Accept contient JSON
            if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'message' => 'Objectif stratégique mis à jour avec succès !',
                    'objectif_strategique' => $objectifStrategique
                ]);
            }

            return redirect()->route('objectifs-strategiques.index')
                ->with('success', 'Objectif stratégique mis à jour avec succès !');

        } catch (\Exception $e) {
            \Log::error('Erreur dans la méthode update ObjectifStrategique:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Vérifier si c'est une requête AJAX ou si le header Accept contient JSON
            if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la modification : ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la modification : ' . $e->getMessage());
        }
    }

    public function destroy(ObjectifStrategique $objectifStrategique)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGeneral()) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier s'il y a des objectifs spécifiques liés
        if ($objectifStrategique->objectifsSpecifiques()->count() > 0) {
            return redirect()->route('objectifs-strategiques.index')
                ->with('error', 'Impossible de supprimer cet objectif stratégique car il contient des objectifs spécifiques.');
        }

        $objectifStrategique->update(['actif' => false]);

        return redirect()->route('objectifs-strategiques.index')
            ->with('success', 'Objectif stratégique supprimé avec succès !');
    }
}
