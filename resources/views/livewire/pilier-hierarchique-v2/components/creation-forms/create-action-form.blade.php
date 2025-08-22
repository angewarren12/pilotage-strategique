<!-- Modal de création d'Action -->
@if($showCreateActionModal)
<div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Créer une Action
                </h5>
                <button type="button" class="btn-close" wire:click="closeCreateActionModal" aria-label="Close"></button>
            </div>
            
            <form wire:submit.prevent="createAction">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="action_code" class="form-label">
                                    <i class="fas fa-code me-1"></i>Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('newAction.code') is-invalid @enderror" 
                                       id="action_code" 
                                       wire:model="newAction.code" 
                                       placeholder="Ex: ACT001"
                                       maxlength="10">
                                @error('newAction.code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Code unique de l'action (max 10 caractères)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="action_owner" class="form-label">
                                    <i class="fas fa-user me-1"></i>Responsable <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('newAction.owner_id') is-invalid @enderror" 
                                        id="action_owner" 
                                        wire:model="newAction.owner_id">
                                    <option value="">Sélectionner un responsable</option>
                                    @foreach($users ?? [] as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('newAction.owner_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="action_libelle" class="form-label">
                            <i class="fas fa-tasks me-1"></i>Libellé <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('newAction.libelle') is-invalid @enderror" 
                               id="action_libelle" 
                               wire:model="newAction.libelle" 
                               placeholder="Libellé de l'action"
                               maxlength="255">
                        @error('newAction.libelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="action_description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control @error('newAction.description') is-invalid @enderror" 
                                  id="action_description" 
                                  wire:model="newAction.description" 
                                  rows="3"
                                  placeholder="Description détaillée de l'action"></textarea>
                        @error('newAction.description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="action_type" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('newAction.type') is-invalid @enderror" 
                                        id="action_type" 
                                        wire:model="newAction.type">
                                    <option value="">Sélectionner un type</option>
                                    <option value="normal">Normal</option>
                                    <option value="projet">Projet</option>
                                </select>
                                @error('newAction.type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="action_date_echeance" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Date d'échéance
                                </label>
                                <input type="date" 
                                       class="form-control @error('newAction.date_echeance') is-invalid @enderror" 
                                       id="action_date_echeance" 
                                       wire:model="newAction.date_echeance">
                                @error('newAction.date_echeance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="action_taux_avancement" class="form-label">
                            <i class="fas fa-percentage me-1"></i>Taux d'avancement initial <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control @error('newAction.taux_avancement') is-invalid @enderror" 
                               id="action_taux_avancement" 
                               wire:model="newAction.taux_avancement" 
                               min="0" 
                               max="100" 
                               step="0.01">
                        @error('newAction.taux_avancement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Pourcentage d'avancement initial (0-100%)
                        </small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeCreateActionModal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: {{ $pilier->getHierarchicalColor(4) }}; border-color: {{ $pilier->getHierarchicalColor(4) }};">
                        <i class="fas fa-save me-1"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
@endif
