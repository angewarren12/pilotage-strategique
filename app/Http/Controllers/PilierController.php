<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PilierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->canCreatePilier()) {
                abort(403, 'Accès non autorisé');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $user = Auth::user();
        
        // Tous les utilisateurs peuvent voir tous les piliers
        // Mais les permissions CRUD sont gérées dans la vue
        $piliers = Pilier::with(['owner', 'objectifsStrategiques'])->get();

        return view('piliers.index', compact('piliers'));
    }

    public function create()
    {
        // Pas besoin d'users car les piliers n'ont pas d'owner
        return view('piliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:piliers',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        try {
            $pilier = Pilier::create([
                'code' => $request->code,
                'libelle' => $request->libelle,
                'description' => $request->description,
                'color' => $request->color ?? '#007bff',
                'owner_id' => null, // Pas d'owner pour les piliers
            ]);

            $message = 'Pilier créé avec succès !';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'pilier' => $pilier
                ]);
            }

            return redirect()->route('piliers.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            $message = 'Erreur lors de la création : ' . $e->getMessage();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $message);
        }
    }

    public function show(Pilier $pilier)
    {
        $user = Auth::user();
        
        // Tous les utilisateurs peuvent voir les détails des piliers
        // Mais les permissions CRUD sont gérées dans la vue
        $pilier->load(['owner', 'objectifsStrategiques.owner', 'objectifsStrategiques.objectifsSpecifiques.owner']);

        return view('piliers.show', compact('pilier'));
    }

    public function edit(Pilier $pilier)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGeneral()) {
            abort(403, 'Accès non autorisé');
        }

        // Pas besoin d'users car les piliers n'ont pas d'owner
        return view('piliers.edit', compact('pilier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pilier $pilier)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            // SUPPRIMER owner_id de la validation car les piliers n'ont pas d'owner
        ]);

        $user = Auth::user();
        $validationService = app(\App\Services\ValidationService::class);

        // Changement de couleur
        if ($pilier->color != $request->color) {
            try {
                $validation = $validationService->createValidationRequest(
                    'pilier',
                    $pilier->id,
                    $user,
                    [
                        'action' => 'change_color',
                        'old_color' => $pilier->color,
                        'new_color' => $request->color,
                        'reason' => 'Modification de la couleur du pilier'
                    ]
                );

                $message = 'Demande de validation créée. Vous recevrez une notification une fois approuvée.';
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message
                    ]);
                }

                return redirect()->back()->with('success', $message);
            } catch (\Exception $e) {
                $message = $e->getMessage();
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 500);
                }

                return redirect()->back()->with('error', $message);
            }
        }

        try {
            // Mise à jour directe si pas de validation requise
            $pilier->update([
                'code' => $request->code,
                'libelle' => $request->libelle,
                'description' => $request->description,
                'color' => $request->color,
            ]);

            $message = 'Pilier mis à jour avec succès.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'pilier' => $pilier
                ]);
            }

            return redirect()->route('piliers.index')->with('success', $message);
            
        } catch (\Exception $e) {
            $message = 'Erreur lors de la mise à jour : ' . $e->getMessage();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            return redirect()->back()->with('error', $message);
        }
    }

    public function destroy(Request $request, Pilier $pilier)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGeneral()) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier s'il y a des objectifs stratégiques liés
        if ($pilier->objectifsStrategiques()->count() > 0) {
            $message = 'Impossible de supprimer ce pilier car il contient des objectifs stratégiques.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }

            return redirect()->route('piliers.index')
                ->with('error', $message);
        }

        // Vérifier s'il y a des sous-actions liées (via la hiérarchie)
        $hasSubActions = false;
        foreach ($pilier->objectifsStrategiques as $objectifStrategique) {
            foreach ($objectifStrategique->objectifsSpecifiques as $objectifSpecifique) {
                if ($objectifSpecifique->actions()->count() > 0) {
                    $hasSubActions = true;
                    break 2;
                }
            }
        }

        if ($hasSubActions) {
            $message = 'Impossible de supprimer ce pilier car il contient des actions et sous-actions.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }

            return redirect()->route('piliers.index')
                ->with('error', $message);
        }

        try {
            // Supprimer directement le pilier (pas de validation requise pour la suppression)
            $pilier->delete();
            
            $message = 'Pilier supprimé avec succès.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('piliers.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            $message = 'Erreur lors de la suppression : ' . $e->getMessage();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }

            return redirect()->route('piliers.index')
                ->with('error', $message);
        }
    }
}
