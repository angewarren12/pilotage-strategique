<!-- Modal de création de Sous-Action -->
@if($showCreateSousActionModal)
<div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(5) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(5)) }};">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Créer une Sous-Action
                </h5>
                <button type="button" class="btn-close" wire:click="closeCreateSousActionModal" aria-label="Close"></button>
            </div>
            
            <form wire:submit.prevent="createSousAction">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sous_action_code" class="form-label">
                                    <i class="fas fa-code me-1"></i>Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('newSousAction.code') is-invalid @enderror" 
                                       id="sous_action_code" 
                                       wire:model="newSousAction.code" 
                                       placeholder="Ex: SA001"
                                       maxlength="10">
                                @error('newSousAction.code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Code unique de la sous-action (max 10 caractères)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sous_action_owner" class="form-label">
                                    <i class="fas fa-user me-1"></i>Responsable <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('newSousAction.owner_id') is-invalid @enderror" 
                                        id="sous_action_owner" 
                                        wire:model="newSousAction.owner_id">
                                    <option value="">Sélectionner un responsable</option>
                                    @foreach($users ?? [] as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('newSousAction.owner_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sous_action_libelle" class="form-label">
                            <i class="fas fa-tasks me-1"></i>Libellé <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('newSousAction.libelle') is-invalid @enderror" 
                               id="sous_action_libelle" 
                               wire:model="newSousAction.libelle" 
                               placeholder="Libellé de la sous-action"
                               maxlength="255">
                        @error('newSousAction.libelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="sous_action_description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control @error('newSousAction.description') is-invalid @enderror" 
                                  id="sous_action_description" 
                                  wire:model="newSousAction.description" 
                                  rows="3"
                                  placeholder="Description détaillée de la sous-action"></textarea>
                        @error('newSousAction.description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sous_action_type" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('newSousAction.type') is-invalid @enderror" 
                                        id="sous_action_type" 
                                        wire:model="newSousAction.type">
                                    <option value="">Sélectionner un type</option>
                                    <option value="normal">Normal</option>
                                    <option value="projet">Projet</option>
                                </select>
                                @error('newSousAction.type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sous_action_date_echeance" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Date d'échéance
                                </label>
                                <input type="date" 
                                       class="form-control @error('newSousAction.date_echeance') is-invalid @enderror" 
                                       id="sous_action_date_echeance" 
                                       wire:model="newSousAction.date_echeance">
                                @error('newSousAction.date_echeance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sous_action_taux_avancement" class="form-label">
                            <i class="fas fa-percentage me-1"></i>Taux d'avancement initial <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control @error('newSousAction.taux_avancement') is-invalid @enderror" 
                               id="sous_action_taux_avancement" 
                               wire:model="newSousAction.taux_avancement" 
                               min="0" 
                               max="100" 
                               step="0.01">
                        @error('newSousAction.taux_avancement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Pourcentage d'avancement initial (0-100%)
                        </small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeCreateSousActionModal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: {{ $pilier->getHierarchicalColor(5) }}; border-color: {{ $pilier->getHierarchicalColor(5) }};">
                        <i class="fas fa-save me-1"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
@endif
