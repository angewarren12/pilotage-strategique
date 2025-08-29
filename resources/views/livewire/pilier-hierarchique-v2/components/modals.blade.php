<!-- Modals pour les Objectifs Stratégiques -->

<!-- Modal de création d'Objectif Stratégique -->
@if($showCreateOSModal)
<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog" data-bs-backdrop="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    Créer un Objectif Stratégique
                </h5>
                <button type="button" class="btn-close" wire:click="closeCreateOSModal" aria-label="Close"></button>
            </div>
            
            <form wire:submit.prevent="createObjectifStrategique">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="os_code" class="form-label">
                                    <i class="fas fa-code me-1"></i>Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('newOS.code') is-invalid @enderror" 
                                       id="os_code" 
                                       wire:model="newOS.code" 
                                       placeholder="Ex: OS001"
                                       maxlength="10">
                                @error('newOS.code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Code unique de l'objectif stratégique (max 10 caractères)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="os_owner" class="form-label">
                                    <i class="fas fa-user me-1"></i>Propriétaire <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('newOS.owner_id') is-invalid @enderror" 
                                        id="os_owner" 
                                        wire:model="newOS.owner_id">
                                    <option value="">Sélectionner un propriétaire</option>
                                    @foreach($users ?? [] as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('newOS.owner_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="os_libelle" class="form-label">
                            <i class="fas fa-list me-1"></i>Libellé <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('newOS.libelle') is-invalid @enderror" 
                               id="os_libelle" 
                               wire:model="newOS.libelle" 
                               placeholder="Libellé de l'objectif stratégique"
                               maxlength="255">
                        @error('newOS.libelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="os_description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control @error('newOS.description') is-invalid @enderror" 
                                  id="os_description" 
                                  wire:model="newOS.description" 
                                  rows="3"
                                  placeholder="Description détaillée de l'objectif stratégique"></textarea>
                        @error('newOS.description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note :</strong> Le taux d'avancement sera calculé automatiquement en fonction des objectifs spécifiques associés.
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeCreateOSModal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: {{ $pilier->getHierarchicalColor(2) }}; border-color: {{ $pilier->getHierarchicalColor(2) }};">
                        <i class="fas fa-save me-1"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal d'édition d'Objectif Stratégique -->
@if($showEditOSModal && $editingObjectifStrategique)
<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog" data-bs-backdrop="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    Modifier l'Objectif Stratégique
                </h5>
                <button type="button" class="btn-close" wire:click="closeEditOSModal" aria-label="Close"></button>
            </div>
            
            <form wire:submit.prevent="updateObjectifStrategique">
                <div class="modal-body">
                    <!-- Debug Info -->
                    @if($editingObjectifStrategique)
                    <div class="alert alert-info mb-3">
                        <strong>Debug :</strong> 
                        ID: {{ $editingObjectifStrategique->id }}, 
                        Code: {{ $editingObjectifStrategique->code }}, 
                        Libellé: {{ $editingObjectifStrategique->libelle }}, 
                        Propriétaire: {{ $editingObjectifStrategique->owner_id }}
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_os_code" class="form-label">
                                    <i class="fas fa-code me-1"></i>Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('editOSCode') is-invalid @enderror" 
                                       id="edit_os_code" 
                                       wire:model="editOSCode" 
                                       placeholder="Ex: OS001"
                                       maxlength="10">
                                @error('editOSCode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_os_owner" class="form-label">
                                    <i class="fas fa-user me-1"></i>Propriétaire <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('editOSOwnerId') is-invalid @enderror" 
                                        id="edit_os_owner" 
                                        wire:model="editOSOwnerId">
                                    <option value="">Sélectionner un propriétaire</option>
                                    @foreach($users ?? [] as $user)
                                        <option value="{{ $user->id }}" {{ $editOSOwnerId == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('editOSOwnerId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_os_libelle" class="form-label">
                            <i class="fas fa-list me-1"></i>Libellé <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('editOSLibelle') is-invalid @enderror" 
                               id="edit_os_libelle" 
                               wire:model="editOSLibelle" 
                               placeholder="Libellé de l'objectif stratégique"
                               maxlength="255">
                        @error('editOSLibelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_os_description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control @error('editOSDescription') is-invalid @enderror" 
                                  id="edit_os_description" 
                                  wire:model="editOSDescription" 
                                  rows="3"
                                  placeholder="Description détaillée de l'objectif stratégique"></textarea>
                        @error('editOSDescription')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note :</strong> Le taux d'avancement sera calculé automatiquement en fonction des objectifs spécifiques associés.
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEditOSModal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: {{ $pilier->getHierarchicalColor(2) }}; border-color: {{ $pilier->getHierarchicalColor(2) }};">
                        <i class="fas fa-save me-1"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Overlay supprimé - Bootstrap gère automatiquement l'arrière-plan sombre -->
@endif

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
                                @error('editOSPCode')
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
                                @error('editOSPOwnerId')
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
                        @error('editOSPLibelle')
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
                        @error('editOSPDescription')
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
                    <button type="button" class="btn btn-secondary" wire:click="closeEditOSPModal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: {{ $pilier->getHierarchicalColor(3) }}; border-color: {{ $pilier->getHierarchicalColor(3) }};">
                        <i class="fas fa-save me-1"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
@endif

<!-- Modals pour les Actions -->

<!-- Modal de création d'Action -->
@if($showCreateActionModal)
<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog" data-bs-backdrop="true">
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
                    
                    <!-- Information sur le taux d'avancement -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Taux d'avancement initial: 0%</strong><br>
                        Le taux d'avancement est automatiquement défini à 0% lors de la création et sera calculé en fonction des sous-actions associées.
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

<!-- Modal d'édition d'Action -->
@if($showEditActionModal && $editingAction)
<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog" data-bs-backdrop="true">
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
                    <!-- Debug Info -->
                    @if($editingAction)
                    <div class="alert alert-info mb-3">
                        <strong>Debug :</strong> 
                        ID: {{ $editingAction->id }}, 
                        Code: {{ $editingAction->code }}, 
                        Libellé: {{ $editingAction->libelle }}, 
                        Propriétaire: {{ $editingAction->owner_id }}
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_action_code" class="form-label">
                                    <i class="fas fa-code me-1"></i>Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('editActionCode') is-invalid @enderror" 
                                       id="edit_action_code" 
                                       wire:model="editActionCode" 
                                       placeholder="Ex: ACT001"
                                       maxlength="10">
                                @error('editActionCode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_action_owner" class="form-label">
                                    <i class="fas fa-user me-1"></i>Responsable <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('editActionOwnerId') is-invalid @enderror" 
                                        id="edit_action_owner" 
                                        wire:model="editActionOwnerId">
                                    <option value="">Sélectionner un responsable</option>
                                    @foreach($users ?? [] as $user)
                                        <option value="{{ $user->id }}" {{ $editActionOwnerId == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('editActionOwnerId')
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
                               class="form-control @error('editActionLibelle') is-invalid @enderror" 
                               id="edit_action_libelle" 
                               wire:model="editActionLibelle" 
                               placeholder="Libellé de l'action"
                               maxlength="255">
                        @error('editActionLibelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_action_description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control @error('editActionDescription') is-invalid @enderror" 
                                  id="edit_action_description" 
                                  wire:model="editActionDescription" 
                                  rows="3"
                                  placeholder="Description détaillée de l'action"></textarea>
                        @error('editActionDescription')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note :</strong> Le taux d'avancement sera calculé automatiquement en fonction des sous-actions associées.
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEditActionModal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: {{ $pilier->getHierarchicalColor(4) }}; border-color: {{ $pilier->getHierarchicalColor(4) }};">
                        <i class="fas fa-save me-1"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
@endif

<!-- Modals pour les Sous-Actions -->

<!-- Modal de création de Sous-Action -->
@if($showCreateSousActionModal)
<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog" data-bs-backdrop="true">
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
                                    <option value="normal">Normal</option>
                                    <option value="projet">Projet</option>
                                </select>
                                @error('newSousAction.type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Type de la sous-action (Normal ou Projet)
                                </small>
                            </div>
                        </div>
                        
                        
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sous_action_date_echeance" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Date d'échéance <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('newSousAction.date_echeance') is-invalid @enderror" 
                                       id="sous_action_date_echeance" 
                                       wire:model="newSousAction.date_echeance"
                                       min="2020-01-01"
                                       max="2030-12-31">
                                @error('newSousAction.date_echeance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sous_action_taux_initial" class="form-label">
                                    <i class="fas fa-percentage me-1"></i>Taux d'avancement initial
                                </label>
                                <input type="number" 
                                       class="form-control @error('newSousAction.taux_avancement') is-invalid @enderror" 
                                       id="sous_action_taux_initial" 
                                       wire:model="newSousAction.taux_avancement"
                                       min="0" 
                                       max="100" 
                                       step="0.1"
                                       value="0">
                                @error('newSousAction.taux_avancement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Taux initial (0-100%), par défaut 0%
                                </small>
                            </div>
                        </div>
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

<!-- Modal d'édition de Sous-Action -->
@if($showEditSousActionModal && $editingSousAction)
<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog" data-bs-backdrop="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(5) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(5)) }};">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    Modifier la Sous-Action
                </h5>
                <button type="button" class="btn-close" wire:click="closeEditSousActionModal" aria-label="Close"></button>
            </div>
            
            <form wire:submit.prevent="updateSousAction">
                <div class="modal-body">
                    <!-- Messages d'erreur de validation -->
                    @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Erreurs de validation
                        </h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_sous_action_code" class="form-label">
                                    <i class="fas fa-code me-1"></i>Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('editSousActionCode') is-invalid @enderror" 
                                       id="edit_sous_action_code" 
                                       wire:model="editSousActionCode" 
                                       placeholder="Ex: SA001"
                                       maxlength="10">
                                @error('editSousActionCode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_sous_action_owner" class="form-label">
                                    <i class="fas fa-user me-1"></i>Responsable <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('editSousActionOwnerId') is-invalid @enderror" 
                                        id="edit_sous_action_owner" 
                                        wire:model="editSousActionOwnerId">
                                    <option value="">Sélectionner un responsable</option>
                                    @foreach($users ?? [] as $user)
                                        <option value="{{ $user->id }}" {{ $editSousActionOwnerId == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('editSousActionOwnerId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_sous_action_libelle" class="form-label">
                            <i class="fas fa-tasks me-1"></i>Libellé <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('editSousActionLibelle') is-invalid @enderror" 
                               id="edit_sous_action_libelle" 
                               wire:model="editSousActionLibelle" 
                               placeholder="Libellé de la sous-action"
                               maxlength="255">
                        @error('editSousActionLibelle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_sous_action_description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control @error('editSousActionDescription') is-invalid @enderror" 
                                  id="edit_sous_action_description" 
                                  wire:model="editSousActionDescription" 
                                  rows="3"
                                  placeholder="Description détaillée de la sous-action"></textarea>
                        @error('editSousActionDescription')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_sous_action_type" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('editSousActionType') is-invalid @enderror" 
                                        id="edit_sous_action_type" 
                                        wire:model="editSousActionType">
                                    <option value="normal">Normal</option>
                                    <option value="projet">Projet</option>
                                </select>
                                @error('editSousActionType')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Type de la sous-action (Normal ou Projet)
                                </small>
                            </div>
                        </div>
                        
                        
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_sous_action_date_echeance" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Date d'échéance <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('editSousActionDateEcheance') is-invalid @enderror" 
                                       id="edit_sous_action_date_echeance" 
                                       wire:model="editSousActionDateEcheance"
                                       min="2020-01-01"
                                       max="2030-12-31">
                                @error('editSousActionDateEcheance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_sous_action_taux" class="form-label">
                                    <i class="fas fa-percentage me-1"></i>Taux d'avancement
                                </label>
                                @if($editSousActionType === 'projet')
                                    <!-- Mode lecture seule pour les projets -->
                                    <input type="number" 
                                           class="form-control" 
                                           id="edit_sous_action_taux" 
                                           value="{{ $editSousActionTauxAvancement }}"
                                           min="0" 
                                           max="100" 
                                           step="0.1"
                                           readonly
                                           style="background-color: #e9ecef; cursor: not-allowed;">
                                    <small class="form-text text-warning">
                                        <i class="fas fa-lock me-1"></i>Progression automatique (Projet)
                                    </small>
                                @else
                                    <!-- Mode lecture seule pour toutes les sous-actions (édition via slider) -->
                                    <input type="number" 
                                           class="form-control @error('editSousActionTauxAvancement') is-invalid @enderror" 
                                           id="edit_sous_action_taux" 
                                           wire:model="editSousActionTauxAvancement"
                                           min="0" 
                                           max="100" 
                                           step="0.1"
                                           readonly
                                           style="background-color: #f8f9fa; cursor: not-allowed;">
                                    @error('editSousActionTauxAvancement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Taux actuel (0-100%) - Modifiable via le slider dans la liste
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($editingSousAction && $editingSousAction->date_realisation)
                    <div class="mb-3">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Terminée le :</strong> {{ \Carbon\Carbon::parse($editingSousAction->date_realisation)->format('d/m/Y') }}
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEditSousActionModal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: {{ $pilier->getHierarchicalColor(5) }}; border-color: {{ $pilier->getHierarchicalColor(5) }};">
                        <i class="fas fa-save me-1"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
@endif

<!-- Modal de gestion des activités -->
@if($showActivitiesModal && $selectedSousActionForActivities)
<div class="modal fade show d-block activities-modal" tabindex="-1" aria-labelledby="activitiesModalLabel" aria-hidden="true" wire:ignore.self style="z-index: 1050;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
                            <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(5) }}; color: white;">
                    <h5 class="modal-title" id="activitiesModalLabel">
                        <i class="fas fa-tasks me-2"></i>
                        Gestion des Activités - {{ $pilier->code }}.{{ $selectedSousActionForActivities->action->objectifSpecifique->objectifStrategique->code }}.{{ $selectedSousActionForActivities->action->objectifSpecifique->code }}.{{ $selectedSousActionForActivities->action->code }}.{{ $selectedSousActionForActivities->code }}  {{ $selectedSousActionForActivities->libelle }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeActivitiesModal"></button>
                </div>
            
            <div class="modal-body">
                <!-- Informations du projet -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, {{ $pilier->getHierarchicalColor(5) }}, {{ $pilier->getHierarchicalColor(4) }});">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informations du Projet
                        </h6>
                    </div>
                    <div class="card-body project-info-compact">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Informations principales à gauche -->
                            <div class="d-flex align-items-center">
                                <div class="me-4">
                                    <i class="fas fa-tag fa-2x text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">{{ $selectedSousActionForActivities->libelle }}</h6>
                                    <div class="small text-muted">
                                        Code: {{ $pilier->code }}.{{ $selectedSousActionForActivities->action->objectifSpecifique->objectifStrategique->code }}.{{ $selectedSousActionForActivities->action->objectifSpecifique->code }}.{{ $selectedSousActionForActivities->action->code }}.{{ $selectedSousActionForActivities->code }} | 
                                        Type: <span class="badge bg-info">{{ ucfirst($selectedSousActionForActivities->type) }}</span>
                                        @if($selectedSousActionForActivities->date_echeance)
                                            | Échéance: <span class="badge bg-warning text-dark">{{ \Carbon\Carbon::parse($selectedSousActionForActivities->date_echeance)->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                    @if($selectedSousActionForActivities->description)
                                    <div class="mt-2 small text-muted">
                                        <i class="fas fa-align-left me-1"></i>{{ Str::limit($selectedSousActionForActivities->description, 80) }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Progression circulaire à droite -->
                            <div class="text-center">
                                <div class="progress-circle" style="--progress: {{ $selectedSousActionForActivities->taux_avancement }}%;">
                                    <svg width="60" height="60" viewBox="0 0 60 60">
                                        <circle cx="30" cy="30" r="25" fill="none" stroke="#e9ecef" stroke-width="4"/>
                                        <circle cx="30" cy="30" r="25" fill="none" stroke="{{ $pilier->getHierarchicalColor(5) }}" stroke-width="4" 
                                                stroke-dasharray="157" stroke-dashoffset="{{ 157 - (157 * $selectedSousActionForActivities->taux_avancement / 100) }}"/>
                                    </svg>
                                    <div class="progress-text">
                                        <span class="progress-percentage fw-bold">{{ number_format($selectedSousActionForActivities->taux_avancement, 1) }}%</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-success">
                                        <i class="fas fa-tasks me-1"></i>{{ $selectedSousActionForActivities->activities->count() }} activité(s)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de création d'activité -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-gradient-success text-white" style="background: linear-gradient(135deg, #28a745, #20c997); cursor: pointer;" wire:click="$toggle('showCreateActivityForm')" title="{{ $showCreateActivityForm ? 'Cliquer pour replier le formulaire' : 'Cliquer pour déplier le formulaire' }}">
                        <h6 class="mb-0 d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-plus me-2"></i>Nouvelle Activité</span>
                            <i class="fas {{ $showCreateActivityForm ? 'fa-chevron-up' : 'fa-chevron-down' }} transition-transform"></i>
                        </h6>
                    </div>
                    @if($showCreateActivityForm)
                    <div class="card-body bg-light">
                        <form wire:submit.prevent="createActivity">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="activity_titre" class="form-label">Titre <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('newActivity.titre') is-invalid @enderror" 
                                               id="activity_titre" 
                                               wire:model="newActivity.titre"
                                               placeholder="Titre de l'activité">
                                        @error('newActivity.titre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="activity_owner" class="form-label">Responsable <span class="text-danger">*</span></label>
                                        <select class="form-select @error('newActivity.owner_id') is-invalid @enderror" 
                                                id="activity_owner" 
                                                wire:model="newActivity.owner_id">
                                            <option value="">Sélectionner un responsable</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('newActivity.owner_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="activity_description" class="form-label">Description</label>
                                <textarea class="form-control @error('newActivity.description') is-invalid @enderror" 
                                          id="activity_description" 
                                          wire:model="newActivity.description"
                                          rows="3"
                                          placeholder="Description de l'activité"></textarea>
                                @error('newActivity.description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="activity_date_debut" class="form-label">
                                            <i class="fas fa-calendar-alt me-1"></i>Date de début <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('newActivity.date_debut') is-invalid @enderror" 
                                               id="activity_date_debut" 
                                               wire:model="newActivity.date_debut">
                                        @error('newActivity.date_debut')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Date de début de l'activité
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="activity_date_fin" class="form-label">
                                            <i class="fas fa-calendar-check me-1"></i>Date de fin <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('newActivity.date_fin') is-invalid @enderror" 
                                               id="activity_date_fin" 
                                               wire:model="newActivity.date_fin">
                                        @error('newActivity.date_fin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            @if($selectedSousActionForActivities->date_echeance)
                                                Ne doit pas dépasser l'échéance du projet : {{ \Carbon\Carbon::parse($selectedSousActionForActivities->date_echeance)->format('d/m/Y') }}
                                            @else
                                                Date de fin de l'activité
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="activity_statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                        <select class="form-select @error('newActivity.statut') is-invalid @enderror" 
                                                id="activity_statut" 
                                                wire:model="newActivity.statut">
                                            <option value="en_attente">En attente</option>
                                            <option value="en_cours">En cours</option>
                                            <option value="termine">Terminé</option>
                                            <option value="bloque">Bloqué</option>
                                        </select>
                                        @error('newActivity.statut')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="activity_progression" class="form-label">Progression initiale <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               class="form-control @error('newActivity.taux_avancement') is-invalid @enderror" 
                                               id="activity_progression" 
                                               wire:model="newActivity.taux_avancement"
                                               min="0" 
                                               max="100" 
                                               step="0.1">
                                        @error('newActivity.taux_avancement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success btn-lg" style="border-radius: 25px; padding: 12px 30px;">
                                    <i class="fas fa-plus me-2"></i>Créer l'Activité
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>

                <!-- Liste des activités existantes -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient-info text-white" style="background: linear-gradient(135deg, #17a2b8, #6f42c1);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i>Activités du Projet ({{ $selectedSousActionForActivities->activities->count() }})
                            </h6>
                            <div class="btn-group" role="group">
                                <button type="button" 
                                        class="btn btn-light btn-sm me-2" 
                                        wire:click="openActivityCalendarModal"
                                        title="Voir le calendrier des activités"
                                        style="border-radius: 20px; padding: 8px 16px;">
                                    <i class="fas fa-calendar-alt me-2"></i>📅 Calendrier
                                </button>
                                <button type="button" 
                                        class="btn btn-warning btn-sm" 
                                        wire:click="openGanttChartModal"
                                        title="Voir le diagramme de Gantt"
                                        style="border-radius: 20px; padding: 8px 16px;">
                                    <i class="fas fa-chart-bar me-2"></i>📊 Gantt
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($selectedSousActionForActivities->activities->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th><i class="fas fa-heading me-1"></i>Titre</th>
                                            <th><i class="fas fa-user me-1"></i>Responsable</th>
                                            <th><i class="fas fa-calendar me-1"></i>Dates</th>
                                            <th><i class="fas fa-info-circle me-1"></i>Statut</th>
                                            <th><i class="fas fa-percentage me-1"></i>Progression</th>
                                            <th><i class="fas fa-cogs me-1"></i>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($selectedSousActionForActivities->activities as $activity)
                                            <tr>
                                                <td>
                                                    <strong>{{ $activity->titre }}</strong>
                                                    @if($activity->description)
                                                        <br><small class="text-muted">{{ Str::limit($activity->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($activity->owner)
                                                        <span class="badge bg-secondary">{{ $activity->owner->name }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="small">
                                                        <div><strong>Début :</strong> {{ \Carbon\Carbon::parse($activity->date_debut)->format('d/m/Y') }}</div>
                                                        <div><strong>Fin :</strong> {{ \Carbon\Carbon::parse($activity->date_fin)->format('d/m/Y') }}</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color: {{ $activity->statut_color }}; color: white;">
                                                        {{ ucfirst(str_replace('_', ' ', $activity->statut)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <div class="progress me-2" style="width: 60px; height: 8px;">
                                                            <div class="progress-bar" 
                                                                 role="progressbar" 
                                                                 style="width: {{ $activity->taux_avancement }}%; background-color: {{ $pilier->getHierarchicalColor(5) }};"
                                                                 aria-valuenow="{{ $activity->taux_avancement }}" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <span class="small fw-bold">{{ number_format($activity->taux_avancement, 1) }}%</span>
                                                    </div>
                                                    <!-- Slider de progression modifiable -->
                                                    <div class="mt-1 text-center">
                                                        <input type="range" 
                                                               class="form-range form-range-sm" 
                                                               min="0" 
                                                               max="100" 
                                                               value="{{ $activity->taux_avancement }}"
                                                               wire:change="updateActivityProgress({{ $activity->id }}, $event.target.value)"
                                                               style="width: 80px; height: 20px;">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        @if($canEditActivity($activity))
                                                            <button type="button" 
                                                                    class="btn btn-outline-primary btn-sm" 
                                                                    wire:click="openEditActivityModal({{ $activity->id }})"
                                                                    title="Modifier l'activité"
                                                                    style="border-radius: 20px;">
                                                                <i class="fas fa-edit me-1"></i>
                                                            </button>
                                                        @endif
                                                        @if($canDeleteActivity($activity))
                                                            <button type="button" 
                                                                    class="btn btn-outline-danger btn-sm" 
                                                                    wire:click="deleteActivity({{ $activity->id }})"
                                                                    onclick="if(!confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')) return false;"
                                                                    title="Supprimer l'activité"
                                                                    style="border-radius: 20px;">
                                                                <i class="fas fa-trash me-1"></i>
                                                            </button>
                                                        @endif
                                                        @if(!$canEditActivity($activity) && !$canDeleteActivity($activity))
                                                            <span class="text-muted small">
                                                                <i class="fas fa-lock me-1"></i>Actions non autorisées
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-tasks fa-4x text-muted opacity-50"></i>
                                </div>
                                <h5 class="text-muted mb-2">Aucune activité créée</h5>
                                <p class="text-muted">Commencez par créer votre première activité pour ce projet</p>
                                <div class="mt-3">
                                    <span class="badge bg-light text-dark fs-6">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Les activités permettront de suivre la progression du projet
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
                            <div class="modal-footer bg-light border-top">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Projet créé le {{ \Carbon\Carbon::parse($selectedSousActionForActivities->created_at)->format('d/m/Y') }}
                        </div>
                        <button type="button" class="btn btn-secondary btn-lg" wire:click="closeActivitiesModal" style="border-radius: 25px; padding: 10px 25px;">
                            <i class="fas fa-times me-2"></i>Fermer
                        </button>
                    </div>
                </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
</div>
@endif

<!-- Modal d'édition d'activité -->
@if($editingActivity)
<div class="modal fade show d-block" tabindex="-1" aria-labelledby="editActivityModalLabel" aria-hidden="true" wire:ignore.self style="z-index: 1060;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editActivityModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    Modifier l'Activité : {{ $editingActivity->titre }}
                </h5>
                <button type="button" class="btn-close btn-close-white" wire:click="closeEditActivityModal"></button>
            </div>
            
            <div class="modal-body">
                <form wire:submit.prevent="updateActivity">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_activity_titre" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Titre <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('editActivityData.titre') is-invalid @enderror" 
                                       id="edit_activity_titre" 
                                       wire:model="editActivityData.titre"
                                       placeholder="Titre de l'activité">
                                @error('editActivityData.titre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_activity_owner" class="form-label">
                                    <i class="fas fa-user me-1"></i>Responsable <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('editActivityData.owner_id') is-invalid @enderror" 
                                        id="edit_activity_owner" 
                                        wire:model="editActivityData.owner_id">
                                    <option value="">Sélectionner un responsable</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('editActivityData.owner_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_activity_description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control @error('editActivityData.description') is-invalid @enderror" 
                                  id="edit_activity_description" 
                                  wire:model="editActivityData.description"
                                  rows="3"
                                  placeholder="Description de l'activité"></textarea>
                        @error('editActivityData.description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_activity_date_debut" class="form-label">
                                    <i class="fas fa-calendar-alt me-1"></i>Date de début <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('editActivityData.date_debut') is-invalid @enderror" 
                                       id="edit_activity_date_debut" 
                                       wire:model="editActivityData.date_debut">
                                @error('editActivityData.date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Date de début de l'activité
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_activity_date_fin" class="form-label">
                                    <i class="fas fa-calendar-check me-1"></i>Date de fin <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('editActivityData.date_fin') is-invalid @enderror" 
                                       id="edit_activity_date_fin" 
                                       wire:model="editActivityData.date_fin">
                                @error('editActivityData.date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    @if($selectedSousActionForActivities->date_echeance)
                                        Ne doit pas dépasser l'échéance du projet : {{ \Carbon\Carbon::parse($selectedSousActionForActivities->date_echeance)->format('d/m/Y') }}
                                    @else
                                        Date de fin de l'activité
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_activity_statut" class="form-label">
                                    <i class="fas fa-info-circle me-1"></i>Statut <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('editActivityData.statut') is-invalid @enderror" 
                                        id="edit_activity_statut" 
                                        wire:model="editActivityData.statut">
                                    <option value="en_attente">En attente</option>
                                    <option value="en_cours">En cours</option>
                                    <option value="termine">Terminé</option>
                                    <option value="bloque">Bloqué</option>
                                </select>
                                @error('editActivityData.statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_activity_progression" class="form-label">
                                    <i class="fas fa-percentage me-1"></i>Progression <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('editActivityData.taux_avancement') is-invalid @enderror" 
                                       id="edit_activity_progression" 
                                       wire:model="editActivityData.taux_avancement"
                                       min="0" 
                                       max="100" 
                                       step="0.1">
                                @error('editActivityData.taux_avancement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary me-2" wire:click="closeEditActivityModal">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 1050;"></div>
</div>
@endif

<!-- Modal du diagramme de Gantt -->
@if($showGanttChartModal && $selectedSousActionForActivities)
<div class="modal fade show d-block" tabindex="-1" aria-labelledby="ganttChartModalLabel" aria-hidden="true" wire:ignore.self style="z-index: 1060;">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header" style="background: #ffc107; color: #212529;">
                <h4 class="modal-title" id="ganttChartModalLabel">
                    <i class="fas fa-chart-bar me-3"></i>
                    📊 Diagramme de Gantt - {{ $pilier->code }}.{{ $selectedSousActionForActivities->action->objectifSpecifique->objectifStrategique->code }}.{{ $selectedSousActionForActivities->action->objectifSpecifique->code }}.{{ $selectedSousActionForActivities->action->code }}.{{ $selectedSousActionForActivities->code }} {{ $selectedSousActionForActivities->libelle }}
                </h4>
                <button type="button" class="btn-close" wire:click="closeGanttChartModal"></button>
            </div>
            
            <div class="modal-body">
                <!-- Navigation du diagramme de Gantt -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-outline-warning me-3" wire:click="previousGanttPeriod">
                                <i class="fas fa-chevron-left me-1"></i>Période précédente
                            </button>
                            <h3 class="mb-0 fw-bold text-warning">
                                {{ \Carbon\Carbon::createFromDate($ganttYear, $ganttMonth, 1)->locale('fr')->monthName }} {{ $ganttYear }}
                            </h3>
                            <button type="button" class="btn btn-outline-warning ms-3" wire:click="nextGanttPeriod">
                                Période suivante<i class="fas fa-chevron-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-outline-secondary" wire:click="goToCurrentGanttPeriod">
                            <i class="fas fa-calendar-day me-1"></i>Aujourd'hui
                        </button>
                    </div>
                </div>

                <!-- Légende des statuts -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="fas fa-palette me-2"></i>Légende des Statuts</h6>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator bg-warning me-2"></div>
                                        <span class="small">En attente</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator bg-primary me-2"></div>
                                        <span class="small">En cours</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator bg-success me-2"></div>
                                        <span class="small">Terminé</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator bg-danger me-2"></div>
                                        <span class="small">Bloqué</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Diagramme de Gantt -->
                <div class="gantt-container">
                    <div class="gantt-header">
                        <div class="gantt-activity-header">Activité</div>
                        <div class="gantt-timeline-header">Timeline</div>
                    </div>
                    
                    <div class="gantt-body">
                        @foreach($selectedSousActionForActivities->activities->sortBy('date_debut') as $activity)
                            <div class="gantt-row">
                                <div class="gantt-activity-info">
                                    <div class="activity-name">{{ $activity->titre }}</div>
                                    <div class="activity-dates">
                                        {{ \Carbon\Carbon::parse($activity->date_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($activity->date_fin)->format('d/m/Y') }}
                                    </div>
                                    <div class="activity-progress">
                                        <span class="badge bg-{{ $activity->statut_color }}">{{ ucfirst(str_replace('_', ' ', $activity->statut)) }}</span>
                                        <span class="badge bg-info">{{ number_format($activity->taux_avancement, 1) }}%</span>
                                    </div>
                                </div>
                                <div class="gantt-timeline">
                                    <div class="gantt-bar activity-{{ $activity->statut }}" 
                                         style="left: {{ $this->calculateGanttBarPosition($activity) }}%; width: {{ $this->calculateGanttBarWidth($activity) }}%;"
                                         title="{{ $activity->titre }} - {{ ucfirst(str_replace('_', ' ', $activity->statut)) }} - {{ number_format($activity->taux_avancement, 1) }}%"
                                         wire:click="openEditActivityModal({{ $activity->id }})">
                                        <div class="gantt-bar-content">
                                            <span class="gantt-bar-title">{{ Str::limit($activity->titre, 20) }}</span>
                                            <div class="gantt-bar-progress">
                                                <div class="progress" style="height: 4px;">
                                                    <div class="progress-bar" style="width: {{ $activity->taux_avancement }}%; background-color: white;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Cliquez sur une barre d'activité pour la modifier
                    </div>
                    <button type="button" class="btn btn-secondary btn-lg" wire:click="closeGanttChartModal" style="border-radius: 25px; padding: 10px 25px;">
                        <i class="fas fa-times me-2"></i>Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 1050;"></div>
</div>
@endif

<!-- Modal du calendrier des activités -->
@if($showActivityCalendarModal && $selectedSousActionForActivities)
<div class="modal fade show d-block" tabindex="-1" aria-labelledby="activityCalendarModalLabel" aria-hidden="true" wire:ignore.self style="z-index: 1060;">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(5) }}; color: white;">
                <h4 class="modal-title" id="activityCalendarModalLabel">
                    <i class="fas fa-calendar-alt me-3"></i>
                    📅 Calendrier des Activités - {{ $pilier->code }}.{{ $selectedSousActionForActivities->action->objectifSpecifique->objectifStrategique->code }}.{{ $selectedSousActionForActivities->action->objectifSpecifique->code }}.{{ $selectedSousActionForActivities->action->code }}.{{ $selectedSousActionForActivities->code }} {{ $selectedSousActionForActivities->libelle }}
                </h4>
                <button type="button" class="btn-close btn-close-white" wire:click="closeActivityCalendarModal"></button>
            </div>
            
            <div class="modal-body">
                <!-- Navigation du calendrier -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-outline-primary me-3" wire:click="previousMonth">
                                <i class="fas fa-chevron-left me-1"></i>Mois précédent
                            </button>
                            <h3 class="mb-0 fw-bold text-primary">
                                {{ \Carbon\Carbon::createFromDate($calendarYear, $calendarMonth, 1)->locale('fr')->monthName }} {{ $calendarYear }}
                            </h3>
                            <button type="button" class="btn btn-outline-primary ms-3" wire:click="nextMonth">
                                Mois suivant<i class="fas fa-chevron-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-outline-secondary" wire:click="goToCurrentMonth">
                            <i class="fas fa-calendar-day me-1"></i>Aujourd'hui
                        </button>
                    </div>
                </div>

                <!-- Légende des statuts -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="fas fa-palette me-2"></i>Légende des Statuts</h6>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator bg-warning me-2"></div>
                                        <span class="small">En attente</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator bg-primary me-2"></div>
                                        <span class="small">En cours</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator bg-success me-2"></div>
                                        <span class="small">Terminé</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator bg-danger me-2"></div>
                                        <span class="small">Bloqué</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendrier -->
                <div class="calendar-container">
                    <div class="calendar-header">
                        <div class="calendar-day-header">Lun</div>
                        <div class="calendar-day-header">Mar</div>
                        <div class="calendar-day-header">Mer</div>
                        <div class="calendar-day-header">Jeu</div>
                        <div class="calendar-day-header">Ven</div>
                        <div class="calendar-day-header">Sam</div>
                        <div class="calendar-day-header">Dim</div>
                    </div>
                    
                    <div class="calendar-grid">
                        @foreach($calendarDays as $day)
                            @php
                                $activityCount = $day['activities']->count();
                                $dayClasses = $day['isCurrentMonth'] ? '' : 'other-month';
                                $dayClasses .= $day['isToday'] ? ' today' : '';
                                
                                // Calculer la hauteur nécessaire basée sur le nombre max de positions
                                $maxPositions = collect($calendarDays)->map(function($d) { return count($d['activityPositions']); })->max() ?: 1;
                                $dayHeight = max(120, $maxPositions * 25 + 40); // 25px par position + 40px pour le header
                            @endphp
                            
                            <div class="calendar-day {{ $dayClasses }}" style="min-height: {{ $dayHeight }}px;">
                                <div class="day-number">{{ $day['day'] }}</div>
                                
                                @if($activityCount > 0)
                                    <!-- Indicateur de quantité d'activités -->
                                    @if($activityCount > 2)
                                        <div class="activity-count-indicator" title="{{ $activityCount }} activité(s)">
                                            {{ $activityCount > 9 ? '9+' : $activityCount }}
                                        </div>
                                    @endif
                                    
                                    <div class="activities-container">
                                        @foreach($day['activityPositions'] as $position => $activity)
                                            @if($activity)
                                                <div class="activity-item activity-{{ $activity->statut }}" 
                                                     style="grid-row: {{ $position + 1 }};"
                                                     title="{{ $activity->titre }} - {{ ucfirst(str_replace('_', ' ', $activity->statut)) }} - {{ number_format($activity->taux_avancement, 1) }}%"
                                                     wire:click="openEditActivityModal({{ $activity->id }})">
                                                    <div class="activity-title">{{ Str::limit($activity->titre, 12) }}</div>
                                                    <div class="activity-progress">
                                                        <div class="progress" style="height: 3px;">
                                                            <div class="progress-bar" style="width: {{ $activity->taux_avancement }}%; background-color: {{ $pilier->getHierarchicalColor(5) }};"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Slot vide pour maintenir l'alignement -->
                                                <div class="activity-slot-empty" style="grid-row: {{ $position + 1 }};"></div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Cliquez sur une activité pour la modifier
                    </div>
                    <button type="button" class="btn btn-secondary btn-lg" wire:click="closeActivityCalendarModal" style="border-radius: 25px; padding: 10px 25px;">
                        <i class="fas fa-times me-2"></i>Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 1050;"></div>
</div>
@endif
