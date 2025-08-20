<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\SousAction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ActivityController extends Controller
{
    /**
     * Afficher la page de gestion des activités pour une sous-action
     */
    public function manage(SousAction $sousAction)
    {
        // Vérifier que la sous-action est de type projet
        if ($sousAction->type !== 'projet') {
            abort(404, 'Cette sous-action ne supporte pas la gestion d\'activités');
        }

        // Charger la sous-action avec toutes ses relations pour le titre hiérarchique
        $sousAction->load([
            'action.objectifSpecifique.objectifStrategique.pilier',
            'action.objectifSpecifique.objectifStrategique',
            'action.objectifSpecifique',
            'action'
        ]);

        // Charger les activités avec leurs relations
        $activities = $sousAction->activities()
            ->with(['owner'])
            ->orderBy('date_debut')
            ->get();

        // Charger tous les utilisateurs pour l'assignation
        $users = User::orderBy('name')->get();

        // Calculer les statistiques
        $stats = [
            'total' => $activities->count(),
            'en_attente' => $activities->where('statut', 'en_attente')->count(),
            'en_cours' => $activities->where('statut', 'en_cours')->count(),
            'termine' => $activities->where('statut', 'termine')->count(),
            'bloque' => $activities->where('statut', 'bloque')->count(),
            'en_retard' => $activities->filter(function($activity) {
                return $activity->est_en_retard && $activity->statut !== 'termine';
            })->count(),
        ];

        return view('activities.manage', compact('sousAction', 'activities', 'users', 'stats'));
    }

    /**
     * Créer une nouvelle activité
     */
         public function store(Request $request)
     {
         // Logs de débogage
         Log::info('🚀 [ACTIVITY] Début de création d\'activité', [
             'request_all' => $request->all(),
             'request_headers' => $request->headers->all(),
             'method' => $request->method(),
             'url' => $request->url(),
             'user_id' => Auth::id(),
             'timestamp' => now(),
             'sous_action_id' => $request->input('sous_action_id'),
             'titre' => $request->input('titre'),
             'date_debut' => $request->input('date_debut'),
             'date_fin' => $request->input('date_fin')
         ]);

                   try {
              Log::info('🔍 [ACTIVITY] Début de validation des données', [
                  'step' => 'validation_start',
                  'timestamp' => now()
              ]);

              $request->validate([
                  'titre' => 'required|string|max:255',
                  'description' => 'nullable|string',
                  'date_debut' => 'required|date',
                  'date_fin' => 'required|date|after:date_debut',
                  'priorite' => 'required|in:basse,moyenne,haute,critique',
                  'owner_id' => 'nullable|exists:users,id',
                  'notes' => 'nullable|string',
                  'sous_action_id' => 'required|exists:sous_actions,id',
              ]);

                                           Log::info('✅ [ACTIVITY] Validation réussie', [
                  'step' => 'validation_success',
                  'validated_data' => $request->all()
              ]);

              Log::info('🔄 [ACTIVITY] Début de la transaction', [
                  'step' => 'transaction_start',
                  'timestamp' => now()
              ]);

              DB::beginTransaction();

                         // Vérifier que la date de fin ne dépasse pas l'échéance de la sous-action
             $sousAction = SousAction::find($request->sous_action_id);
             
             if (!$sousAction) {
                 Log::error('❌ [ACTIVITY] Sous-action non trouvée', [
                     'sous_action_id' => $request->sous_action_id
                 ]);
                 return response()->json([
                     'success' => false,
                     'message' => 'Sous-action non trouvée'
                 ], 404);
             }
             
             Log::info('🔍 [ACTIVITY] Vérification de l\'échéance', [
                 'sous_action_id' => $request->sous_action_id,
                 'sous_action_echeance' => $sousAction->date_echeance,
                 'activity_date_fin' => $request->date_fin,
                 'comparison' => $sousAction->date_echeance ? Carbon::parse($request->date_fin)->gt(Carbon::parse($sousAction->date_echeance)) : 'N/A'
             ]);

             if ($sousAction->date_echeance && Carbon::parse($request->date_fin)->gt(Carbon::parse($sousAction->date_echeance))) {
                 Log::warning('⚠️ [ACTIVITY] Date de fin dépasse l\'échéance', [
                     'activity_date_fin' => $request->date_fin,
                     'sous_action_echeance' => $sousAction->date_echeance
                 ]);
                 return response()->json([
                     'success' => false,
                     'message' => 'La date de fin de l\'activité ne peut pas dépasser l\'échéance de la sous-action (' . Carbon::parse($sousAction->date_echeance)->format('d/m/Y') . ')'
                 ], 422);
             }

             Log::info('📝 [ACTIVITY] Création de l\'activité en base', [
                 'data_to_create' => [
                     'sous_action_id' => $request->sous_action_id,
                     'titre' => $request->titre,
                     'description' => $request->description,
                     'date_debut' => $request->date_debut,
                     'date_fin' => $request->date_fin,
                     'priorite' => $request->priorite,
                     'statut' => $request->statut,
                     'taux_avancement' => $request->taux_avancement,
                     'owner_id' => $request->owner_id ?: Auth::id(),
                     'notes' => $request->notes,
                 ]
             ]);

                           $activityData = [
                  'sous_action_id' => $request->sous_action_id,
                  'titre' => $request->titre,
                  'description' => $request->description,
                  'date_debut' => Carbon::parse($request->date_debut),
                  'date_fin' => Carbon::parse($request->date_fin),
                  'priorite' => $request->priorite,
                  'statut' => 'en_attente', // Valeur par défaut
                  'taux_avancement' => 0, // Valeur par défaut
                  'owner_id' => $request->owner_id ?: Auth::id(),
                  'notes' => $request->notes,
              ];

              // Déterminer le statut automatiquement selon la date de début
              $dateDebut = Carbon::parse($request->date_debut);
              $aujourdhui = Carbon::today();
              
              if ($dateDebut->lte($aujourdhui)) {
                  $activityData['statut'] = 'en_cours';
              }

              Log::info('📝 [ACTIVITY] Données préparées pour création', [
                  'step' => 'data_preparation',
                  'activity_data' => $activityData,
                  'statut_defaut' => 'en_attente',
                  'taux_defaut' => 0
              ]);
             
             Log::info('📝 [ACTIVITY] Tentative de création avec les données', [
                 'activity_data' => $activityData
             ]);
             
                           Log::info('🚀 [ACTIVITY] Tentative de création de l\'activité en base', [
                  'step' => 'activity_creation_start',
                  'timestamp' => now()
              ]);

              $activity = Activity::create($activityData);

              Log::info('✅ [ACTIVITY] Activité créée avec succès', [
                  'step' => 'activity_creation_success',
                  'activity_id' => $activity->id,
                  'activity_data' => $activity->toArray()
              ]);

                         // Recalculer le taux d'avancement de la sous-action
             Log::info('🔄 [ACTIVITY] Recalcul du taux d\'avancement de la sous-action', [
                 'sous_action_id' => $request->sous_action_id,
                 'ancien_taux' => $sousAction->taux_avancement
             ]);

                           try {
                  Log::info('🔄 [ACTIVITY] Début du recalcul du taux d\'avancement', [
                      'sous_action_id' => $request->sous_action_id,
                      'type' => $sousAction->type,
                      'nombre_activites' => $sousAction->activities()->count()
                  ]);
                  
                  $sousAction->recalculerTauxAvancement();
                  
                  Log::info('✅ [ACTIVITY] Taux d\'avancement recalculé', [
                      'sous_action_id' => $request->sous_action_id,
                      'nouveau_taux' => $sousAction->fresh()->taux_avancement
                  ]);
              } catch (\Exception $e) {
                  Log::warning('⚠️ [ACTIVITY] Erreur lors du recalcul du taux d\'avancement', [
                      'error' => $e->getMessage(),
                      'error_file' => $e->getFile(),
                      'error_line' => $e->getLine(),
                      'sous_action_id' => $request->sous_action_id
                  ]);
                  // On continue malgré l'erreur de recalcul
              }

                                       Log::info('🔄 [ACTIVITY] Tentative de commit de la transaction', [
                  'step' => 'transaction_commit_start',
                  'timestamp' => now()
              ]);

              DB::commit();

              Log::info('🎉 [ACTIVITY] Transaction validée, réponse envoyée', [
                  'step' => 'transaction_commit_success',
                  'response_data' => [
                      'success' => true,
                      'message' => 'Activité créée avec succès',
                      'activity_id' => $activity->id
                  ]
              ]);

                           Log::info('📤 [ACTIVITY] Préparation de la réponse JSON', [
                  'step' => 'response_preparation',
                  'timestamp' => now()
              ]);

              $response = response()->json([
                  'success' => true,
                  'message' => 'Activité créée avec succès',
                  'activity' => $activity->load('owner')
              ]);

              Log::info('🎯 [ACTIVITY] Réponse JSON préparée, envoi...', [
                  'step' => 'response_send',
                  'timestamp' => now()
              ]);

              return $response;

                 } catch (\Exception $e) {
             Log::error('💥 [ACTIVITY] Erreur lors de la création', [
                 'error_message' => $e->getMessage(),
                 'error_file' => $e->getFile(),
                 'error_line' => $e->getLine(),
                 'error_trace' => $e->getTraceAsString(),
                 'request_data' => $request->all(),
                 'user_id' => Auth::id(),
                 'timestamp' => now()
             ]);

             DB::rollBack();
             return response()->json([
                 'success' => false,
                 'message' => 'Erreur lors de la création de l\'activité: ' . $e->getMessage()
             ], 500);
         }
    }

    /**
     * Afficher le formulaire d'édition d'une activité
     */
    public function edit(Activity $activity)
    {
        return response()->json([
            'success' => true,
            'activity' => $activity->load('owner')
        ]);
    }

         /**
      * Mettre à jour une activité
      */
     public function update(Request $request, Activity $activity)
     {
         try {
             Log::info('🔄 [ACTIVITY] Début update', [
                 'activity_id' => $activity->id,
                 'request_data' => $request->all(),
                 'timestamp' => now()
             ]);
 
             $request->validate([
                 'titre' => 'required|string|max:255',
                 'description' => 'nullable|string',
                 'date_debut' => 'required|date',
                 'date_fin' => 'required|date|after:date_debut',
                 'priorite' => 'required|in:basse,moyenne,haute,critique',
                 'statut' => 'required|in:en_attente,en_cours,termine,bloque',
                 'taux_avancement' => 'required|numeric|min:0|max:100',
                 'owner_id' => 'nullable|exists:users,id',
                 'notes' => 'nullable|string',
             ]);
 
             DB::beginTransaction();
 
             // Vérifier que la date de fin ne dépasse pas l'échéance de la sous-action
             $sousAction = $activity->sousAction;
             if ($sousAction->date_echeance && Carbon::parse($request->date_fin)->gt(Carbon::parse($sousAction->date_echeance))) {
                 Log::warning('⚠️ [ACTIVITY] Date de fin dépasse l\'échéance lors de l\'édition', [
                     'activity_id' => $activity->id,
                     'date_fin' => $request->date_fin,
                     'echeance' => $sousAction->date_echeance
                 ]);
                 return response()->json([
                     'success' => false,
                     'message' => 'La date de fin de l\'activité ne peut pas dépasser l\'échéance de la sous-action (' . Carbon::parse($sousAction->date_echeance)->format('d/m/Y') . ')'
                 ], 422);
             }
 
             // Utiliser DB::table pour éviter les événements Eloquent
             DB::table('activities')
                 ->where('id', $activity->id)
                 ->update([
                     'titre' => $request->titre,
                     'description' => $request->description,
                     'date_debut' => Carbon::parse($request->date_debut),
                     'date_fin' => Carbon::parse($request->date_fin),
                     'priorite' => $request->priorite,
                     'statut' => $request->statut,
                     'taux_avancement' => $request->taux_avancement,
                     'owner_id' => $request->owner_id ?: Auth::id(),
                     'notes' => $request->notes,
                 ]);
 
             // Mettre à jour l'instance locale
             $activity->refresh();
 
             // Recalculer le taux d'avancement de la sous-action
             try {
                 $sousAction->recalculerTauxAvancement();
                 $sousAction->refresh();
                 
                 Log::info('✅ [ACTIVITY] Taux de la sous-action recalculé après édition', [
                     'sous_action_id' => $sousAction->id,
                     'nouveau_taux' => $sousAction->taux_avancement
                 ]);
             } catch (\Exception $e) {
                 Log::warning('⚠️ [ACTIVITY] Erreur lors du recalcul de la sous-action après édition', [
                     'error' => $e->getMessage(),
                     'sous_action_id' => $sousAction->id
                 ]);
                 // On continue malgré l'erreur de recalcul
             }
 
             DB::commit();
 
             Log::info('✅ [ACTIVITY] Activité mise à jour avec succès', [
                 'activity_id' => $activity->id,
                 'nouveau_titre' => $request->titre
             ]);
 
             return response()->json([
                 'success' => true,
                 'message' => 'Activité mise à jour avec succès',
                 'activity' => $activity->load('owner'),
                 'sous_action' => [
                     'id' => $sousAction->id,
                     'taux_avancement' => $sousAction->taux_avancement
                 ]
             ]);
 
         } catch (\Exception $e) {
             DB::rollBack();
             Log::error('💥 [ACTIVITY] Erreur lors de la mise à jour de l\'activité', [
                 'activity_id' => $activity->id,
                 'error' => $e->getMessage(),
                 'file' => $e->getFile(),
                 'line' => $e->getLine(),
                 'trace' => $e->getTraceAsString()
             ]);
             
             return response()->json([
                 'success' => false,
                 'message' => 'Erreur lors de la mise à jour de l\'activité: ' . $e->getMessage()
             ], 500);
         }
     }

         /**
      * Supprimer une activité
      */
     public function destroy(Activity $activity)
     {
         try {
             Log::info('🗑️ [ACTIVITY] Début suppression', [
                 'activity_id' => $activity->id,
                 'activity_titre' => $activity->titre,
                 'sous_action_id' => $activity->sous_action_id,
                 'timestamp' => now()
             ]);
 
             DB::beginTransaction();
 
             $sousAction = $activity->sousAction;
             $ancienTaux = $sousAction->taux_avancement;
             
             // Sauvegarder les informations avant suppression
             $activityInfo = [
                 'id' => $activity->id,
                 'titre' => $activity->titre,
                 'taux_avancement' => $activity->taux_avancement
             ];
             
             // Supprimer l'activité
             $activity->delete();
 
             Log::info('✅ [ACTIVITY] Activité supprimée de la base', [
                 'activity_id' => $activityInfo['id'],
                 'sous_action_id' => $sousAction->id
             ]);
 
             // Recalculer le taux d'avancement de la sous-action
             try {
                 $sousAction->recalculerTauxAvancement();
                 $sousAction->refresh();
                 
                 Log::info('✅ [ACTIVITY] Taux de la sous-action recalculé après suppression', [
                     'sous_action_id' => $sousAction->id,
                     'ancien_taux' => $ancienTaux,
                     'nouveau_taux' => $sousAction->taux_avancement
                 ]);
             } catch (\Exception $e) {
                 Log::warning('⚠️ [ACTIVITY] Erreur lors du recalcul de la sous-action après suppression', [
                     'error' => $e->getMessage(),
                     'sous_action_id' => $sousAction->id
                 ]);
                 // On continue malgré l'erreur de recalcul
             }
 
             DB::commit();
 
             Log::info('✅ [ACTIVITY] Activité supprimée avec succès', [
                 'activity_id' => $activityInfo['id'],
                 'sous_action_id' => $sousAction->id
             ]);
 
             return response()->json([
                 'success' => true,
                 'message' => 'Activité "' . $activityInfo['titre'] . '" supprimée avec succès',
                 'deleted_activity' => $activityInfo,
                 'sous_action' => [
                     'id' => $sousAction->id,
                     'taux_avancement' => $sousAction->taux_avancement
                 ]
             ]);
 
         } catch (\Exception $e) {
             DB::rollBack();
             Log::error('💥 [ACTIVITY] Erreur lors de la suppression de l\'activité', [
                 'activity_id' => $activity->id,
                 'error' => $e->getMessage(),
                 'file' => $e->getFile(),
                 'line' => $e->getLine(),
                 'trace' => $e->getTraceAsString()
             ]);
             
             return response()->json([
                 'success' => false,
                 'message' => 'Erreur lors de la suppression de l\'activité: ' . $e->getMessage()
             ], 500);
         }
     }

             /**
     * Mettre à jour le taux d'avancement d'une activité
     */
    public function updateProgress(Request $request, Activity $activity)
    {
        try {
            Log::info('🔄 [ACTIVITY] Début updateProgress', [
                'activity_id' => $activity->id,
                'nouveau_taux' => $request->taux_avancement,
                'timestamp' => now()
            ]);

            $request->validate([
                'taux_avancement' => 'required|numeric|min:0|max:100'
            ]);

            // Vérifier si l'activité peut être modifiée
            if (!$activity->peut_etre_modifiee) {
                Log::warning('⚠️ [ACTIVITY] Tentative de modification d\'une activité non commencée', [
                    'activity_id' => $activity->id,
                    'date_debut' => $activity->date_debut,
                    'aujourd_hui' => now()->toDateString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de modifier la progression d\'une activité qui n\'a pas encore commencé'
                ], 422);
            }

            DB::beginTransaction();

            // Utiliser DB::table pour éviter les événements Eloquent
            DB::table('activities')
                ->where('id', $activity->id)
                ->update([
                    'taux_avancement' => $request->taux_avancement
                ]);

            // Mettre à jour l'instance locale
            $activity->taux_avancement = $request->taux_avancement;

            // Recalculer le taux d'avancement de la sous-action
            $sousAction = $activity->sousAction;
             
             Log::info('🔄 [ACTIVITY] Recalcul du taux de la sous-action', [
                 'sous_action_id' => $sousAction->id,
                 'ancien_taux' => $sousAction->taux_avancement
             ]);
             
             try {
                 $sousAction->recalculerTauxAvancement();
                 
                 // Récupérer le nouveau taux après recalcul
                 $sousAction->refresh();
                 
                 Log::info('✅ [ACTIVITY] Taux de la sous-action recalculé', [
                     'sous_action_id' => $sousAction->id,
                     'nouveau_taux' => $sousAction->taux_avancement
                 ]);
             } catch (\Exception $e) {
                 Log::warning('⚠️ [ACTIVITY] Erreur lors du recalcul de la sous-action', [
                     'error' => $e->getMessage(),
                     'sous_action_id' => $sousAction->id
                 ]);
                 // On continue malgré l'erreur de recalcul
             }
 
             DB::commit();
 
             Log::info('✅ [ACTIVITY] Progression mise à jour avec succès', [
                 'activity_id' => $activity->id,
                 'nouveau_taux' => $request->taux_avancement
             ]);
 
             return response()->json([
                 'success' => true,
                 'message' => 'Progression mise à jour avec succès',
                 'activity' => [
                     'id' => $activity->id,
                     'taux_avancement' => $request->taux_avancement,
                     'statut' => $activity->determinerStatutParProgression($request->taux_avancement)
                 ],
                 'sous_action' => [
                     'id' => $sousAction->id,
                     'taux_avancement' => $sousAction->taux_avancement
                 ]
             ]);
 
         } catch (\Exception $e) {
             DB::rollBack();
             Log::error('💥 [ACTIVITY] Erreur lors de la mise à jour de la progression', [
                 'activity_id' => $activity->id,
                 'error' => $e->getMessage(),
                 'file' => $e->getFile(),
                 'line' => $e->getLine(),
                 'trace' => $e->getTraceAsString()
             ]);
             
             return response()->json([
                 'success' => false,
                 'message' => 'Erreur lors de la mise à jour de la progression: ' . $e->getMessage()
             ], 500);
         }
     }
}
