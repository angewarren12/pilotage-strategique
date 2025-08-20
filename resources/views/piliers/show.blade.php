@extends('layouts.app')

@section('title', 'Détails du Pilier ' . $pilier->code)

@section('content')
<div class="container-fluid py-4">
    <!-- Header avec navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('piliers.index') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>Retour aux Piliers
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Détails du Pilier {{ $pilier->code }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- En-tête du pilier -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
        <div class="col-lg-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-layer-group text-white fs-4"></i>
                </div>
                                <div>
                                    <h1 class="h2 mb-1 text-dark">{{ $pilier->libelle }}</h1>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary fs-6 me-3">{{ $pilier->code }}</span>
                                        <span class="text-muted">
                                            <i class="fas fa-bullseye me-1"></i>
                                            {{ $pilier->objectifsStrategiques->count() }} objectif{{ $pilier->objectifsStrategiques->count() > 1 ? 's' : '' }} stratégique{{ $pilier->objectifsStrategiques->count() > 1 ? 's' : '' }}
                                        </span>
                        </div>
                    </div>
                    </div>
                    @if($pilier->description)
                                <p class="text-muted mb-0 fs-6">{{ $pilier->description }}</p>
                            @endif
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="d-flex flex-column align-items-lg-end">
                    <div class="mb-3">
                                    <div class="progress" style="height: 25px; width: 200px;">
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
                                <div class="btn-group" role="group">
                                    <a href="{{ route('piliers.edit', $pilier) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-edit me-1"></i>Modifier
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="supprimerPilier({{ $pilier->id }}, '{{ addslashes($pilier->libelle) }}')">
                                        <i class="fas fa-trash me-1"></i>Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        
    <!-- Statistiques -->
    <div class="row mb-4">
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
                    <div class="display-6 text-success mb-2">{{ $pilier->objectifsStrategiques->count() }}</div>
                    <h6 class="text-muted mb-0">Objectifs Stratégiques</h6>
                            </div>
                        </div>
                    </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-6 text-info mb-2">{{ $pilier->objectifsStrategiques->sum(function($os) { return $os->objectifsSpecifiques->count(); }) }}</div>
                    <h6 class="text-muted mb-0">Objectifs Spécifiques</h6>
                            </div>
                        </div>
                        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-6 text-warning mb-2">{{ $pilier->objectifsStrategiques->filter(function($os) { return $os->taux_avancement == 100; })->count() }}</div>
                    <h6 class="text-muted mb-0">Terminés</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Objectifs Stratégiques -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-bullseye me-2 text-success"></i>
                Objectifs Stratégiques
            </h5>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createObjectifStrategiqueModal" onclick="preparerCreationOS({{ $pilier->id }})">
                <i class="fas fa-plus me-2"></i>
                            Nouvel Objectif
            </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($pilier->objectifsStrategiques->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Code</th>
                                        <th class="border-0">Libellé</th>
                                        <th class="border-0">Description</th>
                                        <th class="border-0">Owner</th>
                                        <th class="border-0">Progression</th>
                                        <th class="border-0">Objectifs Spécifiques</th>
                                        <th class="border-0 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pilier->objectifsStrategiques as $objectifStrategique)
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
                                                <a href="{{ route('objectifs-strategiques.show', $objectifStrategique) }}" 
                                                   class="btn btn-outline-primary btn-sm" 
                                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('objectifs-strategiques.edit', $objectifStrategique) }}" 
                                                   class="btn btn-outline-warning btn-sm" 
                                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm" 
                                                        onclick="supprimerObjectifStrategique({{ $objectifStrategique->id }}, '{{ addslashes($objectifStrategique->libelle) }}')"
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
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createObjectifStrategiqueModal" onclick="preparerCreationOS({{ $pilier->id }})">
                                <i class="fas fa-plus me-2"></i>
                                Créer le premier objectif
                                    </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Créer Objectif Stratégique -->
<div class="modal fade" id="createObjectifStrategiqueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvel Objectif Stratégique</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createObjectifStrategiqueForm">
                <div class="modal-body">
                    <input type="hidden" name="pilier_id" id="createObjectifStrategiquePilierId">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" id="createObjectifStrategiqueCode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" name="owner_id">
                            <option value="">Sélectionner un owner</option>
                            @foreach(App\Models\User::whereHas('role', function($query) {
                                $query->whereIn('nom', ['admin_general', 'owner_os']);
                            })->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Fonction pour préparer la création d'un objectif stratégique
function preparerCreationOS(pilierId) {
    document.getElementById('createObjectifStrategiquePilierId').value = pilierId;
    
    // Suggérer un code basé sur les objectifs existants
    fetch(`/api/piliers/${pilierId}/objectifs-strategiques`)
        .then(response => response.json())
        .then(data => {
            const codeInput = document.getElementById('createObjectifStrategiqueCode');
            const existingCodes = data.objectifs_strategiques ? data.objectifs_strategiques.map(os => os.code) : [];
            let nextCode = 'OS1';
            
            if (existingCodes.length > 0) {
                const numbers = existingCodes.map(code => parseInt(code.replace('OS', ''))).filter(n => !isNaN(n));
                if (numbers.length > 0) {
                    const maxNumber = Math.max(...numbers);
                    nextCode = `OS${maxNumber + 1}`;
                }
            }
            
            codeInput.value = nextCode;
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des codes:', error);
        });
}

// Gestion du formulaire de création d'objectif stratégique
document.getElementById('createObjectifStrategiqueForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Création...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    
    fetch('{{ route("objectifs-strategiques.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Objectif stratégique créé avec succès !');
            window.location.reload();
        } else {
            showToast('error', 'Erreur lors de la création : ' + data.message);
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('error', 'Erreur lors de la création.');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Fonction pour supprimer un objectif stratégique
function supprimerObjectifStrategique(id, libelle) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'objectif stratégique "${libelle}" ?`)) {
        fetch(`/objectifs-strategiques/${id}`, {
            method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
                showToast('success', 'Objectif stratégique supprimé avec succès !');
            window.location.reload();
        } else {
                showToast('error', 'Erreur lors de la suppression : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
            showToast('error', 'Erreur lors de la suppression.');
        });
    }
}

// Fonction pour supprimer un pilier
function supprimerPilier(id, libelle) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le pilier "${libelle}" ? Cette action est irréversible.`)) {
        fetch(`/piliers/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Pilier supprimé avec succès !');
                window.location.href = '{{ route("piliers.index") }}';
            } else {
                showToast('error', 'Erreur lors de la suppression : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('error', 'Erreur lors de la suppression.');
        });
    }
}
</script>
@endpush 