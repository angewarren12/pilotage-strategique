<!-- Modal d'édition d'objectif spécifique -->
<div class="modal fade" id="edit-osp-modal-v2" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    Modifier l'Objectif Spécifique
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Debug Info -->
                <div class="alert alert-info mb-3">
                    <i class="fas fa-bug me-2"></i>
                    <strong>Debug:</strong> 
                    ID: <span id="debug-osp-id">{{ $objectifSpecifiqueToEdit ? $objectifSpecifiqueToEdit->id : 'N/A' }}</span>, 
                    Code: <span id="debug-osp-code">{{ $objectifSpecifiqueToEdit ? $objectifSpecifiqueToEdit->code : 'N/A' }}</span>, 
                    Libellé: <span id="debug-osp-libelle">{{ $objectifSpecifiqueToEdit ? $objectifSpecifiqueToEdit->libelle : 'N/A' }}</span>
                </div>

                <form wire:submit.prevent="updateObjectifSpecifique">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-osp-code" class="form-label">
                                    <i class="fas fa-code me-1"></i>Code *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="edit-osp-code" 
                                       wire:model="objectifSpecifiqueToEdit.code" 
                                       required>
                                @error('objectifSpecifiqueToEdit.code') 
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-osp-owner" class="form-label">
                                    <i class="fas fa-user me-1"></i>Propriétaire *
                                </label>
                                <select class="form-select" 
                                        id="edit-osp-owner" 
                                        wire:model="objectifSpecifiqueToEdit.owner_id" 
                                        required>
                                    <option value="">Sélectionner un propriétaire</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('objectifSpecifiqueToEdit.owner_id') 
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit-osp-libelle" class="form-label">
                            <i class="fas fa-bullseye me-1"></i>Libellé *
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="edit-osp-libelle" 
                               wire:model="objectifSpecifiqueToEdit.libelle" 
                               required>
                        @error('objectifSpecifiqueToEdit.libelle') 
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit-osp-description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control" 
                                  id="edit-osp-description" 
                                  wire:model="objectifSpecifiqueToEdit.description" 
                                  rows="3"></textarea>
                        @error('objectifSpecifiqueToEdit.description') 
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Information sur le taux d'avancement -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Taux d'avancement actuel: {{ $objectifSpecifiqueToEdit ? number_format($objectifSpecifiqueToEdit->taux_avancement, 1) : '0.0' }}%</strong><br>
                        Ce taux est calculé automatiquement en fonction des actions associées et ne peut pas être modifié manuellement.
                    </div>
                </form>
            </div>
            
            <!-- BOUTONS CORRECTEMENT POSITIONNÉS DANS LA MODAL -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="button" class="btn btn-warning" wire:click="updateObjectifSpecifique">
                    <i class="fas fa-save me-2"></i>Mettre à jour
                </button>
            </div>
        </div>
    </div>
</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-osp-code" class="form-label">
                                    <i class="fas fa-code me-1"></i>Code *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="edit-osp-code" 
                                       wire:model="objectifSpecifiqueToEdit.code" 
                                       required>
                                @error('objectifSpecifiqueToEdit.code') 
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-osp-owner" class="form-label">
                                    <i class="fas fa-user me-1"></i>Propriétaire *
                                </label>
                                <select class="form-select" 
                                        id="edit-osp-owner" 
                                        wire:model="objectifSpecifiqueToEdit.owner_id" 
                                        required>
                                    <option value="">Sélectionner un propriétaire</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('objectifSpecifiqueToEdit.owner_id') 
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit-osp-libelle" class="form-label">
                            <i class="fas fa-bullseye me-1"></i>Libellé *
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="edit-osp-libelle" 
                               wire:model="objectifSpecifiqueToEdit.libelle" 
                               required>
                        @error('objectifSpecifiqueToEdit.libelle') 
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit-osp-description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control" 
                                  id="edit-osp-description" 
                                  wire:model="objectifSpecifiqueToEdit.description" 
                                  rows="3"></textarea>
                        @error('objectifSpecifiqueToEdit.description') 
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Information sur le taux d'avancement -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Taux d'avancement actuel: {{ $objectifSpecifiqueToEdit ? number_format($objectifSpecifiqueToEdit->taux_avancement, 1) : '0.0' }}%</strong><br>
                        Ce taux est calculé automatiquement en fonction des actions associées et ne peut pas être modifié manuellement.
                    </div>
                </form>
            </div>
            
            <!-- BOUTONS CORRECTEMENT POSITIONNÉS DANS LA MODAL -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="button" class="btn btn-warning" wire:click="updateObjectifSpecifique">
                    <i class="fas fa-save me-2"></i>Mettre à jour
                </button>
            </div>
        </div>
    </div>
</div>
