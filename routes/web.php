<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PilierController;
use App\Http\Controllers\ObjectifStrategiqueController;
use App\Http\Controllers\ObjectifSpecifiqueController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\SousActionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

// Route d'accueil
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Routes d'authentification (Laravel Breeze ou UI)
Auth::routes();

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reporting', [DashboardController::class, 'reporting'])->name('reporting');
    
    // Routes pour les piliers (Admin général uniquement)
    Route::resource('piliers', PilierController::class);
    
    // Routes pour les objectifs stratégiques
    Route::resource('objectifs-strategiques', ObjectifStrategiqueController::class);
    
    // Routes pour les objectifs spécifiques
    Route::resource('objectifs-specifiques', ObjectifSpecifiqueController::class);
    
    // Routes pour les actions
    Route::resource('actions', ActionController::class);
    
    // Routes pour les sous-actions
    Route::resource('sous-actions', SousActionController::class);
    
    // Route spéciale pour mettre à jour le taux d'avancement des sous-actions (AJAX)
    Route::patch('/sous-actions/{sousAction}/taux-avancement', [SousActionController::class, 'updateTauxAvancement'])
        ->name('sous-actions.update-taux-avancement');
    
    // Routes pour la gestion des utilisateurs (Admin général uniquement)
    Route::resource('users', UserController::class);
    
    // Routes pour les vues hiérarchiques
    Route::get('/hierarchie', function () {
        return view('hierarchie');
    })->name('hierarchie');
    
    Route::get('/tableau-avancement', function () {
        return view('tableau-avancement');
    })->name('tableau-avancement');
});

// Route de déconnexion
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route API pour récupérer tous les piliers avec leurs taux d'avancement
Route::get('/api/piliers', function () {
    $piliers = App\Models\Pilier::with('owner')->get();
    
    // Calculer les taux d'avancement en temps réel pour chaque pilier
    $piliers->each(function ($pilier) {
        $pilier->taux_avancement = $pilier->getTauxAvancementAttribute();
        // S'assurer que le taux est un nombre
        if (!is_numeric($pilier->taux_avancement)) {
            $pilier->taux_avancement = 0;
        }
    });
    
    return response()->json($piliers);
});

