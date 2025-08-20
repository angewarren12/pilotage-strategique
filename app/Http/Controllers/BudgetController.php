<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Pilier;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\Action;
use App\Models\SousAction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BudgetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdminGeneral()) {
                abort(403, 'Accès non autorisé');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Budget::with(['owner', 'pilier', 'objectifStrategique', 'objectifSpecifique', 'action', 'sousAction']);

        // Filtres
        if ($request->filled('annee')) {
            $query->byAnnee($request->annee);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('owner_id')) {
            $query->byOwner($request->owner_id);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $budgets = $query->paginate(15);

        // Statistiques
        $stats = $this->getBudgetStats($request);

        return view('budgets.index', compact('budgets', 'stats'));
    }

    public function create()
    {
        $piliers = Pilier::actif()->get();
        $objectifsStrategiques = ObjectifStrategique::actif()->get();
        $objectifsSpecifiques = ObjectifSpecifique::actif()->get();
        $actions = Action::actif()->get();
        $sousActions = SousAction::actif()->get();
        $users = User::whereHas('role', function($query) {
            $query->whereIn('nom', ['admin_general', 'owner_os', 'owner_pil']);
        })->get();

        return view('budgets.create', compact(
            'piliers',
            'objectifsStrategiques',
            'objectifsSpecifiques',
            'actions',
            'sousActions',
            'users'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'element_type' => 'required|in:pilier,objectif_strategique,objectif_specifique,action,sous_action',
            'element_id' => 'required|integer',
            'montant_alloue' => 'required|numeric|min:0',
            'annee_budgetaire' => 'required|integer|min:2020|max:2030',
            'type_budget' => 'required|in:investissement,fonctionnement,personnel,autre',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'description' => 'nullable|string|max:1000',
            'justification' => 'nullable|string|max:1000',
            'source_financement' => 'nullable|string|max:100',
            'owner_id' => 'nullable|exists:users,id',
            'seuil_alerte' => 'nullable|numeric|min:0|max:100',
            'seuil_critique' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Créer le budget avec la relation appropriée
            $budgetData = [
                'montant_alloue' => $request->montant_alloue,
                'annee_budgetaire' => $request->annee_budgetaire,
                'type_budget' => $request->type_budget,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'description' => $request->description,
                'justification' => $request->justification,
                'source_financement' => $request->source_financement,
                'owner_id' => $request->owner_id,
                'seuil_alerte' => $request->seuil_alerte ?? 80,
                'seuil_critique' => $request->seuil_critique ?? 95,
                'alertes_actives' => true,
            ];

            // Ajouter la relation appropriée
            switch ($request->element_type) {
                case 'pilier':
                    $budgetData['pilier_id'] = $request->element_id;
                    break;
                case 'objectif_strategique':
                    $budgetData['objectif_strategique_id'] = $request->element_id;
                    break;
                case 'objectif_specifique':
                    $budgetData['objectif_specifique_id'] = $request->element_id;
                    break;
                case 'action':
                    $budgetData['action_id'] = $request->element_id;
                    break;
                case 'sous_action':
                    $budgetData['sous_action_id'] = $request->element_id;
                    break;
            }

            $budget = Budget::create($budgetData);

            DB::commit();

            return redirect()->route('budgets.index')
                ->with('success', 'Budget créé avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la création du budget : ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du budget : ' . $e->getMessage());
        }
    }

    public function show(Budget $budget)
    {
        $budget->load(['owner', 'pilier', 'objectifStrategique', 'objectifSpecifique', 'action', 'sousAction']);
        
        // Calculer les statistiques pour ce budget
        $stats = $this->getBudgetStatsForElement($budget);
        
        return view('budgets.show', compact('budget', 'stats'));
    }

    public function edit(Budget $budget)
    {
        $piliers = Pilier::actif()->get();
        $objectifsStrategiques = ObjectifStrategique::actif()->get();
        $objectifsSpecifiques = ObjectifSpecifique::actif()->get();
        $actions = Action::actif()->get();
        $sousActions = SousAction::actif()->get();
        $users = User::whereHas('role', function($query) {
            $query->whereIn('nom', ['admin_general', 'owner_os', 'owner_pil']);
        })->get();

        return view('budgets.edit', compact(
            'budget',
            'piliers',
            'objectifsStrategiques',
            'objectifsSpecifiques',
            'actions',
            'sousActions',
            'users'
        ));
    }

    public function update(Request $request, Budget $budget)
    {
        $request->validate([
            'montant_alloue' => 'required|numeric|min:0',
            'montant_engage' => 'nullable|numeric|min:0',
            'montant_realise' => 'nullable|numeric|min:0',
            'annee_budgetaire' => 'required|integer|min:2020|max:2030',
            'type_budget' => 'required|in:investissement,fonctionnement,personnel,autre',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'description' => 'nullable|string|max:1000',
            'justification' => 'nullable|string|max:1000',
            'source_financement' => 'nullable|string|max:100',
            'owner_id' => 'nullable|exists:users,id',
            'seuil_alerte' => 'nullable|numeric|min:0|max:100',
            'seuil_critique' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            $budget->update([
                'montant_alloue' => $request->montant_alloue,
                'montant_engage' => $request->montant_engage ?? $budget->montant_engage,
                'montant_realise' => $request->montant_realise ?? $budget->montant_realise,
                'annee_budgetaire' => $request->annee_budgetaire,
                'type_budget' => $request->type_budget,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'description' => $request->description,
                'justification' => $request->justification,
                'source_financement' => $request->source_financement,
                'owner_id' => $request->owner_id,
                'seuil_alerte' => $request->seuil_alerte ?? $budget->seuil_alerte,
                'seuil_critique' => $request->seuil_critique ?? $budget->seuil_critique,
            ]);

            DB::commit();

            return redirect()->route('budgets.index')
                ->with('success', 'Budget mis à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la mise à jour du budget : ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour du budget : ' . $e->getMessage());
        }
    }

    public function destroy(Budget $budget)
    {
        try {
            $budget->update(['statut' => 'archive']);
            
            return redirect()->route('budgets.index')
                ->with('success', 'Budget archivé avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'archivage du budget : ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'archivage du budget : ' . $e->getMessage());
        }
    }

    // Méthodes supplémentaires
    public function getBudgetStats(Request $request = null)
    {
        $query = Budget::actif();
        
        if ($request) {
            if ($request->filled('annee')) {
                $query->byAnnee($request->annee);
            }
            if ($request->filled('type')) {
                $query->byType($request->type);
            }
        }

        $budgets = $query->get();

        return [
            'total_budgets' => $budgets->count(),
            'total_alloue' => $budgets->sum('montant_alloue'),
            'total_engage' => $budgets->sum('montant_engage'),
            'total_realise' => $budgets->sum('montant_realise'),
            'total_restant' => $budgets->sum('montant_restant'),
            'taux_engagement_moyen' => $budgets->avg('taux_engagement') ?? 0,
            'taux_realisation_moyen' => $budgets->avg('taux_realisation') ?? 0,
            'budgets_alertes' => $budgets->filter(fn($b) => $b->isAlerteEngagement())->count(),
            'budgets_critiques' => $budgets->filter(fn($b) => $b->isAlerteCritique())->count(),
        ];
    }

    public function getBudgetStatsForElement(Budget $budget)
    {
        // Statistiques pour l'élément spécifique
        $elementType = $budget->element_type;
        $elementId = $budget->getAttribute($elementType . '_id');

        $query = Budget::actif();
        
        switch ($elementType) {
            case 'pilier':
                $query->byPilier($elementId);
                break;
            case 'objectif_strategique':
                $query->byObjectifStrategique($elementId);
                break;
            case 'objectif_specifique':
                $query->byObjectifSpecifique($elementId);
                break;
            case 'action':
                $query->byAction($elementId);
                break;
            case 'sous_action':
                $query->bySousAction($elementId);
                break;
        }

        $budgets = $query->get();

        return [
            'total_budgets' => $budgets->count(),
            'total_alloue' => $budgets->sum('montant_alloue'),
            'total_engage' => $budgets->sum('montant_engage'),
            'total_realise' => $budgets->sum('montant_realise'),
            'total_restant' => $budgets->sum('montant_restant'),
            'taux_engagement_moyen' => $budgets->avg('taux_engagement') ?? 0,
            'taux_realisation_moyen' => $budgets->avg('taux_realisation') ?? 0,
        ];
    }

    // API pour les montants
    public function engagerMontant(Request $request, Budget $budget)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0.01',
        ]);

        try {
            if ($budget->engagerMontant($request->montant)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Montant engagé avec succès',
                    'budget' => $budget->fresh()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Montant insuffisant pour cet engagement'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'engagement : ' . $e->getMessage()
            ], 500);
        }
    }

    public function realiserMontant(Request $request, Budget $budget)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0.01',
        ]);

        try {
            if ($budget->realiserMontant($request->montant)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Montant réalisé avec succès',
                    'budget' => $budget->fresh()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Montant insuffisant pour cette réalisation'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réalisation : ' . $e->getMessage()
            ], 500);
        }
    }
}
