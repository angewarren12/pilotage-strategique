<!-- Modal de création d'objectif stratégique -->
<div class="modal fade" id="create-os-modal-v2" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>
                    Créer un Objectif Stratégique
                </h5>
                <button type="button" class="btn-close btn-close-white" wire:click="closeCreateOSModal"></button>
            </div>
            <div class="modal-body">
                @if($showCreateOSModal)
                    <form wire:submit.prevent="createObjectifStrategique" id="create-os-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="os_code" class="form-label">Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('newOS.code') is-invalid @enderror" 
                                           id="os_code" wire:model="newOS.code" maxlength="10" required>
                                    @error('newOS.code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="os_owner" class="form-label">Propriétaire <span class="text-danger">*</span></label>
                                    <select class="form-select @error('newOS.owner_id') is-invalid @enderror" 
                                            id="os_owner" wire:model="newOS.owner_id" required>
                                        <option value="">Sélectionner un utilisateur</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('newOS.owner_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="os_libelle" class="form-label">Libellé <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('newOS.libelle') is-invalid @enderror" 
                                   id="os_libelle" wire:model="newOS.libelle" maxlength="255" required>
                            @error('newOS.libelle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="os_description" class="form-label">Description</label>
                            <textarea class="form-control @error('newOS.description') is-invalid @enderror" 
                                      id="os_description" wire:model="newOS.description" rows="3"></textarea>
                            @error('newOS.description')
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
                <button type="button" class="btn btn-secondary" wire:click="closeCreateOSModal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="submit" class="btn btn-primary" form="create-os-form">
                    <i class="fas fa-save me-2"></i>Créer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('show-create-os-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('create-os-modal-v2'));
        modal.show();
    });
    
    Livewire.on('hide-create-os-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('create-os-modal-v2'));
        if (modal) {
            modal.hide();
        }
    });
});
</script>