// Routes API pour la navigation hiérarchique
Route::get('/api/piliers/{pilier}/objectifs-strategiques', function (App\Models\Pilier $pilier) {
    \Log::info('🚀 [DEBUG] API appelée pour pilier:', ['pilier_id' => $pilier->id, 'pilier_code' => $pilier->code]);
    
    try {
        $objectifsStrategiques = $pilier->objectifsStrategiques()->with('owner')->get();
        \Log::info('📊 [DEBUG] Objectifs stratégiques trouvés:', ['count' => $objectifsStrategiques->count()]);
        
        // Calculer les taux d'avancement en temps réel pour chaque objectif stratégique
        $objectifsStrategiques->each(function ($objectif) {
            $objectif->taux_avancement = $objectif->getTauxAvancementAttribute();
            // S'assurer que le taux est un nombre
            if (!is_numeric($objectif->taux_avancement)) {
                $objectif->taux_avancement = 0;
            }
            \Log::info('📈 [DEBUG] Taux calculé pour OS:', ['os_id' => $objectif->id, 'taux' => $objectif->taux_avancement]);
        });
        
        $response = [
            'objectifs_strategiques' => $objectifsStrategiques,
            'pilier_code' => $pilier->code,
            'pilier_libelle' => $pilier->libelle,
            'pilier_taux_avancement' => $pilier->taux_avancement
        ];
        
        \Log::info('✅ [DEBUG] Réponse préparée:', $response);
        
        return response()->json($response);
    } catch (\Exception $e) {
        \Log::error('💥 [DEBUG] Erreur dans l\'API:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        
        return response()->json([
            'error' => 'Erreur lors du chargement des données: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/api/objectifs-strategiques/{objectifStrategique}/objectifs-specifiques', function (App\Models\ObjectifStrategique $objectifStrategique) {
    $objectifsSpecifiques = $objectifStrategique->objectifsSpecifiques()->with('owner')->get();
    $pilier = $objectifStrategique->pilier()->with('owner')->first();
    
    // Calculer les taux d'avancement en temps réel pour chaque objectif spécifique
    $objectifsSpecifiques->each(function ($objectif) {
        $objectif->taux_avancement = $objectif->getTauxAvancementAttribute();
        // S'assurer que le taux est un nombre
        if (!is_numeric($objectif->taux_avancement)) {
            $objectif->taux_avancement = 0;
        }
    });
    
    return response()->json([
        'objectifs_specifiques' => $objectifsSpecifiques,
        'taux_avancement' => $objectifStrategique->taux_avancement,
        'objectif_strategique_owner' => $objectifStrategique->owner ? $objectifStrategique->owner->name : null,
        'pilier_code' => $pilier->code,
        'pilier_libelle' => $pilier->libelle,
        'pilier_taux_avancement' => $pilier->taux_avancement,
        'pilier_owner' => $pilier->owner ? $pilier->owner->name : null
    ]);
});

Route::get('/api/objectifs-specifiques/{objectifSpecifique}/actions', function (App\Models\ObjectifSpecifique $objectifSpecifique) {
    $actions = $objectifSpecifique->actions()->with('owner')->get();
    $objectifStrategique = $objectifSpecifique->objectifStrategique()->with('owner')->first();
    $pilier = $objectifStrategique->pilier()->with('owner')->first();
    
    // Calculer les taux d'avancement en temps réel pour chaque action
    $actions->each(function ($action) {
        $action->taux_avancement = $action->getTauxAvancementAttribute();
        // S'assurer que le taux est un nombre
        if (!is_numeric($action->taux_avancement)) {
            $action->taux_avancement = 0;
        }
    });
    
    return response()->json([
        'actions' => $actions,
        'taux_avancement' => $objectifSpecifique->taux_avancement,
        'objectif_specifique_owner' => $objectifSpecifique->owner ? $objectifSpecifique->owner->name : null,
        'pilier_code' => $pilier->code,
        'pilier_libelle' => $pilier->libelle,
        'pilier_taux_avancement' => $pilier->taux_avancement,
        'pilier_owner' => $pilier->owner ? $pilier->owner->name : null,
        'objectif_strategique_code' => $objectifStrategique->code,
        'objectif_strategique_libelle' => $objectifStrategique->libelle,
        'objectif_strategique_taux_avancement' => $objectifStrategique->taux_avancement,
        'objectif_strategique_owner' => $objectifStrategique->owner ? $objectifStrategique->owner->name : null
    ]);
});

Route::get('/api/actions/{action}/sous-actions', function (App\Models\Action $action) {
    $sousActions = $action->sousActions()->with('owner')->get();
    $objectifSpecifique = $action->objectifSpecifique()->with('owner')->first();
    $objectifStrategique = $objectifSpecifique->objectifStrategique()->with('owner')->first();
    $pilier = $objectifStrategique->pilier()->with('owner')->first();
    
    return response()->json([
        'sous_actions' => $sousActions,
        'taux_avancement' => $action->taux_avancement,
        'action_owner' => $action->owner ? $action->owner->name : null,
        'pilier_code' => $pilier->code,
        'pilier_libelle' => $pilier->libelle,
        'pilier_taux_avancement' => $pilier->taux_avancement,
        'pilier_owner' => $pilier->owner ? $pilier->owner->name : null,
        'objectif_strategique_code' => $objectifStrategique->code,
        'objectif_strategique_libelle' => $objectifStrategique->libelle,
        'objectif_strategique_taux_avancement' => $objectifStrategique->taux_avancement,
        'objectif_strategique_owner' => $objectifStrategique->owner ? $objectifStrategique->owner->name : null,
        'objectif_specifique_code' => $objectifSpecifique->code,
        'objectif_specifique_libelle' => $objectifSpecifique->libelle,
        'objectif_specifique_taux_avancement' => $objectifSpecifique->taux_avancement,
        'objectif_specifique_owner' => $objectifSpecifique->owner ? $objectifSpecifique->owner->name : null
    ]);
});

// Routes pour les suggestions de codes
Route::get('/api/objectifs-specifiques/codes/{objectifStrategiqueId}', function ($objectifStrategiqueId) {
    // Récupérer tous les codes d'objectifs spécifiques dans toute la base de données pour vérifier l'unicité globale
    $codes = App\Models\ObjectifSpecifique::pluck('code')->toArray();
    return response()->json(['codes' => $codes]);
});

Route::get('/api/actions/codes/{objectifSpecifiqueId}', function ($objectifSpecifiqueId) {
    $codes = App\Models\Action::where('objectif_specifique_id', $objectifSpecifiqueId)
        ->pluck('code')
        ->toArray();
    return response()->json(['codes' => $codes]);
});

Route::get('/api/sous-actions/codes/{actionId}', function ($actionId) {
    $codes = App\Models\SousAction::where('action_id', $actionId)
        ->pluck('code')
        ->toArray();
    return response()->json(['codes' => $codes]);
});

// Routes pour les suggestions de codes d'objectifs stratégiques
Route::get('/api/objectifs-strategiques/codes/{pilierId}', function ($pilierId) {
    $codes = App\Models\ObjectifStrategique::where('pilier_id', $pilierId)
        ->pluck('code')
        ->toArray();
    return response()->json(['codes' => $codes]);
});

// Routes pour récupérer un objectif stratégique
Route::get('/api/objectifs-strategiques/{objectifStrategique}', function ($objectifStrategique) {
    $objectifStrategique = App\Models\ObjectifStrategique::with(['owner', 'pilier'])->findOrFail($objectifStrategique);
    return response()->json([
        'objectif_strategique' => $objectifStrategique
    ]);
});

// Routes pour récupérer un objectif spécifique
Route::get('/api/objectifs-specifiques/{objectifSpecifique}', function ($objectifSpecifique) {
    $objectifSpecifique = App\Models\ObjectifSpecifique::with(['owner', 'objectifStrategique'])->findOrFail($objectifSpecifique);
    return response()->json([
        'objectif_specifique' => $objectifSpecifique
    ]);
});

// Routes pour créer des objectifs stratégiques via API
Route::post('/api/objectifs-strategiques', function (Request $request) {
    $request->validate([
        'code' => 'required|string|max:10',
        'libelle' => 'required|string|max:255',
        'description' => 'nullable|string',
        'pilier_id' => 'required|exists:piliers,id',
        'owner_id' => 'nullable|exists:users,id',
    ]);

    try {
        $objectifStrategique = App\Models\ObjectifStrategique::create([
            'code' => $request->code,
            'libelle' => $request->libelle,
            'description' => $request->description,
            'pilier_id' => $request->pilier_id,
            'owner_id' => $request->owner_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Objectif stratégique créé avec succès !',
            'objectif_strategique' => $objectifStrategique
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la création : ' . $e->getMessage()
        ], 500);
    }
});

// Routes pour créer des objectifs spécifiques via API
Route::post('/api/objectifs-specifiques', function (Request $request) {
    $request->validate([
        'code' => 'required|string|max:10|unique:objectif_specifiques,code',
        'libelle' => 'required|string|max:255',
        'description' => 'nullable|string',
        'objectif_strategique_id' => 'required|exists:objectif_strategiques,id',
        'owner_id' => 'nullable|exists:users,id',
    ]);

    try {
        $objectifSpecifique = App\Models\ObjectifSpecifique::create([
            'code' => $request->code,
            'libelle' => $request->libelle,
            'description' => $request->description,
            'objectif_strategique_id' => $request->objectif_strategique_id,
            'owner_id' => $request->owner_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Objectif spécifique créé avec succès !',
            'objectif_specifique' => $objectifSpecifique
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la création : ' . $e->getMessage()
        ], 500);
    }
});

// Routes pour modifier des objectifs stratégiques via API
Route::put('/api/objectifs-strategiques/{objectifStrategique}', function (Request $request, $objectifStrategique) {
    $objectifStrategique = App\Models\ObjectifStrategique::findOrFail($objectifStrategique);
    
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

        return response()->json([
            'success' => true,
            'message' => 'Objectif stratégique modifié avec succès !',
            'objectif_strategique' => $objectifStrategique
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la modification : ' . $e->getMessage()
        ], 500);
    }
});

// Routes pour modifier des objectifs spécifiques via API
Route::put('/api/objectifs-specifiques/{objectifSpecifique}', function (Request $request, $objectifSpecifique) {
    $objectifSpecifique = App\Models\ObjectifSpecifique::findOrFail($objectifSpecifique);
    
    $request->validate([
        'code' => 'required|string|max:10|unique:objectif_specifiques,code,' . $objectifSpecifique->id,
        'libelle' => 'required|string|max:255',
        'description' => 'nullable|string',
        'objectif_strategique_id' => 'required|exists:objectif_strategiques,id',
        'owner_id' => 'nullable|exists:users,id',
    ]);

    try {
        $objectifSpecifique->update([
            'code' => $request->code,
            'libelle' => $request->libelle,
            'description' => $request->description,
            'objectif_strategique_id' => $request->objectif_strategique_id,
            'owner_id' => $request->owner_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Objectif spécifique modifié avec succès !',
            'objectif_specifique' => $objectifSpecifique
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la modification : ' . $e->getMessage()
        ], 500);
    }
});

// Routes pour créer des actions via API
Route::post('/api/actions', function (Request $request) {
    $request->validate([
        'code' => 'required|string|max:10|unique:actions,code',
        'libelle' => 'required|string|max:255',
        'description' => 'nullable|string',
        'objectif_specifique_id' => 'required|exists:objectif_specifiques,id',
        'owner_id' => 'nullable|exists:users,id',
    ]);

    try {
        $action = App\Models\Action::create([
            'code' => $request->code,
            'libelle' => $request->libelle,
            'description' => $request->description,
            'objectif_specifique_id' => $request->objectif_specifique_id,
            'owner_id' => $request->owner_id,
            'taux_avancement' => 0,
            'actif' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Action créée avec succès !',
            'action' => $action
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la création : ' . $e->getMessage()
        ], 500);
    }
});

// Routes pour modifier des actions via API
Route::put('/api/actions/{action}', function (Request $request, $action) {
    $action = App\Models\Action::findOrFail($action);
    
    $request->validate([
        'code' => 'required|string|max:10|unique:actions,code,' . $action->id,
        'libelle' => 'required|string|max:255',
        'description' => 'nullable|string',
        'objectif_specifique_id' => 'required|exists:objectif_specifiques,id',
        'owner_id' => 'nullable|exists:users,id',
    ]);

    try {
        $action->update([
            'code' => $request->code,
            'libelle' => $request->libelle,
            'description' => $request->description,
            'objectif_specifique_id' => $request->objectif_specifique_id,
            'owner_id' => $request->owner_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Action modifiée avec succès !',
            'action' => $action
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la modification : ' . $e->getMessage()
        ], 500);
    }
});

// Routes pour créer des sous-actions via API
Route::post('/api/sous-actions', function (Request $request) {
    \Log::info('🚀 [DEBUG] Création de sous-action - Début');
    \log::info('📋 [DEBUG] Données reçues:', $request->all());
    
    $request->validate([
        'code' => 'required|string|max:10',
        'libelle' => 'required|string|max:255',
        'description' => 'nullable|string',
        'taux_avancement' => 'required|numeric|min:0|max:100',
        'action_id' => 'required|exists:actions,id',
        'owner_id' => 'nullable|exists:users,id',
        'date_echeance' => 'nullable|date',
    ]);

    try {
        \Log::info('✅ [DEBUG] Validation réussie, création de la sous-action...');
        
        $sousAction = App\Models\SousAction::create([
            'code' => $request->code,
            'libelle' => $request->libelle,
            'description' => $request->description,
            'taux_avancement' => $request->taux_avancement,
            'action_id' => $request->action_id,
            'owner_id' => $request->owner_id,
            'date_echeance' => $request->date_echeance,
            'actif' => true,
        ]);

        \Log::info('✅ [DEBUG] Sous-action créée avec succès:', $sousAction->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Sous-action créée avec succès !',
            'sous_action' => $sousAction
        ]);
    } catch (\Exception $e) {
        \Log::error('💥 [DEBUG] Erreur lors de la création:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la création : ' . $e->getMessage()
        ], 500);
    }
});

// Routes pour modifier des sous-actions via API
Route::put('/api/sous-actions/{sousAction}', function (Request $request, $sousAction) {
    $sousAction = App\Models\SousAction::findOrFail($sousAction);
    
    $request->validate([
        'code' => 'required|string|max:10',
        'libelle' => 'required|string|max:255',
        'description' => 'nullable|string',
        'taux_avancement' => 'required|numeric|min:0|max:100',
        'action_id' => 'required|exists:actions,id',
        'owner_id' => 'nullable|exists:users,id',
        'date_echeance' => 'nullable|date',
    ]);

    try {
        $sousAction->update([
            'code' => $request->code,
            'libelle' => $request->libelle,
            'description' => $request->description,
            'taux_avancement' => $request->taux_avancement,
            'action_id' => $request->action_id,
            'owner_id' => $request->owner_id,
            'date_echeance' => $request->date_echeance,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sous-action modifiée avec succès !',
            'sous_action' => $sousAction
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la modification : ' . $e->getMessage()
        ], 500);
    }
});

// Route pour récupérer les détails d'une action
Route::get('/api/actions/{action}', function (App\Models\Action $action) {
    return response()->json([
        'action' => [
            'id' => $action->id,
            'code' => $action->code,
            'libelle' => $action->libelle,
            'description' => $action->description,
            'objectif_specifique_id' => $action->objectif_specifique_id,
            'owner_id' => $action->owner_id,
            'owner' => $action->owner ? $action->owner->name : null,
            'taux_avancement' => $action->taux_avancement,
        ]
    ]);
});

// Route pour récupérer les détails d'une sous-action
Route::get('/api/sous-actions/{sousAction}', function (App\Models\SousAction $sousAction) {
    return response()->json([
        'sous_action' => [
            'id' => $sousAction->id,
            'code' => $sousAction->code,
            'libelle' => $sousAction->libelle,
            'description' => $sousAction->description,
            'action_id' => $sousAction->action_id,
            'owner_id' => $sousAction->owner_id,
            'owner' => $sousAction->owner ? $sousAction->owner->name : null,
            'taux_avancement' => $sousAction->taux_avancement,
            'date_echeance' => $sousAction->date_echeance,
        ]
    ]);
});

// Route pour la vue générale
Route::get('/api/vue-generale', function () {
    try {
        $piliers = App\Models\Pilier::with([
            'objectifsStrategiques.objectifsSpecifiques.actions.sousActions.owner',
            'objectifsStrategiques.owner',
            'objectifsStrategiques.objectifsSpecifiques.owner',
            'objectifsStrategiques.objectifsSpecifiques.actions.owner',
            'owner'
        ])->get();
        
        $data = [];
        
        foreach ($piliers as $pilier) {
            $pilierData = [
                'id' => $pilier->id,
                'code' => $pilier->code,
                'libelle' => $pilier->libelle,
                'taux_avancement' => $pilier->taux_avancement,
                'owner' => $pilier->owner ? $pilier->owner->name : null,
                'objectifs_strategiques' => []
            ];
            
            foreach ($pilier->objectifsStrategiques as $os) {
                $osData = [
                    'id' => $os->id,
                    'code' => $os->code,
                    'libelle' => $os->libelle,
                    'taux_avancement' => $os->taux_avancement,
                    'owner' => $os->owner ? $os->owner->name : null,
                    'objectifs_specifiques' => []
                ];
                
                foreach ($os->objectifsSpecifiques as $osp) {
                    $ospData = [
                        'id' => $osp->id,
                        'code' => $osp->code,
                        'libelle' => $osp->libelle,
                        'taux_avancement' => $osp->taux_avancement,
                        'owner' => $osp->owner ? $osp->owner->name : null,
                        'actions' => []
                    ];
                    
                    foreach ($osp->actions as $action) {
                        $actionData = [
                            'id' => $action->id,
                            'code' => $action->code,
                            'libelle' => $action->libelle,
                            'taux_avancement' => $action->taux_avancement,
                            'owner' => $action->owner ? $action->owner->name : null,
                            'sous_actions' => []
                        ];
                        
                        foreach ($action->sousActions as $sousAction) {
                            $actionData['sous_actions'][] = [
                                'id' => $sousAction->id,
                                'code' => $sousAction->code,
                                'libelle' => $sousAction->libelle,
                                'taux_avancement' => $sousAction->taux_avancement,
                                'date_echeance' => $sousAction->date_echeance,
                                'owner' => $sousAction->owner ? $sousAction->owner->name : null,
                            ];
                        }
                        
                        $ospData['actions'][] = $actionData;
                    }
                    
                    $osData['objectifs_specifiques'][] = $ospData;
                }
                
                $pilierData['objectifs_strategiques'][] = $osData;
            }
            
            $data['piliers'][] = $pilierData;
        }
        
        return response()->json($data);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erreur lors du chargement de la vue générale: ' . $e->getMessage()  
        ], 500);
    }
});

