@extends('layouts.app')

@section('title', 'Détails de l\'Action')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">
                        <i class="fas fa-tasks text-success me-2"></i>
                        Détails de l'Action
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('piliers.index') }}">Piliers</a></li>
                            @if($action->objectifSpecifique && $action->objectifSpecifique->objectifStrategique && $action->objectifSpecifique->objectifStrategique->pilier)
                            <li class="breadcrumb-item"><a href="{{ route('piliers.show', $action->objectifSpecifique->objectifStrategique->pilier->id) }}">{{ $action->objectifSpecifique->objectifStrategique->pilier->libelle }}</a></li>
                            @endif
                            @if($action->objectifSpecifique && $action->objectifSpecifique->objectifStrategique)
                            <li class="breadcrumb-item"><a href="{{ route('objectifs-strategiques.show', $action->objectifSpecifique->objectifStrategique->id) }}">{{ $action->objectifSpecifique->objectifStrategique->libelle }}</a></li>
                            @endif
                            @if($action->objectifSpecifique)
                            <li class="breadcrumb-item"><a href="{{ route('objectifs-specifiques.show', $action->objectifSpecifique->id) }}">{{ $action->objectifSpecifique->libelle }}</a></li>
                            @endif
                            <li class="breadcrumb-item active">{{ $action->libelle }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('actions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editActionModal">
                        <i class="fas fa-edit me-1"></i>
                        Modifier
                    </button>
                </div>
            </div>

            <!-- Informations détaillées de l'Action -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-info-circle me-2"></i>
                            Détails de l'Action
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Code complet:</strong>
                                    <span class="badge bg-primary fs-6">{{ $action->code_complet }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Code simple:</strong>
                                    <span class="badge bg-secondary">{{ $action->code }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>Libellé:</strong>
                                <h5 class="text-dark">{{ $action->libelle }}</h5>
                            </div>
                            @if($action->description)
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p class="mb-0 text-muted">{{ $action->description }}</p>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Owner:</strong>
                                    @if($action->owner)
                                        <span class="badge bg-info fs-6">{{ $action->owner->name }}</span>
                                    @else
                                        <span class="text-muted">Non assigné</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <strong>Objectif Spécifique parent:</strong>
                                    @if($action->objectifSpecifique)
                                        <span class="badge bg-warning">{{ $action->objectifSpecifique->libelle }}</span>
                                    @else
                                        <span class="text-muted">Aucun objectif spécifique</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-chart-line me-2"></i>
                            Progression
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <h2 class="text-success mb-3">{{ $action->taux_avancement }}%</h2>
                                <div class="progress mb-3" style="height: 30px;">
                                    <div class="progress-bar bg-{{ $action->statut_color }}"
                                         role="progressbar"
                                         style="width: {{ $action->taux_avancement }}%"
                                         aria-valuenow="{{ $action->taux_avancement }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        {{ $action->taux_avancement }}%
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h4 class="text-muted">{{ $action->sousActions->count() }}</h4>
                                        <small class="text-muted">Sous-actions</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success">{{ $action->sousActions->where('taux_avancement', 100)->count() }}</h4>
                                        <small class="text-muted">Terminées</small>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        Créé le {{ $action->created_at ? $action->created_at->format('d/m/Y') : 'Date inconnue' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sous-actions -->
            <div class="card border-success">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-list me-2"></i>
                        Sous-actions ({{ $action->sousActions->count() }})
                    </div>
                    <button type="button" class="btn btn-light btn-sm" onclick="ajouterSousAction()">
                        <i class="fas fa-plus me-1"></i>
                        Ajouter une Sous-action
                    </button>
                </div>
                <div class="card-body">
                    @if($action->sousActions->count() > 0)
                        <div class="row">
                            @foreach($action->sousActions as $sousAction)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <span class="badge bg-info me-2">{{ $sousAction->code }}</span>
                                            {{ $sousAction->libelle }}
                                        </h6>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('sous-actions.show', $sousAction->id) }}"
                                               class="btn btn-sm btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('sous-actions.edit', $sousAction->id) }}"
                                               class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if($sousAction->description)
                                        <p class="card-text small">{{ Str::limit($sousAction->description, 100) }}</p>
                                        @endif
                                        <div class="mb-2">
                                            <small class="text-muted">Owner:</small>
                                            @if($sousAction->owner)
                                                <span class="badge bg-success">{{ $sousAction->owner->name }}</span>
                                            @else
                                                <span class="text-muted small">Non assigné</span>
                                            @endif
                                        </div>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $sousAction->statut_color }}"
                                                 style="width: {{ $sousAction->taux_avancement }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-{{ $sousAction->statut_color }}">
                                                {{ $sousAction->taux_avancement }}%
                                            </span>
                                            <small class="text-muted">{{ $sousAction->created_at ? $sousAction->created_at->format('d/m/Y') : 'Date inconnue' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune sous-action</h5>
                            <p class="text-muted">Cette action n'a pas encore de sous-actions.</p>
                            <button type="button" class="btn btn-primary" onclick="ajouterSousAction()">
                                <i class="fas fa-plus me-2"></i>
                                Créer la première sous-action
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Éditer Action -->
<div class="modal fade" id="editActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Éditer Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editActionForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" value="{{ $action->code }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" value="{{ $action->libelle }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3">{{ $action->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" name="owner_id">
                            <option value="">Sélectionner un owner</option>
                            @foreach(App\Models\User::whereHas('role', function($query) {
                                $query->whereIn('nom', ['admin_general', 'owner_os', 'owner_pil', 'owner_action']);
                            })->get() as $user)
                                <option value="{{ $user->id }}" {{ $action->owner_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function ajouterSousAction() {
    window.location.href = '{{ route("sous-actions.create") }}?action_id={{ $action->id }}';
}

// Gestion du formulaire d'édition
document.getElementById('editActionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // ROUTE COMMENTÉE POUR DÉBOGAGE
    alert('Fonction d\'édition temporairement désactivée pour débogage');
    
    /*
    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("actions.update", $action->id) }}', {
        method: 'PUT',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Action modifiée avec succès !');
            window.location.reload();
        } else {
            alert('Erreur lors de la modification : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification.');
    });
    */
});
</script>
@endpush 