@extends('layouts.app')

@section('title', 'Détails de l\'Objectif Stratégique')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header simplifié -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">
                        <i class="fas fa-bullseye text-success me-2"></i>
                        Détails de l'Objectif Stratégique
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('piliers.index') }}">Piliers</a></li>
                            @if($objectifStrategique->pilier)
                            <li class="breadcrumb-item"><a href="{{ route('piliers.show', $objectifStrategique->pilier->id) }}">{{ $objectifStrategique->pilier->libelle }}</a></li>
                            @endif
                            <li class="breadcrumb-item active">{{ $objectifStrategique->libelle }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('objectifs-strategiques.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour
                    </a>
                </div>
            </div>

            <!-- Informations basiques -->
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-info-circle me-2"></i>
                    Détails de l'Objectif Stratégique
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Code:</strong>
                            <span class="badge bg-primary fs-6">{{ $objectifStrategique->code }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Libellé:</strong>
                            <h5 class="text-dark">{{ $objectifStrategique->libelle }}</h5>
                        </div>
                    </div>
                    @if($objectifStrategique->description)
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p class="mb-0 text-muted">{{ $objectifStrategique->description }}</p>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Owner:</strong>
                            @if($objectifStrategique->owner)
                                <span class="badge bg-info fs-6">{{ $objectifStrategique->owner->name }}</span>
                            @else
                                <span class="text-muted">Non assigné</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Progression:</strong>
                            <div class="progress mt-1" style="height: 20px;">
                                <div class="progress-bar bg-success"
                                     role="progressbar" 
                                     style="width: {{ $objectifStrategique->taux_avancement }}%"
                                     aria-valuenow="{{ $objectifStrategique->taux_avancement }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ $objectifStrategique->taux_avancement }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Objectifs Spécifiques simplifiés -->
            <div class="card border-success mt-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-list-check me-2"></i>
                        Objectifs Spécifiques ({{ $objectifStrategique->objectifsSpecifiques->count() }})
                    </div>
                    <button type="button" class="btn btn-light btn-sm" onclick="ajouterObjectifSpecifique()">
                        <i class="fas fa-plus me-1"></i>
                        Ajouter un Objectif
                    </button>
                </div>
                <div class="card-body">
                    @if($objectifStrategique->objectifsSpecifiques->count() > 0)
                        <div class="row">
                            @foreach($objectifStrategique->objectifsSpecifiques as $objectifSpecifique)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <span class="badge bg-info me-2">{{ $objectifSpecifique->code }}</span>
                                            {{ $objectifSpecifique->libelle }}
                                        </h6>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('objectifs-specifiques.show', $objectifSpecifique->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('objectifs-specifiques.edit', $objectifSpecifique->id) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if($objectifSpecifique->description)
                                        <p class="card-text small">{{ Str::limit($objectifSpecifique->description, 100) }}</p>
                                        @endif
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: {{ $objectifSpecifique->taux_avancement }}%"></div>
                                        </div>
                                        <span class="badge bg-success">{{ $objectifSpecifique->taux_avancement }}%</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-list-check fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun objectif spécifique</h5>
                            <p class="text-muted">Cet objectif stratégique n'a pas encore d'objectifs spécifiques.</p>
                            <button type="button" class="btn btn-primary" onclick="ajouterObjectifSpecifique()">
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
@endsection

@push('scripts')
<script>
function ajouterObjectifSpecifique() {
    // Rediriger vers la page de création d'objectif spécifique
    window.location.href = '{{ route("objectifs-specifiques.create") }}?objectif_strategique_id={{ $objectifStrategique->id }}';
}
</script>
@endpush 