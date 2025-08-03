<div>
    <!-- Modal principal -->
    @if($showModal)
    <div class="modal fade show" style="display: block; z-index: 1055;" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header fixe avec breadcrumb dynamique -->
                <div class="modal-header bg-gradient-success text-white border-0" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <div class="d-flex align-items-center flex-grow-1">
                        <!-- Icône de navigation -->
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-layer-group text-white"></i>
                        </div>
                        
                        <!-- Breadcrumb dynamique -->
                        <nav aria-label="breadcrumb" class="flex-grow-1">
                            <ol class="breadcrumb mb-0" style="background: transparent;">
                                @foreach($currentBreadcrumb as $index => $item)
                                    <li class="breadcrumb-item {{ $index === count($currentBreadcrumb) - 1 ? 'active' : '' }}">
                                        @if($index === count($currentBreadcrumb) - 1)
                                            <!-- Élément actuel -->
                                            <span class="badge bg-white bg-opacity-25 text-white fs-6">{{ $item['code'] }}</span>
                                            <span class="text-white ms-2">{{ $item['label'] }}</span>
                                        @else
                                            <!-- Élément cliquable -->
                                            <a href="#" wire:click.prevent="{{ $item['action'] }}" class="text-decoration-none">
                                                <span class="badge bg-white bg-opacity-25 text-white fs-6">{{ $item['code'] }}</span>
                                            </a>
                                            <i class="fas fa-chevron-right text-white mx-2"></i>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        </nav>
                    </div>
                    
                    <!-- Bouton fermer -->
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>
                
                <!-- Animation de chargement -->
                @if($isLoading)
                    <div class="text-center py-5" wire:loading>
                        <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="text-muted mt-3">Chargement en cours...</p>
                    </div>
                @endif

                <!-- Body du modal -->
                <div class="modal-body p-0" style="max-height: 80vh; overflow-y: auto;">
                    
                    <!-- Vue principale - Détails du pilier -->
                    @if(!$showObjectifDetails && !$showCreateForm)
                        <!-- Informations du pilier -->
                        @if($showPilierMainView)
                            <div class="p-4 border-bottom">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="mb-3">
                                            <h5 class="text-dark mb-2">{{ $pilier->libelle }}</h5>
                                            @if($pilier->description)
                                                <p class="text-muted mb-0">{{ $pilier->description }}</p>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary fs-6 me-3">{{ $pilier->code }}</span>
                                            <div class="progress me-3" style="width: 200px; height: 20px;">
                                                <div class="progress-bar bg-success" 
                                                     role="progressbar" 
                                                     style="width: {{ $pilier->taux_avancement }}%"
                                                     aria-valuenow="{{ $pilier->taux_avancement }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    <strong>{{ number_format($pilier->taux_avancement, 2) }}%</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 text-lg-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('piliers.edit', $pilier) }}" class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-edit me-1"></i>Modifier
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce pilier ?')) { $wire.deletePilier({{ $pilier->id }}) }">
                                                <i class="fas fa-trash me-1"></i>Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistiques -->
                            <div class="p-4 border-bottom bg-light">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body text-center">
                                                <div class="display-6 text-primary mb-2">{{ number_format($pilier->taux_avancement, 2) }}%</div>
                                                <h6 class="text-muted mb-0">Progression Globale</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body text-center">
                                                <div class="display-6 text-success mb-2">{{ $objectifsStrategiques->count() }}</div>
                                                <h6 class="text-muted mb-0">Objectifs Stratégiques</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body text-center">
                                                <div class="display-6 text-info mb-2">{{ $totalObjectifsSpecifiques }}</div>
                                                <h6 class="text-muted mb-0">Objectifs Spécifiques</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body text-center">
                                                <div class="display-6 text-warning mb-2">{{ $objectifsTermines }}</div>
                                                <h6 class="text-muted mb-0">Terminés</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Liste des objectifs stratégiques -->
                            <div class="p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0 text-dark">
                                    <i class="fas fa-bullseye me-2 text-success"></i>
                                    Objectifs Stratégiques
                                </h5>
                                <button type="button" class="btn btn-success btn-sm" wire:click="showCreateObjectifForm">
                                    <i class="fas fa-plus me-2"></i>
                                    Nouvel Objectif
                                </button>
                            </div>

                            @if($objectifsStrategiques->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Code</th>
                                                <th>Libellé</th>
                                                <th>Description</th>
                                                <th>Owner</th>
                                                <th>Progression</th>
                                                <th>Objectifs Spécifiques</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($objectifsStrategiques as $objectifStrategique)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-success fs-6">{{ $pilier->code }}.{{ $objectifStrategique->code }}</span>
                                                </td>
                                                <td>
                                                    <strong class="text-dark">{{ $objectifStrategique->libelle }}</strong>
                                                </td>
                                                <td>
                                                    @if($objectifStrategique->description)
                                                        <span class="text-muted">{{ Str::limit($objectifStrategique->description, 50) }}</span>
                                                    @else
                                                        <span class="text-muted fst-italic">Aucune description</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($objectifStrategique->owner)
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-user me-1"></i>
                                                            {{ $objectifStrategique->owner->name }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">Non assigné</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-2" style="width: 100px; height: 8px;">
                                                            <div class="progress-bar bg-success" 
                                                                 role="progressbar" 
                                                                 style="width: {{ $objectifStrategique->taux_avancement }}%"
                                                                 aria-valuenow="{{ $objectifStrategique->taux_avancement }}" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">{{ number_format($objectifStrategique->taux_avancement, 1) }}%</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning text-dark">
                                                        {{ $objectifStrategique->objectifsSpecifiques->count() }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                                wire:click="voirObjectifStrategique({{ $objectifStrategique->id }})"
                                                                title="Voir détails">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" 
                                                                wire:click="showEditObjectifStrategiqueForm({{ $objectifStrategique->id }})"
                                                                class="btn btn-outline-warning btn-sm" 
                                                                title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-outline-danger btn-sm" 
                                                                wire:click="deleteObjectifStrategique({{ $objectifStrategique->id }})"
                                                                onclick="if(!confirm('Êtes-vous sûr de vouloir supprimer cet objectif stratégique ?')) return false;"
                                                                title="Supprimer">
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
                                    <div class="mb-3">
                                        <i class="fas fa-bullseye fa-3x text-muted"></i>
                                    </div>
                                    <h5 class="text-muted mb-2">Aucun objectif stratégique</h5>
                                    <p class="text-muted mb-3">Ce pilier n'a pas encore d'objectifs stratégiques.</p>
                                    <button type="button" class="btn btn-success" wire:click="showCreateObjectifForm">
                                        <i class="fas fa-plus me-2"></i>
                                        Créer le premier objectif
                                    </button>
                                </div>
                            @endif
                        </div>
                        @endif
                    @endif

                    <!-- Vue détaillée d'un objectif stratégique -->
                    @if($showObjectifDetails && $selectedObjectifStrategique)
                        <div class="p-4">
                            <!-- 🎯 CARD DU PILIER PARENT - CONTEXTE HIÉRARCHIQUE -->
                            <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="card-body text-white p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-layer-group text-white fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">
                                                        <i class="fas fa-arrow-up me-2"></i>Pilier Parent
                                                    </h6>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-white bg-opacity-25 text-white fs-6 me-2">{{ $pilier->code }}</span>
                                                        <span class="text-white-75">{{ $pilier->libelle }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="bg-white bg-opacity-15 rounded p-2">
                                                <div class="display-6 fw-bold text-primary mb-1">{{ number_format($pilier->taux_avancement, 2) }}%</div>
                                                <small class="text-white-75">Progression Globale</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <i class="fas fa-bullseye me-2"></i>
                                            Détails de l'Objectif Stratégique
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <strong>Code:</strong>
                                                <span class="badge bg-success ms-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Libellé:</strong>
                                                <p class="mb-0">{{ $selectedObjectifStrategique->libelle }}</p>
                                            </div>
                                            @if($selectedObjectifStrategique->description)
                                                <div class="mb-3">
                                                    <strong>Description:</strong>
                                                    <p class="mb-0 text-muted">{{ $selectedObjectifStrategique->description }}</p>
                                                </div>
                                            @endif
                                            <div class="mb-3">
                                                <strong>Owner:</strong>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-user me-1"></i>
                                                    {{ $selectedObjectifStrategique->owner->name ?? 'Non assigné' }}
                                                </span>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Progression:</strong>
                                                <div class="progress mt-2" style="height: 20px;">
                                                    <div class="progress-bar bg-success" 
                                                         role="progressbar" 
                                                         style="width: {{ $selectedObjectifStrategique->taux_avancement }}%"
                                                         aria-valuenow="{{ $selectedObjectifStrategique->taux_avancement }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <strong>{{ number_format($selectedObjectifStrategique->taux_avancement, 2) }}%</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <i class="fas fa-chart-line me-2"></i>
                                            Statistiques
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="display-4 text-warning mb-2">{{ number_format($selectedObjectifStrategique->taux_avancement, 2) }}%</div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="border-end">
                                                        <div class="h4 text-warning">{{ $selectedObjectifStrategique->objectifsSpecifiques->count() }}</div>
                                                        <small class="text-muted">Objectifs Spécifiques</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="h4 text-success">0</div>
                                                    <small class="text-muted">Terminés</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Liste des objectifs spécifiques -->
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list-check me-2 text-warning"></i>
                                        Objectifs Spécifiques ou Pilotage ({{ $selectedObjectifStrategique->objectifsSpecifiques->count() }})
                                    </h5>
                                    <button type="button" class="btn btn-success btn-sm" wire:click="$set('showCreateObjectifSpecifiqueForm', true)" onclick="console.log('🔍 [DEBUG] Bouton Créer Objectif Spécifique cliqué - Test direct')">
                                        <i class="fas fa-plus me-2"></i>
                                        Créer un Objectif Spécifique
                                    </button>

                                    <button type="button" class="btn btn-warning btn-sm ms-2" wire:click="testMethod">
                                        <i class="fas fa-bug me-2"></i>
                                        Test
                                    </button>
                                </div>
                                
                                @if($selectedObjectifStrategique->objectifsSpecifiques->count() > 0)
                                    <div class="row">
                                        @foreach($selectedObjectifStrategique->objectifsSpecifiques as $objectifSpecifique)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card h-100 shadow-sm">
                                                    <div class="card-header bg-gradient-warning text-dark">
                                                        <h6 class="mb-0">
                                                            <span class="badge bg-light text-dark me-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code ?? 'N/A' }}.{{ $objectifSpecifique->code }}</span>
                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <h6 class="card-title text-dark mb-2">{{ $objectifSpecifique->libelle }}</h6>
                                                        @if($objectifSpecifique->description)
                                                            <p class="text-muted small mb-2">{{ Str::limit($objectifSpecifique->description, 100) }}</p>
                                                        @endif
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <small class="text-muted fw-bold">Progression</small>
                                                                <small class="text-success fw-bold">{{ number_format($objectifSpecifique->taux_avancement, 2) }}%</small>
                                                            </div>
                                                            <div class="progress" style="height: 12px; border-radius: 6px;">
                                                                <div class="progress-bar bg-success" 
                                                                     style="width: {{ $objectifSpecifique->taux_avancement }}%"
                                                                     role="progressbar" 
                                                                     aria-valuenow="{{ $objectifSpecifique->taux_avancement }}" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <span class="badge bg-secondary">
                                                                <i class="fas fa-user me-1"></i>
                                                                {{ $objectifSpecifique->owner->name ?? 'Non assigné' }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="badge bg-info">{{ $objectifSpecifique->actions->count() }} actions</span>
                                                            <div class="btn-group" role="group">
                                                                <button type="button" 
                                                                        wire:click="setActionToView({{ $objectifSpecifique->id }})"
                                                                        class="btn btn-warning btn-sm">
                                                                    <i class="fas fa-eye me-1"></i>Voir
                                                                </button>
                                                                <button type="button" 
                                                                        wire:click="setActionToEdit({{ $objectifSpecifique->id }})"
                                                                        class="btn btn-outline-primary btn-sm">
                                                                    <i class="fas fa-edit me-1"></i>Éditer
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-list-check fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">Aucun objectif spécifique pour cet objectif stratégique.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Vue détaillée d'un objectif spécifique -->
                    @if($showObjectifSpecifiqueDetails && $selectedObjectifSpecifiqueDetails)
                        <div class="p-4">
                            
                            <!-- Carte du Pilier Parent -->
                            <div class="card mb-3 border-0 shadow-sm" style="background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);">
                                <div class="card-body text-white p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-layer-group text-white fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">
                                                        <i class="fas fa-arrow-up me-2"></i>Pilier Parent
                                                    </h6>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-white bg-opacity-25 text-white fs-6 me-2">{{ $pilier->code }}</span>
                                                        <span class="text-white-75">{{ $pilier->libelle }}</span>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-white-75">
                                                            <i class="fas fa-bullseye me-1"></i>
                                                            {{ $objectifsStrategiques->count() }} Objectif(s) Stratégique(s)
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="bg-white bg-opacity-15 rounded p-2">
                                                <div class="display-6 fw-bold mb-1" style="color: #8e44ad;">{{ number_format($pilier->taux_avancement, 2) }}%</div>
                                                <small class="text-white-75">Progression</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($selectedObjectifStrategique)
                            <!-- Carte de l'Objectif Stratégique Parent -->
                            <div class="card mb-3 border-0 shadow-sm" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                                <div class="card-body text-white p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-bullseye text-white fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">
                                                        <i class="fas fa-arrow-up me-2"></i>Objectif Stratégique Parent
                                                    </h6>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-white bg-opacity-25 text-white fs-6 me-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}</span>
                                                        <span class="text-white-75">{{ $selectedObjectifStrategique->libelle }}</span>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-white-75">
                                                            <i class="fas fa-list-check me-1"></i>
                                                            {{ $selectedObjectifStrategique->objectifsSpecifiques->count() }} Objectif(s) Spécifique(s)
                                                        </small>
                                                        <div class="mt-1">
                                                            <small class="text-white-75">
                                                                <i class="fas fa-user me-1"></i>
                                                                Owner: {{ $selectedObjectifStrategique->owner ? $selectedObjectifStrategique->owner->name : 'Non assigné' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="bg-white bg-opacity-15 rounded p-2">
                                                <div class="display-6 fw-bold mb-1" style="color: #e74c3c;">{{ number_format($selectedObjectifStrategique->taux_avancement, 2) }}%</div>
                                                <small class="text-white-75">Progression</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif



                            <!-- Détails de l'objectif spécifique -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <i class="fas fa-list-check me-2"></i>
                                            Détails de l'Objectif Spécifique
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <strong>Code:</strong>
                                                <span class="badge bg-warning text-dark ms-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code ?? 'N/A' }}.{{ $selectedObjectifSpecifiqueDetails->code ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Libellé:</strong>
                                                <p class="mb-0">{{ $selectedObjectifSpecifiqueDetails->libelle }}</p>
                                            </div>
                                            @if($selectedObjectifSpecifiqueDetails->description)
                                                <div class="mb-3">
                                                    <strong>Description:</strong>
                                                    <p class="mb-0 text-muted">{{ $selectedObjectifSpecifiqueDetails->description }}</p>
                                                </div>
                                            @endif
                                            <div class="mb-3">
                                                <strong>Owner:</strong>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-user me-1"></i>
                                                    {{ $selectedObjectifSpecifiqueDetails->owner->name ?? 'Non assigné' }}
                                                </span>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Progression:</strong>
                                                <div class="progress mt-2" style="height: 20px;">
                                                    <div class="progress-bar bg-warning" 
                                                         role="progressbar" 
                                                         style="width: {{ $selectedObjectifSpecifiqueDetails->taux_avancement }}%"
                                                         aria-valuenow="{{ $selectedObjectifSpecifiqueDetails->taux_avancement }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <strong>{{ number_format($selectedObjectifSpecifiqueDetails->taux_avancement, 2) }}%</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <i class="fas fa-chart-line me-2"></i>
                                            Statistiques
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="display-4 text-info mb-2">{{ number_format($selectedObjectifSpecifiqueDetails->taux_avancement, 2) }}%</div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="border-end">
                                                        <div class="h4 text-info">{{ $selectedObjectifSpecifiqueDetails->actions->count() }}</div>
                                                        <small class="text-muted">Actions</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="h4 text-success">0</div>
                                                    <small class="text-muted">Terminées</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Liste des actions -->
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="fas fa-tasks me-2 text-info"></i>
                                        Actions ({{ $selectedObjectifSpecifiqueDetails->actions->count() }})
                                    </h5>
                                    <button type="button" 
                                            wire:click="showCreateActionForm"
                                            class="btn btn-info btn-sm">
                                        <i class="fas fa-plus me-2"></i>
                                        Créer une Action
                                    </button>
                                </div>
                                
                                @if($selectedObjectifSpecifiqueDetails->actions->count() > 0)
                                    <div class="row">
                                        @foreach($selectedObjectifSpecifiqueDetails->actions as $action)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card h-100 shadow-sm">
                                                    <div class="card-header bg-gradient-info text-white">
                                                        <h6 class="mb-0">
                                                            <span class="badge bg-light text-dark me-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code ?? 'N/A' }}.{{ $selectedObjectifSpecifiqueDetails->code ?? 'N/A' }}.{{ $action->code }}</span>
                                                            {{ $action->libelle }}
                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if($action->description)
                                                            <p class="text-muted small mb-2">{{ Str::limit($action->description, 100) }}</p>
                                                        @endif
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <small class="text-muted fw-bold">Progression</small>
                                                                <small class="text-info fw-bold">{{ number_format($action->taux_avancement, 2) }}%</small>
                                                            </div>
                                                            <div class="progress" style="height: 12px; border-radius: 6px;">
                                                                <div class="progress-bar bg-info" 
                                                                     style="width: {{ $action->taux_avancement }}%"
                                                                     role="progressbar" 
                                                                     aria-valuenow="{{ $action->taux_avancement }}" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <span class="badge bg-secondary">
                                                                <i class="fas fa-user me-1"></i>
                                                                {{ $action->owner->name ?? 'Non assigné' }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="badge bg-success">{{ $action->sousActions->count() }} sous-actions</span>
                                                            <div class="btn-group" role="group">
                                                                <button type="button" 
                                                                        wire:click="displayActionDetails({{ $action->id }})"
                                                                        class="btn btn-info btn-sm">
                                                                    <i class="fas fa-eye me-1"></i>Voir
                                                                </button>
                                                                <button type="button" 
                                                                        wire:click="showEditActionForm({{ $action->id }})"
                                                                        class="btn btn-outline-primary btn-sm">
                                                                    <i class="fas fa-edit me-1"></i>Éditer
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-tasks fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">Aucune action pour cet objectif spécifique.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Vue détaillée d'une action -->
                    @if($showActionDetails && $selectedAction)
                        <div class="p-4">
                            
                            <!-- Carte du Pilier Parent -->
                            <div class="card mb-3 border-0 shadow-sm" style="background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);">
                                <div class="card-body text-white p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-layer-group text-white fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">
                                                        <i class="fas fa-arrow-up me-2"></i>Pilier Parent
                                                    </h6>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-white bg-opacity-25 text-white fs-6 me-2">{{ $pilier->code }}</span>
                                                        <span class="text-white-75">{{ $pilier->libelle }}</span>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-white-75">
                                                            <i class="fas fa-bullseye me-1"></i>
                                                            {{ $objectifsStrategiques->count() }} Objectif(s) Stratégique(s)
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="bg-white bg-opacity-15 rounded p-2">
                                                <div class="display-6 fw-bold mb-1" style="color: #8e44ad;">{{ number_format($pilier->taux_avancement, 2) }}%</div>
                                                <small class="text-white-75">Progression</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($selectedObjectifStrategique)
                            <!-- Carte de l'Objectif Stratégique Parent -->
                            <div class="card mb-3 border-0 shadow-sm" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                                <div class="card-body text-white p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-bullseye text-white fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">
                                                        <i class="fas fa-arrow-up me-2"></i>Objectif Stratégique Parent
                                                    </h6>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-white bg-opacity-25 text-white fs-6 me-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}</span>
                                                        <span class="text-white-75">{{ $selectedObjectifStrategique->libelle }}</span>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-white-75">
                                                            <i class="fas fa-list-check me-1"></i>
                                                            {{ $selectedObjectifStrategique->objectifsSpecifiques->count() }} Objectif(s) Spécifique(s)
                                                        </small>
                                                        <div class="mt-1">
                                                            <small class="text-white-75">
                                                                <i class="fas fa-user me-1"></i>
                                                                Owner: {{ $selectedObjectifStrategique->owner ? $selectedObjectifStrategique->owner->name : 'Non assigné' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="bg-white bg-opacity-15 rounded p-2">
                                                <div class="display-6 fw-bold mb-1" style="color: #e74c3c;">{{ number_format($selectedObjectifStrategique->taux_avancement, 2) }}%</div>
                                                <small class="text-white-75">Progression</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($selectedObjectifSpecifiqueDetails)
                            <!-- Carte de l'Objectif Spécifique Parent -->
                            <div class="card mb-3 border-0 shadow-sm" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);">
                                <div class="card-body text-white p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-list-check text-white fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">
                                                        <i class="fas fa-arrow-up me-2"></i>Objectif Spécifique Parent
                                                    </h6>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-white bg-opacity-25 text-white fs-6 me-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifiqueDetails->code }}</span>
                                                        <span class="text-white-75">{{ $selectedObjectifSpecifiqueDetails->libelle }}</span>
                                                    </div>
                                                    <div class="mt-2">
                                                        <small class="text-white-75">
                                                            <i class="fas fa-tasks me-1"></i>
                                                            {{ $selectedObjectifSpecifiqueDetails->actions->count() }} Action(s)
                                                        </small>
                                                        <div class="mt-1">
                                                            <small class="text-white-75">
                                                                <i class="fas fa-user me-1"></i>
                                                                Owner: {{ $selectedObjectifSpecifiqueDetails->owner ? $selectedObjectifSpecifiqueDetails->owner->name : 'Non assigné' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="bg-white bg-opacity-15 rounded p-2">
                                                <div class="display-6 fw-bold mb-1" style="color: #ff6b6b;">{{ number_format($selectedObjectifSpecifiqueDetails->taux_avancement, 2) }}%</div>
                                                <small class="text-white-75">Progression</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif



                            <!-- Détails de l'action -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <i class="fas fa-tasks me-2"></i>
                                            Détails de l'Action
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <strong>Code:</strong>
                                                <span class="badge bg-info text-white ms-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code ?? 'N/A' }}.{{ $selectedObjectifSpecifiqueDetails->code ?? 'N/A' }}.{{ $selectedAction->code }}</span>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Libellé:</strong>
                                                <p class="mb-0">{{ $selectedAction->libelle }}</p>
                                            </div>
                                            @if($selectedAction->description)
                                                <div class="mb-3">
                                                    <strong>Description:</strong>
                                                    <p class="mb-0 text-muted">{{ $selectedAction->description }}</p>
                                                </div>
                                            @endif
                                            <div class="mb-3">
                                                <strong>Owner:</strong>
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-user me-1"></i>
                                                    {{ $selectedAction->owner->name ?? 'Non assigné' }}
                                                </span>
                                            </div>
                                            @if($selectedAction->date_echeance)
                                                <div class="mb-3">
                                                    <strong>Date d'échéance:</strong>
                                                    <p class="mb-0 text-muted">{{ \Carbon\Carbon::parse($selectedAction->date_echeance)->format('d/m/Y') }}</p>
                                                </div>
                                            @endif
                                            @if($selectedAction->date_realisation)
                                                <div class="mb-3">
                                                    <strong>Date de réalisation:</strong>
                                                    <p class="mb-0 text-muted">{{ \Carbon\Carbon::parse($selectedAction->date_realisation)->format('d/m/Y') }}</p>
                                                </div>
                                            @endif
                                            <div class="mb-3">
                                                <strong>Progression:</strong>
                                                <div class="progress mt-2" style="height: 20px;">
                                                    <div class="progress-bar bg-info" 
                                                         role="progressbar" 
                                                         style="width: {{ $selectedAction->taux_avancement }}%"
                                                         aria-valuenow="{{ $selectedAction->taux_avancement }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <strong>{{ number_format($selectedAction->taux_avancement, 2) }}%</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <i class="fas fa-chart-line me-2"></i>
                                            Statistiques
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="display-4 text-success mb-2">{{ number_format($selectedAction->taux_avancement, 2) }}%</div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="border-end">
                                                        <div class="h4 text-success">{{ $selectedAction->sousActions->count() }}</div>
                                                        <small class="text-muted">Sous-actions</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="h4 text-warning">0</div>
                                                    <small class="text-muted">Terminées</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Liste des sous-actions -->
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list-ul me-2 text-success"></i>
                                        Sous-actions ({{ $selectedAction->sousActions->count() }})
                                    </h5>
                                    <button type="button" 
                                            wire:click="showCreateSousActionForm"
                                            class="btn btn-success btn-sm">
                                        <i class="fas fa-plus me-2"></i>
                                        Créer une Sous-action
                                    </button>
                                </div>
                                
                                @if($selectedAction->sousActions->count() > 0)
                                    <div class="row">
                                        @foreach($selectedAction->sousActions as $sousAction)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card h-100 shadow-sm">
                                                    <div class="card-header bg-gradient-success text-white">
                                                        <h6 class="mb-0">
                                                            <span class="badge bg-light text-dark me-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code ?? 'N/A' }}.{{ $selectedObjectifSpecifiqueDetails->code ?? 'N/A' }}.{{ $selectedAction->code }}.{{ $sousAction->code }}</span>
                                                            {{ $sousAction->libelle }}
                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if($sousAction->description)
                                                            <p class="text-muted small mb-2">{{ Str::limit($sousAction->description, 100) }}</p>
                                                        @endif
                                                        <div class="mb-2">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <small class="text-muted fw-bold">Progression</small>
                                                                <small class="text-success fw-bold">{{ number_format($sousAction->taux_avancement, 2) }}%</small>
                                                            </div>
                                                            <div class="progress" style="height: 12px; border-radius: 6px;">
                                                                <div class="progress-bar bg-success" 
                                                                     style="width: {{ $sousAction->taux_avancement }}%"
                                                                     role="progressbar" 
                                                                     aria-valuenow="{{ $sousAction->taux_avancement }}" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <span class="badge bg-secondary">
                                                                <i class="fas fa-user me-1"></i>
                                                                {{ $sousAction->owner->name ?? 'Non assigné' }}
                                                            </span>
                                                        </div>
                                                        @if($sousAction->date_echeance)
                                                            <div class="mb-2">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-calendar me-1"></i>
                                                                    Échéance: {{ \Carbon\Carbon::parse($sousAction->date_echeance)->format('d/m/Y') }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                        <div class="mb-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <small class="text-muted fw-bold">Progression</small>
                                                                <small class="text-success fw-bold" id="taux-{{ $sousAction->id }}">{{ number_format($sousAction->taux_avancement, 2) }}%</small>
                                                            </div>
                                                            <div class="progress-container">
                                                                <input type="range" 
                                                                       class="form-range sous-action-slider" 
                                                                       id="slider-{{ $sousAction->id }}"
                                                                       data-sous-action-id="{{ $sousAction->id }}"
                                                                       data-action-id="{{ $selectedAction->id }}"
                                                                       data-objectif-specifique-id="{{ $selectedObjectifSpecifiqueDetails->id }}"
                                                                       data-objectif-strategique-id="{{ $selectedObjectifStrategique->id }}"
                                                                       data-pilier-id="{{ $pilier->id }}"
                                                                       min="0" 
                                                                       max="100" 
                                                                       step="0.1" 
                                                                       value="{{ $sousAction->taux_avancement }}"
                                                                       style="width: 100%; height: 8px;">
                                                                <div class="progress mt-1" style="height: 8px;">
                                                                    <div class="progress-bar bg-success" 
                                                                         id="progress-{{ $sousAction->id }}"
                                                                         role="progressbar" 
                                                                         style="width: {{ $sousAction->taux_avancement }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="btn-group" role="group">
                                                                <button type="button" 
                                                                        wire:click="showEditSousActionForm({{ $sousAction->id }})"
                                                                        class="btn btn-outline-success btn-sm">
                                                                    <i class="fas fa-edit me-1"></i>Éditer
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-list-ul fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">Aucune sous-action pour cette action.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif



                    <!-- Modal Créer Objectif Spécifique -->
                    @if($showCreateObjectifSpecifiqueForm)
                        <div class="modal fade show" style="display: block; z-index: 1060;" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">
                                            <i class="fas fa-plus-circle me-2"></i>
                                            Nouvel Objectif Spécifique
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" wire:click="cancelCreateObjectifSpecifique"></button>
                                    </div>
                                    <form wire:submit.prevent="createObjectifSpecifique">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Code *</label>
                                                <input type="text" class="form-control @error('newObjectifSpecifiqueCode') is-invalid @enderror" 
                                                       wire:model="newObjectifSpecifiqueCode" required>
                                                @error('newObjectifSpecifiqueCode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Format suggéré: PIL1, PIL2, etc.</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Libellé *</label>
                                                <input type="text" class="form-control @error('newObjectifSpecifiqueLibelle') is-invalid @enderror" 
                                                       wire:model="newObjectifSpecifiqueLibelle" required>
                                                @error('newObjectifSpecifiqueLibelle')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control @error('newObjectifSpecifiqueDescription') is-invalid @enderror" 
                                                          wire:model="newObjectifSpecifiqueDescription" rows="3"></textarea>
                                                @error('newObjectifSpecifiqueDescription')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Owner</label>
                                                <select class="form-select @error('newObjectifSpecifiqueOwnerId') is-invalid @enderror" 
                                                        wire:model="newObjectifSpecifiqueOwnerId">
                                                    <option value="">Sélectionner un owner</option>
                                                    @foreach($this->users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('newObjectifSpecifiqueOwnerId')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" wire:click="cancelCreateObjectifSpecifique">
                                                Annuler
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save me-2"></i>Créer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal-backdrop fade show" style="z-index: 1050;"></div>
                    @endif

                    <!-- Modal Éditer Objectif Spécifique -->
                    @if($showEditObjectifSpecifiqueForm && $editingObjectifSpecifique)
                        <div class="modal fade show" style="display: block; z-index: 1060;" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">
                                            <i class="fas fa-edit me-2"></i>
                                            Modifier l'Objectif Spécifique
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" wire:click="cancelEditObjectifSpecifique"></button>
                                    </div>
                                    <form wire:submit.prevent="updateObjectifSpecifique">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Code *</label>
                                                <input type="text" class="form-control @error('editObjectifSpecifiqueCode') is-invalid @enderror" 
                                                       wire:model="editObjectifSpecifiqueCode" required>
                                                @error('editObjectifSpecifiqueCode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Libellé *</label>
                                                <input type="text" class="form-control @error('editObjectifSpecifiqueLibelle') is-invalid @enderror" 
                                                       wire:model="editObjectifSpecifiqueLibelle" required>
                                                @error('editObjectifSpecifiqueLibelle')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control @error('editObjectifSpecifiqueDescription') is-invalid @enderror" 
                                                          wire:model="editObjectifSpecifiqueDescription" rows="3"></textarea>
                                                @error('editObjectifSpecifiqueDescription')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Owner</label>
                                                <select class="form-select @error('editObjectifSpecifiqueOwnerId') is-invalid @enderror" 
                                                        wire:model="editObjectifSpecifiqueOwnerId">
                                                    <option value="">Sélectionner un owner</option>
                                                    @foreach($this->users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('editObjectifSpecifiqueOwnerId')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" wire:click="cancelEditObjectifSpecifique">
                                                Annuler
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Enregistrer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal-backdrop fade show" style="z-index: 1050;"></div>
                    @endif

                    <!-- Modal Créer Objectif Stratégique -->
                    @if($showCreateForm)
                        <div class="modal fade show" style="display: block; z-index: 9999;" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">
                                            <i class="fas fa-plus-circle me-2"></i>
                                            Nouvel Objectif Stratégique
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" wire:click="cancelCreate"></button>
                                    </div>
                                    <form wire:submit.prevent="createObjectifStrategique">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Code *</label>
                                                    <input type="text" class="form-control @error('newObjectifCode') is-invalid @enderror" 
                                                           wire:model="newObjectifCode" required>
                                                    @error('newObjectifCode')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Libellé *</label>
                                                    <input type="text" class="form-control @error('newObjectifLibelle') is-invalid @enderror" 
                                                           wire:model="newObjectifLibelle" required>
                                                    @error('newObjectifLibelle')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control @error('newObjectifDescription') is-invalid @enderror" 
                                                          wire:model="newObjectifDescription" rows="3"></textarea>
                                                @error('newObjectifDescription')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Owner</label>
                                                <select class="form-select @error('newObjectifOwnerId') is-invalid @enderror" 
                                                        wire:model="newObjectifOwnerId">
                                                    <option value="">Sélectionner un owner</option>
                                                    @foreach($this->users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('newObjectifOwnerId')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" wire:click="cancelCreate">
                                                Annuler
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save me-2"></i>Créer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal-backdrop fade show" style="z-index: 1050;"></div>
                    @endif

                    <!-- Modal Éditer Objectif Stratégique -->
                    @if($showEditObjectifStrategiqueForm && $editingObjectifStrategique)
                        <div class="modal fade show" style="display: block; z-index: 9999;" tabindex="-1" data-bs-backdrop="static">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-dark">
                                        <h5 class="modal-title">
                                            <i class="fas fa-edit me-2"></i>
                                            Modifier l'Objectif Stratégique
                                        </h5>
                                        <button type="button" class="btn-close" wire:click="cancelEditObjectifStrategique"></button>
                                    </div>
                                    <form wire:submit.prevent="updateObjectifStrategique">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Code *</label>
                                                    <input type="text" class="form-control @error('editObjectifStrategiqueCode') is-invalid @enderror" 
                                                           wire:model="editObjectifStrategiqueCode" required>
                                                    @error('editObjectifStrategiqueCode')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Libellé *</label>
                                                    <input type="text" class="form-control @error('editObjectifStrategiqueLibelle') is-invalid @enderror" 
                                                           wire:model="editObjectifStrategiqueLibelle" required>
                                                    @error('editObjectifStrategiqueLibelle')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control @error('editObjectifStrategiqueDescription') is-invalid @enderror" 
                                                          wire:model="editObjectifStrategiqueDescription" rows="3"></textarea>
                                                @error('editObjectifStrategiqueDescription')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Owner</label>
                                                <select class="form-select @error('editObjectifStrategiqueOwnerId') is-invalid @enderror" 
                                                        wire:model="editObjectifStrategiqueOwnerId">
                                                    <option value="">Sélectionner un owner</option>
                                                    @foreach($this->users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('editObjectifStrategiqueOwnerId')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" wire:click="cancelEditObjectifStrategique">
                                                Annuler
                                            </button>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save me-2"></i>Enregistrer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal-backdrop fade show" style="z-index: 1050;"></div>
                    @endif


                </div>
            </div>
        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 1054;"></div>
    @endif

    <style>
    /* Animation Slide Suivant */
    .slide-next-enter {
        transform: translateX(100%);
        opacity: 0.3;
        transition: none;
    }

    .slide-next-enter-active {
        transform: translateX(0);
        opacity: 1;
        transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* Animation Slide Précédent */
    .slide-prev-enter {
        transform: translateX(-100%);
        opacity: 0.3;
        transition: none;
    }

    .slide-prev-enter-active {
        transform: translateX(0);
        opacity: 1;
        transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* Animation de chargement */
    .loading-fade {
        opacity: 0.6;
        transition: opacity 0.3s ease;
    }

    .loading-fade.active {
        opacity: 1;
    }

    /* Debug: bordures colorées pour voir les animations */
    .slide-next-enter {
        border: 3px solid red !important;
    }
    
    .slide-next-enter-active {
        border: 3px solid green !important;
    }
    
    .slide-prev-enter {
        border: 3px solid blue !important;
    }
    
    .slide-prev-enter-active {
        border: 3px solid orange !important;
    }

    /* Styles pour les sliders de sous-actions */
    .sous-action-slider {
        -webkit-appearance: none;
        appearance: none;
        height: 8px;
        border-radius: 4px;
        background: #e9ecef;
        outline: none;
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    .sous-action-slider:hover {
        opacity: 1;
    }

    .sous-action-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #28a745;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .sous-action-slider::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #28a745;
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .progress-container {
        position: relative;
    }

    .progress-container .progress {
        margin-top: 8px;
    }
    </style>

    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('startLoading', () => {
            // Arrêter le chargement immédiatement
            @this.stopLoading();
        });

        Livewire.on('startSlideAnimation', (event) => {
            const direction = event.detail?.direction || 'next';
            const modalContent = document.querySelector('.modal-content');
            
            if (modalContent) {
                console.log('🚀 Animation démarrée:', direction);
                
                // Nettoyer les classes précédentes
                modalContent.classList.remove('slide-next-enter', 'slide-next-enter-active');
                modalContent.classList.remove('slide-prev-enter', 'slide-prev-enter-active');
                
                // Ajouter la classe initiale
                modalContent.classList.add(`slide-${direction}-enter`);
                
                // Forcer le reflow pour que l'animation fonctionne
                modalContent.offsetHeight;
                
                // Déclencher l'animation
                setTimeout(() => {
                    modalContent.classList.remove(`slide-${direction}-enter`);
                    modalContent.classList.add(`slide-${direction}-enter-active`);
                    console.log('✅ Animation en cours:', direction);
                }, 50);
                
                // Nettoyer après l'animation
                setTimeout(() => {
                    modalContent.classList.remove(`slide-${direction}-enter-active`);
                    console.log('✅ Animation terminée:', direction);
                }, 400);
            }
        });
        
        Livewire.on('stopSlideAnimation', () => {
            const modalContent = document.querySelector('.modal-content');
            
            if (modalContent) {
                // Nettoyer les classes d'animation
                modalContent.classList.remove('slide-next-enter', 'slide-next-enter-active');
                modalContent.classList.remove('slide-prev-enter', 'slide-prev-enter-active');
                console.log('🧹 Animation nettoyée');
            }
        });

        // Gestion des sliders de sous-actions
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('sous-action-slider')) {
                const slider = e.target;
                const sousActionId = slider.dataset.sousActionId;
                const actionId = slider.dataset.actionId;
                const objectifSpecifiqueId = slider.dataset.objectifSpecifiqueId;
                const objectifStrategiqueId = slider.dataset.objectifStrategiqueId;
                const pilierId = slider.dataset.pilierId;
                const newValue = parseFloat(slider.value);

                console.log('🎯 Slider modifié:', {
                    sousActionId,
                    actionId,
                    objectifSpecifiqueId,
                    objectifStrategiqueId,
                    pilierId,
                    newValue
                });

                // Mettre à jour l'affichage en temps réel
                const tauxElement = document.getElementById(`taux-${sousActionId}`);
                const progressElement = document.getElementById(`progress-${sousActionId}`);
                
                if (tauxElement) {
                    tauxElement.textContent = newValue.toFixed(2) + '%';
                }
                
                if (progressElement) {
                    progressElement.style.width = newValue + '%';
                }

                // Appeler directement la méthode Livewire
                @this.updateSousActionTaux(sousActionId, newValue, actionId, objectifSpecifiqueId, objectifStrategiqueId, pilierId);
            }
        });

        // Écouter les mises à jour des taux parents
        Livewire.on('updateTauxDisplay', (event) => {
            const data = event.detail;
            console.log('🔄 Mise à jour des taux parents:', data);
            
            // Mettre à jour les taux des actions parents
            const actionProgressBars = document.querySelectorAll('.progress-bar');
            actionProgressBars.forEach(bar => {
                if (bar.id && bar.id.includes('progress-')) {
                    // Recalculer le taux basé sur les sous-actions
                    const actionId = bar.id.replace('progress-', '');
                    if (actionId == data.actionId) {
                        // Mettre à jour la barre de progression de l'action
                        const actionTauxElement = document.querySelector(`[data-action-id="${data.actionId}"] .action-taux`);
                        if (actionTauxElement) {
                            // Le taux sera mis à jour par le rechargement des données
                            console.log('✅ Taux action mis à jour');
                        }
                    }
                }
            });
        });
    });
    </script>
</div>

