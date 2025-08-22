<!-- Composant de test -->
<div class="test-component p-4">
    <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Composant de test fonctionnel !</strong><br>
        Livewire est correctement configuré.
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Informations du composant
            </h5>
        </div>
        <div class="card-body">
            <p><strong>Pilier ID:</strong> {{ $pilier->id ?? 'Non défini' }}</p>
            <p><strong>Pilier Code:</strong> {{ $pilier->code ?? 'Non défini' }}</p>
            <p><strong>Pilier Libellé:</strong> {{ $pilier->libelle ?? 'Non défini' }}</p>
            <p><strong>Vue actuelle:</strong> {{ $currentView ?? 'Non définie' }}</p>
        </div>
    </div>
</div>
