<!-- Vue détail d'une Action -->
@if(!$selectedAction)
    <div class="alert alert-warning m-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Aucune action sélectionnée</strong><br>
        Veuillez sélectionner une action pour afficher ses détails.
    </div>
@else
<div class="action-detail-container">
    <!-- Informations de l'action -->
    <div class="action-info bg-white p-4 border-bottom">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    <div class="action-icon me-3" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">{{ $selectedAction->libelle }}</h3>
                        <p class="text-muted mb-0">
                            <strong>Code:</strong> {{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }}.{{ $selectedAction->code }} | 
                            <strong>Responsable:</strong> {{ $selectedAction->owner ? $selectedAction->owner->name : 'Non assigné' }}
                        </p>
                        <p class="text-muted mb-0">
                            <strong>Type:</strong> 
                            <span class="badge" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                                {{ ucfirst($selectedAction->type) }}
                            </span>
                            @if($selectedAction->date_echeance)
                                | <strong>Échéance:</strong> {{ \Carbon\Carbon::parse($selectedAction->date_echeance)->format('d/m/Y') }}
                            @endif
                        </p>
                    </div>
                </div>
                
                @if($selectedAction->description)
                    <p class="text-muted">{{ $selectedAction->description }}</p>
                @endif
            </div>
            
            <div class="col-md-4 text-end">
                <div class="progress-circle mb-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="progress-ring" style="width: 80px; height: 80px;">
                            <svg width="80" height="80" viewBox="0 0 80 80">
                                <circle cx="40" cy="40" r="35" stroke="#e9ecef" stroke-width="6" fill="none"/>
                                <circle cx="40" cy="40" r="35" stroke="{{ $pilier->getHierarchicalColor(4) }}" stroke-width="6" fill="none" 
                                        stroke-dasharray="{{ 2 * pi() * 35 }}" 
                                        stroke-dashoffset="{{ 2 * pi() * 35 * (1 - $selectedAction->taux_avancement / 100) }}"
                                        transform="rotate(-90 40 40)"/>
                            </svg>
                            <div class="progress-text">
                                <span class="fw-bold">{{ number_format($selectedAction->taux_avancement, 1) }}%</span>
                            </div>
                        </div>
                        <small class="text-muted mt-2">Progression</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes des 3 parents (Pilier, Objectif Stratégique, Objectif Spécifique) -->
    <div class="statistics-container p-4 bg-light">
        <div class="row g-3">
            <!-- Carte 1: Détails du Pilier parent -->
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
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
                            {{ $pilier->description ? Str::limit($pilier->description, 60) : 'Aucune description disponible' }}
                        </p>
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-muted me-2">Code :</span>
                            <span class="badge bg-secondary">{{ $pilier->code }}</span>
                        </div>
                        <div class="text-center">
                            <div class="progress-circle" style="--progress: {{ $pilier->taux_avancement }}%;">
                                <svg width="40" height="40" viewBox="0 0 40 40">
                                    <circle cx="20" cy="20" r="15" fill="none" stroke="#e9ecef" stroke-width="4"/>
                                    <circle cx="20" cy="20" r="15" fill="none" stroke="{{ $pilier->getHierarchicalColor(1) }}" stroke-width="4" 
                                            stroke-dasharray="94" stroke-dashoffset="{{ 94 - (94 * $pilier->taux_avancement / 100) }}"/>
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
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
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
                            {{ $selectedObjectifStrategique->description ? Str::limit($selectedObjectifStrategique->description, 60) : 'Aucune description disponible' }}
                        </p>
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-muted me-2">Code :</span>
                            <span class="badge bg-secondary">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}</span>
                        </div>
                        <div class="text-center">
                            <div class="progress-circle" style="--progress: {{ $selectedObjectifStrategique->taux_avancement }}%;">
                                <svg width="40" height="40" viewBox="0 0 40 40">
                                    <circle cx="20" cy="20" r="15" fill="none" stroke="#e9ecef" stroke-width="4"/>
                                    <circle cx="20" cy="20" r="15" fill="none" stroke="{{ $pilier->getHierarchicalColor(2) }}" stroke-width="4" 
                                            stroke-dasharray="94" stroke-dashoffset="{{ 94 - (94 * $selectedObjectifStrategique->taux_avancement / 100) }}"/>
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

            <!-- Carte 3: Détails de l'Objectif Spécifique parent -->
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                <div class="card h-100" style="border-left: 4px solid {{ $pilier->getHierarchicalColor(3) }};">
                    <div class="card-header" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Objectif Spécifique Parent
                        </h6>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title text-primary mb-2">{{ $selectedObjectifSpecifique->libelle }}</h6>
                        <p class="card-text text-muted small mb-2">
                            {{ $selectedObjectifSpecifique->description ? Str::limit($selectedObjectifSpecifique->description, 60) : 'Aucune description disponible' }}
                        </p>
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-muted me-2">Code :</span>
                            <span class="badge bg-secondary">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }}</span>
                        </div>
                        <div class="text-center">
                            <div class="progress-circle" style="--progress: {{ $selectedObjectifSpecifique->taux_avancement }}%;">
                                <svg width="40" height="40" viewBox="0 0 40 40">
                                    <circle cx="20" cy="20" r="15" fill="none" stroke="#e9ecef" stroke-width="4"/>
                                    <circle cx="20" cy="20" r="15" fill="none" stroke="{{ $pilier->getHierarchicalColor(3) }}" stroke-width="4" 
                                            stroke-dasharray="94" stroke-dashoffset="{{ 94 - (94 * $selectedObjectifSpecifique->taux_avancement / 100) }}"/>
                                </svg>
                                <div class="progress-text">
                                    <span class="progress-percentage fw-bold small">{{ number_format($selectedObjectifSpecifique->taux_avancement, 1) }}%</span>
                                    <small class="text-muted d-block">Avancement</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des sous-actions -->
    <div class="sous-actions-container p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-list-ul me-2" style="color: {{ $pilier->getHierarchicalColor(5) }};"></i>
                Sous-Actions
            </h4>
            
            @if($canCreateSousAction)
                <button type="button" 
                        class="btn btn-primary" 
                        style="background: {{ $pilier->getHierarchicalColor(5) }}; border-color: {{ $pilier->getHierarchicalColor(5) }};"
                        wire:click="openCreateSousActionModal">
                    <i class="fas fa-plus me-2"></i>Créer une Sous-Action
                </button>
            @endif
        </div>

        @if($selectedAction->sousActions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead style="background: {{ $pilier->getHierarchicalColor(5) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(5)) }};">
                        <tr>
                            <th style="width: 10%;">
                                <i class="fas fa-code me-1"></i>Code
                            </th>
                            <th style="width: 25%;">
                                <i class="fas fa-tasks me-1"></i>Libellé
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-percentage me-1"></i>Progression
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-user me-1"></i>Responsable
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-calendar me-1"></i>Échéance
                            </th>
                            <th style="width: 20%;">
                                <i class="fas fa-cogs me-1"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedAction->sousActions as $sousAction)
                            <tr>
                                <td>
                                    <span class="badge fw-bold" style="background: {{ $pilier->getHierarchicalColor(5) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(5)) }};">
                                        {{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }}.{{ $selectedAction->code }}.{{ $sousAction->code }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-dark">{{ $sousAction->libelle }}</strong>
                                        @if($sousAction->description)
                                            <br><small class="text-muted">{{ Str::limit($sousAction->description, 80) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-start">
                                        <div class="d-flex justify-content-between align-items-center w-100 mb-1">
                                            <small class="text-muted">Progression</small>
                                            <span class="badge fw-bold" style="background: {{ $pilier->getHierarchicalColor(5) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(5)) }};">
                                                {{ number_format($sousAction->taux_avancement, 2) }}%
                                            </span>
                                        </div>
                                        <div class="progress-slider-container mb-2" style="width: 100%;">
                                            <input type="range" 
                                                   class="form-range progress-slider" 
                                                   style="--progress-color: {{ $pilier->getHierarchicalColor(5) }};"
                                                   min="0" 
                                                   max="100" 
                                                   step="1"
                                                   value="{{ $sousAction->taux_avancement }}"
                                                   wire:change=" updateSousActionProgress({{ $sousAction->id }}, $event.target.value)"
                                                   title="Glissez pour modifier la progression ({{ $sousAction->taux_avancement }}%)">
                                            <div class="progress-labels d-flex justify-content-between mt-1">
                                                <small class="text-muted">0%</small>
                                                <small class="text-muted">100%</small>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($sousAction->owner)
                                        <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(5) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(5)) }}">
                                            <i class="fas fa-user me-1"></i>
                                            {{ Str::limit($sousAction->owner->name, 15) }}
                                        </span>
                                    @else
                                        <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(5) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(5)) }}">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sousAction->date_echeance)
                                        <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(5) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(5)) }}">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ \Carbon\Carbon::parse($sousAction->date_echeance)->format('d/m/Y') }}
                                        </span>
                                        @if($sousAction->date_realisation)
                                            <br><small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Réalisé le {{ \Carbon\Carbon::parse($sousAction->date_realisation)->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                        @if($sousAction->date_realisation)
                                            <br><small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Réalisé le {{ \Carbon\Carbon::parse($sousAction->date_realisation)->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if($canEditSousAction($sousAction))
                                            <!-- Bouton Modifier -->
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-warning btn-action" 
                                                    title="Modifier"
                                                    wire:click="setSousActionToEdit({{ $sousAction->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <!-- Bouton Supprimer -->
                                            @if($canDeleteSousAction($sousAction))
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger btn-action" 
                                                        title="Supprimer"
                                                        wire:click="deleteSousAction({{ $sousAction->id }})"
                                                        onclick="if(!confirm('Êtes-vous sûr de vouloir supprimer cette sous-action ?')) return false;">
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
                <i class="fas fa-list-ul fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucune sous-action</h5>
                <p class="text-muted">Commencez par créer votre première sous-action</p>
                
                @if($canCreateSousAction)
                    <button type="button" 
                            class="btn btn-primary" 
                            style="background: {{ $pilier->getHierarchicalColor(5) }}; border-color: {{ $pilier->getHierarchicalColor(5) }};"
                            wire:click="openCreateSousActionModal">
                        <i class="fas fa-plus me-2"></i>Créer la première sous-action
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
                    <i class="fas fa-rocket me-2" style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                    Actions Rapides
                </h5>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" 
                            class="btn btn-outline-secondary btn-sm"
                            wire:click="retourVersAction">
                        <i class="fas fa-arrow-left me-1"></i>Retour à l'Objectif Spécifique
                    </button>
                    
                    @if($canCreateSousAction)
                        <button type="button" 
                                class="btn btn-outline-primary btn-sm"
                                wire:click="openCreateSousActionModal">
                            <i class="fas fa-plus me-1"></i>Nouvelle Sous-Action
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="col-md-6">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line me-2" style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                    Statistiques
                </h5>
                <div class="row text-center">
                    <div class="col-4">
                        <div class="small text-muted">Total Sous-Actions</div>
                        <div class="fw-bold text-primary">{{ $selectedAction->sousActions->count() }}</div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Progression Action</div>
                        <div class="fw-bold text-success">{{ number_format($selectedAction->taux_avancement, 1) }}%</div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Type</div>
                        <div class="fw-bold text-info">{{ ucfirst($selectedAction->type) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
