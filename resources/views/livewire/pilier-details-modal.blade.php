<div>
    <!-- Modal principal -->
    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header du modal -->
                <div class="modal-header bg-gradient-primary text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-layer-group text-white fs-5"></i>
                        </div>
                        <div>
                            <h4 class="modal-title mb-1">
                                @if($showObjectifDetails && $selectedObjectifStrategique)
                                    <button wire:click="retourListeObjectifs" class="btn btn-link text-white p-0 me-2">
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-white bg-opacity-25 text-white me-2">{{ $pilier->code }}</span>
                                        <i class="fas fa-chevron-right text-white-50 me-2"></i>
                                        <span class="badge bg-success me-2">{{ $selectedObjectifStrategique->code }}</span>
                                        <span class="text-white">Détails de l'Objectif Stratégique</span>
                                    </div>
                                @elseif($showCreateForm)
                                    <button wire:click="cancelCreate" class="btn btn-link text-white p-0 me-2">
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                    Nouvel Objectif Stratégique
                                @else
                                    Détails du Pilier {{ $pilier->code }} - {{ $pilier->libelle }}
                                @endif
                            </h4>
                            @if(!$showObjectifDetails && !$showCreateForm)
                                <small class="text-white-50">
                                    {{ $objectifsStrategiques->count() }} objectif{{ $objectifsStrategiques->count() > 1 ? 's' : '' }} stratégique{{ $objectifsStrategiques->count() > 1 ? 's' : '' }}
                                </small>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>

                <!-- Body du modal -->
                <div class="modal-body p-0" style="max-height: 80vh; overflow-y: auto;">
                    
                    <!-- Vue principale - Détails du pilier -->
                    @if(!$showObjectifDetails && !$showCreateForm)
                        <!-- Informations du pilier -->
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
                                                        <a href="{{ route('objectifs-strategiques.edit', $objectifStrategique) }}" 
                                                           class="btn btn-outline-warning btn-sm" 
                                                           title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
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
                                                <span class="badge bg-success ms-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}</span>
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
                                    <button type="button" class="btn btn-success btn-sm" wire:click="showCreateObjectifSpecifiqueForm">
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
                                                            <span class="badge bg-light text-dark me-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $objectifSpecifique->code }}</span>
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
                                                                        wire:click="showObjectifSpecifiqueDetails({{ $objectifSpecifique->id }})"
                                                                        class="btn btn-warning btn-sm">
                                                                    <i class="fas fa-eye me-1"></i>Voir
                                                                </button>
                                                                <button type="button" 
                                                                        wire:click="showEditObjectifSpecifiqueForm({{ $objectifSpecifique->id }})"
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

                    <!-- Formulaire de création d'objectif stratégique -->
                    @if($showCreateForm)
                        <div class="p-4">
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <i class="fas fa-plus me-2"></i>
                                            Nouvel Objectif Stratégique
                                        </div>
                                        <div class="card-body">
                                            <form wire:submit.prevent="createObjectifStrategique">
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
                                                <div class="d-flex justify-content-end gap-2">
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
                            </div>
                        </div>
                    @endif

                    <!-- Vue détaillée d'un objectif spécifique -->
                    @if($showObjectifSpecifiqueDetails && $selectedObjectifSpecifiqueDetails)
                        <div class="p-4">
                            <!-- Breadcrumb pour l'objectif spécifique -->
                            <nav aria-label="breadcrumb" class="mb-3">
                                <ol class="breadcrumb bg-light p-2 rounded">
                                    <li class="breadcrumb-item">
                                        <a href="#" wire:click.prevent="retourListeObjectifs" class="text-decoration-none">
                                            <i class="fas fa-layer-group me-1"></i> {{ $pilier->code }}
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="#" wire:click.prevent="retourListeObjectifsSpecifiques" class="text-decoration-none">
                                            <i class="fas fa-bullseye me-1"></i> {{ $selectedObjectifStrategique->code }}
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        <i class="fas fa-list-check me-1"></i> {{ $selectedObjectifSpecifiqueDetails->code }}
                                    </li>
                                </ol>
                            </nav>

                            <!-- Carte du contexte hiérarchique -->
                            <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);">
                                <div class="card-body text-white p-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-list-check text-white fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">
                                                        <i class="fas fa-arrow-up me-2"></i>Objectif Spécifique
                                                    </h6>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-white bg-opacity-25 text-white fs-6 me-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifiqueDetails->code }}</span>
                                                        <span class="text-white-75">{{ $selectedObjectifSpecifiqueDetails->libelle }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="bg-white bg-opacity-15 rounded p-2">
                                                <div class="display-6 fw-bold text-white mb-1">{{ number_format($selectedObjectifSpecifiqueDetails->taux_avancement, 2) }}%</div>
                                                <small class="text-white-75">Progression</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                                                <span class="badge bg-warning text-dark ms-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifiqueDetails->code }}</span>
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
                                    <button type="button" class="btn btn-info btn-sm">
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
                                                            <span class="badge bg-light text-dark me-2">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifiqueDetails->code }}.{{ $action->code }}</span>
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
                                                                <button type="button" class="btn btn-info btn-sm">
                                                                    <i class="fas fa-eye me-1"></i>Voir
                                                                </button>
                                                                <button type="button" class="btn btn-outline-primary btn-sm">
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
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>
