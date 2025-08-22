<!-- Modals pour les Objectifs Spécifiques -->

<!-- Modal de création d'Objectif Spécifique -->
@if($showCreateOSPModal)
<div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Créer un Objectif Spécifique
                </h5>
                <button type="button" class="btn-close" wire:click="closeCreateOSPModal" aria-label="Close"></button>
            </div>
            
            <form wire:submit.prevent="createObjectifSpecifique">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="osp_code" class="form-label">
                                    <i class="fas fa-code me-1"></i>Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('newOSP.code') is-invalid @enderror" 
                                       id="osp_code" 
                                       wire:model="newOSP.code" 
                                       placeholder="Ex: OSP001"
                                       maxlength="10">
                                @error('newOSP.code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Code unique de l'objectif spécifique (max 10 caractères)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="osp_owner" class="form-label">
                                    <i class="fas fa-user me-1"></i>Propriétaire <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('newOSP.owner_id') is-invalid @enderror" 
                                        id="osp_owner" 
                                        wire:model="newOSP.owner_id">
                                    <option value="">Sélectionner un propriétaire</option>
                                    @foreach($users ?? [] as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('newOSP.owner_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="osp_libelle" class="form-label">
                            <i class="fas fa-list me-1"></i>Libellé <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('newOSP.libelle') is-invalid @enderror" 
                               id="osp_libelle" 
                               wire:model="newOSP.libelle" 
                               placeholder="Libellé de l'objectif spécifique"
                               maxlength="255">
                        @error('newOSP.libelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="osp_description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control @error('newOSP.description') is-invalid @enderror" 
                                  id="osp_description" 
                                  wire:model="newOSP.description" 
                                  rows="3"
                                  placeholder="Description détaillée de l'objectif spécifique"></textarea>
                        @error('newOSP.description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                                         <div class="mb-3">
                         <div class="alert alert-info">
                             <i class="fas fa-info-circle me-2"></i>
                             <strong>Note :</strong> Le taux d'avancement sera calculé automatiquement en fonction des actions associées.
                         </div>
                     </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeCreateOSPModal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: {{ $pilier->getHierarchicalColor(3) }}; border-color: {{ $pilier->getHierarchicalColor(3) }};">
                        <i class="fas fa-save me-1"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
@endif

<!-- Modal d'édition d'Objectif Spécifique -->
@if($showEditOSPModal && $editingOSP)
<div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    Modifier l'Objectif Spécifique
                </h5>
                <button type="button" class="btn-close" wire:click="closeEditOSPModal" aria-label="Close"></button>
            </div>
            
                         <form wire:submit.prevent="updateObjectifSpecifique">
                 <div class="modal-body">
                     <!-- Debug Info -->
                     @if($editingOSP)
                     <div class="alert alert-info mb-3">
                         <strong>Debug :</strong> 
                         ID: {{ $editingOSP->id }}, 
                         Code: {{ $editingOSP->code }}, 
                         Libellé: {{ $editingOSP->libelle }}, 
                         Propriétaire: {{ $editingOSP->owner_id }}
                     </div>
                     @endif
                     
                     <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_osp_code" class="form-label">
                                    <i class="fas fa-code me-1"></i>Code <span class="text-danger">*</span>
                                </label>
                                                                 <input type="text" 
                                        class="form-control @error('editOSPCode') is-invalid @enderror" 
                                        id="edit_osp_code" 
                                        wire:model="editOSPCode" 
                                        placeholder="Ex: OSP001"
                                        maxlength="10">
                                @error('editingOSP.code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_osp_owner" class="form-label">
                                    <i class="fas fa-user me-1"></i>Propriétaire <span class="text-danger">*</span>
                                </label>
                                                                 <select class="form-select @error('editOSPOwnerId') is-invalid @enderror" 
                                         id="edit_osp_owner" 
                                         wire:model="editOSPOwnerId">
                                     <option value="">Sélectionner un propriétaire</option>
                                     @foreach($users ?? [] as $user)
                                         <option value="{{ $user->id }}" {{ $editOSPOwnerId == $user->id ? 'selected' : '' }}>
                                             {{ $user->name }} ({{ $user->email }})
                                         </option>
                                     @endforeach
                                 </select>
                                @error('editingOSP.owner_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_osp_libelle" class="form-label">
                            <i class="fas fa-list me-1"></i>Libellé <span class="text-danger">*</span>
                        </label>
                                                         <input type="text" 
                                        class="form-control @error('editOSPLibelle') is-invalid @enderror" 
                                        id="edit_osp_libelle" 
                                        wire:model="editOSPLibelle" 
                                        placeholder="Libellé de l'objectif spécifique"
                                        maxlength="255">
                        @error('editingOSP.libelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_osp_description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                                                         <textarea class="form-control @error('editOSPDescription') is-invalid @enderror" 
                                           id="edit_osp_description" 
                                           wire:model="editOSPDescription" 
                                           rows="3"
                                           placeholder="Description détaillée de l'objectif spécifique"></textarea>
                        @error('editingOSP.description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                                         <div class="mb-3">
                         <div class="alert alert-info">
                             <i class="fas fa-info-circle me-2"></i>
                             <strong>Taux d'avancement actuel :</strong> 
                             <span class="badge bg-primary">{{ number_format($editingOSP->taux_avancement, 1) }}%</span>
                             <br>
                             <small class="text-muted">Ce taux est calculé automatiquement en fonction des actions associées et ne peut pas être modifié manuellement.</small>
                         </div>
                     </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEditOSPModal">
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
