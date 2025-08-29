<!-- Vue détail d'un Objectif Spécifique -->
@if(!$selectedObjectifSpecifique)
    <div class="alert alert-warning m-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Aucun objectif spécifique sélectionné</strong><br>
        Veuillez sélectionner un objectif spécifique pour afficher ses détails.
    </div>
@else
<div class="objectif-specifique-detail-container">
    <!-- Informations de l'objectif spécifique -->
    <div class="objectif-specifique-info bg-white p-4 border-bottom">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    <div class="objectif-icon me-3" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }}; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">{{ $selectedObjectifSpecifique->libelle }}</h3>
                        <p class="text-muted mb-0">
                            <strong>Code:</strong> {{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }} | 
                            <strong>Propriétaire:</strong> {{ $selectedObjectifSpecifique->owner ? $selectedObjectifSpecifique->owner->name : 'Non assigné' }}
                        </p>
                        <p class="text-muted mb-0">
                            <strong>Objectif Stratégique:</strong> {{ $selectedObjectifStrategique->libelle }}
                        </p>
                        <p class="text-muted mb-0">
                            @php
                                $maxEcheanceOSP = $selectedObjectifSpecifique->getMaxEcheanceDate();
                            @endphp
                            @if($maxEcheanceOSP)
                                <strong>Échéance 
:</strong> 
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($maxEcheanceOSP)->format('d/m/Y') }}
                                </span>
                            @else
                                <strong>Échéance 
