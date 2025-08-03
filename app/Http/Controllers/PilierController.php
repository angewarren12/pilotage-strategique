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
        
        if ($user->isAdminGeneral()) {
            $piliers = Pilier::with(['owner', 'objectifsStrategiques'])->get();
        } else {
            $piliers = collect(); // Pas d'accès pour les autres rôles
        }

        return view('piliers.index', compact('piliers'));
    }

    public function create()
    {
        $users = User::whereHas('role', function($query) {
            $query->where('nom', 'admin_general');
        })->get();

        return view('piliers.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:piliers',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $pilier = Pilier::create([
                'code' => $request->code,
                'libelle' => $request->libelle,
                'description' => $request->description,
                'owner_id' => null, // Pas d'owner pour les piliers
            ]);

            // Vérifier si c'est une requête AJAX ou si le header Accept contient JSON
            if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'message' => 'Pilier créé avec succès !',
                    'pilier' => $pilier
                ]);
            }

            return redirect()->route('piliers.index')
                ->with('success', 'Pilier créé avec succès !');
        } catch (\Exception $e) {
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

    public function show(Pilier $pilier)
    {
        $user = Auth::user();
        
        // Vérifier les permissions
        if (!$user->isAdminGeneral()) {
            abort(403, 'Accès non autorisé');
        }

        $pilier->load(['owner', 'objectifsStrategiques.owner', 'objectifsStrategiques.objectifsSpecifiques.owner']);

        // Log pour débogage
        Log::info('Pilier show - Objectifs stratégiques chargés', [
            'pilier_id' => $pilier->id,
            'pilier_code' => $pilier->code,
            'objectifs_count' => $pilier->objectifsStrategiques->count(),
            'objectifs_details' => $pilier->objectifsStrategiques->map(function($os) {
                return [
                    'id' => $os->id,
                    'code' => $os->code,
                    'libelle' => $os->libelle
                ];
            })->toArray()
        ]);

        return view('piliers.show', compact('pilier'));
    }

    public function edit(Pilier $pilier)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGeneral()) {
            abort(403, 'Accès non autorisé');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'pilier' => $pilier
            ]);
        }

        $users = User::whereHas('role', function($query) {
            $query->where('nom', 'admin_general');
        })->get();

        return view('piliers.edit', compact('pilier', 'users'));
    }

    public function update(Request $request, Pilier $pilier)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGeneral()) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'code' => 'required|string|max:10|unique:piliers,code,' . $pilier->id,
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $pilier->update([
                'code' => $request->code,
                'libelle' => $request->libelle,
                'description' => $request->description,
                'owner_id' => null, // Pas d'owner pour les piliers
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pilier mis à jour avec succès !',
                    'pilier' => $pilier
                ]);
            }

            return redirect()->route('piliers.index')
                ->with('success', 'Pilier mis à jour avec succès !');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
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

    public function destroy(Pilier $pilier)
    {
        $user = Auth::user();
        
        if (!$user->isAdminGeneral()) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier s'il y a des objectifs stratégiques liés
        if ($pilier->objectifsStrategiques()->count() > 0) {
            $message = 'Impossible de supprimer ce pilier car il contient des objectifs stratégiques.';
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }

            return redirect()->route('piliers.index')
                ->with('error', $message);
        }

        try {
            $pilier->update(['actif' => false]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pilier supprimé avec succès !'
                ]);
            }

            return redirect()->route('piliers.index')
                ->with('success', 'Pilier supprimé avec succès !');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('piliers.index')
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }
}
