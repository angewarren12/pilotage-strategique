<div>
    @if($showModal)
    <div class="modal fade show" style="display: block; z-index: 9999;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <!-- Header du Modal -->
                <div class="modal-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-sitemap fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0">
                                <strong>Vue Hiérarchique - {{ $pilier->libelle ?? 'Pilier' }}</strong>
                            </h5>
                            <small class="text-white-75">Navigation et gestion de la hiérarchie complète</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>

                <!-- Contenu du Modal -->
                <div class="modal-body p-0">
                    @if($isLoading)
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            <p class="mt-2">Chargement de la hiérarchie...</p>
                        </div>
                    @else
                        <!-- Breadcrumb Navigation avec Codification -->
                        <div class="p-3 bg-light border-bottom">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <button type="button" class="btn btn-link p-0" wire:click="retourListeObjectifs">
                                            <i class="fas fa-home me-1"></i>{{ $pilier->code ?? 'P' }} - {{ $pilier->libelle ?? 'Pilier' }}
                                        </button>
                                    </li>
                                    @foreach($breadcrumb as $item)
                                        <li class="breadcrumb-item">
                                            <button type="button" class="btn btn-link p-0" 
                                                    wire:click="naviguerVers{{ ucfirst($item['type']) }}({{ $item['id'] }})">
                                                {{ $item['code'] ?? $item['name'] }} - {{ $item['name'] }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ol>
                            </nav>
                        </div>

                        <!-- Barre d'outils -->
                        <div class="p-3 bg-light border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               placeholder="Rechercher dans la hiérarchie..."
                                               wire:model.live="searchTerm">
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleFullscreen()" title="Plein écran">
                                            <i class="fas fa-expand"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetZoom()" title="Zoom par défaut">
                                            <i class="fas fa-expand-arrows-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contenu principal -->
                        <div class="hierarchique-content" id="hierarchiqueContent">
                            <!-- Vue Pilier -->
                            @if($currentView === 'pilier')
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h4 class="mb-0">
                                                    <i class="fas fa-layer-group me-2 text-primary"></i>
                                                    Objectifs Stratégiques
                                                </h4>
                                                <button type="button" class="btn btn-primary btn-sm" wire:click="showCreateObjectifForm">
                                                    <i class="fas fa-plus me-2"></i>Créer un Objectif Stratégique
                                                </button>
                                            </div>
                                            
                                            @if($pilier->objectifsStrategiques->count() > 0)
                                                <div class="row">
                                                    @foreach($pilier->objectifsStrategiques as $objectifStrategique)
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card h-100 hierarchical-card" 
                                                                 wire:click="naviguerVersObjectifStrategique({{ $objectifStrategique->id }})">
                                                                <div class="card-body">
                                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                                        <h6 class="card-title mb-0">{{ $objectifStrategique->code }}</h6>
                                                                        <div class="dropdown">
                                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                                <i class="fas fa-ellipsis-v"></i>
                                                                            </button>
                                                                            <ul class="dropdown-menu">
                                                                                <li><a class="dropdown-item" href="#" wire:click.stop="editObjectifStrategique({{ $objectifStrategique->id }})">
                                                                                    <i class="fas fa-edit me-2"></i>Éditer
                                                                                </a></li>
                                                                                <li><hr class="dropdown-divider"></li>
                                                                                <li><a class="dropdown-item text-danger" href="#" wire:click.stop="deleteObjectifStrategique({{ $objectifStrategique->id }})">
                                                                                    <i class="fas fa-trash me-2"></i>Supprimer
                                                                                </a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <p class="card-text">{{ $objectifStrategique->libelle }}</p>
                                                                    <div class="progress mb-2" style="height: 8px;">
                                                                        <div class="progress-bar bg-{{ $this->getProgressStatus($objectifStrategique->taux_avancement) }}" 
                                                                             style="width: {{ $objectifStrategique->taux_avancement }}%"></div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <small class="text-muted">{{ number_format($objectifStrategique->taux_avancement, 1) }}%</small>
                                                                        <span class="badge bg-info">{{ $objectifStrategique->owner->name ?? 'Non assigné' }}</span>
                                                                    </div>
                                                                    <div class="mt-2">
                                                                        <small class="text-muted">
                                                                            <i class="fas fa-list me-1"></i>
                                                                            {{ $objectifStrategique->objectifsSpecifiques->count() }} objectif(s) spécifique(s)
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-5">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Aucun objectif stratégique</h5>
                                                    <p class="text-muted">Commencez par créer votre premier objectif stratégique</p>
                                                    <button type="button" class="btn btn-primary" wire:click="showCreateObjectifForm">
                                                        <i class="fas fa-plus me-2"></i>Créer le premier objectif
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header bg-primary text-white">
                                                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statistiques du Pilier</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span>Progression globale</span>
                                                            <span>{{ number_format($pilier->taux_avancement, 1) }}%</span>
                                                        </div>
                                                        <div class="progress" style="height: 10px;">
                                                            <div class="progress-bar bg-{{ $this->getProgressStatus($pilier->taux_avancement) }}" 
                                                                 style="width: {{ $pilier->taux_avancement }}%"></div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row text-center">
                                                        <div class="col-6">
                                                            <div class="h4 text-primary mb-0">{{ $pilier->objectifsStrategiques->count() }}</div>
                                                            <small class="text-muted">Objectifs Stratégiques</small>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="h4 text-info mb-0">{{ $pilier->objectifsStrategiques->sum(function($os) { return $os->objectifsSpecifiques->count(); }) }}</div>
                                                            <small class="text-muted">Objectifs Spécifiques</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <hr>
                                                    
                                                    <div class="row text-center">
                                                        <div class="col-6">
                                                            <div class="h4 text-warning mb-0">{{ $pilier->objectifsStrategiques->sum(function($os) { return $os->objectifsSpecifiques->sum(function($ospec) { return $ospec->actions->count(); }); }) }}</div>
                                                            <small class="text-muted">Actions</small>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="h4 text-success mb-0">{{ $pilier->objectifsStrategiques->sum(function($os) { return $os->objectifsSpecifiques->sum(function($ospec) { return $ospec->actions->sum(function($action) { return $action->sousActions->count(); }); }); }) }}</div>
                                                            <small class="text-muted">Sous-actions</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($currentView === 'objectifStrategique')
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h4 class="mb-0">
                                                    <i class="fas fa-bullseye me-2 text-info"></i>
                                                    Objectifs Spécifiques
                                                </h4>
                                                <button type="button" class="btn btn-info btn-sm" wire:click="showCreateObjectifSpecifiqueForm">
                                                    <i class="fas fa-plus me-2"></i>Créer un Objectif Spécifique
                                                </button>
                                            </div>
                                            
                                            @if($selectedObjectifStrategique->objectifsSpecifiques->count() > 0)
                                                <div class="row">
                                                    @foreach($selectedObjectifStrategique->objectifsSpecifiques as $objectifSpecifique)
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card h-100 hierarchical-card" 
                                                                 wire:click="naviguerVersObjectifSpecifique({{ $objectifSpecifique->id }})">
                                                                <div class="card-body">
                                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                                        <h6 class="card-title mb-0">{{ $objectifSpecifique->code }}</h6>
                                                                        <div class="dropdown">
                                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                                <i class="fas fa-ellipsis-v"></i>
                                                                            </button>
                                                                            <ul class="dropdown-menu">
                                                                                <li><a class="dropdown-item" href="#" wire:click.stop="editObjectifSpecifique({{ $objectifSpecifique->id }})">
                                                                                    <i class="fas fa-edit me-2"></i>Éditer
                                                                                </a></li>
                                                                                <li><hr class="dropdown-divider"></li>
                                                                                <li><a class="dropdown-item text-danger" href="#" wire:click.stop="deleteObjectifSpecifique({{ $objectifSpecifique->id }})">
                                                                                    <i class="fas fa-trash me-2"></i>Supprimer
                                                                                </a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <p class="card-text">{{ $objectifSpecifique->libelle }}</p>
                                                                    <div class="progress mb-2" style="height: 8px;">
                                                                        <div class="progress-bar bg-{{ $this->getProgressStatus($objectifSpecifique->taux_avancement) }}" 
                                                                             style="width: {{ $objectifSpecifique->taux_avancement }}%"></div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <small class="text-muted">{{ number_format($objectifSpecifique->taux_avancement, 1) }}%</small>
                                                                        <span class="badge bg-info">{{ $objectifSpecifique->owner->name ?? 'Non assigné' }}</span>
                                                                    </div>
                                                                    <div class="mt-2">
                                                                        <small class="text-muted">
                                                                            <i class="fas fa-list me-1"></i>
                                                                            {{ $objectifSpecifique->actions->count() }} action(s)
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-5">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Aucun objectif spécifique</h5>
                                                    <p class="text-muted">Commencez par créer votre premier objectif spécifique</p>
                                                    <button type="button" class="btn btn-info" wire:click="showCreateObjectifSpecifiqueForm">
                                                        <i class="fas fa-plus me-2"></i>Créer le premier objectif
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header bg-info text-white">
                                                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statistiques de l'Objectif Stratégique</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span>Progression globale</span>
                                                            <span>{{ number_format($selectedObjectifStrategique->taux_avancement, 1) }}%</span>
                                                        </div>
                                                        <div class="progress" style="height: 10px;">
                                                            <div class="progress-bar bg-{{ $this->getProgressStatus($selectedObjectifStrategique->taux_avancement) }}" 
                                                                 style="width: {{ $selectedObjectifStrategique->taux_avancement }}%"></div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row text-center">
                                                        <div class="col-6">
                                                            <div class="h4 text-info mb-0">{{ $selectedObjectifStrategique->objectifsSpecifiques->count() }}</div>
                                                            <small class="text-muted">Objectifs Spécifiques</small>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="h4 text-warning mb-0">{{ $selectedObjectifStrategique->objectifsSpecifiques->sum(function($ospec) { return $ospec->actions->count(); }) }}</div>
                                                            <small class="text-muted">Actions</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            <!-- Vue Objectif Spécifique -->
                            @elseif($currentView === 'objectifSpecifique')
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h4 class="mb-0">
                                                    <i class="fas fa-tasks me-2 text-warning"></i>
                                                    Actions
                                                </h4>
                                                <button type="button" class="btn btn-warning btn-sm" wire:click="showCreateActionForm">
                                                    <i class="fas fa-plus me-2"></i>Créer une Action
                                                </button>
                                            </div>
                                            
                                            @if($selectedObjectifSpecifique->actions->count() > 0)
                                                <div class="row">
                                                    @foreach($selectedObjectifSpecifique->actions as $action)
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card h-100 hierarchical-card" 
                                                                 wire:click="naviguerVersAction({{ $action->id }})">
                                                                <div class="card-body">
                                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                                        <h6 class="card-title mb-0">{{ $action->code }}</h6>
                                                                        <div class="dropdown">
                                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                                <i class="fas fa-ellipsis-v"></i>
                                                                            </button>
                                                                            <ul class="dropdown-menu">
                                                                                <li><a class="dropdown-item" href="#" wire:click.stop="editAction({{ $action->id }})">
                                                                                    <i class="fas fa-edit me-2"></i>Éditer
                                                                                </a></li>
                                                                                <li><hr class="dropdown-divider"></li>
                                                                                <li><a class="dropdown-item text-danger" href="#" wire:click.stop="deleteAction({{ $action->id }})">
                                                                                    <i class="fas fa-trash me-2"></i>Supprimer
                                                                                </a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <p class="card-text">{{ $action->libelle }}</p>
                                                                    <div class="progress mb-2" style="height: 8px;">
                                                                        <div class="progress-bar bg-{{ $this->getProgressStatus($action->taux_avancement) }}" 
                                                                             style="width: {{ $action->taux_avancement }}%"></div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <small class="text-muted">{{ number_format($action->taux_avancement, 1) }}%</small>
                                                                        <span class="badge bg-warning">{{ $action->owner->name ?? 'Non assigné' }}</span>
                                                                    </div>
                                                                    <div class="mt-2">
                                                                        <small class="text-muted">
                                                                            <i class="fas fa-list me-1"></i>
                                                                            {{ $action->sousActions->count() }} sous-action(s)
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-5">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Aucune action</h5>
                                                    <p class="text-muted">Commencez par créer votre première action</p>
                                                    <button type="button" class="btn btn-warning" wire:click="showCreateActionForm">
                                                        <i class="fas fa-plus me-2"></i>Créer la première action
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header bg-warning text-white">
                                                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statistiques de l'Objectif Spécifique</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span>Progression globale</span>
                                                            <span>{{ number_format($selectedObjectifSpecifique->taux_avancement, 1) }}%</span>
                                                        </div>
                                                        <div class="progress" style="height: 10px;">
                                                            <div class="progress-bar bg-{{ $this->getProgressStatus($selectedObjectifSpecifique->taux_avancement) }}" 
                                                                 style="width: {{ $selectedObjectifSpecifique->taux_avancement }}%"></div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row text-center">
                                                        <div class="col-6">
                                                            <div class="h4 text-warning mb-0">{{ $selectedObjectifSpecifique->actions->count() }}</div>
                                                            <small class="text-muted">Actions</small>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="h4 text-success mb-0">{{ $selectedObjectifSpecifique->actions->sum(function($action) { return $action->sousActions->count(); }) }}</div>
                                                            <small class="text-muted">Sous-actions</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            <!-- Vue Action -->
                            @elseif($currentView === 'action')
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h4 class="mb-0">
                                                    <i class="fas fa-list-check me-2 text-success"></i>
                                                    Sous-actions
                                                </h4>
                                                <button type="button" class="btn btn-success btn-sm" wire:click="showCreateSousActionForm">
                                                    <i class="fas fa-plus me-2"></i>Créer une Sous-action
                                                </button>
                                            </div>
                                            
                                            @if($selectedAction->sousActions->count() > 0)
                                                <div class="row">
                                                    @foreach($selectedAction->sousActions as $sousAction)
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card h-100 hierarchical-card" 
                                                                 wire:click="naviguerVersSousAction({{ $sousAction->id }})">
                                                                <div class="card-body">
                                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                                        <h6 class="card-title mb-0">{{ $sousAction->code }}</h6>
                                                                        <div class="dropdown">
                                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                                <i class="fas fa-ellipsis-v"></i>
                                                                            </button>
                                                                            <ul class="dropdown-menu">
                                                                                <li><a class="dropdown-item" href="#" wire:click.stop="editSousAction({{ $sousAction->id }})">
                                                                                    <i class="fas fa-edit me-2"></i>Éditer
                                                                                </a></li>
                                                                                <li><hr class="dropdown-divider"></li>
                                                                                <li><a class="dropdown-item text-danger" href="#" wire:click.stop="deleteSousAction({{ $sousAction->id }})">
                                                                                    <i class="fas fa-trash me-2"></i>Supprimer
                                                                                </a></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <p class="card-text">{{ $sousAction->libelle }}</p>
                                                                    <div class="progress mb-2" style="height: 8px;">
                                                                        <div class="progress-bar bg-{{ $this->getProgressStatus($sousAction->taux_avancement) }}" 
                                                                             style="width: {{ $sousAction->taux_avancement }}%"></div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <small class="text-muted">{{ number_format($sousAction->taux_avancement, 1) }}%</small>
                                                                        <span class="badge bg-success">{{ $sousAction->owner->name ?? 'Non assigné' }}</span>
                                                                    </div>
                                                                    <div class="mt-2">
                                                                        <small class="text-muted">
                                                                            <i class="fas fa-calendar me-1"></i>
                                                                            @if($sousAction->date_echeance)
                                                                                Échéance: {{ \Carbon\Carbon::parse($sousAction->date_echeance)->format('d/m/Y') }}
                                                                            @else
                                                                                Aucune échéance
                                                                            @endif
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-5">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Aucune sous-action</h5>
                                                    <p class="text-muted">Commencez par créer votre première sous-action</p>
                                                    <button type="button" class="btn btn-success" wire:click="showCreateSousActionForm">
                                                        <i class="fas fa-plus me-2"></i>Créer la première sous-action
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header bg-success text-white">
                                                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statistiques de l'Action</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <span>Progression globale</span>
                                                            <span>{{ number_format($selectedAction->taux_avancement, 1) }}%</span>
                                                        </div>
                                                        <div class="progress" style="height: 10px;">
                                                            <div class="progress-bar bg-{{ $this->getProgressStatus($selectedAction->taux_avancement) }}" 
                                                                 style="width: {{ $selectedAction->taux_avancement }}%"></div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row text-center">
                                                        <div class="col-12">
                                                            <div class="h4 text-success mb-0">{{ $selectedAction->sousActions->count() }}</div>
                                                            <small class="text-muted">Sous-actions</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            <!-- Vue Sous-Action -->
                            @elseif($currentView === 'sousAction')
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header bg-success text-white">
                                                    <h5 class="mb-0">
                                                        <i class="fas fa-list-check me-2"></i>
                                                        Détails de la Sous-action
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6>Informations générales</h6>
                                                            <p><strong>Code:</strong> {{ $selectedSousAction->code }}</p>
                                                            <p><strong>Libellé:</strong> {{ $selectedSousAction->libelle }}</p>
                                                            <p><strong>Description:</strong> {{ $selectedSousAction->description ?: 'Aucune description' }}</p>
                                                            <p><strong>Propriétaire:</strong> {{ $selectedSousAction->owner->name ?? 'Non assigné' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>Progression et dates</h6>
                                                            <div class="mb-3">
                                                                <label class="form-label">Progression</label>
                                                                <input type="range" class="form-range" 
                                                                       min="0" max="100" step="5"
                                                                       value="{{ $selectedSousAction->taux_avancement }}"
                                                                       wire:change="updateSousActionTaux({{ $selectedSousAction->id }}, $event.target.value)">
                                                                <div class="d-flex justify-content-between">
                                                                    <small>0%</small>
                                                                    <small>{{ $selectedSousAction->taux_avancement }}%</small>
                                                                    <small>100%</small>
                                                                </div>
                                                            </div>
                                                            <p><strong>Date d'échéance:</strong> 
                                                                @if($selectedSousAction->date_echeance)
                                                                    {{ \Carbon\Carbon::parse($selectedSousAction->date_echeance)->format('d/m/Y') }}
                                                                @else
                                                                    Non définie
                                                                @endif
                                                            </p>
                                                            <p><strong>Date de réalisation:</strong> 
                                                                @if($selectedSousAction->date_realisation)
                                                                    {{ \Carbon\Carbon::parse($selectedSousAction->date_realisation)->format('d/m/Y') }}
                                                                @else
                                                                    Non terminée
                                                                @endif
                                                            </p>
                                                            @if($selectedSousAction->date_echeance)
                                                                <p><strong>Écart:</strong> 
                                                                    <span class="badge bg-{{ $this->calculateEcart($selectedSousAction->date_echeance, $selectedSousAction->date_realisation) ? 'success' : 'warning' }}">
                                                                        {{ $this->calculateEcart($selectedSousAction->date_echeance, $selectedSousAction->date_realisation) }}
                                                                    </span>
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Footer du Modal -->
                <div class="modal-footer">
                    <div class="d-flex justify-content-between w-100">
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Vue hiérarchique interactive
                            </small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">
                                <i class="fas fa-times me-1"></i>
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9998;"></div>
    @endif

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }
        
        .hierarchical-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid #e9ecef;
        }
        
        .hierarchical-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-color: #007bff;
        }
        
        .hierarchical-content {
            transition: transform 0.3s ease;
        }
        
        .breadcrumb .btn-link {
            text-decoration: none;
            color: #6c757d;
        }
        
        .breadcrumb .btn-link:hover {
            color: #007bff;
        }
        
        .progress-bar {
            transition: width 0.8s ease;
        }
        
        /* Styles pour le plein écran */
        .fullscreen-content {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9999;
            background: white;
            overflow: auto;
        }
    </style>

    <script>
        // Variables pour le modal hiérarchique uniquement
        let hierarchiqueCurrentZoom = 100;
        let hierarchiqueIsFullscreen = false;
        
        function resetZoom() {
            hierarchiqueCurrentZoom = 100;
            updateHierarchiqueZoom();
        }
        
        function updateHierarchiqueZoom() {
            const content = document.getElementById('hierarchiqueContent');
            if (content) {
                content.style.transform = `scale(${hierarchiqueCurrentZoom / 100})`;
                content.style.transformOrigin = 'top left';
            }
        }
        
        function toggleFullscreen() {
            const content = document.getElementById('hierarchiqueContent');
            
            if (!hierarchiqueIsFullscreen) {
                content.classList.add('fullscreen-content');
                hierarchiqueIsFullscreen = true;
            } else {
                content.classList.remove('fullscreen-content');
                hierarchiqueIsFullscreen = false;
            }
        }
    </script>
</div> 