// Routes pour récupérer les taux d'avancement en temps réel
Route::get('/api/piliers/{pilier}/taux-avancement', function (App\Models\Pilier $pilier) {
    return response()->json([
        'success' => true,
        'taux' => $pilier->taux_avancement
    ]);
});

Route::get('/api/objectifs-strategiques/{objectifStrategique}/taux-avancement', function (App\Models\ObjectifStrategique $objectifStrategique) {
    return response()->json([
        'success' => true,
        'taux' => $objectifStrategique->taux_avancement
    ]);
});

Route::get('/api/objectifs-specifiques/{objectifSpecifique}/taux-avancement', function (App\Models\ObjectifSpecifique $objectifSpecifique) {
    return response()->json([
        'success' => true,
        'taux' => $objectifSpecifique->taux_avancement
    ]);
});

Route::get('/api/actions/{action}/taux-avancement', function (App\Models\Action $action) {
    return response()->json([
        'success' => true,
        'taux' => $action->taux_avancement
    ]);
});

// Route pour mettre à jour le taux d'avancement d'une sous-action et calculer les taux parents en temps réel
Route::put('/api/sous-actions/{sousAction}/taux-avancement', function (Request $request, $sousAction) {
    \Log::info('🔄 [DEBUG] Mise à jour du taux de sous-action - Début', [
        'sous_action_id' => $sousAction, 
        'nouveau_taux' => $request->taux_avancement,
        'timestamp' => now()
    ]);
    
    try {
        $sousAction = App\Models\SousAction::findOrFail($sousAction);
        \Log::info('✅ [DEBUG] Sous-action trouvée', [
            'sous_action_id' => $sousAction->id,
            'ancien_taux' => $sousAction->taux_avancement,
            'nouveau_taux' => $request->taux_avancement,
            'action_id' => $sousAction->action_id
        ]);
        
        // Mettre à jour le taux de la sous-action
        $sousAction->update(['taux_avancement' => $request->taux_avancement]);
        \Log::info('✅ [DEBUG] Taux de sous-action mis à jour en BD', [
            'sous_action_id' => $sousAction->id,
            'taux_apres_update' => $sousAction->fresh()->taux_avancement
        ]);
        
        // Récupérer l'action parent et calculer son taux en temps réel
        $action = $sousAction->action;
        if (!$action) {
            throw new Exception('Action parent non trouvée');
        }
        
        $actionTaux = $action->taux_avancement; // Utilise l'accesseur calculé
        \Log::info('📊 [DEBUG] Taux d\'action calculé en temps réel', [
            'action_id' => $action->id,
            'action_code' => $action->code,
            'taux_action' => $actionTaux
        ]);
        
        // Récupérer l'objectif spécifique parent et calculer son taux en temps réel
        $objectifSpecifique = $action->objectifSpecifique;
        if (!$objectifSpecifique) {
            throw new Exception('Objectif spécifique parent non trouvé');
        }
        
        $objectifSpecifiqueTaux = $objectifSpecifique->taux_avancement; // Utilise l'accesseur calculé
        \Log::info('📊 [DEBUG] Taux d\'objectif spécifique calculé en temps réel', [
            'objectif_specifique_id' => $objectifSpecifique->id,
            'objectif_specifique_code' => $objectifSpecifique->code,
            'taux_objectif_specifique' => $objectifSpecifiqueTaux
        ]);
        
        // Récupérer l'objectif stratégique parent et calculer son taux en temps réel
        $objectifStrategique = $objectifSpecifique->objectifStrategique;
        if (!$objectifStrategique) {
            throw new Exception('Objectif stratégique parent non trouvé');
        }
        
        $objectifStrategiqueTaux = $objectifStrategique->taux_avancement; // Utilise l'accesseur calculé
        \Log::info('📊 [DEBUG] Taux d\'objectif stratégique calculé en temps réel', [
            'objectif_strategique_id' => $objectifStrategique->id,
            'objectif_strategique_code' => $objectifStrategique->code,
            'taux_objectif_strategique' => $objectifStrategiqueTaux
        ]);
        
        // Récupérer le pilier parent et calculer son taux en temps réel
        $pilier = $objectifStrategique->pilier;
        if (!$pilier) {
            throw new Exception('Pilier parent non trouvé');
        }
        
        $pilierTaux = $pilier->taux_avancement; // Utilise l'accesseur calculé
        \Log::info('📊 [DEBUG] Taux de pilier calculé en temps réel', [
            'pilier_id' => $pilier->id,
            'pilier_code' => $pilier->code,
            'taux_pilier' => $pilierTaux
        ]);
        
        \Log::info('🎉 [DEBUG] Tous les calculs terminés avec succès', [
            'resume' => [
                'sous_action' => $sousAction->taux_avancement,
                'action' => $actionTaux,
                'objectif_specifique' => $objectifSpecifiqueTaux,
                'objectif_strategique' => $objectifStrategiqueTaux,
                'pilier' => $pilierTaux
            ]
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Taux d\'avancement mis à jour avec succès !',
            'updated_taux' => [
                'sous_action' => $sousAction->taux_avancement,
                'action' => $actionTaux,
                'objectif_specifique' => $objectifSpecifiqueTaux,
                'objectif_strategique' => $objectifStrategiqueTaux,
                'pilier' => $pilierTaux
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('💥 [DEBUG] Erreur lors de la mise à jour du taux:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
        ], 500);
    }
});