:</strong> 
                                <span class="badge bg-light text-muted">
                                    <i class="fas fa-calendar-times me-1"></i>
                                    Aucune échéance
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
                
                @if($selectedObjectifSpecifique->description)
                    <p class="text-muted">{{ $selectedObjectifSpecifique->description }}</p>
                @endif
            </div>
            
            <div class="col-md-4 text-end">
                <div class="progress-circle mb-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="progress-ring" style="width: 80px; height: 80px;">
                            <svg width="80" height="80" viewBox="0 0 80 80">
                                <circle cx="40" cy="40" r="35" stroke="#e9ecef" stroke-width="6" fill="none"/>
                                <circle cx="40" cy="40" r="35" stroke="{{ $pilier->getHierarchicalColor(3) }}" stroke-width="6" fill="none" 
                                        stroke-dasharray="{{ 2 * pi() * 35 }}" 
                                        stroke-dashoffset="{{ 2 * pi() * 35 * (1 - $selectedObjectifSpecifique->taux_avancement / 100) }}"
                                        transform="rotate(-90 40 40)"/>
                            </svg>
                            <div class="progress-text">
                                <span class="fw-bold">{{ number_format($selectedObjectifSpecifique->taux_avancement, 1) }}%</span>
                            </div>
                        </div>
                        <small class="text-muted mt-2">Progression</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes des parents (Pilier et Objectif Stratégique) -->
    <div class="statistics-container p-4 bg-light">
        <div class="row g-3">
            <!-- Carte 1: Détails du Pilier parent -->
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100" style="border-left: 4px solid {{ $pilier->getHierarchicalColor(1) }};">
                    <div class="card-header" style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }};">
                        <h6 class="mb-0">
                            <i class="fas fa-layer-group me-2"></i>
                            Pilier Parent
                        </h6>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title text-primary mb-2">{{ $pilier->libelle }}</h6>
                        <p class="card-text text-muted small mb-2">
                            {{ $pilier->description ? Str::limit($pilier->description, 80) : 'Aucune description disponible' }}
                        </p>
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-muted me-2">Code :</span>
                            <span class="badge bg-secondary">{{ $pilier->code }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            @php
                                $maxEcheancePilier = $pilier->getMaxEcheanceDate();
                            @endphp
                            @if($maxEcheancePilier)
                                <span class="text-muted me-2">Échéance 
 :</span>
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($maxEcheancePilier)->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted me-2">Échéance 
 :</span>
                                <span class="badge bg-light text-muted">
                                    <i class="fas fa-calendar-times me-1"></i>
                                    Aucune
                                </span>
                            @endif
                        </div>
                        <div class="text-center">
                            <div class="progress-circle" style="--progress: {{ $pilier->taux_avancement }}%;">
                                <svg width="50" height="50" viewBox="0 0 50 50">
                                    <circle cx="25" cy="25" r="20" fill="none" stroke="#e9ecef" stroke-width="5"/>
                                    <circle cx="25" cy="25" r="20" fill="none" stroke="{{ $pilier->getHierarchicalColor(1) }}" stroke-width="5" 
                                            stroke-dasharray="126" stroke-dashoffset="{{ 126 - (126 * $pilier->taux_avancement / 100) }}"/>
                                </svg>
                                <div class="progress-text">
                                    <span class="progress-percentage fw-bold small">{{ number_format($pilier->taux_avancement, 1) }}%</span>
                                    <small class="text-muted d-block">Avancement</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Carte 2: Détails de l'Objectif Stratégique parent -->
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100" style="border-left: 4px solid {{ $pilier->getHierarchicalColor(2) }};">
                    <div class="card-header" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                        <h6 class="mb-0">
                            <i class="fas fa-bullseye me-2"></i>
                            Objectif Stratégique Parent
                        </h6>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title text-primary mb-2">{{ $selectedObjectifStrategique->libelle }}</h6>
                        <p class="card-text text-muted small mb-2">
                            {{ $selectedObjectifStrategique->description ? Str::limit($selectedObjectifStrategique->description, 80) : 'Aucune description disponible' }}
                        </p>
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-muted me-2">Code :</span>
                            <span class="badge bg-secondary">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            @php
                                $maxEcheanceOS = $selectedObjectifStrategique->getMaxEcheanceDate();
                            @endphp
                            @if($maxEcheanceOS)
                                <span class="text-muted me-2">Échéance 
 :</span>
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($maxEcheanceOS)->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted me-2">Échéance 
 :</span>
                                <span class="badge bg-light text-muted">
                                    <i class="fas fa-calendar-times me-1"></i>
                                    Aucune
                                </span>
                            @endif
                        </div>
                        <div class="text-center">
                            <div class="progress-circle" style="--progress: {{ $selectedObjectifStrategique->taux_avancement }}%;">
                                <svg width="50" height="50" viewBox="0 0 50 50">
                                    <circle cx="25" cy="25" r="20" fill="none" stroke="#e9ecef" stroke-width="5"/>
                                    <circle cx="25" cy="25" r="20" fill="none" stroke="{{ $pilier->getHierarchicalColor(2) }}" stroke-width="5" 
                                            stroke-dasharray="126" stroke-dashoffset="{{ 126 - (126 * $selectedObjectifStrategique->taux_avancement / 100) }}"/>
                                </svg>
                                <div class="progress-text">
                                    <span class="progress-percentage fw-bold small">{{ number_format($selectedObjectifStrategique->taux_avancement, 1) }}%</span>
                                    <small class="text-muted d-block">Avancement</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des actions -->
    <div class="actions-container p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-tasks me-2" style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                Actions
            </h4>
            
            @if($canCreateAction)
                <button type="button" 
                        class="btn btn-primary" 
                        style="background: {{ $pilier->getHierarchicalColor(4) }}; border-color: {{ $pilier->getHierarchicalColor(4) }};"
                        wire:click="openCreateActionModal">
                    <i class="fas fa-plus me-2"></i>Créer une Action
                </button>
            @endif
        </div>

        @if($selectedObjectifSpecifique->actions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                        <tr>
                            <th style="width: 10%;">
                                <i class="fas fa-code me-1"></i>Code
                            </th>
                            <th style="width: 20%;">
                                <i class="fas fa-tasks me-1"></i>Libellé
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-percentage me-1"></i>Progression
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-calendar-alt me-1"></i>Date d'échéance
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-user me-1"></i>Responsable
                            </th>
                            <th style="width: 10%;">
                                <i class="fas fa-list me-1"></i>Sous-Actions
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-cogs me-1"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedObjectifSpecifique->actions as $action)
                            <tr>
                                <td>
                                    <span class="badge fw-bold" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                                        {{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }}.{{ $action->code }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-dark">{{ $action->libelle }}</strong>
                                        @if($action->description)
                                            <br><small class="text-muted">{{ Str::limit($action->description, 80) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-start">
                                        <div class="d-flex justify-content-between align-items-center w-100 mb-1">
                                            <small class="text-muted">Progression</small>
                                            <span class="badge fw-bold" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                                                {{ number_format($action->taux_avancement, 2) }}%
                                            </span>
                                        </div>
                                        <div class="progress mb-2 progress-compact" style="width: 100%; background: #e9ecef;">
                                            <div class="progress-bar" 
                                                 style="width: {{ $action->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(4) }};"
                                                 role="progressbar" 
                                                 aria-valuenow="{{ $action->taux_avancement }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $maxEcheanceAction = $action->getMaxEcheanceDate();
                                    @endphp
                                    @if($maxEcheanceAction)
                                        <span class="badge bg-warning text-dark" title="Date d'Échéance 
imale des sous-actions">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ \Carbon\Carbon::parse($maxEcheanceAction)->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted">
                                            <i class="fas fa-calendar-times me-1"></i>
                                            Aucune échéance
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($action->responsable)
                                        <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}">
                                            <i class="fas fa-user me-1"></i>
                                            {{ Str::limit($action->responsable->name, 15) }}
                                        </span>
                                    @else
                                        <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}">
                                        <i class="fas fa-list me-1"></i>
                                        {{ $action->sousActions->count() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- Bouton Voir -->
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary btn-action" 
                                                title="Voir les détails"
                                                wire:click="naviguerVersAction({{ $action->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        @if($canEditAction($action))
                                            <!-- Bouton Modifier -->
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-warning btn-action" 
                                                    title="Modifier"
                                                    wire:click="setActionToEdit({{ $action->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <!-- Bouton Supprimer -->
                                            @if($canDeleteAction($action))
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger btn-action" 
                                                        title="Supprimer"
                                                        wire:click="deleteAction({{ $action->id }})"
                                                        onclick="if(!confirm('Êtes-vous sûr de vouloir supprimer cette action ?')) return false;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        @endif
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
                <h5 class="text-muted">Aucune action</h5>
                <p class="text-muted">Commencez par créer votre première action</p>
                
                @if($canCreateAction)
                    <button type="button" 
                            class="btn btn-primary" 
                            style="background: {{ $pilier->getHierarchicalColor(4) }}; border-color: {{ $pilier->getHierarchicalColor(4) }};"
                            wire:click="openCreateActionModal">
                        <i class="fas fa-plus me-2"></i>Créer la première action
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Actions rapides -->
    <div class="quick-actions-container p-4 bg-light border-top">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">
                    <i class="fas fa-rocket me-2" style="color: {{ $pilier->getHierarchicalColor(3) }};"></i>
                    Actions Rapides
                </h5>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" 
                            class="btn btn-outline-secondary btn-sm"
                            wire:click="retourVersObjectifStrategique">
                        <i class="fas fa-arrow-left me-1"></i>Retour à l'Objectif Stratégique
                    </button>
                    
                    @if($canCreateAction)
                        <button type="button" 
                                class="btn btn-outline-primary btn-sm"
                                wire:click="openCreateActionModal">
                            <i class="fas fa-plus me-1"></i>Nouvelle Action
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="col-md-6">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line me-2" style="color: {{ $pilier->getHierarchicalColor(3) }};"></i>
                    Statistiques
                </h5>
                <div class="row text-center">
                    <div class="col-4">
                        <div class="small text-muted">Total Actions</div>
                        <div class="fw-bold text-primary">{{ $selectedObjectifSpecifique->actions->count() }}</div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Total Sous-Actions</div>
                        <div class="fw-bold text-success">{{ $selectedObjectifSpecifique->actions->flatMap->sousActions->count() }}</div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Progression</div>
                        <div class="fw-bold text-info">{{ number_format($selectedObjectifSpecifique->taux_avancement, 1) }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
