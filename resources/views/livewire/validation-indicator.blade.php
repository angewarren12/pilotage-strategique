<div class="validation-indicator">
    <!-- Indicateur de validation dans la navbar -->
    @if($pendingValidations->count() > 0)
        <div class="validation-badge">
            <button class="btn btn-warning btn-sm position-relative" wire:click="showValidationDetails({{ $pendingValidations->first()->id }})">
                <i class="fas fa-clock"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $pendingValidations->count() }}
                </span>
            </button>
        </div>
    @endif

    <!-- Modal de validation -->
    @if($showValidationModal && $selectedValidation)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-clock text-warning me-2"></i>
                            Détails de la Validation
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeValidationModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Informations de la validation -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Élément :</strong>
                                <span class="badge bg-primary">{{ $selectedValidation->element_type }}</span>
                                <span class="badge bg-secondary">{{ $selectedValidation->element_id }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Demandé par :</strong>
                                {{ $selectedValidation->requestedBy->name ?? 'Utilisateur inconnu' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Date de demande :</strong>
                                {{ $selectedValidation->formatted_requested_at }}
                            </div>
                            <div class="col-md-6">
                                <strong>Statut :</strong>
                                <span class="badge bg-warning">En attente</span>
                            </div>
                        </div>

                        <!-- Détails de la validation -->
                        @if($selectedValidation->validation_data)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Détails de la demande</h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $data = $selectedValidation->validation_data;
                                    @endphp
                                    
                                    @if(isset($data['action']))
                                        <p><strong>Action :</strong> {{ ucfirst(str_replace('_', ' ', $data['action'])) }}</p>
                                    @endif
                                    
                                    @if(isset($data['reason']))
                                        <p><strong>Raison :</strong> {{ $data['reason'] }}</p>
                                    @endif
                                    
                                    @if(isset($data['old_owner_id']) && isset($data['new_owner_id']))
                                        <p><strong>Changement de propriétaire :</strong></p>
                                        <ul>
                                            <li>Ancien : {{ \App\Models\User::find($data['old_owner_id'])->name ?? 'N/A' }}</li>
                                            <li>Nouveau : {{ \App\Models\User::find($data['new_owner_id'])->name ?? 'N/A' }}</li>
                                        </ul>
                                    @endif
                                    
                                    @if(isset($data['old_deadline']) && isset($data['new_deadline']))
                                        <p><strong>Changement d'échéance :</strong></p>
                                        <ul>
                                            <li>Ancienne : {{ $data['old_deadline'] }}</li>
                                            <li>Nouvelle : {{ $data['new_deadline'] }}</li>
                                        </ul>
                                    @endif
                                    
                                    @if(isset($data['old_status']) && isset($data['new_status']))
                                        <p><strong>Changement de statut :</strong></p>
                                        <ul>
                                            <li>Ancien : {{ $data['old_status'] }}</li>
                                            <li>Nouveau : {{ $data['new_status'] }}</li>
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Actions de validation -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="approvalComments">Commentaires d'approbation (optionnel)</label>
                                    <textarea class="form-control" id="approvalComments" wire:model="approvalComments" rows="3"></textarea>
                                </div>
                                <button class="btn btn-success mt-2" wire:click="approveValidation">
                                    <i class="fas fa-check me-2"></i>Approuver
                                </button>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rejectionReason">Raison du rejet *</label>
                                    <textarea class="form-control" id="rejectionReason" wire:model="rejectionReason" rows="3" required></textarea>
                                </div>
                                <button class="btn btn-danger mt-2" wire:click="rejectValidation">
                                    <i class="fas fa-times me-2"></i>Rejeter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeValidationModal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.validation-indicator {
    position: relative;
}

.validation-badge {
    display: inline-block;
}

.modal.show {
    z-index: 1050;
}

.badge {
    font-size: 0.75em;
}
</style> 