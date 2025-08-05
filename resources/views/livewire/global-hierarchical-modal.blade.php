<div>
    <!-- Modal Hiérarchique Global -->
    @if($showModal)
    <div class="modal fade show" style="display: block; z-index: 9999;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <!-- Header du Modal -->
                <div class="modal-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-layer-group fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0">
                                <strong>Vue Hiérarchique</strong>
                            </h5>
                            @if($pilier)
                                <small class="text-white-75">{{ $pilier->code }} - {{ $pilier->libelle }}</small>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>

                <!-- Breadcrumb -->
                @if(count($currentBreadcrumb) > 0)
                    <div class="modal-body py-2 bg-light">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                @foreach($currentBreadcrumb as $index => $item)
                                    <li class="breadcrumb-item {{ $index === count($currentBreadcrumb) - 1 ? 'active' : '' }}">
                                        @if($index === count($currentBreadcrumb) - 1)
                                            <span class="badge bg-primary">{{ $item['label'] }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $item['label'] }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        </nav>
                    </div>
                @endif

                <!-- Contenu du Modal -->
                <div class="modal-body p-0">
                    <!-- Vue principale du pilier -->
                    @if($showPilierMainView && $pilier)
                        <div class="p-4">
                            <!-- Statistiques du pilier -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card border-primary text-center">
                                        <div class="card-body">
                                            <div class="display-6 text-primary mb-1">{{ $totalObjectifsSpecifiques }}</div>
                                            <small class="text-muted">Objectifs Spécifiques</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-success text-center">
                                        <div class="card-body">
                                            <div class="display-6 text-success mb-1">{{ $totalActions }}</div>
                                            <small class="text-muted">Actions</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-info text-center">
                                        <div class="card-body">
                                            <div class="display-6 text-info mb-1">{{ $totalSousActions }}</div>
                                            <small class="text-muted">Sous-Actions</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-warning text-center">
                                        <div class="card-body">
                                            <div class="display-6 text-warning mb-1">{{ $objectifsTermines }}</div>
                                            <small class="text-muted">Terminés</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Liste des objectifs stratégiques -->
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-bullseye me-2"></i>
                                            Objectifs Stratégiques ({{ $objectifsStrategiques->count() }})
                                        </h6>
                                        @if(Auth::user()->canCreateObjectifStrategique())
                                            <button type="button" class="btn btn-light btn-sm" wire:click="showCreateObjectifForm">
                                                <i class="fas fa-plus me-2"></i>
                                                Créer un Objectif
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($objectifsStrategiques->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Code</th>
                                                        <th>Libellé</th>
                                                        <th>Description</th>
                                                        <th>Progression</th>
                                                        <th>Owner</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($objectifsStrategiques as $objectifStrategique)
                                                    <tr>
                                                        <td>
                                                            <span class="badge bg-primary">{{ $pilier->code }}.{{ $objectifStrategique->code }}</span>
                                                        </td>
                                                        <td>
                                                            <strong>{{ $objectifStrategique->libelle }}</strong>
                                                        </td>
                                                        <td>
                                                            @if($objectifStrategique->description)
                                                                {{ Str::limit($objectifStrategique->description, 50) }}
                                                            @else
                                                                <span class="text-muted">Aucune description</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar bg-success" 
                                                                     style="width: {{ $objectifStrategique->taux_avancement }}%"
                                                                     role="progressbar" 
                                                                     aria-valuenow="{{ $objectifStrategique->taux_avancement }}" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="100">
                                                                    {{ number_format($objectifStrategique->taux_avancement, 1) }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary">
                                                                {{ $objectifStrategique->owner->name ?? 'Non assigné' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                                                        wire:click="voirObjectifStrategique({{ $objectifStrategique->id }})"
                                                                        title="Voir détails">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                @if(Auth::user()->canUpdateObjectifStrategique())
                                                                    <button type="button" 
                                                                            wire:click="setActionToEditObjectifStrategique({{ $objectifStrategique->id }})"
                                                                            class="btn btn-outline-warning btn-sm" 
                                                                            title="Modifier">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                @endif
                                                                @if(Auth::user()->isAdminGeneral())
                                                                    <button type="button" 
                                                                            class="btn btn-outline-danger btn-sm" 
                                                                            wire:click="deleteObjectifStrategique({{ $objectifStrategique->id }})"
                                                                            onclick="if(!confirm('Êtes-vous sûr de vouloir supprimer cet objectif stratégique ?')) return false;"
                                                                            title="Supprimer">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
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
                                            <div class="mb-3">
                                                <i class="fas fa-bullseye fa-3x text-muted"></i>
                                            </div>
                                            <h5 class="text-muted mb-2">Aucun objectif stratégique</h5>
                                            <p class="text-muted mb-3">Ce pilier n'a pas encore d'objectifs stratégiques.</p>
                                            @if(Auth::user()->canCreateObjectifStrategique())
                                                <button type="button" class="btn btn-primary" wire:click="showCreateObjectifForm">
                                                    <i class="fas fa-plus me-2"></i>
                                                    Créer le premier objectif
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Ici on peut ajouter les autres vues (objectif stratégique, spécifique, action, sous-action) -->
                    <!-- Pour l'instant, on garde la structure simple -->
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9998;"></div>
    @endif

    <script>
    document.addEventListener('livewire:init', () => {
        // Écouteur pour actualiser la page après fermeture du modal
        Livewire.on('refreshPage', () => {
            console.log('🔄 [REFRESH] Actualisation de la page...');
            setTimeout(() => {
                window.location.reload();
            }, 100);
        });
    });
    </script>
</div>
