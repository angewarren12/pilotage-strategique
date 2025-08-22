<!-- Vue liste des sous-actions -->
<div class="sous-action-list-container p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-list-check me-2" style="color: {{ $pilier->getHierarchicalColor(5) }};"></i>
            Détails de la Sous-action : {{ $selectedSousAction->libelle }}
        </h4>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Composant en cours de développement</strong><br>
        Cette vue affichera bientôt les détails complets de la sous-action avec toutes les fonctionnalités.
    </div>
</div>
