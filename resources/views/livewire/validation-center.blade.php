<div class="validation-center">
    <!-- Bouton de validation avec badge -->
    <div class="position-relative">
        <button class="btn btn-outline-warning position-relative btn-validation-mobile" wire:click="toggleValidations" title="Validations">
            <i class="fas fa-shield-alt"></i>
            @if(count($validations) > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                    {{ count($validations) > 99 ? '99+' : count($validations) }}
                </span>
            @endif
        </button>
    </div>

    <!-- Panneau des validations -->
    @if($showValidations)
        <div class="validation-panel position-absolute top-100 end-0 mt-2 bg-white border rounded shadow-lg">
            <!-- Header du panneau -->
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h6 class="mb-0">
                    <i class="fas fa-shield-alt me-2"></i>
                    Validations
                </h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" wire:click="toggleValidations" title="Fermer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Statistiques rapides -->
            @if(!empty($stats))
                <div class="p-3 border-bottom bg-light">
                    <div class="row text-center">
                        <div class="col-3">
                            <small class="text-muted d-block">En attente</small>
                            <span class="badge bg-primary">{{ $stats['pending'] ?? 0 }}</span>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block">Approuvées</small>
                            <span class="badge bg-success">{{ $stats['approved'] ?? 0 }}</span>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block">Rejetées</small>
                            <span class="badge bg-danger">{{ $stats['rejected'] ?? 0 }}</span>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block">Taux</small>
                            <span class="badge bg-info">{{ $stats['approval_rate'] ?? 0 }}%</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Liste des validations -->
            <div class="validation-list" style="max-height: min(400px, 50vh); overflow-y: auto;">
                @if(count($validations) > 0)
                    @foreach($validations as $validation)
                        <div class="validation-item p-2 p-md-3 border-bottom {{ $validation->isPending() ? 'bg-light' : '' }}" 
                             wire:click="openValidationModal({{ $validation->id }})"
                             style="cursor: pointer;"
                             data-validation-id="{{ $validation->id }}">
                            <div class="d-flex align-items-start gap-3">
                                <!-- Icône de validation -->
                                <div class="flex-shrink-0">
                                    <i class="{{ $this->getValidationIcon($validation->status) }} fs-5"></i>
                                </div>
                                
                                <!-- Contenu de la validation -->
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start">
                                        <h6 class="mb-1 text-truncate {{ $validation->isPending() ? 'fw-bold' : '' }}">
                                            {{ $validation->element_name }}
                                        </h6>
                                        <div class="d-flex gap-1 mt-1 mt-md-0">
                                            <span class="badge bg-{{ $this->getValidationColor($validation->status) }} small">
                                                {{ ucfirst($validation->status) }}
                                            </span>
                                            @if($this->canValidate($validation))
                                                <span class="badge bg-info small d-none d-md-inline">Vous pouvez valider</span>
                                                <span class="badge bg-info small d-md-none">Validable</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="mb-1 text-muted small">
                                        <strong>{{ $validation->requestedBy->name ?? 'Utilisateur inconnu' }}</strong>
                                        <span class="d-none d-sm-inline">a demandé une validation</span>
                                        <span class="d-sm-none">validation</span>
                                    </p>
                                    
                                    <!-- Affichage du type de validation -->
                                    <div class="mb-1">
                                        @if($validation->type === 'completion')
                                            <span class="badge bg-success small">
                                                <i class="fas fa-check-circle me-1"></i>Achèvement
                                            </span>
                                        @elseif($validation->type === 'progression_decrease')
                                            <span class="badge bg-warning small">
                                                <i class="fas fa-arrow-down me-1"></i>Diminution Progression
                                            </span>
                                        @else
                                            <span class="badge bg-secondary small">
                                                <i class="fas fa-question-circle me-1"></i>{{ ucfirst($validation->type ?? 'validation') }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Détails spécifiques pour les diminutions de progression -->
                                    @if($validation->type === 'progression_decrease' && $validation->current_value && $validation->requested_value)
                                        <div class="mb-1">
                                            <small class="text-muted">
                                                <i class="fas fa-arrow-down me-1"></i>
                                                {{ $validation->current_value }}% → {{ $validation->requested_value }}%
                                            </small>
                                        </div>
                                    @endif
                                    
                                    <small class="text-muted">{{ $validation->formatted_requested_at }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-shield-alt text-muted fs-1"></i>
                        <p class="text-muted mt-2">Aucune validation en attente</p>
                    </div>
                @endif
            </div>

            <!-- Footer du panneau -->
            @if(count($validations) > 0)
                <div class="p-3 border-top bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            {{ count($validations) }} validation(s) en attente
                        </small>
                        <a href="#" class="text-decoration-none small">Voir toutes</a>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Modal de détails de validation -->
    @if($showValidationDetails && $selectedValidation)
        <div class="modal fade show" style="display: block; z-index: 1060;" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="d-flex align-items-center gap-3">
                            <i class="{{ $this->getValidationIcon($selectedValidation->status) }} fs-4"></i>
                            <div>
                                <h5 class="modal-title mb-0">Validation - {{ $selectedValidation->element_name }}</h5>
                                <small class="text-muted">{{ $selectedValidation->element_code }}</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" wire:click="closeValidationDetails"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="validation-details">
                            <!-- Informations de base -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Demandeur :</strong>
                                    <p>{{ $selectedValidation->requestedBy->name ?? 'Utilisateur inconnu' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Date de demande :</strong>
                                    <p>{{ $selectedValidation->formatted_requested_at }}</p>
                                </div>
                            </div>

                            <!-- Statut -->
                            <div class="mb-3">
                                <strong>Statut :</strong>
                                <span class="badge bg-{{ $this->getValidationColor($selectedValidation->status) }} ms-2">
                                    {{ ucfirst($selectedValidation->status) }}
                                </span>
                            </div>

                            <!-- Détails spécifiques selon le type de validation -->
                            @if($selectedValidation->type === 'progression_decrease')
                                <div class="card mb-3">
                                    <div class="card-header bg-warning text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-arrow-down me-2"></i>
                                            Demande de Diminution de Progression
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Progression actuelle :</strong>
                                                <div class="mt-1">
                                                    <span class="badge bg-primary fs-6">{{ $selectedValidation->current_value }}%</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Progression demandée :</strong>
                                                <div class="mt-1">
                                                    <span class="badge bg-warning fs-6">{{ $selectedValidation->requested_value }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                        @if($selectedValidation->reason)
                                            <div class="mt-3">
                                                <strong>Raison de la demande :</strong>
                                                <p class="mb-0 mt-1">{{ $selectedValidation->reason }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @elseif($selectedValidation->type === 'completion')
                                <div class="card mb-3">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-check-circle me-2"></i>
                                            Demande d'Achèvement
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">
                                            <strong>Demande :</strong> Validation pour passer la progression de 
                                            <span class="badge bg-warning">{{ $selectedValidation->current_value ?? 99 }}%</span> 
                                            à <span class="badge bg-success">100%</span>
                                        </p>
                                        @if($selectedValidation->reason)
                                            <div class="mt-2">
                                                <strong>Raison :</strong> {{ $selectedValidation->reason }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @elseif($selectedValidation->validation_data)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Détails de la demande</h6>
                                    </div>
                                    <div class="card-body">
                                        <pre class="mb-0"><code>{{ json_encode($selectedValidation->validation_data, JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                </div>
                            @endif

                            <!-- Actions de validation -->
                            @if($selectedValidation->isPending() && $this->canValidate($selectedValidation))
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Actions de validation</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Approuver -->
                                        <div class="mb-3">
                                            <label class="form-label">Commentaires d'approbation (optionnel)</label>
                                            <textarea class="form-control" wire:model="approvalComments" rows="2" 
                                                      placeholder="Commentaires..."></textarea>
                                        </div>
                                        
                                        <!-- Rejeter -->
                                        <div class="mb-3">
                                            <label class="form-label">Raison du rejet</label>
                                            <textarea class="form-control" wire:model="rejectionReason" rows="2" 
                                                      placeholder="Raison du rejet..."></textarea>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-success" wire:click="approveValidation">
                                                <i class="fas fa-check me-1"></i>
                                                Approuver
                                            </button>
                                            <button type="button" class="btn btn-danger" wire:click="rejectValidation">
                                                <i class="fas fa-times me-1"></i>
                                                Rejeter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Historique -->
                            @if($selectedValidation->validatedBy)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Validation effectuée</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Validé par :</strong> {{ $selectedValidation->validatedBy->name }}</p>
                                        <p><strong>Date :</strong> {{ $selectedValidation->formatted_validated_at }}</p>
                                        @if($selectedValidation->comments)
                                            <p><strong>Commentaires :</strong> {{ $selectedValidation->comments }}</p>
                                        @endif
                                        @if($selectedValidation->rejection_reason)
                                            <p><strong>Raison du rejet :</strong> {{ $selectedValidation->rejection_reason }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeValidationDetails">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show" style="z-index: 1055;"></div>
    @endif

    <style>
    .validation-center {
        position: relative;
    }

    .validation-panel {
        animation: slideDown 0.3s ease-out;
    }

    .validation-item {
        transition: all 0.2s ease;
    }

    .validation-item:hover {
        background-color: rgba(255, 193, 7, 0.05) !important;
        transform: translateX(2px);
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .validation-list::-webkit-scrollbar {
        width: 6px;
    }

    .validation-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .validation-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .validation-list::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Responsive pour petits écrans */
    @media (max-width: 576px) {
        .validation-panel {
            left: 10px !important;
            right: 10px !important;
            width: auto !important;
            max-width: none !important;
        }
        
        .validation-item {
            padding: 0.75rem !important;
        }
        
        .modal-dialog {
            margin: 10px !important;
            max-width: none !important;
        }
        
        .modal-header .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .modal-header .btn-close {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    }

    @media (max-width: 768px) {
        .validation-panel {
            width: min(400px, 90vw) !important;
        }
    }
    </style>

    <script>
    // Fermer le panneau de validations en cliquant à l'extérieur
    document.addEventListener('click', function(event) {
        const validationCenter = document.querySelector('.validation-center');
        const validationPanel = document.querySelector('.validation-panel');
        
        if (validationPanel && !validationCenter.contains(event.target)) {
            @this.toggleValidations();
        }
    });

    // Actualiser les validations toutes les 30 secondes
    setInterval(function() {
        @this.loadValidations();
    }, 30000);
    </script>
</div>
