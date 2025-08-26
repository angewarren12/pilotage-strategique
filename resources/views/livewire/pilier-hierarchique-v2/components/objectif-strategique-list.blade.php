<!-- Vue détail d'un Objectif Stratégique -->
<style>
    .objectif-strategique-detail-container {
        background: #f8f9fa;
        min-height: 100vh;
    }
    
    .objectif-strategique-info {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-radius: 8px;
        margin: 1rem;
    }
    
    .objectif-icon {
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transition: transform 0.3s ease;
    }
    
    .objectif-icon:hover {
        transform: scale(1.1);
    }
    
    .progress-ring {
        position: relative;
        display: inline-block;
    }
    
    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        width: 100%;
        font-size: 14px;
        font-weight: bold;
    }
    
    .progress-percentage {
        font-size: 16px;
        line-height: 1.2;
        color: #495057;
        display: block;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .progress-circle {
        transition: transform 0.3s ease;
    }
    
    .progress-circle:hover {
        transform: scale(1.05);
    }
    
    .statistics-container .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 8px;
    }
    
    .statistics-container .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
    
    .objectifs-specifiques-container {
        background: white;
        border-radius: 8px;
        margin: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .progress-compact {
        height: 6px;
        border-radius: 3px;
    }
    
    .badge-enhanced {
        padding: 6px 12px;
        font-size: 0.8rem;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    
    .btn-action:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .quick-actions-container {
        border-radius: 8px;
        margin: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .quick-actions-container .btn {
        border-radius: 20px;
        font-size: 0.85rem;
        padding: 6px 16px;
        transition: all 0.3s ease;
    }
    
    .quick-actions-container .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .display-6 {
        font-size: 2.5rem;
        font-weight: 700;
    }
    
    @media (max-width: 768px) {
        .objectif-strategique-info .row {
            flex-direction: column;
        }
        
        .objectif-strategique-info .col-md-4 {
            text-align: center !important;
            margin-top: 1rem;
        }
        
        .statistics-container .row > div {
            margin-bottom: 1rem;
        }
        
        .quick-actions-container .row {
            flex-direction: column;
        }
        
        .quick-actions-container .col-md-6 {
            margin-bottom: 1rem;
        }
    }
</style>

@if(!$selectedObjectifStrategique)
    <div class="alert alert-warning m-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Aucun objectif stratégique sélectionné</strong><br>
        Veuillez sélectionner un objectif stratégique pour afficher ses détails.
    </div>
@else
<div class="objectif-strategique-detail-container">
    <!-- Informations de l'objectif stratégique -->
    <div class="objectif-strategique-info bg-white p-4 border-bottom">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    <div class="objectif-icon me-3" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }}; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">{{ $selectedObjectifStrategique->libelle }}</h3>
                        <p class="text-muted mb-0">
                            <strong>Code:</strong> {{ $pilier->code }}.{{ $selectedObjectifStrategique->code }} | 
                            <strong>Propriétaire:</strong> {{ $selectedObjectifStrategique->owner ? $selectedObjectifStrategique->owner->name : 'Non assigné' }}
                        </p>
                    </div>
                </div>
                
                @if($selectedObjectifStrategique->description)
                    <p class="text-muted">{{ $selectedObjectifStrategique->description }}</p>
                @endif
            </div>
            
            <div class="col-md-4 text-end">
                <div class="progress-circle mb-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="progress-ring" style="width: 80px; height: 80px;">
                            <svg width="80" height="80" viewBox="0 0 80 80">
                                <circle cx="40" cy="40" r="35" stroke="#e9ecef" stroke-width="6" fill="none"/>
                                <circle cx="40" cy="40" r="35" stroke="{{ $pilier->getHierarchicalColor(2) }}" stroke-width="6" fill="none" 
                                        stroke-dasharray="{{ 2 * pi() * 35 }}" 
                                        stroke-dashoffset="{{ 2 * pi() * 35 * (1 - $selectedObjectifStrategique->taux_avancement / 100) }}"
                                        transform="rotate(-90 40 40)"/>
                            </svg>
                            <div class="progress-text">
                                <span class="fw-bold">{{ number_format($selectedObjectifStrategique->taux_avancement, 1) }}%</span>
                            </div>
                        </div>
                        <small class="text-muted mt-2">Progression globale</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte détail du Pilier parent -->
    <div class="statistics-container p-4 bg-light">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-12">
                <div class="card" style="border-left: 4px solid {{ $pilier->getHierarchicalColor(1) }};">
                    <div class="card-header" style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }};">
                        <h5 class="mb-0">
                            <i class="fas fa-layer-group me-2"></i>
                            Détails du Pilier Parent
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h6 class="card-title text-primary mb-2">{{ $pilier->libelle }}</h6>
                                <p class="card-text text-muted small mb-2">
                                    {{ $pilier->description ? Str::limit($pilier->description, 120) : 'Aucune description disponible' }}
                                </p>
                                <div class="d-flex align-items-center">
                                    <span class="text-muted me-2">Code :</span>
                                    <span class="badge bg-secondary">{{ $pilier->code }}</span>
                                </div>
                            </div>
                            <div class="col-md-5 text-center">
                                <div class="progress-ring" style="position: relative;">
                                    <svg width="100" height="100" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="45" fill="none" stroke="#e9ecef" stroke-width="6"/>
                                        <circle cx="50" cy="50" r="45" fill="none" stroke="{{ $pilier->getHierarchicalColor(1) }}" stroke-width="6" 
                                                stroke-dasharray="282.74" stroke-dashoffset="{{ 282.74 - (282.74 * $pilier->taux_avancement / 100) }}"/>
                                    </svg>
                                    <div class="progress-text" style="position: absolute !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; text-align: center !important; width: 100% !important;">
                                        <span class="progress-percentage" style="font-size: 18px !important; line-height: 1.2 !important; color: #495057 !important; display: block !important; text-shadow: 0 1px 2px rgba(0,0,0,0.1) !important; font-weight: bold !important;">{{ number_format($pilier->taux_avancement, 1) }}%</span>
                                        <small class="text-muted d-block" style="font-size: 12px !important;">Avancement</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des objectifs spécifiques -->
    <div class="objectifs-specifiques-container p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-list me-2" style="color: {{ $pilier->getHierarchicalColor(3) }};"></i>
                Objectifs Spécifiques
            </h4>
            
            @if($canCreateObjectifSpecifique)
                <button type="button" 
                        class="btn btn-primary" 
                        style="background: {{ $pilier->getHierarchicalColor(3) }}; border-color: {{ $pilier->getHierarchicalColor(3) }};"
                        wire:click="openCreateOSPModal">
                    <i class="fas fa-plus me-2"></i>Créer un Objectif Spécifique
                </button>
            @endif
        </div>

        @if($selectedObjectifStrategique->objectifsSpecifiques->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">
                        <tr>
                            <th style="width: 10%;">
                                <i class="fas fa-code me-1"></i>Code
                            </th>
                            <th style="width: 25%;">
                                <i class="fas fa-list me-1"></i>Libellé
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-percentage me-1"></i>Progression
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-user me-1"></i>Propriétaire
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-tasks me-1"></i>Actions
                            </th>
                            <th style="width: 20%;">
                                <i class="fas fa-cogs me-1"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedObjectifStrategique->objectifsSpecifiques as $objectifSpecifique)
                            <tr>
                                <td>
                                    <span class="badge fw-bold" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">
                                        {{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $objectifSpecifique->code }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-dark">{{ $objectifSpecifique->libelle }}</strong>
                                        @if($objectifSpecifique->description)
                                            <br><small class="text-muted">{{ Str::limit($objectifSpecifique->description, 80) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-start">
                                        <div class="d-flex justify-content-between align-items-center w-100 mb-1">
                                            <small class="text-muted">Progression</small>
                                            <span class="badge fw-bold" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">
                                                {{ number_format($objectifSpecifique->taux_avancement, 2) }}%
                                            </span>
                                        </div>
                                        <div class="progress mb-2 progress-compact" style="width: 100%; background: #e9ecef;">
                                            <div class="progress-bar" 
                                                 style="width: {{ $objectifSpecifique->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(3) }};"
                                                 role="progressbar" 
                                                 aria-valuenow="{{ $objectifSpecifique->taux_avancement }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($objectifSpecifique->owner)
                                        <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }}">
                                            <i class="fas fa-user me-1"></i>
                                            {{ Str::limit($objectifSpecifique->owner->name, 15) }}
                                        </span>
                                    @else
                                        <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }}">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }}">
                                        <i class="fas fa-tasks me-1"></i>
                                        {{ $objectifSpecifique->actions->count() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- Bouton Voir -->
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary btn-action" 
                                                title="Voir les détails"
                                                wire:click="naviguerVersObjectifSpecifique({{ $objectifSpecifique->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        @if($canEditObjectifSpecifique($objectifSpecifique))
                                            <!-- Bouton Modifier -->
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-warning btn-action" 
                                                    title="Modifier"
                                                    wire:click="setObjectifSpecifiqueToEdit({{ $objectifSpecifique->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <!-- Bouton Supprimer -->
                                            @if($canDeleteObjectifSpecifique($objectifSpecifique))
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger btn-action" 
                                                        title="Supprimer"
                                                        wire:click="deleteObjectifSpecifique({{ $objectifSpecifique->id }})"
                                                        onclick="if(!confirm('Êtes-vous sûr de vouloir supprimer cet objectif spécifique ?')) return false;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
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
                <i class="fas fa-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucun objectif spécifique</h5>
                <p class="text-muted">Commencez par créer votre premier objectif spécifique</p>
                
                @if($canCreateObjectifSpecifique)
                    <button type="button" 
                            class="btn btn-primary" 
                            style="background: {{ $pilier->getHierarchicalColor(3) }}; border-color: {{ $pilier->getHierarchicalColor(3) }};"
                            wire:click="openCreateOSPModal">
                        <i class="fas fa-plus me-2"></i>Créer le premier objectif
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Actions rapides -->
    <div class="quick-actions-container p-4 bg-light border-top">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">
                    <i class="fas fa-rocket me-2" style="color: {{ $pilier->getHierarchicalColor(2) }};"></i>
                    Actions Rapides
                </h5>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" 
                            class="btn btn-outline-secondary btn-sm"
                            wire:click="retourVersPilier">
                        <i class="fas fa-arrow-left me-1"></i>Retour au Pilier
                    </button>
                    
                    @if($canCreateObjectifSpecifique)
                        <button type="button" 
                                class="btn btn-outline-primary btn-sm"
                                wire:click="openCreateOSPModal">
                            <i class="fas fa-plus me-1"></i>Nouvel Objectif Spécifique
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="col-md-6">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line me-2" style="color: {{ $pilier->getHierarchicalColor(2) }};"></i>
                    Statistiques
                </h5>
                <div class="row text-center">
                    <div class="col-4">
                        <div class="small text-muted">Total OSP</div>
                        <div class="fw-bold text-primary">{{ $selectedObjectifStrategique->objectifsSpecifiques->count() }}</div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Total Actions</div>
                        <div class="fw-bold text-success">{{ $selectedObjectifStrategique->objectifsSpecifiques->flatMap->actions->count() }}</div>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Progression</div>
                        <div class="fw-bold text-info">{{ number_format($selectedObjectifStrategique->taux_avancement, 1) }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inclusion des modals -->
@include('livewire.pilier-hierarchique-v2.components.modals')
@endif
