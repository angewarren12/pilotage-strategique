@extends('layouts.app')

@section('title', 'Nouvelle Sous-Action - Plateforme de Stratelia')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus me-2 text-primary"></i>
                Nouvelle Sous-Action
            </h1>
            <p class="text-muted">Créer une nouvelle sous-action avec son taux d'avancement</p>
        </div>
        <div>
            <a href="{{ route('sous-actions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Informations de la Sous-Action
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('sous-actions.store') }}" id="createSousActionForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">
                                    <i class="fas fa-hashtag me-1"></i>Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code') }}" 
                                       placeholder="SA1, SA2, etc." required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Code unique de la sous-action</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="action_id" class="form-label">
                                    <i class="fas fa-tasks me-1"></i>Action Parente <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('action_id') is-invalid @enderror" 
                                        id="action_id" name="action_id" required>
                                    <option value="">Sélectionner une action</option>
                                    @foreach($actions as $action)
                                        <option value="{{ $action->id }}" {{ old('action_id') == $action->id ? 'selected' : '' }}>
                                            {{ $action->code_complet }} - {{ $action->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('action_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="libelle" class="form-label">
                                <i class="fas fa-tag me-1"></i>Libellé <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('libelle') is-invalid @enderror" 
                                   id="libelle" name="libelle" value="{{ old('libelle') }}" 
                                   placeholder="Description courte de la sous-action" required>
                            @error('libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Description détaillée de la sous-action">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="owner_id" class="form-label">
                                    <i class="fas fa-user me-1"></i>Owner
                                </label>
                                <select class="form-select @error('owner_id') is-invalid @enderror" 
                                        id="owner_id" name="owner_id">
                                    <option value="">Sélectionner un owner</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('owner_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->role->libelle ?? 'Sans rôle' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('owner_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="taux_avancement" class="form-label">
                                    <i class="fas fa-percentage me-1"></i>Taux d'avancement <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex align-items-center">
                                    <input type="range" class="form-range flex-grow-1 me-3" 
                                           id="taux_avancement" name="taux_avancement" 
                                           min="0" max="100" step="5" value="{{ old('taux_avancement', 0) }}">
                                    <span class="fw-bold min-w-50" id="taux_value">0%</span>
                                </div>
                                <input type="hidden" name="taux_input" id="taux_input" value="{{ old('taux_avancement', 0) }}">
                                @error('taux_avancement')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_echeance" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Date d'échéance
                                </label>
                                <input type="date" class="form-control @error('date_echeance') is-invalid @enderror" 
                                       id="date_echeance" name="date_echeance" value="{{ old('date_echeance') }}">
                                @error('date_echeance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="date_realisation" class="form-label">
                                    <i class="fas fa-check-circle me-1"></i>Date de réalisation
                                </label>
                                <input type="date" class="form-control @error('date_realisation') is-invalid @enderror" 
                                       id="date_realisation" name="date_realisation" value="{{ old('date_realisation') }}">
                                @error('date_realisation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('sous-actions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Créer la Sous-Action
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Informations sur l'action parente -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations sur l'Action Parente
                    </h6>
                </div>
                <div class="card-body" id="actionInfo">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-tasks fa-2x mb-3"></i>
                        <p>Sélectionnez une action pour voir ses détails</p>
                    </div>
                </div>
            </div>
            
            <!-- Aide -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>
                        Aide
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="fw-bold">Code</h6>
                        <p class="small text-muted">Utilisez un code unique comme SA1, SA2, etc. Le code complet sera généré automatiquement.</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Taux d'avancement</h6>
                        <p class="small text-muted">Saisissez le pourcentage d'avancement actuel de la sous-action (0-100%).</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Échéance</h6>
                        <p class="small text-muted">Définissez la date limite pour la réalisation de cette sous-action.</p>
                    </div>
                    <div>
                        <h6 class="fw-bold">Owner</h6>
                        <p class="small text-muted">Assignez un responsable pour le suivi de cette sous-action.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .min-w-50 {
        min-width: 50px;
    }
    
    .form-range {
        height: 6px;
    }
    
    .form-range::-webkit-slider-thumb {
        background: var(--primary-green);
    }
    
    .form-range::-moz-range-thumb {
        background: var(--primary-green);
    }
</style>
@endpush

@push('scripts')
<script>
    // Synchroniser le slider et l'affichage du taux
    $('#taux_avancement').on('input', function() {
        const value = $(this).val();
        $('#taux_value').text(value + '%');
        $('#taux_input').val(value);
    });
    
    // Afficher les informations de l'action sélectionnée
    $('#action_id').on('change', function() {
        const actionId = $(this).val();
        if (actionId) {
            // Simuler l'affichage des informations de l'action
            const selectedOption = $(this).find('option:selected');
            const actionText = selectedOption.text();
            
            $('#actionInfo').html(`
                <div class="mb-3">
                    <h6 class="fw-bold">Action sélectionnée</h6>
                    <p class="mb-2">${actionText}</p>
                </div>
                <div class="mb-3">
                    <h6 class="fw-bold">Hiérarchie</h6>
                    <div class="small">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-layer-group me-2 text-primary"></i>
                            <span>Pilier → Objectif Stratégique → Objectif Spécifique → Action</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h6 class="fw-bold">Code complet</h6>
                    <p class="mb-0"><code>${actionText.split(' - ')[0]}.SA[VOTRE_CODE]</code></p>
                </div>
            `);
        } else {
            $('#actionInfo').html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-tasks fa-2x mb-3"></i>
                    <p>Sélectionnez une action pour voir ses détails</p>
                </div>
            `);
        }
    });
    
    // Validation du formulaire
    $('#createSousActionForm').on('submit', function(e) {
        const code = $('#code').val();
        const actionId = $('#action_id').val();
        const libelle = $('#libelle').val();
        
        if (!code || !actionId || !libelle) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return false;
        }
        
        // Afficher un indicateur de chargement
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Création en cours...');
        submitBtn.prop('disabled', true);
    });
    
    // Initialisation
    $(document).ready(function() {
        // Définir la date d'échéance par défaut (dans 30 jours)
        if (!$('#date_echeance').val()) {
            const defaultDate = new Date();
            defaultDate.setDate(defaultDate.getDate() + 30);
            $('#date_echeance').val(defaultDate.toISOString().split('T')[0]);
        }
    });
</script>
@endpush 