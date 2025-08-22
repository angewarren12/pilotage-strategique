<!-- Modal d'édition d'Action -->
@if($showEditActionModal && $editingAction)
<div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    Modifier l'Action
                </h5>
                <button type="button" class="btn-close" wire:click="closeEditActionModal" aria-label="Close"></button>
            </div>
            
            <form wire:submit.prevent="updateAction">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_action_code" class="form-label">
                                    <i class="fas fa-code me-1"></i>Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('editingAction.code') is-invalid @enderror" 
                                       id="edit_action_code" 
                                       wire:model="editingAction.code" 
                                       placeholder="Ex: ACT001"
                                       maxlength="10">
                                @error('editingAction.code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_action_owner" class="form-label">
                                    <i class="fas fa-user me-1"></i>Responsable <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('editingAction.owner_id') is-invalid @enderror" 
                                        id="edit_action_owner" 
                                        wire:model="editingAction.owner_id">
                                    <option value="">Sélectionner un responsable</option>
                                    @foreach($users ?? [] as $user)
                                        <option value="{{ $user->id }}" {{ $editingAction->owner_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('editingAction.owner_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_action_libelle" class="form-label">
                            <i class="fas fa-tasks me-1"></i>Libellé <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('editingAction.libelle') is-invalid @enderror" 
                               id="edit_action_libelle" 
                               wire:model="editingAction.libelle" 
                               placeholder="Libellé de l'action"
                               maxlength="255">
                        @error('editingAction.libelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_action_description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control @error('editingAction.description') is-invalid @enderror" 
                                  id="edit_action_description" 
                                  wire:model="editingAction.description" 
                                  rows="3"
                                  placeholder="Description détaillée de l'action"></textarea>
                        @error('editingAction.description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_action_type" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('editingAction.type') is-invalid @enderror" 
                                        id="edit_action_type" 
                                        wire:model="editingAction.type">
                                    <option value="">Sélectionner un type</option>
                                    <option value="normal" {{ $editingAction->type == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="projet" {{ $editingAction->type == 'projet' ? 'selected' : '' }}>Projet</option>
                                </select>
                                @error('editingAction.type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_action_date_echeance" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Date d'échéance
                                </label>
                                <input type="date" 
                                       class="form-control @error('editingAction.date_echeance') is-invalid @enderror" 
                                       id="edit_action_date_echeance" 
                                       wire:model="editingAction.date_echeance">
                                @error('editingAction.date_echeance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_action_taux_avancement" class="form-label">
                            <i class="fas fa-percentage me-1"></i>Taux d'avancement <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control @error('editingAction.taux_avancement') is-invalid @enderror" 
                               id="edit_action_taux_avancement" 
                               wire:model="editingAction.taux_avancement" 
                               min="0" 
                               max="100" 
                               step="0.01">
                        @error('editingAction.taux_avancement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Pourcentage d'avancement actuel (0-100%)
                        </small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEditActionModal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
@endif
