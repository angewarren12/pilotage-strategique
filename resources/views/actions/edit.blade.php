@extends('layouts.app')

@section('title', 'Modifier l\'Action')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">
                        <i class="fas fa-edit text-success me-2"></i>
                        Modifier l'Action
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('actions.index') }}">Actions</a></li>
                            <li class="breadcrumb-item active">Modifier</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('actions.show', $action->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour
                    </a>
                </div>
            </div>

            <!-- Formulaire de modification -->
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-edit me-2"></i>
                    Modifier l'Action
                </div>
                <div class="card-body">
                    <form action="{{ route('actions.update', $action->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code *</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code', $action->code) }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="libelle" class="form-label">Libellé *</label>
                                    <input type="text" class="form-control @error('libelle') is-invalid @enderror" 
                                           id="libelle" name="libelle" value="{{ old('libelle', $action->libelle) }}" required>
                                    @error('libelle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $action->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="objectif_specifique_id" class="form-label">Objectif Spécifique *</label>
                                    <select class="form-select @error('objectif_specifique_id') is-invalid @enderror" 
                                            id="objectif_specifique_id" name="objectif_specifique_id" required>
                                        <option value="">Sélectionner un objectif spécifique</option>
                                        @foreach(App\Models\ObjectifSpecifique::all() as $objectifSpecifique)
                                            <option value="{{ $objectifSpecifique->id }}" 
                                                    {{ old('objectif_specifique_id', $action->objectif_specifique_id) == $objectifSpecifique->id ? 'selected' : '' }}>
                                                {{ $objectifSpecifique->code_complet }} - {{ $objectifSpecifique->libelle }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('objectif_specifique_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="owner_id" class="form-label">Owner</label>
                                    <select class="form-select @error('owner_id') is-invalid @enderror" 
                                            id="owner_id" name="owner_id">
                                        <option value="">Sélectionner un owner</option>
                                        @foreach(App\Models\User::whereHas('role', function($query) {
                                            $query->whereIn('nom', ['admin_general', 'owner_os', 'owner_pil', 'owner_action']);
                                        })->get() as $user)
                                            <option value="{{ $user->id }}" 
                                                    {{ old('owner_id', $action->owner_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('owner_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('actions.show', $action->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-suggestion du code basé sur l'objectif spécifique sélectionné
document.getElementById('objectif_specifique_id').addEventListener('change', function() {
    const objectifSpecifiqueId = this.value;
    if (objectifSpecifiqueId) {
        // Récupérer les codes existants pour cet objectif spécifique
        fetch(`/api/actions/codes/${objectifSpecifiqueId}`)
            .then(response => response.json())
            .then(data => {
                const codeInput = document.getElementById('code');
                const existingCodes = data.codes;
                let nextCode = 'A1';
                
                if (existingCodes.length > 0) {
                    // Trouver le prochain numéro disponible
                    const numbers = existingCodes.map(code => parseInt(code.replace('A', ''))).filter(n => !isNaN(n));
                    if (numbers.length > 0) {
                        const maxNumber = Math.max(...numbers);
                        nextCode = `A${maxNumber + 1}`;
                    }
                }
                
                codeInput.value = nextCode;
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des codes:', error);
            });
    }
});
</script>
@endpush 