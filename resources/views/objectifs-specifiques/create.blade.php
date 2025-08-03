@extends('layouts.app')

@section('title', 'Créer un Objectif Spécifique')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus me-2"></i>
                Créer un Objectif Spécifique
            </h1>
            <p class="text-muted">Ajouter un nouvel objectif spécifique au pilotage stratégique</p>
        </div>
        
        <a href="{{ route('objectifs-specifiques.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour à la liste
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Formulaire -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Informations de l'Objectif Spécifique
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('objectifs-specifiques.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">
                                    Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code') }}" 
                                       placeholder="Ex: PIL1" maxlength="10" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Code unique de l'objectif spécifique (max 10 caractères)</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="objectif_strategique_id" class="form-label">
                                    Objectif Stratégique <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('objectif_strategique_id') is-invalid @enderror" 
                                        id="objectif_strategique_id" name="objectif_strategique_id" required>
                                    <option value="">Sélectionner un objectif stratégique</option>
                                    @foreach($objectifsStrategiques as $os)
                                        <option value="{{ $os->id }}" {{ old('objectif_strategique_id') == $os->id ? 'selected' : '' }}>
                                            {{ $os->code_complet }} - {{ $os->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('objectif_strategique_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="libelle" class="form-label">
                                Libellé <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('libelle') is-invalid @enderror" 
                                   id="libelle" name="libelle" value="{{ old('libelle') }}" 
                                   placeholder="Libellé de l'objectif spécifique" maxlength="255" required>
                            @error('libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Description détaillée de l'objectif spécifique">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="owner_id" class="form-label">Owner</label>
                            <select class="form-select @error('owner_id') is-invalid @enderror" 
                                    id="owner_id" name="owner_id">
                                <option value="">Sélectionner un owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('owner_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->role->libelle ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('owner_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Personne responsable de cet objectif spécifique</small>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('objectifs-specifiques.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Créer l'Objectif Spécifique
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Informations -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Conseils</h6>
                        <ul class="mb-0">
                            <li>Le code doit être unique et court</li>
                            <li>Le libellé doit être clair et descriptif</li>
                            <li>L'owner sera responsable de la création des actions</li>
                            <li>Le taux d'avancement sera calculé automatiquement</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Important</h6>
                        <p class="mb-0">Une fois créé, l'objectif spécifique ne pourra être supprimé que s'il ne contient aucune action.</p>
                    </div>
                </div>
            </div>
            
            <!-- Objectif Stratégique sélectionné -->
            <div class="card mt-3" id="objectif-strategique-info" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bullseye me-2"></i>
                        Objectif Stratégique
                    </h5>
                </div>
                <div class="card-body" id="objectif-strategique-details">
                    <!-- Les détails seront affichés ici via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const objectifStrategiqueSelect = document.getElementById('objectif_strategique_id');
    const objectifStrategiqueInfo = document.getElementById('objectif-strategique-info');
    const objectifStrategiqueDetails = document.getElementById('objectif-strategique-details');
    
    // Données des objectifs stratégiques (seraient normalement récupérées via AJAX)
    const objectifsStrategiques = @json($objectifsStrategiques);
    
    objectifStrategiqueSelect.addEventListener('change', function() {
        const selectedId = this.value;
        const selectedOS = objectifsStrategiques.find(os => os.id == selectedId);
        
        if (selectedOS) {
            objectifStrategiqueDetails.innerHTML = `
                <div class="mb-2">
                    <strong>Code:</strong> <span class="badge bg-primary">${selectedOS.code_complet}</span>
                </div>
                <div class="mb-2">
                    <strong>Libellé:</strong> ${selectedOS.libelle}
                </div>
                <div class="mb-2">
                    <strong>Pilier:</strong> <span class="badge bg-secondary">${selectedOS.pilier ? selectedOS.pilier.code : 'N/A'}</span>
                </div>
                <div class="mb-2">
                    <strong>Taux d'avancement:</strong>
                    <div class="progress mt-1" style="height: 8px;">
                        <div class="progress-bar bg-${getStatusColor(selectedOS.taux_avancement)}" 
                             style="width: ${selectedOS.taux_avancement}%"></div>
                    </div>
                    <small class="text-muted">${selectedOS.taux_avancement}%</small>
                </div>
            `;
            objectifStrategiqueInfo.style.display = 'block';
        } else {
            objectifStrategiqueInfo.style.display = 'none';
        }
    });
    
    function getStatusColor(taux) {
        if (taux >= 100) return 'success';
        if (taux >= 75) return 'info';
        if (taux >= 50) return 'warning';
        return 'danger';
    }
    
    // Déclencher l'événement au chargement si une valeur est déjà sélectionnée
    if (objectifStrategiqueSelect.value) {
        objectifStrategiqueSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection 