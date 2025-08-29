@extends('layouts.app')

@section('title', 'Créer un Pilier')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus me-2"></i>
                Créer un Pilier
            </h1>
        </div>
        <a href="{{ route('piliers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('piliers.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Code</label>
                        <input type="text" class="form-control" id="code" name="code" required>
                    </div>
                    <!-- Champ owner_id supprimé car les piliers n'ont pas d'owner -->
                </div>
                <div class="mb-3">
                    <label for="libelle" class="form-label">Libellé</label>
                    <input type="text" class="form-control" id="libelle" name="libelle" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                
                <!-- 🎨 Sélecteur de couleur -->
                <div class="mb-4">
                    <label class="form-label">Couleur du Pilier</label>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Cette couleur sera utilisée pour identifier visuellement ce pilier et tous ses éléments hiérarchiques
                    </p>
                    <div class="color-palette">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <div class="color-option" data-color="#007bff">
                                    <div class="color-preview" style="background: linear-gradient(135deg, #00AE9E 0%, #008F82 100%);">
                                        <span class="color-name">Bleu</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="color-option" data-color="#28a745">
                                    <div class="color-preview" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);">
                                        <span class="color-name">Vert</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="color-option" data-color="#dc3545">
                                    <div class="color-preview" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                                        <span class="color-name">Rouge</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="color-option" data-color="#ffc107">
                                    <div class="color-preview" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);">
                                        <span class="color-name">Jaune</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="color-option" data-color="#6f42c1">
                                    <div class="color-preview" style="background: linear-gradient(135deg, #6f42c1 0%, #5a2d91 100%);">
                                        <span class="color-name">Violet</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="color-option" data-color="#fd7e14">
                                    <div class="color-preview" style="background: linear-gradient(135deg, #fd7e14 0%, #e55a00 100%);">
                                        <span class="color-name">Orange</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="color-option" data-color="#20c997">
                                    <div class="color-preview" style="background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);">
                                        <span class="color-name">Turquoise</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="color-option" data-color="#e83e8c">
                                    <div class="color-preview" style="background: linear-gradient(135deg, #e83e8c 0%, #d63384 100%);">
                                        <span class="color-name">Rose</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="color" id="selected_color" value="#007bff">
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('piliers.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Créer le pilier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.color-palette {
    margin-top: 10px;
}

.color-option {
    cursor: pointer;
    transition: transform 0.2s ease;
}

.color-option:hover {
    transform: scale(1.05);
}

.color-option.selected {
    transform: scale(1.1);
}

.color-preview {
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    border: 3px solid transparent;
    transition: all 0.2s ease;
}

.color-option.selected .color-preview {
    border-color: #333;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.color-name {
    font-size: 14px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorOptions = document.querySelectorAll('.color-option');
    const selectedColorInput = document.getElementById('selected_color');
    
    // Sélectionner la première couleur par défaut
    colorOptions[0].classList.add('selected');
    
    colorOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Retirer la sélection précédente
            colorOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Ajouter la sélection à l'option cliquée
            this.classList.add('selected');
            
            // Mettre à jour la valeur cachée
            const color = this.getAttribute('data-color');
            selectedColorInput.value = color;
        });
    });
});
</script>
@endsection 