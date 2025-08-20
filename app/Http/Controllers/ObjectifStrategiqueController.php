<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ObjectifStrategique;
use App\Models\Pilier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ObjectifSpecifique;
use App\Notifications\ObjectifStrategiqueAssigned;

class ObjectifStrategiqueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            /** @var User $user */
            $user = Auth::user();
            if (!$user->canCreateObjectifStrategique()) {
                abort(403, 'Accès non autorisé');
            }
            return $next($request);
        })->only(['create', 'store']);
    }

    public function index()
    {
        /** @var User $user */
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
        /** @var User $user */
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
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user->isAdminGeneral()) {
            abort(403, 'Accès non autorisé');
        }

        // Log pour débogage
        Log::info('Données reçues:', $request->all());
        Log::info('Début de la méthode store');

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

        Log::info('Validation réussie');

        try {
            Log::info('Début de la transaction');
            DB::beginTransaction();

            Log::info('Création de l\'objectif stratégique avec les données:', [
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

            Log::info('Objectif stratégique créé avec succès, ID:', ['id' => $objectifStrategique->id]);

            // Créer les objectifs spécifiques si fournis
            if ($request->has('objectifs_specifiques') && is_array($request->objectifs_specifiques)) {
                Log::info('Création des objectifs spécifiques');
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

            Log::info('Avant commit de la transaction');
            DB::commit();
            Log::info('Transaction commitée avec succès');

            // Envoyer une notification à l'utilisateur assigné si un owner est spécifié
            if ($request->owner_id) {
                $owner = User::find($request->owner_id);
                if ($owner) {
                    $owner->notify(new ObjectifStrategiqueAssigned($objectifStrategique));
                    Log::info('Notification envoyée à l\'utilisateur:', ['user_id' => $owner->id, 'user_name' => $owner->name]);
                }
            }

            // Vérifier si c'est une requête AJAX ou si le header Accept contient JSON
            if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'message' => 'Objectif stratégique créé avec succès !',
                    'objectif_strategique' => $objectifStrategique->load(['owner', 'pilier'])
                ]);
            }

            return redirect()->route('objectifs-strategiques.index')
                ->with('success', 'Objectif stratégique créé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'objectif stratégique:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            DB::rollBack();

            if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
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
        /** @var User $user */
        $user = Auth::user();
        
        // Vérifier les permissions
        if (!$user->isAdminGeneral() && !$user->isOwnerOS()) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->isOwnerOS() && $objectifStrategique->owner_id !== $user->id) {
            abort(403, 'Accès non autorisé');
        }

        $objectifStrategique->load(['owner', 'pilier', 'objectifsSpecifiques.owner', 'objectifsSpecifiques.actions.owner', 'objectifsSpecifiques.actions.sousActions.owner']);

        return view('objectifs-strategiques.show', compact('objectifStrategique'));
    }

    public function edit(ObjectifStrategique $objectifStrategique)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Vérifier les permissions : seul l'admin ou le owner peut éditer
        if (!$user->canEditObjectifStrategique($objectifStrategique)) {
            abort(403, 'Accès non autorisé. Seuls l\'administrateur général et le propriétaire de cet objectif stratégique peuvent le modifier.');
        }

        $piliers = Pilier::actif()->get();
        $users = User::whereHas('role', function($query) {
            $query->whereIn('nom', ['admin_general', 'owner_os']);
        })->get();

        return view('objectifs-strategiques.edit', compact('objectifStrategique', 'piliers', 'users'));
    }

    public function update(Request $request, ObjectifStrategique $objectifStrategique)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Vérifier les permissions : seul l'admin ou le owner peut modifier
        if (!$user->canEditObjectifStrategique($objectifStrategique)) {
            abort(403, 'Accès non autorisé. Seuls l\'administrateur général et le propriétaire de cet objectif stratégique peuvent le modifier.');
        }

        $request->validate([
            'code' => 'required|string|max:10',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pilier_id' => 'required|exists:piliers,id',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $validationService = app(\App\Services\ValidationService::class);

        // Vérifier si des changements critiques nécessitent une validation
        $requiresValidation = false;
        $validationData = [];

        // Changement de propriétaire
        if ($objectifStrategique->owner_id != $request->owner_id) {
            $requiresValidation = true;
            $validationData = [
                'action' => 'change_owner',
                'old_owner_id' => $objectifStrategique->owner_id,
                'new_owner_id' => $request->owner_id,
                'reason' => 'Changement de propriétaire de l\'objectif stratégique'
            ];
        }

        // Changement de pilier (impact structurel)
        if ($objectifStrategique->pilier_id != $request->pilier_id) {
            $requiresValidation = true;
            $validationData = [
                'action' => 'change_structure',
                'old_pilier_id' => $objectifStrategique->pilier_id,
                'new_pilier_id' => $request->pilier_id,
                'reason' => 'Changement de pilier pour l\'objectif stratégique'
            ];
        }

        // Si validation requise
        if ($requiresValidation) {
            try {
                $validation = $validationService->createValidationRequest(
                    'objectif_strategique',
                    $objectifStrategique->id,
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
        try {
            $oldOwnerId = $objectifStrategique->owner_id;
            
            $objectifStrategique->update([
                'code' => $request->code,
                'libelle' => $request->libelle,
                'description' => $request->description,
                'pilier_id' => $request->pilier_id,
                'owner_id' => $request->owner_id,
            ]);

            // Envoyer une notification si le propriétaire a changé
            if ($oldOwnerId != $request->owner_id && $request->owner_id) {
                $newOwner = User::find($request->owner_id);
                if ($newOwner) {
                    $newOwner->notify(new ObjectifStrategiqueAssigned($objectifStrategique));
                }
            }

            return redirect()->route('objectifs-strategiques.index')
                ->with('success', 'Objectif stratégique mis à jour avec succès !');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la modification : ' . $e->getMessage());
        }
    }

    public function destroy(ObjectifStrategique $objectifStrategique)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Vérifier les permissions : seul l'admin ou le owner peut supprimer
        if (!$user->canDeleteObjectifStrategique($objectifStrategique)) {
            abort(403, 'Accès non autorisé. Seuls l\'administrateur général et le propriétaire de cet objectif stratégique peuvent le supprimer.');
        }

        // Vérifier s'il y a des objectifs spécifiques liés
        if ($objectifStrategique->objectifsSpecifiques()->count() > 0) {
            return redirect()->route('objectifs-strategiques.index')
                ->with('error', 'Impossible de supprimer cet objectif stratégique car il contient des objectifs spécifiques.');
        }

        // Créer une demande de validation pour la suppression
        $validationService = app(\App\Services\ValidationService::class);
        
        try {
            $validation = $validationService->createValidationRequest(
                'objectif_strategique',
                $objectifStrategique->id,
                $user,
                [
                    'action' => 'delete_element',
                    'element_name' => $objectifStrategique->libelle,
                    'element_code' => $objectifStrategique->code,
                    'reason' => 'Suppression de l\'objectif stratégique demandée'
                ]
            );

            return redirect()->route('objectifs-strategiques.index')->with('success', 
                'Demande de validation pour la suppression créée. Vous recevrez une notification une fois approuvée.'
            );
        } catch (\Exception $e) {
            return redirect()->route('objectifs-strategiques.index')->with('error', $e->getMessage());
        }
    }
}
