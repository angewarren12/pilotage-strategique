<!-- Modal d'édition d'objectif stratégique -->
<div class="modal fade" id="edit-os-modal-v2" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    Modifier l'Objectif Stratégique
                </h5>
                <button type="button" class="btn-close" wire:click="closeEditOSModal"></button>
            </div>
            <div class="modal-body">
                @if($showEditOSModal && $editingOS)
                    <form wire:submit.prevent="updateObjectifStrategique" id="edit-os-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_os_code" class="form-label">Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('editingOS.code') is-invalid @enderror" 
                                           id="edit_os_code" wire:model="editingOS.code" maxlength="10" required>
                                    @error('editingOS.code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_os_owner" class="form-label">Propriétaire <span class="text-danger">*</span></label>
                                    <select class="form-select @error('editingOS.owner_id') is-invalid @enderror" 
                                            id="edit_os_owner" wire:model="editingOS.owner_id" required>
                                        <option value="">Sélectionner un utilisateur</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('editingOS.owner_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_os_libelle" class="form-label">Libellé <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('editingOS.libelle') is-invalid @enderror" 
                                   id="edit_os_libelle" wire:model="editingOS.libelle" maxlength="255" required>
                            @error('editingOS.libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_os_description" class="form-label">Description</label>
                            <textarea class="form-control @error('editingOS.description') is-invalid @enderror" 
                                      id="edit_os_description" wire:model="editingOS.description" rows="3"></textarea>
                            @error('editingOS.description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Note: Le taux d'avancement est calculé automatiquement -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note :</strong> Le taux d'avancement est calculé automatiquement en fonction des objectifs spécifiques et actions associés.
                        </div>
                    </form>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeEditOSModal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="submit" class="btn btn-warning" form="edit-os-form">
                    <i class="fas fa-save me-2"></i>Mettre à jour
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('show-edit-os-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('edit-os-modal-v2'));
        modal.show();
    });
    
    Livewire.on('hide-edit-os-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('edit-os-modal-v2'));
        if (modal) {
            modal.hide();
        }
    });
});
</script>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('show-edit-os-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('edit-os-modal-v2'));
        modal.show();
    });
    
    Livewire.on('hide-edit-os-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('edit-os-modal-v2'));
        if (modal) {
            modal.hide();
        }
    });
});
</script>
