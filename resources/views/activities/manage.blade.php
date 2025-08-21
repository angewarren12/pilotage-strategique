@extends('layouts.app')

@section('title', 'Gestion des activités - ' . $sousAction->titre)

@section('content')

<!-- 
    CALENDRIER DES ACTIVITÉS - NOUVELLES FONCTIONNALITÉS
    
    ✅ Modification de la progression au clic sur une activité
    ✅ Affichage de la progression de chaque activité avec barre de progression
    ✅ Suppression de la vue semaine (uniquement vue mois et liste)
    ✅ Vue mois étendue pour les activités multi-mois
    ✅ Tooltip informatif au survol des activités
    ✅ Clic gauche: Modifier progression | Clic droit: Voir détails
    ✅ Mise à jour en temps réel de la progression
    ✅ Recalcul automatique de la sous-action
-->

<div class="container-fluid">
    <!-- En-tête avec navigation -->
   

         <!-- En-tête de la page -->
     <div class="row mb-4">
         <div class="col-12">
             <div class="d-flex justify-content-between align-items-center">
                 <div>
                     <h1 class="h3 mb-2">
                         <i class="fas fa-tasks me-2 text-primary"></i>
                         Gestion des activités
                     </h1>
                     <p class="text-muted mb-0">
                         Sous-action : <strong>{{ $sousAction->libelle }}</strong>
                     </p>
                 </div>
                 <div class="d-flex gap-2">
                     <button type="button" class="btn btn-primary" onclick="openCreateActivityModal()">
                         <i class="fas fa-plus me-1"></i>Nouvelle activité
                     </button>
                 </div>
             </div>
         </div>
     </div>

     <!-- Détails de la sous-action -->
     <div class="row mb-4">
         <div class="col-12">
             <div class="card">
                 <div class="card-header">
                     <h5 class="mb-0">
                         <i class="fas fa-info-circle me-2 text-info"></i>Détails de la sous-action
                     </h5>
                 </div>
                 <div class="card-body">
                     <div class="row">
                         <div class="col-md-4">
                             <h6 class="text-muted mb-2">Code</h6>
                             <p class="mb-3"><span class="badge bg-secondary">{{ $sousAction->code }}</span></p>
                             
                             <h6 class="text-muted mb-2">Libellé</h6>
                             <p class="mb-3">{{ $sousAction->libelle }}</p>
                             
                             <h6 class="text-muted mb-2">Type</h6>
                             <p class="mb-3">
                                 <span class="badge {{ $sousAction->type === 'projet' ? 'bg-success' : 'bg-info' }}">
                                     {{ ucfirst($sousAction->type) }}
                                 </span>
                             </p>
                         </div>
                         <div class="col-md-4">
                             <h6 class="text-muted mb-2">Description</h6>
                             <p class="mb-3">{{ $sousAction->description ?: 'Aucune description' }}</p>
                             
                             <h6 class="text-muted mb-2">Statut</h6>
                             <p class="mb-3">
                                 <span class="badge {{ $sousAction->statut === 'termine' ? 'bg-success' : ($sousAction->statut === 'en_retard' ? 'bg-danger' : 'bg-primary') }}">
                                     {{ ucfirst(str_replace('_', ' ', $sousAction->statut)) }}
                                 </span>
                             </p>
                             
                             <h6 class="text-muted mb-2">Propriétaire</h6>
                             <p class="mb-3">
                                 @if($sousAction->owner)
                                     <i class="fas fa-user me-2 text-primary"></i>
                                     {{ $sousAction->owner->name }}
                                 @else
                                     <span class="text-muted">Non assigné</span>
                                 @endif
                             </p>
                         </div>
                         <div class="col-md-4">
                             <h6 class="text-muted mb-2">Échéance</h6>
                             <p class="mb-3">
                                 @if($sousAction->date_echeance)
                                     <i class="fas fa-calendar-alt me-2 text-warning"></i>
                                     {{ \Carbon\Carbon::parse($sousAction->date_echeance)->format('d/m/Y') }}
                                 @else
                                     <span class="text-muted">Aucune échéance définie</span>
                                 @endif
                             </p>
                             
                             <h6 class="text-muted mb-2">Date de réalisation</h6>
                             <p class="mb-3">
                                 @if($sousAction->date_realisation)
                                     <i class="fas fa-check-circle me-2 text-success"></i>
                                     {{ \Carbon\Carbon::parse($sousAction->date_realisation)->format('d/m/Y') }}
                                 @else
                                     <span class="text-muted">Non réalisée</span>
                                 @endif
                             </p>
                             
                             <h6 class="text-muted mb-2">Progression</h6>
                             <p class="mb-3">
                                 <div class="d-flex align-items-center">
                                     <div class="progress me-2" style="width: 120px; height: 12px;">
                                         <div class="progress-bar" style="width: {{ $sousAction->taux_avancement }}%; background: {{ $sousAction->taux_avancement >= 75 ? '#28a745' : ($sousAction->taux_avancement >= 50 ? '#ffc107' : '#007bff') }};"></div>
                                     </div>
                                     <span class="fw-bold fs-6">{{ $sousAction->taux_avancement }}%</span>
                                 </div>
                             </p>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <!-- Boutons d'outils -->
     <div class="row mb-4">
         <div class="col-12">
             <div class="d-flex gap-2">
                 <button type="button" class="btn btn-primary" onclick="openCreateActivityModal()">
                     <i class="fas fa-plus me-1"></i>Nouvelle activité
                 </button>
                 <button type="button" class="btn btn-outline-success" onclick="openActivityCalendar()">
                     <i class="fas fa-calendar me-1"></i>Calendrier des activités
                 </button>
             </div>
         </div>
     </div>

    

    <!-- Liste des activités -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Liste des activités
                        </h5>
                        
                        
                    </div>
                </div>
                <div class="card-body">
                    @if($activities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                                        <thead>
                            <tr>
                                <th>Activité</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Progression</th>
                                <th>Criticité</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                                <tbody>
                                                                         @foreach($activities as $activity)
                                         <tr data-activity-id="{{ $activity->id }}">
                                             <td>
                                                <div>
                                                    <!-- Titre de l'activité -->
                                                    <strong class="text-primary">{{ $activity->titre }}</strong>
                                                    
                                                    <!-- Propriétaire -->
                                                    <div class="mt-1">
                                                        <small class="text-info">
                                                            <i class="fas fa-user me-1"></i>
                                                            @if($activity->owner)
                                                                {{ $activity->owner->name }}
                                                            @else
                                                                Non assigné
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <!-- Dates -->
                                                    <div class="mb-1">
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            <strong>Début:</strong><br>
                                                            {{ $activity->date_debut->format('d/m/Y') }}
                                                        </small>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            <strong>Fin:</strong><br>
                                                            {{ $activity->date_fin->format('d/m/Y') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <!-- Statut principal -->
                                                    <span class="badge small-badge mb-1" style="background: {{ $activity->statut_color }}; color: white; font-size: 0.7em;">
                                                        {{ $activity->statuts_list[$activity->statut] ?? ucfirst($activity->statut) }}
                                                    </span>
                                                    
                                                    <!-- Indicateur de retard -->
                                                    @if($activity->est_en_retard && $activity->statut !== 'termine')
                                                        <div class="mt-1">
                                                            <small class="text-danger">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>En retard
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <!-- Barre de progression -->
                                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                                        <div class="progress me-2" style="width: 80px; height: 8px;">
                                                            <div class="progress-bar" style="width: {{ $activity->taux_avancement }}%; background: {{ $activity->taux_avancement >= 75 ? '#28a745' : ($activity->taux_avancement >= 50 ? '#ffc107' : '#007bff') }};"></div>
                                                        </div>
                                                        <span class="fw-bold text-muted">{{ $activity->taux_avancement }}%</span>
                                                    </div>
                                                    
                                                    <!-- Bouton de modification de progression -->
                                                    @if($activity->a_commence)
                                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                                data-bs-toggle="popover" 
                                                                data-bs-placement="left"
                                                                data-bs-html="true"
                                                                data-bs-content="
                                                                <div class='p-2'>
                                                                    <label class='form-label'>Progression: <span id='slider-value-{{ $activity->id }}'>{{ $activity->taux_avancement }}%</span></label>
                                                                    <input type='range' class='form-range' 
                                                                           id='progress-slider-{{ $activity->id }}' 
                                                                           min='0' max='100' value='{{ $activity->taux_avancement }}' 
                                                                           step='5'
                                                                           oninput='updateSliderValue({{ $activity->id }}, this.value)'
                                                                           onchange='updateProgressRealTime({{ $activity->id }}, this.value)'>
                                                                    <div class='d-flex justify-content-between mt-2'>
                                                                        <small>0%</small>
                                                                        <small>100%</small>
                                                                    </div>
                                                                </div>"
                                                                title="Modifier la progression">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @else
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i>En attente
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <!-- Niveau de criticité basé sur la priorité -->
                                                    @if($activity->priorite === 'elevee')
                                                        <span class="badge bg-danger small-badge">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>Critique
                                                        </span>
                                                    @elseif($activity->priorite === 'moyenne')
                                                        <span class="badge bg-warning text-dark small-badge">
                                                            <i class="fas fa-exclamation me-1"></i>Élevée
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success small-badge">
                                                            <i class="fas fa-check me-1"></i>Normale
                                                        </span>
                                                    @endif
                                                    
                                                    <!-- Indicateur de délai -->
                                                    @if($activity->est_en_retard && $activity->statut !== 'termine')
                                                        <div class="mt-1">
                                                            <small class="text-danger">
                                                                <i class="fas fa-clock me-1"></i>Délai dépassé
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" onclick="openEditActivityModal({{ $activity->id }})" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" onclick="deleteActivity({{ $activity->id }})" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune activité créée</h5>
                            <p class="text-muted">Commencez par créer votre première activité pour ce projet</p>
                            <button type="button" class="btn btn-primary" onclick="openCreateActivityModal()">
                                <i class="fas fa-plus me-1"></i>Créer une activité
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de création d'activité -->
<div class="modal fade" id="createActivityModal" tabindex="-1" aria-labelledby="createActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createActivityModalLabel">
                    <i class="fas fa-plus-circle me-2 text-primary"></i>
                    Nouvelle activité
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createActivityForm">
                <div class="modal-body">
                    <input type="hidden" name="sous_action_id" value="{{ $sousAction->id }}">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="titre" class="form-label">Titre de l'activité *</label>
                                <input type="text" class="form-control" id="titre" name="titre" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="priorite" class="form-label">Priorité *</label>
                                <select class="form-select" id="priorite" name="priorite" required>
                                    <option value="basse">Basse</option>
                                    <option value="moyenne">Moyenne</option>
                                    <option value="haute">Haute</option>
                                    <option value="critique">Critique</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                                         <div class="row">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="date_debut" class="form-label">Date de début *</label>
                                 <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                        @if($sousAction->date_echeance)
                                            max="{{ $sousAction->date_echeance }}"
                                        @endif
                                        required>
                                 <small class="form-text text-muted">Format: JJ/MM/AAAA</small>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="date_fin" class="form-label">Date de fin *</label>
                                 <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                        @if($sousAction->date_echeance)
                                            max="{{ $sousAction->date_echeance }}"
                                        @endif
                                        required>
                                 <small class="form-text text-muted">
                                     Format: JJ/MM/AAAA
                                     @if($sousAction->date_echeance)
                                         <br><span class="text-warning">⚠️ Ne doit pas dépasser le {{ \Carbon\Carbon::parse($sousAction->date_echeance)->format('d/m/Y') }}</span>
                                     @endif
                                 </small>
                             </div>
                         </div>
                     </div>

                                           <!-- Les champs statut et taux d'avancement sont définis automatiquement -->

                     <div class="row">
                         <div class="col-md-12">
                             <div class="mb-3">
                                 <label for="owner_id" class="form-label">Assigné à</label>
                                 <select class="form-select" id="owner_id" name="owner_id">
                                     <option value="">Sélectionner un utilisateur</option>
                                     @foreach($users as $user)
                                         <option value="{{ $user->id }}">{{ $user->name }}</option>
                                     @endforeach
                                 </select>
                             </div>
                         </div>
                     </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            @if(isset($sousAction))
                            <button type="button" class="btn btn-outline-info" onclick="returnToCalendar()">
                                <i class="fas fa-calendar me-1"></i>Retour au calendrier
                            </button>
                            @endif
                        </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Créer l'activité
                    </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'édition d'activité -->
<div class="modal fade" id="editActivityModal" tabindex="-1" aria-labelledby="editActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editActivityModalLabel">
                    <i class="fas fa-edit me-2 text-primary"></i>
                    Modifier l'activité
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editActivityForm">
                                 <div class="modal-body">
                     <input type="hidden" id="edit_activity_id" name="activity_id">
                     <input type="hidden" name="_method" value="PUT">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="edit_titre" class="form-label">Titre de l'activité *</label>
                                <input type="text" class="form-control" id="edit_titre" name="titre" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_priorite" class="form-label">Priorité *</label>
                                <select class="form-select" id="edit_priorite" name="priorite" required>
                                    <option value="basse">Basse</option>
                                    <option value="moyenne">Moyenne</option>
                                    <option value="haute">Haute</option>
                                    <option value="critique">Critique</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>

                                         <div class="row">
                                                   <div class="col-md-6">
                              <div class="mb-3">
                                  <label for="edit_date_debut" class="form-label">Date de début *</label>
                                  <input type="date" class="form-control" id="edit_date_debut" name="date_debut" 
                                         @if($sousAction->date_echeance)
                                             max="{{ $sousAction->date_echeance }}"
                                         @endif
                                         required>
                                  <small class="form-text text-muted">Format: JJ/MM/AAAA</small>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="mb-3">
                                  <label for="edit_date_fin" class="form-label">Date de fin *</label>
                                  <input type="date" class="form-control" id="edit_date_fin" name="date_fin" 
                                         @if($sousAction->date_echeance)
                                             max="{{ $sousAction->date_echeance }}"
                                         @endif
                                         required>
                                  <small class="form-text text-muted">
                                      Format: JJ/MM/AAAA
                                      @if($sousAction->date_echeance)
                                          <br><span class="text-warning">⚠️ Ne doit pas dépasser le {{ \Carbon\Carbon::parse($sousAction->date_echeance)->format('d/m/Y') }}</span>
                                      @endif
                                  </small>
                              </div>
                          </div>
                     </div>

                     <div class="row">
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="edit_statut" class="form-label">Statut *</label>
                                 <select class="form-select" id="edit_statut" name="statut" required>
                                     <option value="en_attente">En attente</option>
                                     <option value="en_cours">En cours</option>
                                     <option value="termine">Terminé</option>
                                     <option value="bloque">Bloqué</option>
                                 </select>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="mb-3">
                                 <label for="edit_taux_avancement" class="form-label">Taux d'avancement *</label>
                                 <input type="number" class="form-control" id="edit_taux_avancement" name="taux_avancement" min="0" max="100" step="5" required>
                             </div>
                         </div>
                     </div>

                                           <div class="row">
                          <div class="col-md-12">
                              <div class="mb-3">
                                  <label for="edit_owner_id" class="form-label">Assigné à</label>
                                  <select class="form-select" id="edit_owner_id" name="owner_id">
                                      <option value="">Sélectionner un utilisateur</option>
                                      @foreach($users as $user)
                                          <option value="{{ $user->id }}">{{ $user->name }}</option>
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                      </div>

                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



 @endsection
 
 @push('styles')
 <style>
     /* Styles pour les badges réduits */
     .small-badge {
         font-size: 0.7em !important;
         padding: 0.25em 0.5em !important;
         border-radius: 4px !important;
     }
     


     

     

     

     


       

       

     

     

     

     

     

     

     

     

     

     

     
     
    
     
    
     /* Styles pour le calendrier */
     .calendar-container {
         background: #fff;
         border-radius: 8px;
         overflow: hidden;
         height: calc(100vh - 300px);
         min-height: 600px;
     }
     
     /* Styles pour le modal plein écran */
     .modal-fullscreen .modal-content {
         height: 100vh;
         border-radius: 0;
     }
     
     .modal-fullscreen .modal-body {
         height: calc(100vh - 120px);
         overflow-y: auto;
         padding: 20px;
     }
     
     .modal-fullscreen .modal-header {
         padding: 15px 20px;
         border-bottom: 2px solid #dee2e6;
     }
     
     .modal-fullscreen .modal-footer {
         padding: 15px 20px;
         border-top: 2px solid #dee2e6;
     }
     
     .calendar-header {
         display: grid;
         grid-template-columns: repeat(7, 1fr);
         background: #f8f9fa;
         border-bottom: 1px solid #dee2e6;
     }
     
     .calendar-day-header {
         padding: 12px 8px;
         text-align: center;
         font-weight: 600;
         color: #495057;
         border-right: 1px solid #dee2e6;
     }
     
     .calendar-day-header:last-child {
         border-right: none;
     }
     
     .calendar-grid {
         display: grid;
         grid-template-columns: repeat(7, 1fr);
         min-height: 500px;
         height: 100%;
     }
     
     .calendar-day {
         border-right: 1px solid #dee2e6;
         border-bottom: 1px solid #dee2e6;
         padding: 12px;
         min-height: 120px;
         height: auto;
         position: relative;
         background: #fff;
         display: flex;
         flex-direction: column;
     }
     
     .calendar-day:nth-child(7n) {
         border-right: none;
     }
     
     .calendar-day:not(.current-month) {
         background: #f8f9fa;
         color: #adb5bd;
     }
     
     .calendar-day.other-month {
         background: #f8f9fa;
         color: #adb5bd;
     }
     

     
     .calendar-day.today {
         background: #e3f2fd;
         border: 2px solid #2196f3;
     }
     
     .date-number {
         font-weight: 600;
         margin-bottom: 8px;
         color: #495057;
         font-size: 0.9rem;
         text-align: center;
         background: #e9ecef;
         padding: 3px 6px;
         border-radius: 4px;
         border: 1px solid #ced4da;
         white-space: nowrap;
         min-width: fit-content;
     }
     
     .calendar-activity {
         transition: all 0.2s ease;
         min-height: 50px;
         display: flex;
         flex-direction: column;
         justify-content: space-between;
         margin-bottom: 4px;
         flex-grow: 1;
     }
     
     .calendar-activity:hover {
         transform: scale(1.02);
         box-shadow: 0 2px 4px rgba(0,0,0,0.2);
     }
     
     .activity-title {
         font-weight: 600;
         margin-bottom: 4px;
         font-size: 0.85rem;
         line-height: 1.2;
     }
     
     .activity-progress {
         display: flex;
         flex-direction: column;
         align-items: center;
         gap: 1px;
     }
     
     .progress-mini {
         width: 100%;
         border-radius: 2px;
         overflow: hidden;
         margin-bottom: 3px;
     }
     
     .progress-bar-mini {
         border-radius: 2px;
         transition: width 0.3s ease;
         height: 4px;
     }
     
     /* Style pour les activités multi-jours */
     .calendar-activity.multi-day {
         border-left: 3px solid rgba(255,255,255,0.8);
         border-radius: 0 3px 3px 0;
     }
     
     /* Amélioration de l'affichage des activités */
     .calendar-activity {
         box-shadow: 0 1px 3px rgba(0,0,0,0.1);
         border: 1px solid rgba(255,255,255,0.2);
     }
     
     /* Styles pour le tooltip des activités */
     .activity-tooltip {
         position: fixed;
         z-index: 9999;
         background: #fff;
         border: 1px solid #dee2e6;
         border-radius: 8px;
         box-shadow: 0 4px 12px rgba(0,0,0,0.15);
         padding: 12px;
         max-width: 280px;
         font-size: 0.85rem;
         transform: translateX(-50%);
     }
     
     .tooltip-header {
         border-bottom: 1px solid #dee2e6;
         padding-bottom: 8px;
         margin-bottom: 8px;
         color: #495057;
     }
     
     .tooltip-content {
         margin-bottom: 8px;
     }
     
     .tooltip-content > div {
         margin-bottom: 4px;
         color: #6c757d;
     }
     
     .tooltip-footer {
         border-top: 1px solid #dee2e6;
         padding-top: 8px;
         text-align: center;
     }
     
     .calendar-legend {
         display: flex;
         gap: 15px;
         align-items: center;
     }
     
     .legend-item {
         display: flex;
         align-items: center;
         gap: 5px;
     }
     
     .legend-color {
         width: 16px;
         height: 16px;
         border-radius: 3px;
         display: inline-block;
     }
     
           /* Styles pour la vue liste */
      .activity-row:hover {
          background: #f8f9fa;
          transition: background-color 0.2s ease;
      }
      
      .list-header {
          background: #e9ecef !important;
      }
      
      /* Indicateurs visuels pour les activités en retard */
      .calendar-activity.overdue {
          border: 2px solid #dc3545;
          animation: pulse 2s infinite;
      }
      
      @keyframes pulse {
          0% { opacity: 1; }
          50% { opacity: 0.7; }
          100% { opacity: 1; }
      }
      
      /* Responsive */
      @media (max-width: 768px) {
          .calendar-day {
              min-height: 60px;
              padding: 4px;
          }
          
          .calendar-activity {
              font-size: 0.7rem !important;
              padding: 1px 2px !important;
          }
          
          .calendar-legend {
              flex-direction: column;
              gap: 8px;
              align-items: flex-start;
          }
          
         .date-number {
              font-size: 0.8rem;
             padding: 2px 4px;
         }
         

     }
     
     /* Styles spécifiques pour le mode plein écran */
     @media (min-width: 1200px) {
         .calendar-day {
             min-height: 140px;
             padding: 15px;
         }
         
         .calendar-activity {
             min-height: 60px;
         }
         
         .activity-title {
             font-size: 0.9rem;
         }
         
         .progress-mini {
             height: 5px;
         }
         
         .progress-bar-mini {
             height: 5px;
          }
      }
 </style>
 @endpush
 
 @push('scripts')
 <script>
 
  

   
   // Initialiser les données au chargement de la page
  document.addEventListener('DOMContentLoaded', function() {
      console.log('📋 [PAGE] Initialisation des données des activités');
      
      // Informations sur la sous-action
      const sousAction = @json($sousAction);
      console.log('🏗️ [PAGE] Sous-action consultée:', {
          id: sousAction.id,
          code: sousAction.code,
          libelle: sousAction.libelle,
          type: sousAction.type,
          description: sousAction.description,
          statut: sousAction.statut,
          taux_avancement: sousAction.taux_avancement,
          date_echeance: sousAction.date_echeance,
          date_realisation: sousAction.date_realisation,
          owner: sousAction.owner ? sousAction.owner.name : 'Non assigné'
      });
      
      // Charger les données des activités pour tous les composants
      const activities = @json($activities);
      console.log('📋 [PAGE] Données brutes des activités reçues:', activities);
      
      activitiesData = activities.map(activity => ({
          id: activity.id,
          titre: activity.titre,
          description: activity.description,
          date_debut: new Date(activity.date_debut),
          date_fin: new Date(activity.date_fin),
          statut: activity.statut,
          priorite: activity.priorite,
          owner: activity.owner ? activity.owner.name : 'Non assigné',
          progression: parseFloat(activity.taux_avancement) || 0,
          est_en_retard: activity.est_en_retard || false
      }));
      
      console.log('📊 [PAGE] Activités chargées:', activitiesData.length);
      console.log('📋 [PAGE] Détails des activités transformées:', activitiesData);
      
      // Vérifier la hiérarchie complète
      if (sousAction.action && sousAction.action.objectif_specifique && sousAction.action.objectif_specifique.objectif_strategique && sousAction.action.objectif_specifique.objectif_strategique.pilier) {
          console.log('🏛️ [PAGE] Hiérarchie complète:', {
              pilier: {
                  code: sousAction.action.objectif_specifique.objectif_strategique.pilier.code,
                  libelle: sousAction.action.objectif_specifique.objectif_strategique.pilier.libelle
              },
              objectif_strategique: {
                  code: sousAction.action.objectif_specifique.objectif_strategique.code,
                  libelle: sousAction.action.objectif_specifique.objectif_strategique.libelle
              },
              objectif_specifique: {
                  code: sousAction.action.objectif_specifique.code,
                  libelle: sousAction.action.objectif_specifique.libelle
              },
              action: {
                  code: sousAction.action.code,
                  libelle: sousAction.action.libelle
              },
              sous_action: {
                  code: sousAction.code,
                  libelle: sousAction.libelle
              }
          });
      }
      
      // Initialiser les variables globales
      ganttActivities = [...activitiesData];
      ganttFilteredActivities = [...activitiesData];
      filteredActivities = [...activitiesData];
      
      // Mettre à jour les statistiques
      updateStatistics();
      
      console.log('✅ [PAGE] Initialisation terminée avec succès');
  });
  
  // Charger les données pour le diagramme de Gantt
  function loadGanttData() {
      console.log('🔍 [GANTT] Chargement des données, activitiesData:', activitiesData);
      
      if (!activitiesData || activitiesData.length === 0) {
          console.warn('⚠️ [GANTT] Aucune donnée d\'activité disponible');
          return;
      }
      
      ganttActivities = activitiesData.map(activity => ({
          id: activity.id,
          titre: activity.titre,
          date_debut: new Date(activity.date_debut),
          date_fin: new Date(activity.date_fin),
          statut: activity.statut,
          priorite: activity.priorite,
          taux_avancement: parseFloat(activity.progression) || 0,
          owner: activity.owner,
          est_en_retard: activity.est_en_retard
      }));
      
      ganttFilteredActivities = [...ganttActivities];
             console.log('📊 [GANTT] Données Gantt chargées:', ganttActivities.length, 'activités');
       console.log('📋 [GANTT] Détails des activités Gantt:', ganttActivities);
       
       // Mettre à jour le compteur d'activités
       updateGanttActivityCount();
   }
  
  // Générer le diagramme de Gantt
  function generateGanttChart() {
      const header = document.getElementById('ganttHeader');
      const body = document.getElementById('ganttBody');
      
      if (!header || !body) {
          console.error('❌ Éléments Gantt non trouvés');
          return;
      }
      
      // Générer l'en-tête
      generateGanttHeader(header);
      
      // Générer le corps
      generateGanttBody(body);
  }
  
  // Générer l'en-tête du diagramme de Gantt
  function generateGanttHeader(headerElement) {
      const headerRow = document.createElement('div');
      headerRow.className = 'gantt-header-row';
      
             // En-tête de la colonne des activités
       const activityHeader = document.createElement('div');
       activityHeader.className = 'gantt-header-cell';
       activityHeader.style.width = '200px';
       activityHeader.textContent = 'Activités';
       headerRow.appendChild(activityHeader);
       
       // Ajouter la colonne Date début
       const startDateHeader = document.createElement('div');
       startDateHeader.className = 'gantt-header-cell';
       startDateHeader.style.width = '100px';
       startDateHeader.textContent = 'Date début';
       headerRow.appendChild(startDateHeader);
       
       // Ajouter la colonne Date fin
       const endDateHeader = document.createElement('div');
       endDateHeader.className = 'gantt-header-cell';
       endDateHeader.style.width = '100px';
       endDateHeader.textContent = 'Date fin';
       headerRow.appendChild(endDateHeader);
       
       // Générer les colonnes de dates selon la vue
      const dates = getGanttDates();
      dates.forEach(date => {
          const dateCell = document.createElement('div');
          dateCell.className = 'gantt-header-cell';
          
          if (ganttViewMode === 'week') {
              dateCell.textContent = date.toLocaleDateString('fr-FR', { weekday: 'short', day: '2-digit' });
          } else if (ganttViewMode === 'month') {
              dateCell.textContent = date.toLocaleDateString('fr-FR', { day: '2-digit' });
          } else {
              dateCell.textContent = date.toLocaleDateString('fr-FR', { month: 'short', day: '2-digit' });
          }
          
          // Marquer aujourd'hui
          if (isSameDay(date, new Date())) {
              dateCell.style.backgroundColor = '#fff3cd';
              dateCell.style.fontWeight = 'bold';
          }
          
          headerRow.appendChild(dateCell);
      });
      
             // Ajouter un indicateur de période en haut
       let periodIndicator;
       
       if (ganttViewMode === 'week') {
           periodIndicator = document.createElement('div');
           periodIndicator.className = 'gantt-period-indicator';
           const startOfWeek = new Date(ganttCurrentDate);
           const day = startOfWeek.getDay();
           const diff = startOfWeek.getDate() - day + (day === 0 ? -6 : 1);
           startOfWeek.setDate(diff);
           
           const endOfWeek = new Date(startOfWeek);
           endOfWeek.setDate(startOfWeek.getDate() + 6);
           
           periodIndicator.textContent = `Semaine du ${startOfWeek.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' })} au ${endOfWeek.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' })}`;
           
       } else if (ganttViewMode === 'month') {
           periodIndicator = document.createElement('div');
           periodIndicator.className = 'gantt-period-indicator';
           const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                              'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
           const currentMonth = monthNames[ganttCurrentDate.getMonth()];
           const currentYear = ganttCurrentDate.getFullYear();
           periodIndicator.textContent = `${currentMonth} ${currentYear}`;
           
       } else if (ganttViewMode === 'quarter') {
           periodIndicator = document.createElement('div');
           periodIndicator.className = 'gantt-period-indicator';
           const quarter = Math.floor(ganttCurrentDate.getMonth() / 3) + 1;
           const year = ganttCurrentDate.getFullYear();
           periodIndicator.textContent = `${quarter}er Trimestre ${year}`;
       }
       
       if (periodIndicator) {
           headerElement.innerHTML = '';
           headerElement.appendChild(periodIndicator);
       }
       
       headerElement.appendChild(headerRow);
   }
  
  // Générer le corps du diagramme de Gantt
  function generateGanttBody(bodyElement) {
      bodyElement.innerHTML = '';
      
      ganttFilteredActivities.forEach(activity => {
          const row = createGanttRow(activity);
          bodyElement.appendChild(row);
      });
  }
  
  // Créer une ligne du diagramme de Gantt
  function createGanttRow(activity) {
      const row = document.createElement('div');
      row.className = 'gantt-row';
      
             // En-tête de la ligne (nom de l'activité + métadonnées)
       const rowHeader = document.createElement('div');
       rowHeader.className = 'gantt-row-header';
       rowHeader.style.width = '200px';
       
       const title = document.createElement('div');
       title.className = 'activity-title';
       title.textContent = activity.titre;
       
       const meta = document.createElement('div');
       meta.className = 'activity-meta';
       meta.innerHTML = `
           <span class="badge small-badge" style="background: ${getStatusColor(activity.statut)}; color: white;">
               ${getStatusText(activity.statut)}
           </span>
           <span class="badge small-badge" style="background: ${getPriorityColor(activity.priorite)}; color: white;">
               ${activity.priorite}
           </span>
           <br>
           <small>${activity.owner}</small>
       `;
       
       rowHeader.appendChild(title);
       rowHeader.appendChild(meta);
       row.appendChild(rowHeader);
       
       // Colonne Date début
       const startDateCell = document.createElement('div');
       startDateCell.className = 'gantt-date-cell';
       startDateCell.style.width = '100px';
       startDateCell.textContent = new Date(activity.date_debut).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
       row.appendChild(startDateCell);
       
       // Colonne Date fin
       const endDateCell = document.createElement('div');
       endDateCell.className = 'gantt-date-cell';
       endDateCell.style.width = '100px';
       endDateCell.textContent = new Date(activity.date_fin).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
       row.appendChild(endDateCell);
      
      // Timeline avec les barres d'activités
      const timeline = document.createElement('div');
      timeline.className = 'gantt-timeline';
      
      const dates = getGanttDates();
      dates.forEach(date => {
          const cell = document.createElement('div');
          cell.className = 'gantt-timeline-cell';
          
          // Marquer aujourd'hui
          if (isSameDay(date, new Date())) {
              cell.classList.add('today');
          }
          
          // Marquer les weekends
          if (date.getDay() === 0 || date.getDay() === 6) {
              cell.classList.add('weekend');
          }
          
          timeline.appendChild(cell);
      });
      
      // Ajouter la barre d'activité
      addGanttBar(timeline, activity, dates);
      
      row.appendChild(timeline);
      return row;
  }
  
     // Ajouter une barre d'activité au timeline
   function addGanttBar(timeline, activity, dates) {
       // Convertir les dates en objets Date si ce sont des strings
       const startDate = new Date(activity.date_debut);
       const endDate = new Date(activity.date_fin);
       
       const startIndex = dates.findIndex(date => isSameDay(date, startDate));
       const endIndex = dates.findIndex(date => isSameDay(date, endDate));
       
       console.log(`🎯 [GANTT] Positionnement de l'activité ${activity.titre}:`, {
           date_debut: startDate.toISOString().split('T')[0],
           date_fin: endDate.toISOString().split('T')[0],
           startIndex,
           endIndex,
           totalDates: dates.length
       });
       
       if (startIndex === -1 || endIndex === -1) {
           console.warn(`⚠️ [GANTT] Impossible de positionner l'activité ${activity.titre}: dates hors de la plage affichée`);
           return;
       }
       
       const bar = document.createElement('div');
       bar.className = `gantt-bar ${activity.statut}`;
       
       if (activity.est_en_retard && activity.statut !== 'termine') {
           bar.classList.add('overdue');
       }
       
       // Positionner la barre avec précision
       const dayWidth = 50; // 50px par jour
       const left = startIndex * dayWidth;
       const width = (endIndex - startIndex + 1) * dayWidth;
       
       bar.style.left = `${left}px`;
       bar.style.width = `${width}px`;
       bar.style.top = '25px';
       
       console.log(`📍 [GANTT] Barre positionnée: left=${left}px, width=${width}px`);
      
      // Afficher la progression
      if (activity.taux_avancement > 0) {
          const progress = document.createElement('div');
          progress.className = 'gantt-progress';
          progress.style.width = `${activity.taux_avancement}%`;
          bar.appendChild(progress);
      }
      
             // Texte de la barre
       const progressText = document.createElement('div');
       progressText.textContent = `${Math.round(activity.taux_avancement || 0)}%`;
       progressText.style.cssText = `
           position: absolute;
           top: 50%;
           left: 50%;
           transform: translate(-50%, -50%);
           font-weight: 700;
           font-size: 0.85rem;
           color: white;
           text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
           z-index: 7;
           pointer-events: none;
       `;
       bar.appendChild(progressText);
       
       // Tooltip au survol
      bar.addEventListener('mouseenter', (e) => showGanttTooltip(e, activity));
      bar.addEventListener('mouseleave', hideGanttTooltip);
      
      // Clic pour voir les détails
      bar.addEventListener('click', () => showGanttActivityDetails(activity));
      
      timeline.appendChild(bar);
  }
  
     // Obtenir les dates pour le diagramme de Gantt
   function getGanttDates() {
       const dates = [];
       const startDate = new Date(ganttCurrentDate);
       
       if (ganttViewMode === 'week') {
           // Ajuster au début de la semaine (lundi)
           const day = startDate.getDay();
           const diff = startDate.getDate() - day + (day === 0 ? -6 : 1);
           startDate.setDate(diff);
           
           // Étendre la vue semaine pour inclure toutes les activités
           // Calculer la date de fin la plus éloignée parmi toutes les activités
           let maxEndDate = new Date(startDate);
           maxEndDate.setDate(startDate.getDate() + 6); // Fin de la semaine initiale
           
           ganttActivities.forEach(activity => {
               const activityEndDate = new Date(activity.date_fin);
               if (activityEndDate > maxEndDate) {
                   maxEndDate = new Date(activityEndDate);
               }
           });
           
           // Générer les dates du début de la semaine jusqu'à la fin de l'activité la plus éloignée
           const totalDays = Math.ceil((maxEndDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
           
           for (let i = 0; i < totalDays; i++) {
               const date = new Date(startDate);
               date.setDate(startDate.getDate() + i);
               dates.push(date);
           }
       } else if (ganttViewMode === 'month') {
           // Ajuster au début du mois
           startDate.setDate(1);
           
           // Obtenir le nombre exact de jours dans le mois
           const year = startDate.getFullYear();
           const month = startDate.getMonth();
           const daysInMonth = new Date(year, month + 1, 0).getDate();
           
           // Vérifier si des activités s'étendent au-delà du mois
           let maxEndDate = new Date(startDate);
           maxEndDate.setDate(startDate.getDate() + daysInMonth - 1); // Fin du mois
           
           ganttActivities.forEach(activity => {
               const activityEndDate = new Date(activity.date_fin);
               if (activityEndDate > maxEndDate) {
                   maxEndDate = new Date(activityEndDate);
               }
           });
           
           // Générer les dates du début du mois jusqu'à la fin de l'activité la plus éloignée
           const totalDays = Math.ceil((maxEndDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
           
           for (let i = 0; i < totalDays; i++) {
               const date = new Date(startDate);
               date.setDate(startDate.getDate() + i);
               dates.push(date);
           }
       } else if (ganttViewMode === 'quarter') {
           // Ajuster au début du trimestre
           const quarter = Math.floor(startDate.getMonth() / 3);
           startDate.setMonth(quarter * 3);
           startDate.setDate(1);
           
           // Calculer la fin du trimestre
           const endQuarter = new Date(startDate);
           endQuarter.setMonth(startDate.getMonth() + 3);
           endQuarter.setDate(0); // Dernier jour du mois précédent
           
           const daysInQuarter = Math.ceil((endQuarter - startDate) / (1000 * 60 * 60 * 24));
           
           for (let i = 0; i < daysInQuarter; i++) {
               const date = new Date(startDate);
               date.setDate(startDate.getDate() + i);
               dates.push(date);
           }
       }
       
       console.log(`📅 [GANTT] Dates générées pour la vue ${ganttViewMode}:`, {
           startDate: startDate.toISOString().split('T')[0],
           endDate: dates[dates.length - 1]?.toISOString().split('T')[0],
           totalDays: dates.length,
           dates: dates.map(d => d.toISOString().split('T')[0])
       });
       
       return dates;
   }
  
  // Fonctions utilitaires pour le diagramme de Gantt
  function ganttPreviousPeriod() {
      if (ganttViewMode === 'week') {
          ganttCurrentDate.setDate(ganttCurrentDate.getDate() - 7);
      } else if (ganttViewMode === 'month') {
          ganttCurrentDate.setMonth(ganttCurrentDate.getMonth() - 1);
      } else if (ganttViewMode === 'quarter') {
          ganttCurrentDate.setMonth(ganttCurrentDate.getMonth() - 3);
      }
      generateGanttChart();
  }
  
  function ganttNextPeriod() {
      if (ganttViewMode === 'week') {
          ganttCurrentDate.setDate(ganttCurrentDate.getDate() + 7);
      } else if (ganttViewMode === 'month') {
          ganttCurrentDate.setMonth(ganttCurrentDate.getMonth() + 1);
      } else if (ganttViewMode === 'quarter') {
          ganttCurrentDate.setMonth(ganttCurrentDate.getMonth() + 3);
      }
      generateGanttChart();
  }
  
  function ganttGoToToday() {
      ganttCurrentDate = new Date();
      generateGanttChart();
  }
  
  function changeGanttView() {
      ganttViewMode = document.getElementById('ganttViewMode').value;
      generateGanttChart();
  }
  
  function applyGanttFilters() {
      const statusFilter = document.getElementById('ganttStatusFilter').value;
      const priorityFilter = document.getElementById('ganttPriorityFilter').value;
      
      ganttCurrentFilters = {
          status: statusFilter,
          priority: priorityFilter
      };
      
      // Filtrer les activités
      ganttFilteredActivities = ganttActivities.filter(activity => {
          const matchesStatus = !statusFilter || activity.statut === statusFilter;
          const matchesPriority = !priorityFilter || activity.priorite === priorityFilter;
          return matchesStatus && matchesPriority;
      });
      
      // Mettre à jour le compteur et régénérer
      updateGanttActivityCount();
      generateGanttChart();
  }
  
  function showGanttTooltip(event, activity) {
      const tooltip = document.createElement('div');
      tooltip.className = 'gantt-tooltip';
      tooltip.innerHTML = `
          <strong>${activity.titre}</strong><br>
          Début: ${activity.date_debut.toLocaleDateString('fr-FR')}<br>
          Fin: ${activity.date_fin.toLocaleDateString('fr-FR')}<br>
          Progression: ${activity.taux_avancement}%<br>
          Statut: ${getStatusText(activity.statut)}<br>
          Priorité: ${activity.priorite}<br>
          Assigné à: ${activity.owner}
      `;
      
      document.body.appendChild(tooltip);
      
      const rect = event.target.getBoundingClientRect();
      tooltip.style.left = `${rect.left + rect.width / 2}px`;
      tooltip.style.top = `${rect.top - tooltip.offsetHeight - 10}px`;
      
      // Stocker la référence pour le supprimer
      event.target.tooltip = tooltip;
  }
  
  function hideGanttTooltip() {
      const tooltips = document.querySelectorAll('.gantt-tooltip');
      tooltips.forEach(tooltip => tooltip.remove());
  }
  
  function showGanttActivityDetails(activity) {
      // Utiliser la fonction existante pour afficher les détails
      showActivityDetails(activity);
  }
  
  function exportGanttToImage() {
      // TODO: Implémenter l'export en image
      alert('🚧 Fonctionnalité d\'export en cours de développement...');
  }
  
  // Basculer l'affichage de la légende
  function toggleGanttLegend() {
      const legend = document.getElementById('ganttLegend');
      if (legend.style.display === 'none') {
          legend.style.display = 'block';
      } else {
          legend.style.display = 'none';
      }
  }
  
  // Mettre à jour le compteur d'activités
  function updateGanttActivityCount() {
      const countElement = document.getElementById('ganttActivityCount');
      if (countElement) {
          const count = ganttFilteredActivities.length;
          countElement.textContent = `${count} activité${count > 1 ? 's' : ''}`;
          countElement.className = count > 0 ? 'badge bg-primary fs-6' : 'badge bg-secondary fs-6';
      }
  }
  
  // Fonction utilitaire pour comparer les dates
  function isSameDay(date1, date2) {
      return date1.getFullYear() === date2.getFullYear() &&
             date1.getMonth() === date2.getMonth() &&
             date1.getDate() === date2.getDate();
  }

   // Fonction pour ouvrir le calendrier des activités
  function openActivityCalendar() {
      console.log('📅 [CALENDAR] Ouverture du calendrier des activités');
      
      // Marquer que l'utilisateur est dans le calendrier
      window.wasInCalendar = true;
      
      // Créer le modal du calendrier
      const calendarModal = createCalendarModal();
      document.body.appendChild(calendarModal);
      
      // Afficher le modal
      const modal = new bootstrap.Modal(calendarModal);
      modal.show();
      
      // Initialiser le calendrier
      initializeCalendar();
      
      // Nettoyer le modal après fermeture
      calendarModal.addEventListener('hidden.bs.modal', function() {
          document.body.removeChild(calendarModal);
          // Marquer que l'utilisateur n'est plus dans le calendrier
          window.wasInCalendar = false;
      });
  }
  
  // Fonction pour créer le modal du calendrier
  function createCalendarModal() {
      const modal = document.createElement('div');
      modal.className = 'modal fade';
      modal.id = 'calendarModal';
      modal.setAttribute('tabindex', '-1');
      modal.setAttribute('aria-labelledby', 'calendarModalLabel');
      modal.setAttribute('aria-hidden', 'true');
      
                             modal.innerHTML = `
                           <div class="modal-dialog modal-fullscreen">
                               <div class="modal-content">
                                   <div class="modal-header">
                                       <h5 class="modal-title" id="calendarModalLabel">
                                           <i class="fas fa-calendar me-2 text-success"></i>
                                           Calendrier des activités - {{ $sousAction->action->objectifSpecifique->objectifStrategique->pilier->code }}.{{ $sousAction->action->objectifSpecifique->objectifStrategique->code }}.{{ $sousAction->action->objectifSpecifique->code }}.{{ $sousAction->action->code }}.{{ $sousAction->code }} > {{ $sousAction->libelle }}
                                       </h5>
                                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                   </div>
                  <div class="modal-body">
                                             <div class="row mb-3">
                           <div class="col-md-4">
                               <div class="btn-group" role="group">
                                   <button type="button" class="btn btn-outline-primary" onclick="previousMonth()">
                                       <i class="fas fa-chevron-left"></i>
                                   </button>
                                   <button type="button" class="btn btn-primary" id="currentMonthDisplay">
                                       {{ \Carbon\Carbon::now()->format('F Y') }}
                                   </button>
                                   <button type="button" class="btn btn-outline-primary" onclick="nextMonth()">
                                       <i class="fas fa-chevron-right"></i>
                                   </button>
                               </div>
                                   <div class="mt-2">
                                       <small class="text-muted" id="periodIndicator">
                                           Vue étendue pour toutes les activités
                                       </small>
                               </div>
                           </div>
                           <div class="col-md-4">
                               <div class="d-flex gap-2 align-items-center">
                                   <button type="button" class="btn btn-success btn-sm" onclick="createActivityFromCalendar()">
                                       <i class="fas fa-plus me-1"></i>Créer une activité
                                   </button>
                                   <select class="form-select form-select-sm" id="statusFilter" onchange="applyFilters()">
                                       <option value="">Tous les statuts</option>
                                       <option value="en_attente">En attente</option>
                                       <option value="en_cours">En cours</option>
                                       <option value="termine">Terminé</option>
                                       <option value="bloque">Bloqué</option>
                                   </select>
                                   <select class="form-select form-select-sm" id="priorityFilter" onchange="applyFilters()">
                                       <option value="">Toutes les priorités</option>
                                       <option value="basse">Basse</option>
                                       <option value="moyenne">Moyenne</option>
                                       <option value="haute">Haute</option>
                                       <option value="critique">Critique</option>
                                   </select>
                               </div>
                           </div>
                           <div class="col-md-4 text-end">
                               <div class="d-flex gap-2 justify-content-end">
                                   <input type="text" class="form-control form-control-sm" id="searchActivity" placeholder="Rechercher une activité..." onkeyup="applyFilters()">
                                   <button type="button" class="btn btn-outline-secondary btn-sm" onclick="goToToday()">
                                       <i class="fas fa-calendar-day me-1"></i>Aujourd'hui
                                   </button>
                                   <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleCalendarView()">
                                       <i class="fas fa-th-large me-1"></i>Vue
                                   </button>
                               </div>
                           </div>
                       </div>
                      
                                             <!-- Résumé des activités -->
                       <div class="row mb-3">
                           <div class="col-12">
                               <div class="card">
                                   <div class="card-body py-2">
                                       <div class="row text-center">
                                           <div class="col-md-2">
                                               <div class="d-flex flex-column align-items-center">
                                                   <span class="badge bg-success fs-6" id="totalActivities">0</span>
                                                   <small class="text-muted">Total</small>
                                               </div>
                                           </div>
                                           <div class="col-md-2">
                                               <div class="d-flex flex-column align-items-center">
                                                   <span class="badge bg-primary fs-6" id="enCoursCount">0</span>
                                                   <small class="text-muted">En cours</small>
                                               </div>
                                           </div>
                                           <div class="col-md-2">
                                               <div class="d-flex flex-column align-items-center">
                                                   <span class="badge bg-warning fs-6" id="termineCount">0</span>
                                                   <small class="text-muted">Terminé</small>
                                               </div>
                                           </div>
                                           <div class="col-md-2">
                                               <div class="d-flex flex-column align-items-center">
                                                   <span class="badge bg-danger fs-6" id="bloqueCount">0</span>
                                                   <small class="text-muted">Bloqué</small>
                                               </div>
                                           </div>
                                           <div class="col-md-2">
                                               <div class="d-flex flex-column align-items-center">
                                                   <span class="badge bg-info fs-6" id="retardCount">0</span>
                                                   <small class="text-muted">En retard</small>
                                               </div>
                                           </div>
                                           <div class="col-md-2">
                                               <div class="d-flex flex-column align-items-center">
                                                   <span class="badge bg-secondary fs-6" id="progressionMoyenne">0%</span>
                                                   <small class="text-muted">Progression</small>
                                               </div>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                       
                       <div class="calendar-container">
                           <!-- Section d'aide pour la création -->
                           <div class="alert alert-info mb-3" role="alert">
                               <div class="d-flex align-items-center">
                                   <i class="fas fa-info-circle me-2"></i>
                                   <div class="flex-grow-1">
                                       <strong>Astuce :</strong> Utilisez le bouton "Nouvelle activité" en bas pour créer une activité directement depuis le calendrier.
                                   </div>
                                   <button type="button" class="btn btn-sm btn-outline-info" onclick="createActivityFromCalendar()">
                                       <i class="fas fa-plus me-1"></i>Créer maintenant
                                   </button>
                               </div>
                           </div>
                           
                           <div class="calendar-header">
                               <div class="calendar-day-header">Dim</div>
                               <div class="calendar-day-header">Lun</div>
                               <div class="calendar-day-header">Mar</div>
                               <div class="calendar-day-header">Mer</div>
                               <div class="calendar-day-header">Jeu</div>
                               <div class="calendar-day-header">Ven</div>
                               <div class="calendar-day-header">Sam</div>
                           </div>
                           <div class="calendar-grid" id="calendarGrid">
                               <!-- Le calendrier sera généré ici -->
                           </div>
                       </div>
                  </div>
                  <div class="modal-footer">
                      <div class="d-flex justify-content-between align-items-center w-100">
                      <div class="calendar-legend">
                          <span class="legend-item">
                              <span class="legend-color" style="background: #28a745;"></span>
                              <small>À faire</small>
                          </span>
                          <span class="legend-item">
                              <span class="legend-color" style="background: #007bff;"></span>
                              <small>En cours</small>
                          </span>
                          <span class="legend-item">
                              <span class="legend-color" style="background: #ffc107;"></span>
                              <small>Terminé</small>
                          </span>
                          <span class="legend-item">
                              <span class="legend-color" style="background: #dc3545;"></span>
                              <small>Bloqué</small>
                          </span>
                      </div>
                          <div class="d-flex gap-2">
                              <button type="button" class="btn btn-success" onclick="createActivityFromCalendar()">
                                  <i class="fas fa-plus me-1"></i>Nouvelle activité
                              </button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      `;
      
      return modal;
  }
  
  // Variables globales pour le calendrier
  let currentDate = new Date();
  let activitiesData = [];
  let filteredActivities = [];
  
  // Variables globales pour le diagramme de Gantt
  let ganttCurrentDate = new Date();
  let ganttViewMode = 'week';
  let ganttActivities = [];
  let ganttFilteredActivities = [];
  let currentView = 'month'; // 'month', 'week', 'list'
  let currentFilters = {
      status: '',
      priority: '',
      search: ''
  };
  
  // Fonction pour initialiser le calendrier
  function initializeCalendar() {
      // Charger les données des activités
      loadActivitiesForCalendar();
      
      // Générer le calendrier
      generateCalendar();
  }
  
  // Fonction pour charger les activités pour le calendrier
  function loadActivitiesForCalendar() {
      // Utiliser les données déjà disponibles ou les recharger
      const activities = @json($activities);
      activitiesData = activities.map(activity => ({
          id: activity.id,
          titre: activity.titre,
          description: activity.description,
          date_debut: new Date(activity.date_debut),
          date_fin: new Date(activity.date_fin),
          statut: activity.statut,
          priorite: activity.priorite,
          owner: activity.owner ? activity.owner.name : 'Non assigné',
          progression: parseFloat(activity.taux_avancement) || 0
      }));
      
      // Initialiser les activités filtrées
      filteredActivities = [...activitiesData];
      
      console.log('📅 [CALENDAR] Activités chargées:', activitiesData.length);
      console.log('📊 [CALENDAR] Données des activités:', activitiesData.map(a => ({
          id: a.id,
          titre: a.titre,
          progression: a.progression,
          type_progression: typeof a.progression
      })));
      
      // Mettre à jour les statistiques
      updateStatistics();
  }
  
  // Fonction pour générer le calendrier
  function generateCalendar() {
      const year = currentDate.getFullYear();
      const month = currentDate.getMonth();
      
      // Mettre à jour l'affichage du mois
      const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                         'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
      document.getElementById('currentMonthDisplay').textContent = `${monthNames[month]} ${year}`;
      
      const grid = document.getElementById('calendarGrid');
      grid.innerHTML = '';
      
      // Obtenir le premier jour du mois et le nombre de jours
      const firstDay = new Date(year, month, 1);
      const lastDay = new Date(year, month + 1, 0);
      let startDate = new Date(firstDay);
      startDate.setDate(startDate.getDate() - firstDay.getDay());
      
      // Vérifier si des activités s'étendent au-delà du mois actuel
      let extendedStartDate = new Date(startDate);
      let extendedEndDate = new Date(lastDay);
      
      if (filteredActivities.length > 0) {
          // Trouver la date de début la plus précoce et la date de fin la plus tardive
          const earliestStart = new Date(Math.min(...filteredActivities.map(a => new Date(a.date_debut))));
          const latestEnd = new Date(Math.max(...filteredActivities.map(a => new Date(a.date_fin))));
          
          // Étendre la vue si nécessaire pour inclure toutes les activités
          if (earliestStart < startDate) {
              extendedStartDate = new Date(earliestStart);
              extendedStartDate.setDate(extendedStartDate.getDate() - extendedStartDate.getDay());
          }
          
          if (latestEnd > lastDay) {
              extendedEndDate = new Date(latestEnd);
              // Ajouter des jours pour compléter la semaine
              const daysToAdd = (7 - extendedEndDate.getDay()) % 7;
              extendedEndDate.setDate(extendedEndDate.getDate() + daysToAdd);
          }
      }
      
      // Calculer le nombre total de jours à afficher
      const totalDays = Math.ceil((extendedEndDate - extendedStartDate) / (1000 * 60 * 60 * 24)) + 1;
      
      // Générer les cellules du calendrier étendu
      for (let i = 0; i < totalDays; i++) {
          const date = new Date(extendedStartDate);
          date.setDate(extendedStartDate.getDate() + i);
          
          const cell = document.createElement('div');
          cell.className = 'calendar-day';
          
          // Ajouter la classe pour le mois actuel
          if (date.getMonth() === month) {
              cell.classList.add('current-month');
          } else {
              cell.classList.add('other-month');
          }
          
          // Ajouter la classe pour aujourd'hui
          const today = new Date();
          if (date.toDateString() === today.toDateString()) {
              cell.classList.add('today');
          }
          
          // Créer le contenu de la cellule
          const dateNumber = document.createElement('div');
          dateNumber.className = 'date-number';
          dateNumber.textContent = date.getDate();
          
          
           
           // Modifier l'affichage de la date pour inclure le mois si c'est le premier jour ou hors du mois actuel
           if (date.getDate() === 1 || date.getMonth() !== month) {
               const monthName = monthNames[date.getMonth()].substring(0, 3);
               dateNumber.innerHTML = `${date.getDate()} ${monthName}`;
           }
          
          cell.appendChild(dateNumber);
          
          // Ajouter les activités pour cette date
          const activitiesForDate = getActivitiesForDate(date);
          activitiesForDate.forEach(activity => {
              const activityElement = createActivityElement(activity);
              cell.appendChild(activityElement);
          });
          
          grid.appendChild(cell);
      }
      
      console.log('📅 [CALENDAR] Calendrier généré avec vue étendue:', {
          mois_actuel: `${monthNames[month]} ${year}`,
          date_debut_etendue: extendedStartDate.toLocaleDateString('fr-FR'),
          date_fin_etendue: extendedEndDate.toLocaleDateString('fr-FR'),
          total_jours: totalDays,
          activites_chargees: filteredActivities.length
      });
      
      // Mettre à jour l'indicateur de période
      updatePeriodIndicator(extendedStartDate, extendedEndDate, month, year);
  }
  
  // Fonction pour mettre à jour l'indicateur de période
  function updatePeriodIndicator(startDate, endDate, currentMonth, currentYear) {
      const periodIndicator = document.getElementById('periodIndicator');
      if (!periodIndicator) return;
      
      const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                         'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
      
      const startMonth = startDate.getMonth();
      const startYear = startDate.getFullYear();
      const endMonth = endDate.getMonth();
      const endYear = endDate.getFullYear();
      
      let periodText = '';
      
      if (startMonth === currentMonth && startYear === currentYear && 
          endMonth === currentMonth && endYear === currentYear) {
          // Vue limitée au mois actuel
          periodText = `Vue du mois ${monthNames[currentMonth]} ${currentYear}`;
      } else {
          // Vue étendue
          if (startYear === endYear) {
              if (startMonth === endMonth) {
                  periodText = `Vue étendue: ${monthNames[startMonth]} ${startYear}`;
              } else {
                  periodText = `Vue étendue: ${monthNames[startMonth]} à ${monthNames[endMonth]} ${startYear}`;
              }
          } else {
              periodText = `Vue étendue: ${monthNames[startMonth]} ${startYear} à ${monthNames[endMonth]} ${endYear}`;
          }
      }
      
      periodIndicator.textContent = periodText;
  }
  
  // Fonction pour obtenir les activités pour une date donnée
  function getActivitiesForDate(date) {
      return filteredActivities.filter(activity => {
          const start = new Date(activity.date_debut);
          const end = new Date(activity.date_fin);
          const checkDate = new Date(date);
          
          // Réinitialiser l'heure pour la comparaison
          start.setHours(0, 0, 0, 0);
          end.setHours(0, 0, 0, 0);
          checkDate.setHours(0, 0, 0, 0);
          
          return checkDate >= start && checkDate <= end;
      });
  }
  
  // Fonction pour créer un élément d'activité
  function createActivityElement(activity) {
      const element = document.createElement('div');
      element.className = 'calendar-activity';
      element.setAttribute('data-activity-id', activity.id);
      
      // Ajouter la classe multi-day si l'activité s'étend sur plusieurs jours
      const startDate = new Date(activity.date_debut);
      const endDate = new Date(activity.date_fin);
      const daysDiff = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
      
      if (daysDiff > 1) {
          element.classList.add('multi-day');
      }
      
      // Déterminer la couleur selon le statut
      const statusColors = {
          'en_attente': '#28a745',
          'en_cours': '#007bff',
          'termine': '#ffc107',
          'bloque': '#dc3545'
      };
      
      element.style.backgroundColor = statusColors[activity.statut] || '#6c757d';
      element.style.color = 'white';
      element.style.fontSize = '0.75rem';
      element.style.padding = '2px 4px';
      element.style.margin = '1px 0';
      element.style.borderRadius = '3px';
      element.style.cursor = 'pointer';
      element.style.overflow = 'hidden';
      element.style.textOverflow = 'ellipsis';
      element.style.whiteSpace = 'nowrap';
      element.style.position = 'relative';
      
      // Afficher le titre et la progression
      element.innerHTML = `
          <div class="activity-title">${activity.titre}</div>
          <div class="activity-progress">
              <div class="progress-mini" style="height: 2px; background: rgba(255,255,255,0.3); margin: 1px 0;">
                  <div class="progress-bar-mini" style="height: 100%; background: white; width: ${activity.progression || 0}%; transition: width 0.3s ease;"></div>
              </div>
              <small style="font-size: 0.6rem; opacity: 0.9;">${activity.progression || 0}%</small>
          </div>
      `;
      
      element.title = `${activity.titre} (${activity.owner}) - Progression: ${activity.progression || 0}%\n\nClic droit: Voir les détails et modifier la progression`;
      
      // Ajouter un événement de clic droit pour voir les détails avec modification de progression
      element.addEventListener('contextmenu', (e) => {
          e.preventDefault();
          e.stopPropagation();
          showActivityDetailsWithProgress(activity);
      });
      
      // Ajouter un événement de survol pour afficher un résumé
      element.addEventListener('mouseenter', (e) => {
          showActivityTooltip(e, activity);
      });
      
      element.addEventListener('mouseleave', hideActivityTooltip);
      
      return element;
  }
  
  // Fonction pour afficher un tooltip d'activité
  function showActivityTooltip(event, activity) {
      const tooltip = document.createElement('div');
      tooltip.className = 'activity-tooltip';
      tooltip.innerHTML = `
          <div class="tooltip-header">
              <strong>${activity.titre}</strong>
          </div>
          <div class="tooltip-content">
              <div><i class="fas fa-user me-1"></i>${activity.owner}</div>
              <div><i class="fas fa-calendar me-1"></i>${new Date(activity.date_debut).toLocaleDateString('fr-FR')} - ${new Date(activity.date_fin).toLocaleDateString('fr-FR')}</div>
              <div><i class="fas fa-chart-line me-1"></i>Progression: ${activity.progression || 0}%</div>
              <div><i class="fas fa-info-circle me-1"></i>Statut: ${getStatusText(activity.statut)}</div>
          </div>
          <div class="tooltip-footer">
              <small class="text-muted">Clic gauche: Modifier progression | Clic droit: Détails</small>
          </div>
      `;
      
      document.body.appendChild(tooltip);
      
      // Positionner le tooltip
      const rect = event.target.getBoundingClientRect();
      tooltip.style.left = `${rect.left + rect.width / 2}px`;
      tooltip.style.top = `${rect.top - tooltip.offsetHeight - 10}px`;
      
      // Stocker la référence pour le supprimer
      event.target.tooltip = tooltip;
  }
  
  // Fonction pour masquer le tooltip
  function hideActivityTooltip() {
      const tooltips = document.querySelectorAll('.activity-tooltip');
      tooltips.forEach(tooltip => tooltip.remove());
  }
  
  // Fonction pour créer une activité depuis le calendrier
  function createActivityFromCalendar() {
      // Marquer que l'utilisateur était dans le calendrier
      window.wasInCalendar = true;
      
      // Fermer le modal du calendrier
      const calendarModal = bootstrap.Modal.getInstance(document.getElementById('calendarModal'));
      calendarModal.hide();
      
      // Attendre un peu que le modal se ferme
      setTimeout(() => {
          // Ouvrir le modal de création d'activité
          const createModal = new bootstrap.Modal(document.getElementById('createActivityModal'));
          createModal.show();
          
          // Initialiser la validation des dates
          initializeDateValidation();
          
          console.log('📅 [CALENDAR] Ouverture du modal de création depuis le calendrier');
      }, 300);
  }
  
  // Fonction pour revenir au calendrier depuis le modal de création
  function returnToCalendar() {
      // Fermer le modal de création
      const createModal = bootstrap.Modal.getInstance(document.getElementById('createActivityModal'));
      createModal.hide();
      
      // Attendre un peu que le modal se ferme
      setTimeout(() => {
          // Rouvrir le calendrier
          openActivityCalendar();
          
          console.log('📅 [CALENDAR] Retour au calendrier depuis la création');
      }, 300);
  }
  

  
  // Fonction pour afficher les détails d'une activité avec modification de progression
  function showActivityDetailsWithProgress(activity) {
      const detailsHtml = `
          <div class="p-3">
              <h6 class="mb-3 text-primary">${activity.titre}</h6>
              
              <!-- Informations de base -->
              <div class="row mb-3">
                  <div class="col-6">
                      <small class="text-muted">Début:</small><br>
                      <strong>${activity.date_debut.toLocaleDateString('fr-FR')}</strong>
                  </div>
                  <div class="col-6">
                      <small class="text-muted">Fin:</small><br>
                      <strong>${activity.date_fin.toLocaleDateString('fr-FR')}</strong>
                  </div>
              </div>
              
              <div class="row mb-3">
                  <div class="col-6">
                      <small class="text-muted">Statut:</small><br>
                      <span class="badge" style="background: ${getStatusColor(activity.statut)};">
                          ${getStatusText(activity.statut)}
                      </span>
                  </div>
                  <div class="col-6">
                      <small class="text-muted">Assigné à:</small><br>
                      <strong>${activity.owner}</strong>
                  </div>
              </div>
              
              <!-- Modification de la progression -->
              <hr class="my-3">
              <div class="mb-3">
                  <label class="form-label fw-bold">
                      <i class="fas fa-chart-line me-2 text-primary"></i>
                      Progression actuelle: <span class="text-primary" id="currentProgressDisplay">${activity.progression || 0}%</span>
                  </label>
                  <input type="range" class="form-range" id="progressSliderDetails" 
                         min="0" max="100" value="${activity.progression || 0}" 
                         step="5" oninput="updateProgressDisplayDetails(this.value)">
                  <div class="d-flex justify-content-between">
                      <small class="text-muted">0%</small>
                      <small class="text-muted">100%</small>
                  </div>
              </div>
              
              <div class="mb-3">
                  <label class="form-label">Nouvelle progression:</label>
                  <div class="input-group">
                      <input type="number" class="form-control" id="progressInputDetails" 
                             min="0" max="100" value="${activity.progression || 0}" 
                             oninput="updateProgressSliderDetails(this.value)">
                      <span class="input-group-text">%</span>
                  </div>
              </div>
              
              <div class="alert alert-info">
                  <i class="fas fa-info-circle me-2"></i>
                  <small>La progression sera mise à jour en temps réel sur toutes les instances de cette activité dans le calendrier.</small>
              </div>
          </div>
      `;
      
      // Utiliser Bootstrap Modal ou Popover pour afficher les détails
      const modal = new bootstrap.Modal(document.getElementById('calendarModal'));
      modal.hide();
      
      // Créer un modal temporaire pour les détails avec progression
      const detailsModal = createDetailsModalWithProgress(detailsHtml, activity);
      document.body.appendChild(detailsModal);
      
      const detailsBsModal = new bootstrap.Modal(detailsModal);
      detailsBsModal.show();
      
      // Nettoyer après fermeture
      detailsModal.addEventListener('hidden.bs.modal', function() {
          document.body.removeChild(detailsModal);
          // Rouvrir le calendrier
          modal.show();
      });
  }
  
  // Fonction pour créer un modal de détails avec progression
  function createDetailsModalWithProgress(content, activity) {
      const modal = document.createElement('div');
      modal.className = 'modal fade';
      modal.setAttribute('tabindex', '-1');
      
      modal.innerHTML = `
          <div class="modal-dialog modal-lg">
              <div class="modal-content">
                  <div class="modal-header">
                      <h6 class="modal-title">
                          <i class="fas fa-info-circle me-2 text-primary"></i>
                          Détails de l'activité
                      </h6>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                      ${content}
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                      <button type="button" class="btn btn-primary" onclick="saveProgressFromDetails(${activity.id})">
                          <i class="fas fa-save me-1"></i>Enregistrer la progression
                      </button>
                  </div>
              </div>
          </div>
      `;
      
      return modal;
  }
  
  // Fonction pour obtenir la couleur du statut
  function getStatusColor(statut) {
      const colors = {
          'en_attente': '#28a745',
          'en_cours': '#007bff',
          'termine': '#ffc107',
          'bloque': '#dc3545'
      };
      return colors[statut] || '#6c757d';
  }
  
  // Fonction pour obtenir le texte du statut
  function getStatusText(statut) {
      const texts = {
          'en_attente': 'En attente',
          'en_cours': 'En cours',
          'termine': 'Terminé',
          'bloque': 'Bloqué'
      };
      return texts[statut] || statut;
  }
  
  // Fonction pour aller au mois précédent
  function previousMonth() {
      currentDate.setMonth(currentDate.getMonth() - 1);
      generateCalendar();
  }
  
  // Fonction pour aller au mois suivant
  function nextMonth() {
      currentDate.setMonth(currentDate.getMonth() + 1);
      generateCalendar();
  }
  
  // Fonction pour aller à aujourd'hui
  function goToToday() {
      currentDate = new Date();
      generateCalendar();
  }
  
  // Fonction pour appliquer les filtres
  function applyFilters() {
      const statusFilter = document.getElementById('statusFilter').value;
      const priorityFilter = document.getElementById('priorityFilter').value;
      const searchFilter = document.getElementById('searchActivity').value.toLowerCase();
      
      currentFilters = {
          status: statusFilter,
          priority: priorityFilter,
          search: searchFilter
      };
      
      // Filtrer les activités
      filteredActivities = activitiesData.filter(activity => {
          const matchesStatus = !statusFilter || activity.statut === statusFilter;
          const matchesPriority = !priorityFilter || activity.priorite === priorityFilter;
          const matchesSearch = !searchFilter || 
              activity.titre.toLowerCase().includes(searchFilter) ||
              activity.owner.toLowerCase().includes(searchFilter);
          
          return matchesStatus && matchesPriority && matchesSearch;
      });
      
      console.log('🔍 [FILTERS] Filtres appliqués:', currentFilters);
      console.log('📊 [FILTERS] Activités filtrées:', filteredActivities.length);
      
      // Mettre à jour les statistiques
      updateStatistics();
      
      // Régénérer le calendrier
      generateCalendar();
  }
  
  // Fonction pour mettre à jour les statistiques
  function updateStatistics() {
      const total = activitiesData.length;
      const enCours = activitiesData.filter(a => a.statut === 'en_cours').length;
      const termine = activitiesData.filter(a => a.statut === 'termine').length;
      const bloque = activitiesData.filter(a => a.statut === 'bloque').length;
      const enRetard = activitiesData.filter(a => {
          const today = new Date();
          const dateFin = new Date(a.date_fin);
          return dateFin < today && a.statut !== 'termine';
      }).length;
      
      // Calculer la progression moyenne
      const progressionMoyenne = activitiesData.length > 0 ? 
          Math.round(activitiesData.reduce((sum, a) => sum + (parseFloat(a.progression) || 0), 0) / activitiesData.length) : 0;
      
      console.log('📊 [STATS] Calcul progression moyenne:', {
          total_activites: activitiesData.length,
          progression_moyenne: progressionMoyenne,
          details: activitiesData.map(a => ({ id: a.id, progression: a.progression, parsed: parseFloat(a.progression) || 0 }))
      });
      
      // Mettre à jour l'affichage
      document.getElementById('totalActivities').textContent = total;
      document.getElementById('enCoursCount').textContent = enCours;
      document.getElementById('termineCount').textContent = termine;
      document.getElementById('bloqueCount').textContent = bloque;
      document.getElementById('retardCount').textContent = enRetard;
      document.getElementById('progressionMoyenne').textContent = progressionMoyenne + '%';
      
      console.log('📊 [STATS] Statistiques mises à jour');
  }
  
  // Fonction pour basculer entre les vues (uniquement mois et liste)
  function toggleCalendarView() {
      if (currentView === 'month') {
          currentView = 'list';
          generateListView();
      } else {
          currentView = 'month';
          generateCalendar();
      }
      
      // Mettre à jour le bouton
      const viewButton = document.querySelector('[onclick="toggleCalendarView()"]');
      const icons = {
          'month': 'fas fa-th-large',
          'list': 'fas fa-list'
      };
      const labels = {
          'month': 'Vue',
          'list': 'Liste'
      };
      
      viewButton.innerHTML = `<i class="${icons[currentView]} me-1"></i>${labels[currentView]}`;
      
      console.log('🔄 [VIEW] Vue changée vers:', currentView);
  }
  

  
  // Fonction pour générer la vue liste
  function generateListView() {
      const grid = document.getElementById('calendarGrid');
      grid.innerHTML = '';
      
      // Créer l'en-tête de la liste
      const listHeader = document.createElement('div');
      listHeader.className = 'list-header row fw-bold p-2 border-bottom bg-light';
      listHeader.innerHTML = `
          <div class="col-3">Activité</div>
          <div class="col-2">Statut</div>
          <div class="col-2">Priorité</div>
          <div class="col-2">Dates</div>
          <div class="col-2">Assigné à</div>
          <div class="col-1">Progression</div>
      `;
      grid.appendChild(listHeader);
      
      // Trier les activités par date de début
      const sortedActivities = [...filteredActivities].sort((a, b) => 
          new Date(a.date_debut) - new Date(b.date_debut)
      );
      
      // Générer les lignes d'activités
      sortedActivities.forEach(activity => {
          const activityRow = document.createElement('div');
          activityRow.className = 'activity-row row p-2 border-bottom align-items-center';
          activityRow.style.cursor = 'pointer';
          
          activityRow.innerHTML = `
              <div class="col-3">
                  <strong>${activity.titre}</strong>
                  ${activity.description ? `<br><small class="text-muted">${activity.description}</small>` : ''}
              </div>
              <div class="col-2">
                  <span class="badge" style="background: ${getStatusColor(activity.statut)};">
                      ${getStatusText(activity.statut)}
                  </span>
              </div>
              <div class="col-2">
                  <span class="badge" style="background: ${getPriorityColor(activity.priorite)};">
                      ${activity.priorite.charAt(0).toUpperCase() + activity.priorite.slice(1)}
                  </span>
              </div>
              <div class="col-2">
                  <small>
                      <i class="fas fa-play text-success"></i> ${new Date(activity.date_debut).toLocaleDateString('fr-FR')}<br>
                      <i class="fas fa-flag-checkered text-danger"></i> ${new Date(activity.date_fin).toLocaleDateString('fr-FR')}
                  </small>
              </div>
              <div class="col-2">
                  <span class="badge bg-info">${activity.owner}</span>
              </div>
              <div class="col-1">
                  <div class="progress" style="height: 8px;">
                      <div class="progress-bar" style="width: ${activity.progression || 0}%; background: ${getStatusColor(activity.statut)};"></div>
                  </div>
                  <small class="text-muted">${activity.progression || 0}%</small>
              </div>
          `;
          
          // Ajouter un événement de clic
          activityRow.addEventListener('click', () => {
              showActivityDetails(activity);
          });
          
          grid.appendChild(activityRow);
      });
  }
  

  
  // Fonction pour obtenir la couleur de la priorité
  function getPriorityColor(priority) {
      const colors = {
          'basse': '#28a745',
          'moyenne': '#ffc107',
          'haute': '#fd7e14',
          'critique': '#dc3545'
      };
      return colors[priority] || '#6c757d';
  }

 // Fonction pour initialiser la validation des dates
 function initializeDateValidation() {
     const sousActionEcheance = '{{ $sousAction->date_echeance }}';
     
     if (sousActionEcheance) {
         const echeanceDate = new Date(sousActionEcheance);
         const today = new Date();
         
         // Formater la date d'échéance pour l'attribut max
         const echeanceFormatted = echeanceDate.toISOString().split('T')[0];
         
         // Mettre à jour les attributs max des champs de date
         const dateDebutInput = document.getElementById('date_debut');
         const dateFinInput = document.getElementById('date_fin');
         
         if (dateDebutInput) {
             dateDebutInput.max = echeanceFormatted;
             dateDebutInput.min = today.toISOString().split('T')[0];
         }
         
         if (dateFinInput) {
             dateFinInput.max = echeanceFormatted;
             dateFinInput.min = today.toISOString().split('T')[0];
         }
         
         console.log('📅 [DATE VALIDATION] Dates grisées au-delà du', echeanceFormatted);
     }
 }

 // Fonction de validation des dates côté client
 function validateActivityDates() {
     const sousActionEcheance = '{{ $sousAction->date_echeance }}';
     const dateDebut = document.getElementById('date_debut').value;
     const dateFin = document.getElementById('date_fin').value;
     
     if (!dateDebut || !dateFin) {
         alert('⚠️ Veuillez remplir les dates de début et de fin');
         return false;
     }
     
     const debutDate = new Date(dateDebut);
     const finDate = new Date(dateFin);
     const today = new Date();
     
     // Vérifier que la date de début n'est pas dans le passé
     if (debutDate < today.setHours(0, 0, 0, 0)) {
         alert('⚠️ La date de début ne peut pas être dans le passé');
         return false;
     }
     
     // Vérifier que la date de fin est après la date de début
     if (finDate <= debutDate) {
         alert('⚠️ La date de fin doit être après la date de début');
         return false;
     }
     
     // Vérifier que la date de fin ne dépasse pas l'échéance
     if (sousActionEcheance) {
         const echeanceDate = new Date(sousActionEcheance);
         if (finDate > echeanceDate) {
             alert('⚠️ La date de fin ne peut pas dépasser l\'échéance de la sous-action');
             return false;
         }
     }
     
     console.log('✅ [DATE VALIDATION] Dates validées avec succès');
     return true;
 }

 // Fonction de validation des dates côté client pour l'édition
 function validateEditActivityDates() {
     const sousActionEcheance = '{{ $sousAction->date_echeance }}';
     const dateDebut = document.getElementById('edit_date_debut').value;
     const dateFin = document.getElementById('edit_date_fin').value;
     
     if (!dateDebut || !dateFin) {
         alert('⚠️ Veuillez remplir les dates de début et de fin');
         return false;
     }
     
     const debutDate = new Date(dateDebut);
     const finDate = new Date(dateFin);
     const today = new Date();
     
     // Vérifier que la date de début n'est pas dans le passé
     if (debutDate < today.setHours(0, 0, 0, 0)) {
         alert('⚠️ La date de début ne peut pas être dans le passé');
         return false;
     }
     
     // Vérifier que la date de fin est après la date de début
     if (finDate <= debutDate) {
         alert('⚠️ La date de fin doit être après la date de début');
         return false;
     }
     
     // Vérifier que la date de fin ne dépasse pas l'échéance
     if (sousActionEcheance) {
         const echeanceDate = new Date(sousActionEcheance);
         if (finDate > echeanceDate) {
             alert('⚠️ La date de fin ne peut pas dépasser l\'échéance de la sous-action');
             return false;
         }
     }
     
     console.log('✅ [EDIT DATE VALIDATION] Dates validées avec succès');
     return true;
 }

  // Fonction pour ouvrir le modal de création d'activité
 function openCreateActivityModal() {
     const modal = new bootstrap.Modal(document.getElementById('createActivityModal'));
     modal.show();
     
     // Initialiser la validation des dates
     initializeDateValidation();
 }

 // Fonction pour ouvrir le modal d'édition d'activité
 function openEditActivityModal(activityId) {
     console.log('🔄 [EDIT] Ouverture du modal d\'édition pour l\'activité', activityId);
     
     // Charger les données de l'activité via AJAX
     fetch(`/activities/${activityId}/edit`)
         .then(response => {
             if (!response.ok) {
                 throw new Error(`HTTP error! status: ${response.status}`);
             }
             return response.json();
         })
         .then(data => {
             if (data.success) {
                 const activity = data.activity;
                 console.log('✅ [EDIT] Données de l\'activité chargées:', activity);
                 
                 // Remplir le formulaire avec vérification des éléments
                 const formElements = {
                     'edit_activity_id': activity.id,
                     'edit_titre': activity.titre,
                     'edit_description': activity.description || '',
                     'edit_date_debut': new Date(activity.date_debut).toISOString().split('T')[0],
                     'edit_date_fin': new Date(activity.date_fin).toISOString().split('T')[0],
                     'edit_priorite': activity.priorite,
                     'edit_statut': activity.statut,
                     'edit_taux_avancement': activity.taux_avancement,
                     'edit_owner_id': activity.owner_id || '',
                     'edit_notes': activity.notes || ''
                 };
                 
                 // Remplir chaque champ avec vérification
                 Object.entries(formElements).forEach(([elementId, value]) => {
                     const element = document.getElementById(elementId);
                     if (element) {
                         element.value = value;
                     } else {
                         console.error(`❌ [EDIT] Élément non trouvé: ${elementId}`);
                     }
                 });
                 
                 // Ouvrir le modal
                 const modal = new bootstrap.Modal(document.getElementById('editActivityModal'));
                 modal.show();
                 
                 console.log('✅ [EDIT] Modal d\'édition ouvert avec succès');
             } else {
                 console.error('❌ [EDIT] Erreur lors du chargement:', data.message);
                 showToast('error', 'Erreur lors du chargement de l\'activité: ' + data.message);
             }
         })
         .catch(error => {
             console.error('💥 [EDIT] Erreur:', error);
             showToast('error', 'Erreur lors du chargement de l\'activité');
         });
 }

 // Fonction pour supprimer une activité
 function deleteActivity(activityId) {
     // Récupérer le titre de l'activité pour la confirmation
     const activityRow = document.querySelector(`tr[data-activity-id="${activityId}"]`);
     const activityTitle = activityRow ? activityRow.querySelector('strong').textContent : 'cette activité';
     
     console.log('🗑️ [DELETE] Tentative de suppression de l\'activité:', activityId, activityTitle);
     
     // Confirmation personnalisée
     if (confirm(`Êtes-vous sûr de vouloir supprimer l'activité "${activityTitle}" ?\n\nCette action est irréversible et mettra à jour la progression de la sous-action.`)) {
         
         // Afficher un indicateur de chargement
         const deleteButton = activityRow.querySelector('.btn-outline-danger');
         const originalContent = deleteButton.innerHTML;
         deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';
         deleteButton.disabled = true;
         
         fetch(`/activities/${activityId}`, {
             method: 'DELETE',
             headers: {
                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                 'Content-Type': 'application/json',
             },
         })
         .then(response => {
             if (!response.ok) {
                 throw new Error(`HTTP error! status: ${response.status}`);
             }
             return response.json();
         })
         .then(data => {
             if (data.success) {
                 console.log('✅ [DELETE] Activité supprimée avec succès:', data);
                 
                 // Afficher un toast de succès
                 showToast('success', data.message);
                 
                 // Mettre à jour la progression de la sous-action si disponible
                 if (data.sous_action) {
                     updateSousActionProgress(data.sous_action.id, data.sous_action.taux_avancement);
                 }
                 
                 // Supprimer la ligne du tableau avec animation
                 activityRow.style.transition = 'all 0.5s ease';
                 activityRow.style.opacity = '0';
                 activityRow.style.transform = 'translateX(-100%)';
                 
                 setTimeout(() => {
                     activityRow.remove();
                     
                     // Vérifier s'il reste des activités
                     const remainingActivities = document.querySelectorAll('tbody tr[data-activity-id]');
                     if (remainingActivities.length === 0) {
                         // Afficher le message "Aucune activité"
                         const tbody = document.querySelector('tbody');
                         tbody.innerHTML = `
                             <tr>
                                 <td colspan="7" class="text-center py-5">
                                     <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                     <h5 class="text-muted">Aucune activité créée</h5>
                                     <p class="text-muted">Commencez par créer votre première activité pour ce projet</p>
                                     <button type="button" class="btn btn-primary" onclick="openCreateActivityModal()">
                                         <i class="fas fa-plus me-1"></i>Créer une activité
                                     </button>
                                 </td>
                             </tr>
                         `;
                     }
                 }, 500);
                 
             } else {
                 console.error('❌ [DELETE] Erreur lors de la suppression:', data.message);
                 showToast('error', 'Erreur lors de la suppression: ' + data.message);
                 
                 // Restaurer le bouton
                 deleteButton.innerHTML = originalContent;
                 deleteButton.disabled = false;
             }
         })
         .catch(error => {
             console.error('💥 [DELETE] Erreur:', error);
             showToast('error', 'Erreur lors de la suppression de l\'activité');
             
             // Restaurer le bouton
             deleteButton.innerHTML = originalContent;
             deleteButton.disabled = false;
         });
     }
 }

 // Fonction pour mettre à jour la valeur affichée du slider
 function updateSliderValue(activityId, value) {
     const sliderValue = document.getElementById(`slider-value-${activityId}`);
     if (sliderValue) {
         sliderValue.textContent = value + '%';
     }
 }

 // Fonction pour mettre à jour la progression en temps réel
 function updateProgressRealTime(activityId, newProgress) {
     console.log(`🔄 [PROGRESS] Mise à jour en temps réel: Activité ${activityId} -> ${newProgress}%`);
     
     fetch(`/activities/${activityId}/progress`, {
         method: 'PATCH',
         headers: {
             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
             'Content-Type': 'application/json',
         },
         body: JSON.stringify({
             taux_avancement: newProgress
         })
     })
     .then(response => {
         if (!response.ok) {
             // Gérer spécifiquement les erreurs de validation
             if (response.status === 422) {
                 return response.json().then(errorData => {
                     throw new Error(errorData.message || 'Erreur de validation');
                 });
             }
             throw new Error(`HTTP error! status: ${response.status}`);
         }
         return response.json();
     })
     .then(data => {
         if (data.success) {
             console.log('✅ [PROGRESS] Progression mise à jour avec succès', data);
             
             // Mettre à jour l'affichage de l'activité
             updateActivityDisplay(activityId, newProgress);
             
             // Mettre à jour la progression de la sous-action
             if (data.sous_action) {
                 updateSousActionProgress(data.sous_action.id, data.sous_action.taux_avancement);
             }
             
             // Mettre à jour le statut de l'activité
             if (data.activity && data.activity.statut) {
                 updateActivityStatus(activityId, data.activity.statut);
             }
             
             // Afficher un toast de succès
             showToast('success', `Progression mise à jour: ${newProgress}%`);
             
         } else {
             console.error('❌ [PROGRESS] Erreur lors de la mise à jour:', data.message);
             showToast('error', 'Erreur lors de la mise à jour: ' + data.message);
         }
     })
     .catch(error => {
         console.error('💥 [PROGRESS] Erreur:', error);
         showToast('error', 'Erreur lors de la mise à jour de la progression');
     });
 }

 // Fonction pour mettre à jour l'affichage de l'activité
 function updateActivityDisplay(activityId, newProgress) {
     const progressBar = document.querySelector(`tr[data-activity-id="${activityId}"] .progress-bar`);
     const progressText = document.querySelector(`tr[data-activity-id="${activityId}"] .fw-bold`);
     
     if (progressBar && progressText) {
         progressBar.style.width = newProgress + '%';
         progressText.textContent = newProgress + '%';
     }
 }

 // Fonction pour mettre à jour la progression de la sous-action
 function updateSousActionProgress(sousActionId, newProgress) {
     const sousActionProgressBar = document.querySelector('.card-body .progress-bar');
     const sousActionProgressText = document.querySelector('.card-body .fw-bold');
     
     if (sousActionProgressBar && sousActionProgressText) {
         sousActionProgressBar.style.width = newProgress + '%';
         sousActionProgressText.textContent = newProgress + '%';
         
         // Mettre à jour la couleur selon la progression
         let newColor = '#007bff'; // Bleu par défaut
         if (newProgress >= 75) newColor = '#28a745'; // Vert
         else if (newProgress >= 50) newColor = '#ffc107'; // Orange
         
         sousActionProgressBar.style.background = newColor;
     }
 }

 // Fonction pour mettre à jour le statut de l'activité
 function updateActivityStatus(activityId, newStatut) {
     const statusBadge = document.querySelector(`tr[data-activity-id="${activityId}"] .badge[style*="background"]`);
     
     if (statusBadge) {
         // Mettre à jour le texte du statut
         const statutText = getStatutText(newStatut);
         statusBadge.textContent = statutText;
         
         // Mettre à jour la couleur du statut
         const statutColor = getStatutColor(newStatut);
         statusBadge.style.background = statutColor;
     }
 }

 // Fonction pour obtenir le texte du statut
 function getStatutText(statut) {
     const statuts = {
                     'en_attente': 'En attente',
         'en_cours': 'En cours',
         'termine': 'Terminé',
         'bloque': 'Bloqué'
     };
     return statuts[statut] || statut;
 }

 // Fonction pour obtenir la couleur du statut
 function getStatutColor(statut) {
     const colors = {
                     'en_attente': '#6c757d',
         'en_cours': '#007bff',
         'termine': '#28a745',
         'bloque': '#dc3545'
     };
     return colors[statut] || '#6c757d';
 }

   // Fonction pour afficher un toast
  function showToast(type, message) {
      // Créer un toast Bootstrap
      const toastContainer = document.getElementById('toast-container') || createToastContainer();
      
      // Déterminer la classe de couleur selon le type
      let bgClass = 'bg-info'; // Par défaut
      let icon = 'fas fa-info-circle';
      
      switch(type) {
          case 'success':
              bgClass = 'bg-success';
              icon = 'fas fa-check-circle';
              break;
          case 'error':
              bgClass = 'bg-danger';
              icon = 'fas fa-exclamation-circle';
              break;
          case 'warning':
              bgClass = 'bg-warning';
              icon = 'fas fa-exclamation-triangle';
              break;
          case 'info':
              bgClass = 'bg-info';
              icon = 'fas fa-info-circle';
              break;
      }
      
      const toastHtml = `
          <div class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="d-flex">
                  <div class="toast-body">
                      <i class="${icon} me-2"></i>
                      ${message}
                  </div>
                  <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
              </div>
          </div>
      `;
      
      toastContainer.insertAdjacentHTML('beforeend', toastHtml);
      
      // Afficher le dernier toast
      const toasts = toastContainer.querySelectorAll('.toast');
      const lastToast = toasts[toasts.length - 1];
      
      const bsToast = new bootstrap.Toast(lastToast);
      bsToast.show();
      
      // Supprimer le toast après 4 secondes
      setTimeout(() => {
          if (lastToast.parentNode) {
              lastToast.parentNode.removeChild(lastToast);
          }
      }, 4000);
      
      console.log(`🍞 [TOAST] Toast ${type} affiché: ${message}`);
  }

 // Fonction pour créer le conteneur de toasts
 function createToastContainer() {
     const container = document.createElement('div');
     container.id = 'toast-container';
     container.className = 'toast-container position-fixed top-0 end-0 p-3';
     container.style.zIndex = '9999';
     document.body.appendChild(container);
     return container;
 }

 // Fonction pour mettre à jour l'affichage d'une activité dans le tableau
 function updateActivityInTable(activity) {
     const activityRow = document.querySelector(`tr[data-activity-id="${activity.id}"]`);
     if (!activityRow) return;
     
     // Mettre à jour le titre et la description
     const titleCell = activityRow.querySelector('td:first-child strong');
     if (titleCell) titleCell.textContent = activity.titre;
     
     const descriptionCell = activityRow.querySelector('td:first-child small');
     if (descriptionCell && activity.description) {
         descriptionCell.textContent = activity.description.length > 50 ? 
             activity.description.substring(0, 50) + '...' : activity.description;
     }
     
     // Mettre à jour la priorité
     const priorityBadge = activityRow.querySelector('td:nth-child(2) .badge');
     if (priorityBadge) {
         priorityBadge.textContent = activity.priorite.charAt(0).toUpperCase() + activity.priorite.slice(1);
         priorityBadge.style.background = getPrioriteColor(activity.priorite);
     }
     
     // Mettre à jour le statut
     const statusBadge = activityRow.querySelector('td:nth-child(3) .badge');
     if (statusBadge) {
         statusBadge.textContent = getStatutText(activity.statut);
         statusBadge.style.background = getStatutColor(activity.statut);
     }
     
     // Mettre à jour les dates
     const dateCells = activityRow.querySelectorAll('td:nth-child(4) small');
     if (dateCells.length >= 2) {
         const dateDebut = new Date(activity.date_debut).toLocaleDateString('fr-FR');
         const dateFin = new Date(activity.date_fin).toLocaleDateString('fr-FR');
         
         dateCells[0].innerHTML = `<i class="fas fa-play me-1 text-success"></i>${dateDebut}`;
         dateCells[1].innerHTML = `<i class="fas fa-flag-checkered me-1 text-danger"></i>${dateFin}`;
     }
     
     // Mettre à jour l'assignation
     const ownerCell = activityRow.querySelector('td:nth-child(5) .badge');
     if (ownerCell && activity.owner) {
         ownerCell.textContent = activity.owner.name;
     }
     
     // Mettre à jour la progression
     const progressBar = activityRow.querySelector('.progress-bar');
     const progressText = activityRow.querySelector('.fw-bold');
     if (progressBar && progressText) {
         progressBar.style.width = activity.taux_avancement + '%';
         progressText.textContent = activity.taux_avancement + '%';
     }
     
     console.log('✅ [TABLE] Affichage de l\'activité mis à jour dans le tableau');
 }

 // Fonction pour obtenir la couleur de la priorité
 function getPrioriteColor(priorite) {
     const colors = {
         'basse': '#28a745',
         'moyenne': '#ffc107',
         'haute': '#fd7e14',
         'critique': '#dc3545'
     };
     return colors[priorite] || '#6c757d';
 }

 // Fonction pour mettre à jour l'affichage de l'activité
 function updateActivityDisplay(activityId, newProgress) {
     const progressBar = document.querySelector(`tr[data-activity-id="${activityId}"] .progress-bar`);
     const progressText = document.querySelector(`tr[data-activity-id="${activityId}"] .fw-bold`);
     
     if (progressBar && progressText) {
         progressBar.style.width = newProgress + '%';
         progressText.textContent = newProgress + '%';
     }
 }

   // Gestion du formulaire de création
  document.getElementById('createActivityForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      console.log('🔄 [CREATE] Soumission du formulaire de création');
      
      // Validation côté client des dates
      if (!validateActivityDates()) {
          return;
      }
      
      const formData = new FormData(this);
      
      // Afficher un indicateur de chargement
      const submitButton = this.querySelector('button[type="submit"]');
      const originalContent = submitButton.innerHTML;
      submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création...';
      submitButton.disabled = true;
      
      fetch('/activities', {
          method: 'POST',
          headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
          body: formData
      })
      .then(response => {
          if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
      })
      .then(data => {
          if (data.success) {
              console.log('✅ [CREATE] Activité créée avec succès:', data);
              
              // Afficher un toast de succès
              showToast('success', 'Activité créée avec succès !');
              
              // Fermer le modal
              const modal = bootstrap.Modal.getInstance(document.getElementById('createActivityModal'));
              modal.hide();
              
              // Réinitialiser le formulaire
              this.reset();
              
              // Recharger la page pour afficher la nouvelle activité
              setTimeout(() => {
                  location.reload();
              }, 1000);
              
              // Optionnel : Revenir au calendrier après création
              // Si l'utilisateur était dans le calendrier, on peut le rouvrir
              if (window.wasInCalendar) {
                  setTimeout(() => {
                      openActivityCalendar();
                  }, 1500);
              }
              
          } else {
              console.error('❌ [CREATE] Erreur lors de la création:', data.message);
              showToast('error', 'Erreur lors de la création: ' + data.message);
              
              // Restaurer le bouton
              submitButton.innerHTML = originalContent;
              submitButton.disabled = false;
          }
      })
      .catch(error => {
          console.error('💥 [CREATE] Erreur:', error);
          
          // Afficher l'erreur détaillée
          let errorMessage = 'Erreur lors de la création de l\'activité';
          if (error.message) {
              errorMessage += ': ' + error.message;
          }
          showToast('error', errorMessage);
          
          // Restaurer le bouton
          submitButton.innerHTML = originalContent;
          submitButton.disabled = false;
      });
  });

 // Gestion du formulaire d'édition
 document.getElementById('editActivityForm').addEventListener('submit', function(e) {
     e.preventDefault();
     
     console.log('🔄 [EDIT] Soumission du formulaire d\'édition');
     
     // Validation côté client des dates
     if (!validateEditActivityDates()) {
         return;
     }
     
     const formData = new FormData(this);
     const activityId = document.getElementById('edit_activity_id').value;
     
     // Afficher un indicateur de chargement
     const submitButton = this.querySelector('button[type="submit"]');
     const originalContent = submitButton.innerHTML;
     submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mise à jour...';
     submitButton.disabled = true;
     
           fetch(`/activities/${activityId}`, {
          method: 'POST',
          headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
          body: formData
      })
     .then(response => {
         if (!response.ok) {
             throw new Error(`HTTP error! status: ${response.status}`);
         }
         return response.json();
     })
     .then(data => {
         if (data.success) {
             console.log('✅ [EDIT] Activité mise à jour avec succès:', data);
             
             // Afficher un toast de succès
             showToast('success', data.message);
             
             // Mettre à jour la progression de la sous-action si disponible
             if (data.sous_action) {
                 updateSousActionProgress(data.sous_action.id, data.sous_action.taux_avancement);
             }
             
             // Mettre à jour l'affichage de l'activité dans le tableau
             updateActivityInTable(data.activity);
             
             // Fermer le modal
             const modal = bootstrap.Modal.getInstance(document.getElementById('editActivityModal'));
             modal.hide();
             
             // Réinitialiser le formulaire
             this.reset();
             
         } else {
             console.error('❌ [EDIT] Erreur lors de la mise à jour:', data.message);
             showToast('error', 'Erreur lors de la mise à jour: ' + data.message);
             
             // Restaurer le bouton
             submitButton.innerHTML = originalContent;
             submitButton.disabled = false;
         }
     })
           .catch(error => {
          console.error('💥 [EDIT] Erreur:', error);
          
          // Afficher l'erreur détaillée
          let errorMessage = 'Erreur lors de la mise à jour de l\'activité';
          if (error.message) {
              errorMessage += ': ' + error.message;
          }
          showToast('error', errorMessage);
          
          // Restaurer le bouton
          submitButton.innerHTML = originalContent;
          submitButton.disabled = false;
      });
 });

 // Initialiser les popovers Bootstrap
 document.addEventListener('DOMContentLoaded', function() {
     // Initialiser tous les popovers
     const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
     const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
         return new bootstrap.Popover(popoverTriggerEl, {
             trigger: 'click',
             html: true,
             sanitize: false
         });
     });
     
     console.log('🎯 [POPOVER] Popovers initialisés:', popoverList.length);
     
     // Mettre à jour automatiquement le statut des activités selon la date de début
     updateActivitiesStatusOnLoad();
 });
 
 // Fonction pour mettre à jour automatiquement le statut des activités au chargement
 function updateActivitiesStatusOnLoad() {
     console.log('🔄 [STATUS] Mise à jour automatique du statut des activités...');
     
     const activities = @json($activities);
     const today = new Date();
     today.setHours(0, 0, 0, 0);
     
     activities.forEach(activity => {
         const dateDebut = new Date(activity.date_debut);
         dateDebut.setHours(0, 0, 0, 0);
         
         // Vérifier si le statut doit être mis à jour
         let shouldUpdate = false;
         let newStatus = activity.statut;
         
         if (dateDebut <= today && activity.statut === 'en_attente') {
             newStatus = 'en_cours';
             shouldUpdate = true;
         } else if (dateDebut > today && activity.statut === 'en_cours') {
                             newStatus = 'en_attente';
             shouldUpdate = true;
         }
         
         if (shouldUpdate) {
             console.log(`🔄 [STATUS] Mise à jour du statut de l'activité ${activity.id}: ${activity.statut} -> ${newStatus}`);
             
             // Mettre à jour l'affichage du statut
             const statusBadge = document.querySelector(`tr[data-activity-id="${activity.id}"] .badge[style*="background"]`);
             if (statusBadge) {
                 statusBadge.textContent = getStatutText(newStatus);
                 statusBadge.style.background = getStatutColor(newStatus);
             }
             
             // Mettre à jour l'état du bouton de progression
             updateProgressionButtonState(activity.id, newStatus === 'en_cours');
         }
     });
 }
 
 // Fonction pour mettre à jour l'état du bouton de progression
 function updateProgressionButtonState(activityId, canEdit) {
     const row = document.querySelector(`tr[data-activity-id="${activityId}"]`);
     if (!row) return;
     
     const editButton = row.querySelector('.btn-outline-primary[data-bs-toggle="popover"]');
     const disabledButton = row.querySelector('.btn-outline-secondary[disabled]');
     
     if (canEdit) {
         // Activer le bouton d'édition
         if (editButton) editButton.style.display = 'inline-block';
         if (disabledButton) disabledButton.style.display = 'none';
     } else {
         // Désactiver le bouton d'édition
         if (editButton) editButton.style.display = 'none';
         if (disabledButton) disabledButton.style.display = 'inline-block';
     }
 }

 // Fonction pour afficher le modal de modification de progression
 function showProgressEditModal(activity) {
     const modalHtml = `
         <div class="modal fade" id="progressEditModal" tabindex="-1" aria-labelledby="progressEditModalLabel" aria-hidden="true">
             <div class="modal-dialog modal-sm">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h6 class="modal-title" id="progressEditModalLabel">
                             <i class="fas fa-chart-line me-2 text-primary"></i>
                             Modifier la progression
                         </h6>
                         <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                     </div>
                     <div class="modal-body">
                         <div class="mb-3">
                             <h6 class="text-primary">${activity.titre}</h6>
                             <small class="text-muted">Assigné à: ${activity.owner}</small>
                         </div>
                         
                         <div class="mb-3">
                             <label class="form-label">Progression actuelle: <span class="fw-bold text-primary" id="currentProgress">${activity.progression || 0}%</span></label>
                             <input type="range" class="form-range" id="progressSlider" 
                                    min="0" max="100" value="${activity.progression || 0}" 
                                    step="5" oninput="updateProgressDisplay(this.value)">
                             <div class="d-flex justify-content-between">
                                 <small class="text-muted">0%</small>
                                 <small class="text-muted">100%</small>
                             </div>
                         </div>
                         
                         <div class="mb-3">
                             <label class="form-label">Nouvelle progression:</label>
                             <div class="input-group">
                                 <input type="number" class="form-control" id="progressInput" 
                                        min="0" max="100" value="${activity.progression || 0}" 
                                        oninput="updateProgressSlider(this.value)">
                                 <span class="input-group-text">%</span>
                             </div>
                         </div>
                         
                         <div class="alert alert-info">
                             <i class="fas fa-info-circle me-2"></i>
                             <small>La progression sera mise à jour en temps réel et la sous-action sera recalculée automatiquement.</small>
                         </div>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                         <button type="button" class="btn btn-primary" onclick="saveProgress(${activity.id})">
                             <i class="fas fa-save me-1"></i>Enregistrer
                         </button>
                     </div>
                 </div>
             </div>
         </div>
     `;
     
     // Supprimer l'ancien modal s'il existe
     const oldModal = document.getElementById('progressEditModal');
     if (oldModal) {
         oldModal.remove();
     }
     
     // Ajouter le nouveau modal au body
     document.body.insertAdjacentHTML('beforeend', modalHtml);
     
     // Afficher le modal
     const modal = new bootstrap.Modal(document.getElementById('progressEditModal'));
     modal.show();
     
     // Nettoyer après fermeture
     document.getElementById('progressEditModal').addEventListener('hidden.bs.modal', function() {
         this.remove();
     });
 }
 
 // Fonction pour mettre à jour l'affichage de la progression
 function updateProgressDisplay(value) {
     document.getElementById('currentProgress').textContent = value + '%';
     document.getElementById('progressInput').value = value;
 }
 
 // Fonction pour synchroniser le slider avec l'input
 function updateProgressSlider(value) {
     if (value >= 0 && value <= 100) {
         document.getElementById('progressSlider').value = value;
         document.getElementById('currentProgress').textContent = value + '%';
     }
 }
 
 // Fonction pour sauvegarder la progression
 function saveProgress(activityId) {
     const newProgress = parseInt(document.getElementById('progressInput').value);
     
     if (isNaN(newProgress) || newProgress < 0 || newProgress > 100) {
         showToast('error', 'Veuillez entrer une progression valide entre 0 et 100%');
         return;
     }
     
     // Afficher un indicateur de chargement
     const saveButton = document.querySelector('#progressEditModal .btn-primary');
     const originalContent = saveButton.innerHTML;
     saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde...';
     saveButton.disabled = true;
     
     // Appeler l'API pour mettre à jour la progression
     fetch(`/activities/${activityId}/progress`, {
         method: 'PATCH',
         headers: {
             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
             'Content-Type': 'application/json',
         },
         body: JSON.stringify({
             taux_avancement: newProgress
         })
     })
     .then(response => {
         if (!response.ok) {
             if (response.status === 422) {
                 return response.json().then(errorData => {
                     throw new Error(errorData.message || 'Erreur de validation');
                 });
             }
             throw new Error(`HTTP error! status: ${response.status}`);
         }
         return response.json();
     })
     .then(data => {
         if (data.success) {
             console.log('✅ [PROGRESS] Progression mise à jour avec succès', data);
             
             // Mettre à jour l'affichage de l'activité dans le calendrier
             updateCalendarActivityProgress(activityId, newProgress);
             
             // Mettre à jour la progression de la sous-action
             if (data.sous_action) {
                 updateSousActionProgress(data.sous_action.id, data.sous_action.taux_avancement);
             }
             
             // Mettre à jour le statut de l'activité si nécessaire
             if (data.activity && data.activity.statut) {
                 updateCalendarActivityStatus(activityId, data.activity.statut);
             }
             
             // Afficher un toast de succès
             showToast('success', `Progression mise à jour: ${newProgress}%`);
             
             // Fermer le modal
             const modal = bootstrap.Modal.getInstance(document.getElementById('progressEditModal'));
             modal.hide();
             
         } else {
             console.error('❌ [PROGRESS] Erreur lors de la mise à jour:', data.message);
             showToast('error', 'Erreur lors de la mise à jour: ' + data.message);
         }
     })
     .catch(error => {
         console.error('💥 [PROGRESS] Erreur:', error);
         showToast('error', 'Erreur lors de la mise à jour de la progression');
     })
     .finally(() => {
         // Restaurer le bouton
         saveButton.innerHTML = originalContent;
         saveButton.disabled = false;
     });
 }
 
 // Fonction pour mettre à jour la progression d'une activité dans le calendrier
 function updateCalendarActivityProgress(activityId, newProgress) {
     const activityElement = document.querySelector(`.calendar-activity[data-activity-id="${activityId}"]`);
     if (activityElement) {
         const progressBar = activityElement.querySelector('.progress-bar-mini');
         const progressText = activityElement.querySelector('small');
         
         if (progressBar && progressText) {
             progressBar.style.width = newProgress + '%';
             progressText.textContent = newProgress + '%';
         }
         
         // Mettre à jour le titre
         activityElement.title = activityElement.title.replace(/Progression: \d+%/, `Progression: ${newProgress}%`);
     }
 }
 
 // Fonction pour mettre à jour le statut d'une activité dans le calendrier
 function updateCalendarActivityStatus(activityId, newStatus) {
     const activityElement = document.querySelector(`.calendar-activity[data-activity-id="${activityId}"]`);
     if (activityElement) {
         const statusColors = {
             'en_attente': '#28a745',
             'en_cours': '#007bff',
             'termine': '#ffc107',
             'bloque': '#dc3545'
         };
         
         activityElement.style.backgroundColor = statusColors[newStatus] || '#6c757d';
     }
 }
</script>
@endpush
