<!-- Modals pour les Objectifs Stratégiques -->

<!-- Modal de création d'Objectif Stratégique -->
@if($showCreateOSModal)
<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog">
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
<div class="modal-backdrop fade show"></div>
@endif

<!-- Modal d'édition d'Objectif Stratégique -->
@if($showEditOSModal && $editingObjectifStrategique)
<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog">
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
<div class="modal-backdrop fade show"></div>
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
<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog">
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
<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog">
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
<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog">
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
                                <label for="sous_action_date_echeance" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Date d'échéance <span class="text-danger">*</span>
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
<div class="modal fade show" style="display: block; z-index: 1050;" tabindex="-1" role="dialog">
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
                    
                    <!-- Debug Info -->
                    @if($editingSousAction)
                    <div class="alert alert-info mb-3">
                        <strong>Debug :</strong> 
                        ID: {{ $editingSousAction->id }}, 
                        Code: {{ $editingSousAction->code }}, 
                        Libellé: {{ $editingSousAction->libelle }}, 
                        Propriétaire: {{ $editingSousAction->owner_id }},
                        Taux: {{ $editingSousAction->taux_avancement }}%
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
                                <label for="edit_sous_action_date_echeance" class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Date d'échéance <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('editSousActionDateEcheance') is-invalid @enderror" 
                                       id="edit_sous_action_date_echeance" 
                                       wire:model="editSousActionDateEcheance">
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
                                    Taux actuel (0-100%)
                                </small>
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
