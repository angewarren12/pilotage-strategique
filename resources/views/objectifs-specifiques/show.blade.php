@extends('layouts.app')

@section('title', 'Détails de l\'Objectif Spécifique')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">
                        <i class="fas fa-list-check text-success me-2"></i>
                        Détails de l'Objectif Spécifique
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('piliers.index') }}">Piliers</a></li>
                            @if($objectifSpecifique->objectifStrategique && $objectifSpecifique->objectifStrategique->pilier)
                            <li class="breadcrumb-item"><a href="{{ route('piliers.show', $objectifSpecifique->objectifStrategique->pilier->id) }}">{{ $objectifSpecifique->objectifStrategique->pilier->libelle }}</a></li>
                            @endif
                            @if($objectifSpecifique->objectifStrategique)
                            <li class="breadcrumb-item"><a href="{{ route('objectifs-strategiques.show', $objectifSpecifique->objectifStrategique->id) }}">{{ $objectifSpecifique->objectifStrategique->libelle }}</a></li>
                            @endif
                            <li class="breadcrumb-item active">{{ $objectifSpecifique->libelle }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('objectifs-specifiques.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editObjectifModal">
                        <i class="fas fa-edit me-1"></i>
                        Modifier
                    </button>
                </div>
            </div>

            <!-- Informations détaillées de l'Objectif Spécifique -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-info-circle me-2"></i>
                            Détails de l'Objectif Spécifique
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Code complet:</strong>
                                    <span class="badge bg-primary fs-6">{{ $objectifSpecifique->code_complet }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Code simple:</strong>
                                    <span class="badge bg-secondary">{{ $objectifSpecifique->code }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>Libellé:</strong>
                                <h5 class="text-dark">{{ $objectifSpecifique->libelle }}</h5>
                            </div>
                            @if($objectifSpecifique->description)
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p class="mb-0 text-muted">{{ $objectifSpecifique->description }}</p>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Owner:</strong>
                                    @if($objectifSpecifique->owner)
                                        <span class="badge bg-info fs-6">{{ $objectifSpecifique->owner->name }}</span>
                                    @else
                                        <span class="text-muted">Non assigné</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <strong>Objectif Stratégique parent:</strong>
                                    @if($objectifSpecifique->objectifStrategique)
                                        <span class="badge bg-warning">{{ $objectifSpecifique->objectifStrategique->libelle }}</span>
                                    @else
                                        <span class="text-muted">Aucun objectif stratégique</span>
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
                                <h2 class="text-success mb-3">{{ $objectifSpecifique->taux_avancement }}%</h2>
                                <div class="progress mb-3" style="height: 30px;">
                                    <div class="progress-bar bg-{{ $objectifSpecifique->statut_color }}"
                                         role="progressbar"
                                         style="width: {{ $objectifSpecifique->taux_avancement }}%"
                                         aria-valuenow="{{ $objectifSpecifique->taux_avancement }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        {{ $objectifSpecifique->taux_avancement }}%
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h4 class="text-muted">{{ $objectifSpecifique->actions->count() }}</h4>
                                        <small class="text-muted">Actions</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success">{{ $objectifSpecifique->actions->where('taux_avancement', 100)->count() }}</h4>
                                        <small class="text-muted">Terminées</small>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        Créé le {{ $objectifSpecifique->created_at ? $objectifSpecifique->created_at->format('d/m/Y') : 'Date inconnue' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-success">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-tasks me-2"></i>
                        Actions ({{ $objectifSpecifique->actions->count() }})
                    </div>
                    <button type="button" class="btn btn-light btn-sm" onclick="ajouterAction()">
                        <i class="fas fa-plus me-1"></i>
                        Ajouter une Action
                    </button>
                </div>
                <div class="card-body">
                    @if($objectifSpecifique->actions->count() > 0)
                        <div class="row">
                            @foreach($objectifSpecifique->actions as $action)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <span class="badge bg-info me-2">{{ $action->code }}</span>
                                            {{ $action->libelle }}
                                        </h6>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('actions.show', $action->id) }}"
                                               class="btn btn-sm btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('actions.edit', $action->id) }}"
                                               class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if($action->description)
                                        <p class="card-text small">{{ Str::limit($action->description, 100) }}</p>
                                        @endif
                                        <div class="mb-2">
                                            <small class="text-muted">Owner:</small>
                                            @if($action->owner)
                                                <span class="badge bg-success">{{ $action->owner->name }}</span>
                                            @else
                                                <span class="text-muted small">Non assigné</span>
                                            @endif
                                        </div>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $action->statut_color }}"
                                                 style="width: {{ $action->taux_avancement }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-{{ $action->statut_color }}">
                                                {{ $action->taux_avancement }}%
                                            </span>
                                            <small class="text-muted">{{ $action->created_at ? $action->created_at->format('d/m/Y') : 'Date inconnue' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune action</h5>
                            <p class="text-muted">Cet objectif spécifique n'a pas encore d'actions.</p>
                            <button type="button" class="btn btn-primary" onclick="ajouterAction()">
                                <i class="fas fa-plus me-2"></i>
                                Créer la première action
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Éditer Objectif Spécifique -->
<div class="modal fade" id="editObjectifModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Éditer Objectif Spécifique</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editObjectifForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" value="{{ $objectifSpecifique->code }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" value="{{ $objectifSpecifique->libelle }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3">{{ $objectifSpecifique->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" name="owner_id">
                            <option value="">Sélectionner un owner</option>
                            @foreach(App\Models\User::whereHas('role', function($query) {
                                $query->whereIn('nom', ['admin_general', 'owner_os', 'owner_pil']);
                            })->get() as $user)
                                <option value="{{ $user->id }}" {{ $objectifSpecifique->owner_id == $user->id ? 'selected' : '' }}>
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
function ajouterAction() {
    window.location.href = '{{ route("actions.create") }}?objectif_specifique_id={{ $objectifSpecifique->id }}';
}

// Gestion du formulaire d'édition
document.getElementById('editObjectifForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // ROUTE COMMENTÉE POUR DÉBOGAGE
    alert('Fonction d\'édition temporairement désactivée pour débogage');
    
    /*
    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route("objectifs-specifiques.update", $objectifSpecifique->id) }}', {
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
            alert('Objectif spécifique modifié avec succès !');
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