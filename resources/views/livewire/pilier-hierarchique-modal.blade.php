@php
use Illuminate\Support\Facades\Auth;
@endphp

<div>
    @if($showModal)
    <div class="modal fade show" style="display: block; z-index: 9999;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <!-- Header du Modal -->
                <div class="modal-header text-white" style="background: {{ $pilier->getHierarchicalColor(0) }};">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-sitemap fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0">
                                <strong>Vue Hiérarchique - Détail du Pilier : {{ $pilier->libelle ?? 'Pilier' }}</strong>
                            </h5>
                            <small class="text-white-75">Navigation et gestion de la hiérarchie complète</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>

                <!-- Contenu du Modal -->
                <div class="modal-body p-0">
                    @if($isLoading)
                        <div class="text-center py-5">
                            <div class="spinner-border" style="color: {{ $pilier->getHierarchicalColor(0) }};" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            <p class="mt-2">Chargement de la hiérarchie...</p>
                        </div>
                    @else
                        <!-- Breadcrumb Navigation avec Codification -->
                        <div class="p-3 bg-light border-bottom">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <button type="button" class="btn btn-link p-0" wire:click="retourListeObjectifs">
                                            <i class="fas fa-home me-1"></i>{{ $pilier->code ?? 'P' }}
                                        </button>
                                    </li>
                                    @foreach($breadcrumb as $item)
                                        <li class="breadcrumb-item">
                                            <button type="button" class="btn btn-link p-0" 
                                                    wire:click="naviguerVers{{ ucfirst($item['type']) }}({{ $item['id'] }})">
                                                {{ $item['name'] }}
                                            </button>
                                        </li>
                                    @endforeach
                                </ol>
                            </nav>
                        </div>

                        <!-- Barre d'outils -->
                        <div class="p-3 bg-light border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               placeholder="Rechercher dans la hiérarchie..."
                                               wire:model.live="searchTerm">
                                    </div>
                                </div>
                                                                    <div class="col-md-6 text-end">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleFullscreen()" title="Plein écran">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetZoom()" title="Zoom par défaut">
                                                <i class="fas fa-expand-arrows-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                            </div>
                        </div>

                        <!-- Titre dynamique selon la vue -->
                        @if($currentView === 'pilier')
                            <div class="view-title mb-4">
                                <h3 class="text-dark mb-2">
                                    <i class="fas fa-layer-group me-2" style="color: {{ $pilier->getHierarchicalColor(0) }};"></i>
                                    Détail du Pilier {{ $pilier->code }} - {{ $pilier->libelle }}
                                </h3>
                                <p class="text-muted mb-0">Vue générale et gestion des objectifs stratégiques</p>
                                
                                <!-- Informations clés : Owner et Pourcentage -->
                                <div class="view-info mt-3">
                                    <div class="row">
                                        
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <i class="fas fa-percentage me-2" style="color: {{ $pilier->getHierarchicalColor(0) }};"></i>
                                                <strong>Progression :</strong> 
                                                <span class="badge fs-6" style="background: {{ $pilier->getHierarchicalColor(0) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(0)) }}; ">{{ number_format($pilier->taux_avancement, 1) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($currentView === 'objectifStrategique')
                            <div class="view-title mb-4">
                                <h3 class="text-dark mb-2">
                                    <i class="fas fa-bullseye me-2" style="color: {{ $pilier->getHierarchicalColor(1) }};"></i>
                                    Détail de l'Objectif Stratégique {{ $pilier->code }}.{{ $selectedObjectifStrategique->code }} - {{ $selectedObjectifStrategique->libelle }}
                                </h3>
                                <p class="text-muted mb-0">Gestion des objectifs Spécifiques et actions</p>
                                
                                <!-- Informations clés : Owner et Pourcentage -->
                                <div class="view-info mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <i class="fas fa-user me-2" style="color: {{ $pilier->getHierarchicalColor(1) }};"></i>
                                                <strong>Owner :</strong> 
                                                <span class="badge" style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }};">{{ $selectedObjectifStrategique->owner ? $selectedObjectifStrategique->owner->name : 'Non assigné' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <i class="fas fa-percentage me-2" style="color: {{ $pilier->getHierarchicalColor(1) }};"></i>
                                                <strong>Progression :</strong> 
                                                <span class="badge fs-6" style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }};">{{ number_format($selectedObjectifStrategique->taux_avancement, 1) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($currentView === 'objectifSpecifique')
                            <div class="view-title mb-4">
                                <h3 class="text-dark mb-2">
                                    <i class="fas fa-bullseye me-2" style="color: {{ $pilier->getHierarchicalColor(2) }};"></i>
                                    Détail de l'Objectif Spécifique {{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }} - {{ $selectedObjectifSpecifique->libelle }}
                                </h3>
                                <p class="text-muted mb-0">Gestion des actions et sous-actions</p>
                                
                                <!-- Informations clés : Owner et Pourcentage -->
                                <div class="view-info mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <i class="fas fa-user me-2" style="color: {{ $pilier->getHierarchicalColor(2) }};"></i>
                                                <strong>Owner :</strong> 
                                                <span class="badge" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">{{ $selectedObjectifSpecifique->owner ? $selectedObjectifSpecifique->owner->name : 'Non assigné' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <i class="fas fa-percentage me-2" style="color: {{ $pilier->getHierarchicalColor(2) }};"></i>
                                                <strong>Progression :</strong> 
                                                <span class="badge fs-6" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">{{ number_format($selectedObjectifSpecifique->taux_avancement, 1) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($currentView === 'action')
                            <div class="view-title mb-4">
                                <h3 class="text-dark mb-2">
                                    <i class="fas fa-tasks me-2" style="color: {{ $pilier->getHierarchicalColor(3) }};"></i>
                                    Détail de l'Action {{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }}.{{ $selectedAction->code }} - {{ $selectedAction->libelle }}
                                </h3>
                                <p class="text-muted mb-0">Gestion des sous-actions et suivi de progression</p>
                                
                                <!-- Informations clés : Owner et Pourcentage -->
                                <div class="view-info mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <i class="fas fa-user me-2" style="color: {{ $pilier->getHierarchicalColor(3) }};"></i>
                                                <strong>Owner :</strong> 
                                                <span class="badge" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">{{ $selectedAction->owner ? $selectedAction->owner->name : 'Non assigné' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <i class="fas fa-percentage me-2" style="color: {{ $pilier->getHierarchicalColor(3) }};"></i>
                                                <strong>Progression :</strong> 
                                                <span class="badge fs-6" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">{{ number_format($selectedAction->taux_avancement, 1) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($currentView === 'sousAction')
                            <div class="view-title mb-4">
                                <h3 class="text-dark mb-2">
                                    <i class="fas fa-list-check me-2" style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                                    Détail de la Sous-Action {{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }}.{{ $selectedAction->code }}.{{ $selectedSousAction->code }} - {{ $selectedSousAction->libelle }}
                                </h3>
                                <p class="text-muted mb-0">Suivi Détaillé et gestion de la progression</p>
                                
                                <!-- Informations clés : Owner et Pourcentage -->
                                <div class="view-info mt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <i class="fas fa-user me-2" style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                                                <strong>Owner :</strong> 
                                                <span class="badge" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">{{ $selectedSousAction->owner ? $selectedSousAction->owner->name : 'Non assigné' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <i class="fas fa-percentage me-2" style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                                                <strong>Progression :</strong> 
                                                <span class="badge fs-6" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">{{ number_format($selectedSousAction->taux_avancement, 1) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <style>
                            .view-title {
                                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                                border-radius: 12px;
                                padding: 1.5rem;
                                border-left: 4px solid #007bff;
                                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                            }
                            
                            .view-title h3 {
                                font-weight: 600;
                                margin-bottom: 0.5rem;
                            }
                            
                            .view-title p {
                                font-size: 0.95rem;
                                margin-bottom: 0;
                            }
                            
                            .view-title i {
                                font-size: 1.2em;
                            }
                            
                            .view-info {
                                border-top: 1px solid #dee2e6;
                                padding-top: 1rem;
                            }
                            
                            .info-item {
                                display: flex;
                                align-items: center;
                                margin-bottom: 0.5rem;
                            }
                            
                            .info-item i {
                                font-size: 1.1em;
                                width: 20px;
                            }
                            
                            .info-item strong {
                                margin-right: 0.5rem;
                                color: #495057;
                            }
                            
                            .info-item .badge {
                                font-size: 0.9rem;
                                padding: 0.5rem 0.75rem;
                            }
                            
                            .info-item .badge.bg-primary {
                                background-color: #007bff !important;
                            }
                            
                            .info-item .badge.bg-success {
                                background-color: #28a745 !important;
                            }

                            /* ======================================== */
                            /* STYLES POUR LES ACTIVITéS */
                            /* ======================================== */
                            
                            .activities-section {
                                border-top: 1px solid #dee2e6;
                                padding-top: 1.5rem;
                            }
                            
                            .activity-card {
                                background: white;
                                transition: all 0.3s ease;
                                border-left-width: 4px !important;
                            }
                            
                            .activity-card:hover {
                                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                                transform: translateY(-2px);
                            }
                            
                            .activity-actions {
                                display: flex;
                                gap: 0.5rem;
                            }
                            
                            .activity-info small {
                                font-size: 0.8rem;
                            }
                            
                            .activity-status .badge {
                                font-size: 0.75rem;
                                padding: 0.25rem 0.5rem;
                            }
                            
                            .activity-progress .progress {
                                background: #e9ecef;
                                border-radius: 4px;
                            }
                            
                            .activity-progress .form-range {
                            
                            /* ======================================== */
                            /* STYLES POUR L'HARMONISATION DES COULEURS */
                            /* ======================================== */
                            
                            .hierarchy-card {
                                transition: all 0.3s ease;
                                border-radius: 12px;
                                overflow: hidden;
                                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                            }
                            
                            .hierarchy-card:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 8px 15px rgba(0,0,0,0.15);
                            }
                            
                            .hierarchy-header {
                                padding: 1rem;
                                font-weight: 600;
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                            }
                            
                            .hierarchy-icon {
                                font-size: 1.2rem;
                                margin-right: 0.5rem;
                            }
                            
                            .hierarchy-info h6 {
                                margin: 0;
                                font-size: 1rem;
                            }
                            
                            .hierarchy-info small {
                                opacity: 0.8;
                            }
                            
                            .hierarchy-body {
                                padding: 1rem;
                                background: white;
                            }
                            
                            .hierarchy-stats {
                                display: flex;
                                justify-content: space-around;
                                margin-bottom: 1rem;
                            }
                            
                            .stat-item {
                                text-align: center;
                            }
                            
                            .stat-value {
                                font-size: 1.5rem;
                                font-weight: 700;
                                margin-bottom: 0.25rem;
                            }
                            
                            .stat-label {
                                font-size: 0.8rem;
                                color: #6c757d;
                                text-transform: uppercase;
                                letter-spacing: 0.5px;
                            }
                            
                            .hierarchy-progress {
                                margin-top: 1rem;
                            }
                            
                            .hierarchy-progress .progress {
                                border-radius: 10px;
                                overflow: hidden;
                            }
                            
                            /* Styles pour les badges harmonisés */
                            .badge {
                                transition: all 0.3s ease;
                                border-radius: 6px;
                                font-weight: 500;
                            }
                            
                            .badge:hover {
                                transform: scale(1.05);
                            }
                            
                            /* Styles pour les boutons harmonisés */
                            .btn-outline-primary:hover,
                            .btn-outline-warning:hover,
                            .btn-outline-danger:hover {
                                background-color: var(--pilier-color, #007bff);
                                border-color: var(--pilier-color, #007bff);
                                color: white;
                            }
                            
                            /* Animation pour les barres de progression */
                            .progress-bar {
                                transition: width 0.8s ease-in-out;
                            }
                            
                            /* Styles pour les toasts harmonisés */
                            .toast {
                                border-radius: 12px;
                                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                                border: none;
                            }
                            
                            .toast-header {
                                border-radius: 12px 12px 0 0;
                                font-weight: 600;
                            }
                                background: transparent;
                            }
                            
                            .activity-progress .form-range::-webkit-slider-thumb {
                                background: #007bff;
                                border: 2px solid #fff;
                                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                            }
                            
                            .activity-progress .form-range::-moz-range-thumb {
                                background: #007bff;
                                border: 2px solid #fff;
                                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                            }
                        </style>

                        <!-- Contenu principal -->
                        <div class="hierarchique-content" id="hierarchiqueContent">
                            <!-- Vue Pilier -->
                            @if($currentView === 'pilier')
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h4 class="mb-0">
                                                    <i class="fas fa-layer-group me-2" style="color: {{ $pilier->getHierarchicalColor(1) }};"></i>
                                                    Objectifs Stratégiques
                                                </h4>
                                                
                                                <!-- BADGE DE TEST VISIBLE -->
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        showCreateObjectifForm: {{ $showCreateObjectifForm ? 'TRUE' : 'FALSE' }}
                                                    </span>
                                                    
                                                                                                    <!-- BOUTON DE TEST -->
                                                <button type="button" class="btn btn-sm btn-warning" wire:click="testModalDisplay" title="Test Modal">
                                                    <i class="fas fa-flask me-1"></i>Test
                                                </button>
                                                
                                                <!-- BOUTON BOOTSTRAP PUR (TEST) -->
                                                <button type="button" class="btn btn-sm btn-info" onclick="openBootstrapModal()" title="Test Bootstrap">
                                                    <i class="fas fa-rocket me-1"></i>Bootstrap Test
                                                </button>
                                                
                                                @if(Auth::user()->isAdminGeneral())
                                                <button type="button" class="btn btn-sm" style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }}; border-color: {{ $pilier->getHierarchicalColor(1) }};" onclick="openNewOSModal()">
                                                    <i class="fas fa-plus me-2"></i>Créer un Objectif Stratégique
                                                </button>
                                                @endif
                                                </div>
                                            </div>
                                            
                                            @if($pilier->objectifsStrategiques->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }};">
                                                            <tr>
                                                                <th>Code</th>
                                                                <th>Libellé</th>
                                                                <th>Description</th>
                                                                <th>Owner</th>
                                                                <th>Progression</th>
                                                                <th>Objectifs Spécifiques</th>
                                                                <th class="text-center">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                    @foreach($pilier->objectifsStrategiques as $objectifStrategique)
                                                            <tr>
                                                                                                                                 <td>
                                                                     <span class="badge fs-6" style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }};">{{ $pilier->code }}.{{ $objectifStrategique->code }}</span>
                                                                 </td>
                                                                <td>
                                                                    <strong class="text-dark">{{ $objectifStrategique->libelle }}</strong>
                                                                </td>
                                                                <td>
                                                                    @if($objectifStrategique->description)
                                                                        <span class="text-muted">{{ Str::limit($objectifStrategique->description, 50) }}</span>
                                                                    @else
                                                                        <span class="text-muted fst-italic">Aucune description</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($objectifStrategique->owner)
                                                                        <span class="badge" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                                                                            <i class="fas fa-user me-1"></i>
                                                                            {{ $objectifStrategique->owner->name }}
                                                                        </span>
                                                                    @else
                                        <span class="badge" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">Non assigné</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="progress me-2" style="width: 100px; height: 8px;">
                                                                        <div class="progress-bar" 
                                                                                 role="progressbar" 
                                                                                 style="width: {{ $objectifStrategique->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(1) }};"
                                                                                 aria-valuenow="{{ $objectifStrategique->taux_avancement }}" 
                                                                                 aria-valuemin="0" 
                                                                                 aria-valuemax="100">
                                                                    </div>
                                                                        </div>
                                                                        <small class="text-muted">{{ number_format($objectifStrategique->taux_avancement, 1) }}%</small>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="badge" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                                                                        {{ $objectifStrategique->objectifsSpecifiques->count() }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="btn-group" role="group">
                                                                        <!-- Bouton Voir Détails - accessible Ã  tous -->
                                                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                                                wire:click="naviguerVersObjectifStrategique({{ $objectifStrategique->id }})"
                                                                                title="Voir Détails"
                                                                                style="border-color: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getHierarchicalColor(1) }};">
                                                                            <i class="fas fa-eye"></i>
                                                                        </button>
                                                                        
                                                                        <!-- Boutons Modifier/Supprimer - seulement pour l'owner ET l'admin général -->
                                                                        @if(Auth::user()->isAdminGeneral() || (Auth::user()->id == $objectifStrategique->owner_id))
                                                                                                                                <button type="button" 
                                                                wire:click="setActionToEditObjectifStrategique({{ $objectifStrategique->id }})"
                                                                class="btn btn-outline-warning btn-sm" 
                                                                title="Modifier"
                                                                style="border-color: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getHierarchicalColor(1) }};">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                                        <button type="button" 
                                                                                class="btn btn-outline-danger btn-sm" 
                                                                                wire:click="deleteObjectifStrategique({{ $objectifStrategique->id }})"
                                                                                onclick="if(!confirm('Êtes-vous sûr de vouloir supprimer cet Objectif Stratégique?')) return false;"
                                                                                title="Supprimer"
                                                                                style="border-color: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getHierarchicalColor(1) }};">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
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
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Aucun Objectif Stratégique
                                                    </h5>
                                                    <p class="text-muted">Commencez par créer votre premier Objectif Stratégique
                                                    </p>
                                                    @if(Auth::user()->isAdminGeneral())
                                                    <button type="button" class="btn btn-primary" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }}; border-color: {{ $pilier->getHierarchicalColor(2) }};" wire:click="showCreateObjectifForm">
                                                        <i class="fas fa-plus me-2"></i>Créer le premier objectif
                                                    </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <!-- ðŸŽ¯ STATISTIQUES HIéRARCHIQUES DU PILIER -->
                                            <div class="hierarchy-stats-container">
                                                <!-- PILIER (NIVEAU RACINE) -->
                                                <div class="hierarchy-card root-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(0) }};">
                                                    <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(0) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(0)) }};">
                                                        <div class="hierarchy-icon">
                                                            <i class="fas fa-layer-group"></i>
                                                </div>
                                                        <div class="hierarchy-info">
                                                            <h6 class="mb-0">Pilier</h6>
                                                            <small>{{ $pilier->code }} - {{ $pilier->libelle }}</small>
                                                        </div>
                                                        <div class="hierarchy-arrow">
                                                            <i class="fas fa-chevron-down"></i>
                                                        </div>
                                                    </div>
                                                    <div class="hierarchy-body">
                                                        <div class="hierarchy-stats">
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: {{ $pilier->getHierarchicalColor(0) }};">{{ number_format($pilier->taux_avancement, 1) }}%</div>
                                                                <div class="stat-label">Progression</div>
                                                        </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: {{ $pilier->getHierarchicalColor(0) }};">{{ $pilier->objectifsStrategiques->count() }}</div>
                                                                <div class="stat-label">OS</div>
                                                        </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: {{ $pilier->getHierarchicalColor(0) }};">{{ $pilier->objectifsStrategiques->sum(function($os) { return $os->objectifsSpecifiques->count(); }) }}</div>
                                                                <div class="stat-label">OSP</div>
                                                    </div>
                                                        </div>
                                                        <div class="hierarchy-progress">
                                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                                <div class="progress-bar" style="width: {{ $pilier->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(0) }};"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($currentView === 'objectifStrategique')
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h4 class="mb-0">
                                                    <i class="fas fa-bullseye me-2" style="color: {{ $pilier->getHierarchicalColor(2) }};"></i>
                                                    Objectifs Spécifiques
                                                </h4>
                                                @if(Auth::user()->isAdminGeneral() || (Auth::user()->id == $selectedObjectifStrategique->owner_id))
                                                <button type="button" class="btn btn-sm" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }}; border-color: {{ $pilier->getHierarchicalColor(2) }};" wire:click="openCreateOSPModal">
                                                    <i class="fas fa-plus me-2"></i>Créer un Objectif Spécifique
                                                </button>
                                                @endif
                                            </div>
                                            
                                            @if($selectedObjectifStrategique->objectifsSpecifiques->count() > 0)
                                                 <div class="table-responsive">
                                                     <table class="table table-hover">
                                                         <thead style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                                                             <tr>
                                                                 <th>Code</th>
                                                                 <th>Libellé</th>
                                                                 <th>Description</th>
                                                                 <th>Owner</th>
                                                                 <th>Progression</th>
                                                                 <th>Actions</th>
                                                                 <th class="text-center">Actions</th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                    @foreach($selectedObjectifStrategique->objectifsSpecifiques as $objectifSpecifique)
                                                             <tr>
                                                                 <td>
                                                                     <span class="badge fs-6" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $objectifSpecifique->code }}</span>
                                                                 </td>
                                                                 <td>
                                                                     <strong class="text-dark">{{ $objectifSpecifique->libelle }}</strong>
                                                                 </td>
                                                                 <td>
                                                                     @if($objectifSpecifique->description)
                                                                         <span class="text-muted">{{ Str::limit($objectifSpecifique->description, 50) }}</span>
                                                                     @else
                                                                         <span class="text-muted fst-italic">Aucune description</span>
                                                                     @endif
                                                                 </td>
                                                                 <td>
                                                                     @if($objectifSpecifique->owner)
                                                                         <span class="badge" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                                                                             <i class="fas fa-user me-1"></i>
                                                                             {{ $objectifSpecifique->owner->name }}
                                                                         </span>
                                                                     @else
                                                                         <span class="badge bg-secondary">Non assigné</span>
                                                                     @endif
                                                                 </td>
                                                                 <td>
                                                                     <div class="d-flex align-items-center">
                                                                         <div class="progress me-2" style="width: 100px; height: 8px;">
                                                                        <div class="progress-bar bg-{{ $this->getProgressStatus($objectifSpecifique->taux_avancement) }}" 
                                                                                  role="progressbar" 
                                                                                  style="background: {{ $pilier->getHierarchicalColor(2) }}; width: {{ $objectifSpecifique->taux_avancement }}%"
                                                                                  aria-valuenow="{{ $objectifSpecifique->taux_avancement }}" 
                                                                                  aria-valuemin="0" 
                                                                                  aria-valuemax="100">
                                                                    </div>
                                                                    </div>
                                                                        <small class="text-muted">{{ number_format($objectifSpecifique->taux_avancement, 1) }}%</small>
                                                                    </div>
                                                                 </td>
                                                                 <td>
                                                                     <span class="badge" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }}; border-color: {{ $pilier->getHierarchicalColor(2) }};">
                                                                         {{ $objectifSpecifique->actions->count() }}
                                                                     </span>
                                                                 </td>
                                                                 <td class="text-center">
                                                                     <div class="btn-group" role="group">
                                                                         <!-- Bouton Voir Détails - accessible Ã  tous -->
                                                                         <button type="button" class="btn btn-outline-info btn-sm" style="border-color: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};"
                                                                                 wire:click="naviguerVersObjectifSpecifique({{ $objectifSpecifique->id }})"
                                                                                 title="Voir Détails">
                                                                             <i class="fas fa-eye" style="color: {{ $pilier->getHierarchicalColor(2) }};"></i>
                                                                         </button>
                                                                         
                                                                         <!-- Boutons Modifier/Supprimer - seulement pour admin, owner OS parent ET owner OS spécifique -->
                                                                         @if(Auth::user()->isAdminGeneral() || (Auth::user()->id == $selectedObjectifStrategique->owner_id) || (Auth::user()->id == $objectifSpecifique->owner_id))
                                                                         <button type="button" 
                                                                                 wire:click="setActionToEditObjectifSpecifique({{ $objectifSpecifique->id }})"
                                                                                 class="btn btn-outline-warning btn-sm" 
                                                                                 title="Modifier">
                                                                             <i class="fas fa-edit" style="color: {{ $pilier->getHierarchicalColor(2) }};"></i>
                                                                         </button>
                                                                         <button type="button" 
                                                                                 class="btn btn-outline-danger btn-sm" 
                                                                                 wire:click="deleteObjectifSpecifique({{ $objectifSpecifique->id }})"
                                                                                 onclick="if(!confirm('ÃŠtes-vous sÃ»r de vouloir supprimer cet objectif spécifique ?')) return false;"
                                                                                 title="Supprimer">
                                                                             <i class="fas fa-trash" style="color: {{ $pilier->getHierarchicalColor(2) }};"></i>
                                                                         </button>
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
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Aucun objectif spécifique</h5>
                                                    <p class="text-muted">Commencez par créer votre premier objectif spécifique</p>
                                                    @if(Auth::user()->isAdminGeneral() || (Auth::user()->id == $selectedObjectifStrategique->owner_id))
                                                    <button type="button" class="btn btn-info" wire:click="openCreateOSPModal">
                                                        <i class="fas fa-plus me-2"></i>Créer le premier objectif
                                                    </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <!-- ðŸŽ¯ HIERARCHIE DES STATISTIQUES PARENT -->
                                            <div class="hierarchy-stats-container">
                                                
                                                <!-- PILIER PARENT (NIVEAU 0) -->
                                                <div class="hierarchy-card parent-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(0) }};">
                                                    <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(0) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(0)) }};">
                                                        <div class="hierarchy-icon">
                                                            <i class="fas fa-layer-group"></i>
                                                        </div>
                                                        <div class="hierarchy-info">
                                                            <h6 class="mb-0">Pilier Parent</h6>
                                                            <small>{{ $pilier->code }} - {{ $pilier->libelle }}</small>
                                                        </div>
                                                        <div class="hierarchy-arrow">
                                                            <i class="fas fa-chevron-down"></i>
                                                        </div>
                                                    </div>
                                                    <div class="hierarchy-body">
                                                        <div class="hierarchy-stats">
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ number_format($pilier->taux_avancement, 1) }}%</div>
                                                                <div class="stat-label">Progression</div>
                                                            </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $pilier->objectifsStrategiques->count() }}</div>
                                                                <div class="stat-label">OS</div>
                                                            </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $pilier->objectifsStrategiques->sum(function($os) { return $os->objectifsSpecifiques->count(); }) }}</div>
                                                                <div class="stat-label">OSP</div>
                                                            </div>
                                                        </div>
                                                        <div class="hierarchy-progress">
                                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                                <div class="progress-bar" style="width: {{ $pilier->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(0) }};"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- FLÃˆCHE HIéRARCHIQUE -->
                                                <div class="hierarchy-arrow-container">
                                                    <div class="hierarchy-arrow-line"></div>
                                                    <div class="hierarchy-arrow-head">
                                                        <i class="fas fa-chevron-down"></i>
                                                    </div>
                                                </div>
                                                
                                                <!-- OBJECTIF STRATEGIQUE PARENT (NIVEAU 1) -->
                                                <div class="hierarchy-card parent-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(1) }};">
                                                    <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }};">
                                                        <div class="hierarchy-icon">
                                                            <i class="fas fa-bullseye"></i>
                                                        </div>
                                                        <div class="hierarchy-info">
                                                            <h6 class="mb-0">Objectif Stratégique
</h6>
                                                            <small>{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }} - {{ $selectedObjectifStrategique->libelle }}</small>
                                                        </div>
                                                        <div class="hierarchy-arrow">
                                                            <i class="fas fa-chevron-down"></i>
                                                        </div>
                                                    </div>
                                                    <div class="hierarchy-body">
                                                        <div class="hierarchy-stats">
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ number_format($selectedObjectifStrategique->taux_avancement, 1) }}%</div>
                                                                <div class="stat-label">Progression</div>
                                                            </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: black;">{{ $selectedObjectifStrategique->objectifsSpecifiques->count() }}</div>
                                                                <div class="stat-label">OSP</div>
                                                            </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifStrategique->objectifsSpecifiques->sum(function($ospec) { return $ospec->actions->count(); }) }}</div>
                                                                <div class="stat-label">Actions</div>
                                                            </div>
                                                        </div>
                                                        <div class="hierarchy-progress">
                                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                                <div class="progress-bar" style="width: {{ $selectedObjectifStrategique->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(1) }};"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- FLÃˆCHE HIéRARCHIQUE -->
                                                <div class="hierarchy-arrow-container">
                                                    <div class="hierarchy-arrow-line"></div>
                                                    <div class="hierarchy-arrow-head">
                                                        <i class="fas fa-chevron-down"></i>
                                                    </div>
                                                </div>
                                                
                                                <!-- OBJECTIF SPÃ‰CIFIQUE COURANT (NIVEAU 2) - AFFICHÃ‰ SEULEMENT SI SÃ‰LECTIONNÃ‰ ET DANS LA BONNE VUE -->
                                                @if($selectedObjectifSpecifique && $currentView === 'objectifSpecifique')
                                                <div class="hierarchy-card current-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(2) }};">
                                                    <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                                                        <div class="hierarchy-icon">
                                                            <i class="fas fa-bullseye"></i>
                                                        </div>
                                                        <div class="hierarchy-info">
                                                            <h6 class="mb-0">Objectif Spécifique</h6>
                                                            <small>{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }} - {{ $selectedObjectifSpecifique->libelle }}</small>
                                                        </div>
                                                        <div class="hierarchy-arrow">
                                                            <i class="fas fa-chevron-down"></i>
                                                        </div>
                                                    </div>
                                                    <div class="hierarchy-body">
                                                        <div class="hierarchy-stats">
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ number_format($selectedObjectifSpecifique->taux_avancement, 1) }}%</div>
                                                                <div class="stat-label">Progression</div>
                                                            </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifSpecifique->actions->count() }}</div>
                                                                <div class="stat-label">Actions</div>
                                                            </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifSpecifique->actions->sum(function($action) { return $action->sousActions->count(); }) }}</div>
                                                                <div class="stat-label">Sous-Actions</div>
                                                            </div>
                                                        </div>
                                                        <div class="hierarchy-progress">
                                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                                <div class="progress-bar" style="width: {{ $selectedObjectifSpecifique->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(2) }};"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            

                            <!-- Vue Objectif Spécifique -->
                            @elseif($currentView === 'objectifSpecifique')
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h4 class="mb-0">
                                                    <i class="fas fa-tasks me-2" style="color: {{ $pilier->getHierarchicalColor(3) }};"></i>
                                                    Actions
                                                </h4>
                                                @if(Auth::user()->isAdminGeneral() || (Auth::user()->id == $selectedObjectifStrategique->owner_id) || (Auth::user()->id == $selectedObjectifSpecifique->owner_id))
                                                <button type="button" class="btn btn-sm" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }}; border-color: {{ $pilier->getHierarchicalColor(3) }};" wire:click="openCreateActionModal">
                                                    <i class="fas fa-plus me-2"></i>Créer une Action
                                                </button>
                                                @endif
                                            </div>
                                            
                                            @if($selectedObjectifSpecifique && $selectedObjectifSpecifique->actions->count() > 0)
                                                 <div class="table-responsive">
                                                     <table class="table table-hover">
                                                         <thead style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">
                                                             <tr>
                                                                 <th>Code</th>
                                                                 <th>Libellé</th>
                                                                 <th>Description</th>
                                                                 <th>Owner</th>
                                                                 <th>Progression</th>
                                                                 <th>Sous-actions</th>
                                                                 <th class="text-center">Actions</th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                    @foreach($selectedObjectifSpecifique->actions as $action)
                                                             <tr>
                                                                 <td>
                                                                     <span class="badge fs-6" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }}.{{ $action->code }}</span>
                                                                 </td>
                                                                 <td>
                                                                     <strong class="text-dark">{{ $action->libelle }}</strong>
                                                                 </td>
                                                                 <td>
                                                                     @if($action->description)
                                                                         <span class="text-muted">{{ Str::limit($action->description, 50) }}</span>
                                                                     @else
                                                                         <span class="text-muted fst-italic">Aucune description</span>
                                                                     @endif
                                                                 </td>
                                                                 <td>
                                                                     @if($action->owner)
                                                                         <span class="badge" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                                                                             <i class="fas fa-user me-1"></i>
                                                                             {{ $action->owner->name }}
                                                                         </span>
                                                                     @else
                                                                         <span class="badge" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">Non assigné</span>
                                                                     @endif
                                                                 </td>
                                                                 <td>
                                                                     <div class="d-flex align-items-center">
                                                                         <div class="progress me-2" style="width: 100px; height: 8px;">
                                                                        <div class="progress-bar" 
                                                                                  role="progressbar" 
                                                                                  style="width: {{ $action->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(3) }};color: {{$pilier->getTextColor($pilier->getHierarchicalColor(2)) }}"
                                                                                  aria-valuenow="{{ $action->taux_avancement }}" 
                                                                                  aria-valuemin="0" 
                                                                                  aria-valuemax="100">
                                                                    </div>
                                                                    </div>
                                                                        <small class="text-muted">{{ number_format($action->taux_avancement, 1) }}%</small>
                                                                    </div>
                                                                 </td>
                                                                 <td>
                                                                     <span class="badge" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                                                                         {{ $action->sousActions->count() }}
                                                                     </span>
                                                                 </td>
                                                                 <td class="text-center">
                                                                     <div class="btn-group" role="group">
                                                                         <!-- Bouton Voir Détails - accessible Ã  tous -->
                                                                         <button type="button" class="btn btn-outline-warning btn-sm" 
                                                                                 wire:click="naviguerVersAction({{ $action->id }})"
                                                                                 title="Voir Détails"
                                                                                 style="border-color: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getHierarchicalColor(3) }};">
                                                                             <i class="fas fa-eye"></i>
                                                                         </button>
                                                                         
                                                                         <!-- Boutons Modifier/Supprimer - seulement pour admin, owner OS parent, owner OSP parent ET owner Action -->
                                                                         @if(Auth::user()->isAdminGeneral() || (Auth::user()->id == $selectedObjectifStrategique->owner_id) || (Auth::user()->id == $selectedObjectifSpecifique->owner_id) || (Auth::user()->id == $action->owner_id))
                                                                         <button type="button" 
                                                                                 wire:click="setActionToEditAction({{ $action->id }})"
                                                                                 class="btn btn-outline-warning btn-sm" 
                                                                                 title="Modifier"
                                                                                 style="border-color: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getHierarchicalColor(3) }};">
                                                                             <i class="fas fa-edit"></i>
                                                                         </button>
                                                                         <button type="button" 
                                                                                 class="btn btn-outline-danger btn-sm" 
                                                                                 wire:click="deleteAction({{ $action->id }})"
                                                                                 onclick="if(!confirm('ÃŠtes-vous sÃ»r de vouloir supprimer cette action ?')) return false;"
                                                                                 title="Supprimer"
                                                                                 style="border-color: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getHierarchicalColor(3) }};">
                                                                             <i class="fas fa-trash"></i>
                                                                         </button>
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
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Aucune action</h5>
                                                    <p class="text-muted">Commencez par créer votre première action</p>
                                                                            @if(Auth::user()->isAdminGeneral() || (Auth::user()->id == $selectedObjectifStrategique->owner_id) || (Auth::user()->id == $selectedObjectifSpecifique->owner_id))
                                                                            <button type="button" class="btn" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }}; border-color: {{ $pilier->getHierarchicalColor(3) }};" wire:click="openCreateActionModal">
                            <i class="fas fa-plus me-2"></i>Créer la première action
                        </button>
                                                                            @endif
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-4">
                                             <!-- ðŸŽ¯ HIERARCHIE COMPLETE DES STATISTIQUES PARENT -->
                                            <div class="hierarchy-stats-container">
                                                
                                                <!-- PILIER PARENT (NIVEAU 0) -->
                                                <div class="hierarchy-card parent-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(0) }};">
                                                    <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(0) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(0)) }};">
                                                        <div class="hierarchy-icon">
                                                            <i class="fas fa-layer-group"></i>
                                                </div>
                                                        <div class="hierarchy-info">
                                                            <h6 class="mb-0">Pilier Parent</h6>
                                                            <small>{{ $pilier->code }} - {{ $pilier->libelle }}</small>
                                                        </div>
                                                        <div class="hierarchy-arrow">
                                                            <i class="fas fa-chevron-down"></i>
                                                     </div>
                                                 </div>
                                                    <div class="hierarchy-body">
                                                        <div class="hierarchy-stats">
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ number_format($pilier->taux_avancement, 1) }}%</div>
                                                                <div class="stat-label">Progression</div>
                                                         </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $pilier->objectifsStrategiques->count() }}</div>
                                                                <div class="stat-label">OS</div>
                                                         </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $pilier->objectifsStrategiques->sum(function($os) { return $os->objectifsSpecifiques->count(); }) }}</div>
                                                                <div class="stat-label">OSP</div>
                                                            </div>
                                                        </div>
                                                        <div class="hierarchy-progress">
                                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                                <div class="progress-bar" style="width: {{ $pilier->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(0) }};"></div>
                                                            </div>
                                                         </div>
                                                     </div>
                                                        </div>
                                                    </div>
                                                    
                                                <!-- FLÃˆCHE HIéRARCHIQUE -->
                                                <div class="hierarchy-arrow-container">
                                                    <div class="hierarchy-arrow-line"></div>
                                                    <div class="hierarchy-arrow-head">
                                                        <i class="fas fa-chevron-down"></i>
                                                         </div>
                                                         </div>
                                                
                                                <!-- OBJECTIF STRATEGIQUE PARENT (NIVEAU 1) -->
                                                <div class="hierarchy-card parent-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(1) }};">
                                                    <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }};">
                                                        <div class="hierarchy-icon">
                                                            <i class="fas fa-bullseye"></i>
                                                     </div>
                                                        <div class="hierarchy-info">
                                                            <h6 class="mb-0">Objectif Stratégique
</h6>
                                                            <small>{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }} - {{ $selectedObjectifStrategique->libelle }}</small>
                                                 </div>
                                                        <div class="hierarchy-arrow">
                                                            <i class="fas fa-chevron-down"></i>
                                                        </div>
                                                        </div>
                                                    <div class="hierarchy-body">
                                                        <div class="hierarchy-stats">
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ number_format($selectedObjectifStrategique->taux_avancement, 1) }}%</div>
                                                                <div class="stat-label">Progression</div>
                                                    </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifStrategique->objectifsSpecifiques->count() }}</div>
                                                                <div class="stat-label">OSP</div>
                                                </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifStrategique->objectifsSpecifiques->sum(function($ospec) { return $ospec->actions->count(); }) }}</div>
                                                                <div class="stat-label">Actions</div>
                                                            </div>
                                                        </div>
                                                        <div class="hierarchy-progress">
                                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                                <div class="progress-bar" style="width: {{ $selectedObjectifStrategique->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(1) }};"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- FLÃˆCHE HIéRARCHIQUE -->
                                                <div class="hierarchy-arrow-container">
                                                    <div class="hierarchy-arrow-line"></div>
                                                    <div class="hierarchy-arrow-head">
                                                        <i class="fas fa-chevron-down"></i>
                                            </div>
                                        </div>
                                             
                                                                                           <!-- OBJECTIF SPÃ‰CIFIQUE COURANT (NIVEAU 2) -->
                                                <div class="hierarchy-card current-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(2) }};">
                                                  <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                                                      <div class="hierarchy-icon">
                                                          <i class="fas fa-bullseye"></i>
                                                      </div>
                                                      <div class="hierarchy-info">
                                                          <h6 class="mb-0">Objectif Spécifique</h6>
                                                          <small>{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }} - {{ $selectedObjectifSpecifique->libelle }}</small>
                                                      </div>
                                                      <div class="hierarchy-arrow">
                                                          <i class="fas fa-chevron-down"></i>
                                                      </div>
                                                  </div>
                                                  <div class="hierarchy-body">
                                                      <div class="hierarchy-stats">
                                                          <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ number_format($selectedObjectifSpecifique->taux_avancement, 1) }}%</div>
                                                              <div class="stat-label">Progression</div>
                                                          </div>
                                                          <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifSpecifique->actions->count() }}</div>
                                                              <div class="stat-label">Actions</div>
                                                          </div>
                                                          <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifSpecifique->actions->sum(function($action) { return $action->sousActions->count(); }) }}</div>
                                                              <div class="stat-label">Sous-Actions</div>
                                                          </div>
                                                      </div>
                                                      <div class="hierarchy-progress">
                                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                                <div class="progress-bar" style="width: {{ $selectedObjectifSpecifique->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(2) }};"></div>
                                                            </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                         </div>
                                    </div>
                                </div>
                            
                            
                            <!-- Vue Action -->
                            @elseif($currentView === 'action')
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h4 class="mb-0">
                                                    <i class="fas fa-list-check me-2" style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                                                    Sous-actionss
                                                </h4>
                                                <button type="button" class="btn btn-sm" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}; border-color: {{ $pilier->getHierarchicalColor(4) }};" wire:click="openModalCreateSousAction" onclick="console.log('ðŸ–±ï¸ Clic sur bouton Créer une sous-action')">
                                                    <i class="fas fa-plus me-2"></i>Créer une Sous-action
                                                </button>
                                               
                                            </div>
                                            
                                            @if($selectedAction->sousActions->count() > 0)
                                                 <div class="table-responsive">
                                                    <table class="table table-hover table-striped table-sous-actions">
                                                         <thead style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                                                             <tr>
                                                                <th style="width: 10%;">
                                                                    Code
                                                                </th>
                                                                <th style="width: 20%;">
                                                                    Libellé
                                                                </th>
                                                                <th style="width: 8%;">
                                                                    Type
                                                                </th>
                                                                <th style="width: 18%;">
                                                                    </i>Progression
                                                                </th>
                                                                <th style="width: 15%;">
                                                                    Owner
                                                                </th>
                                                                <th style="width: 17%;">
                                                                    Echéance
                                                                </th>
                                                                <th style="width: 12%;">
                                                                    Actions
                                                                </th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                    @foreach($selectedAction->sousActions as $sousAction)
                                                             <tr>
                                                                 <td>
                                                                        <span class="badge fw-bold" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                                                                            {{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }}.{{ $selectedAction->code }}.{{ $sousAction->code }}
                                                                        </span>
                                                                 </td>
                                                                 <td>
                                                                        <div>
                                                                     <strong class="text-dark">{{ $sousAction->libelle }}</strong>
                                                                     @if($sousAction->description)
                                                                                <br><small class="text-muted">{{ Str::limit($sousAction->description, 80) }}</small>
                                                                     @endif
                                                                        </div>
                                                                 </td>
                                                                 <td>
                                                                        @if($sousAction->type === 'normal')
                                                                            <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}">
                                                                                <i class="fas fa-tasks me-1"></i>Normal
                                                                            </span>
                                                                       
                                                                     @else
                                                                            <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}">
                                                                                <i class="fas fa-question me-1"></i>Non défini
                                                                            </span>
                                                                     @endif
                                                                 </td>
                                                                 <td>
                                                                        <div class="d-flex flex-column align-items-start">
                                                                            <div class="d-flex justify-content-between align-items-center w-100 mb-1">
                                                                                <span class="badge fw-bold" style=" color: black;">
                                                                                    {{ number_format($sousAction->taux_avancement, 1) }}%
                                                                                </span>
                                                                            </div>
                                                                            <div class="progress mb-2 progress-compact" style="width: 100%; background: #e9ecef;">
                                                                                <div class="progress-bar" 
                                                                                     style="width: {{ $sousAction->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(4) }};"
                                                                                  role="progressbar" 
                                                                                  aria-valuenow="{{ $sousAction->taux_avancement }}" 
                                                                                  aria-valuemin="0" 
                                                                                  aria-valuemax="100">
                                                                    </div>
                                                                         </div>
                                                                            
                                                                    </div>
                                                                 </td>
                                                                 <td>
                                                                     @if($sousAction->owner)
                                                                            <span class="badge badge-enhanced"style="background: {{ $pilier->getHierarchicalColor(2) }}; ">
                                                                             <i class="fas fa-user me-1"></i>
                                                                                {{ Str::limit($sousAction->owner->name, 15) }}
                                                                         </span>
                                                                     @else
                                                                            <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}">Non assigné</span>
                                                                     @endif
                                                                 </td>
                                                                 <td>
                                                                        <div class="d-flex flex-column align-items-start">
                                                                            @if($sousAction->date_echeance)
                                                                                <div class="mb-1">
                                                                                    <i class="fas fa-calendar-alt" style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                                                                                    <span class="badge bg-info badge-enhanced">{{ \Carbon\Carbon::parse($sousAction->date_echeance)->format('d/m/Y') }}</span>
                                                                                </div>
                                                                            @else
                                                                                <div class="mb-1">
                                                                                    <i class="fas fa-calendar-alt text-muted column-icon"></i>
                                                                                    <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}">Non définie</span>
                                                                                </div>
                                                                            @endif
                                                                            
                                                                            @if($sousAction->taux_avancement == 100 && $sousAction->date_realisation)
                                                                                <div>
                                                                                   <small> <i class="fas fa-check-circle "style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>Réalisé le {{ \Carbon\Carbon::parse($sousAction->date_realisation)->format('d/m/Y') }}</small>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                 </td>
                                                                    <td>
                                                                        <div class="d-flex gap-1">
                                                                            <!-- Bouton Voir -->
                                                                            <button type="button" 
                                                                                class="btn btn-sm btn-outline-primary btn-action" 
                                                                                title="Voir les Détails"
                                                                                wire:click="naviguerVersSousAction({{ $sousAction->id }})" onclick="console.log('ðŸ'ï¸ Voir Détails sous-action {{ $sousAction->id }}')">
                                                                             <i class="fas fa-eye"></i>
                                                                         </button>
                                                                            
                                                                            <!-- Bouton Modifier -->
                                                                         <button type="button" 
                                                                                class="btn btn-sm btn-outline-warning btn-action" 
                                                                                 wire:click="setActionToEditSousAction({{ $sousAction->id }})"
                                                                                 title="Modifier">
                                                                             <i class="fas fa-edit"></i>
                                                                         </button>
                                                                            
                                                                            <!-- Boutons Spécifiques au type Projet -->
                                                                            @if($sousAction->type === 'projet')
                                                                         <button type="button" 
                                                                                    class="btn btn-sm btn-outline-info btn-action btn-projet" 
                                                                                    title="Calendrier d'activités"
                                                                                    onclick="console.log('ðŸ"… Ouvrir calendrier pour sous-action {{ $sousAction->id }}')">
                                                                                    <i class="fas fa-calendar-alt"></i>
                                                                                </button>
                                                                                
                                                                                <button type="button" 
                                                                                    class="btn btn-sm btn-outline-secondary btn-action btn-projet" 
                                                                                    title="Diagramme de Gantt"
                                                                                    onclick="console.log('ðŸ"Š Ouvrir Gantt pour sous-action {{ $sousAction->id }}')">
                                                                                    <i class="fas fa-chart-bar"></i>
                                                                                </button>
                                                                            @endif
                                                                            
                                                                            <!-- Bouton Supprimer -->
                                                                            <button type="button" 
                                                                                class="btn btn-sm btn-outline-danger btn-action" 
                                                                                 wire:click="deleteSousAction({{ $sousAction->id }})"
                                                                                 onclick="if(!confirm('ÃŠtes-vous sÃ»r de vouloir supprimer cette sous-action ?')) return false;"
                                                                                 title="Supprimer">
                                                                             <i class="fas fa-trash"></i>
                                                                         </button>
                                                                    </div>
                                                                 </td>
                                                             </tr>
                                                    @endforeach
                                                         </tbody>
                                                     </table>
                                                </div>
                                            @else
                                                <div class="text-center py-5">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Aucune sous-action</h5>
                                                    <p class="text-muted">Commencez par créer votre première sous-action</p>
                                                                            @if(Auth::user()->isAdminGeneral() || (Auth::user()->id == $selectedObjectifStrategique->owner_id) || (Auth::user()->id == $selectedObjectifSpecifique->owner_id))
                                                                            <button type="button" class="btn style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}; border-color: {{ $pilier->getHierarchicalColor(4) }};" wire:click="openCreateSousActionModal">
                            <i class="fas fa-plus me-2"></i>Créer la première sous-action
                        </button>
                                                                            @endif
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-4">
                                             <!-- ðŸŽ¯ HIERARCHIE COMPLETE DES STATISTIQUES PARENT -->
                                            <div class="hierarchy-stats-container">
                                                
                                                <!-- PILIER PARENT (NIVEAU 0) -->
                                                <div class="hierarchy-card parent-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(0) }};">
                                                    <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(0) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(0)) }};">
                                                        <div class="hierarchy-icon">
                                                            <i class="fas fa-layer-group"></i>
                                                </div>
                                                        <div class="hierarchy-info">
                                                            <h6 class="mb-0">Pilier Parent</h6>
                                                            <small>{{ $pilier->code }} - {{ $pilier->libelle }}</small>
                                                        </div>
                                                        <div class="hierarchy-arrow">
                                                            <i class="fas fa-chevron-down"></i>
                                                     </div>
                                                 </div>
                                                    <div class="hierarchy-body">
                                                        <div class="hierarchy-stats">
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ number_format($pilier->taux_avancement, 1) }}%</div>
                                                                <div class="stat-label">Progression</div>
                                                         </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $pilier->objectifsStrategiques->count() }}</div>
                                                                <div class="stat-label">OS</div>
                                                         </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $pilier->objectifsStrategiques->sum(function($os) { return $os->objectifsSpecifiques->count(); }) }}</div>
                                                                <div class="stat-label">OSP</div>
                                                            </div>
                                                        </div>
                                                        <div class="hierarchy-progress">
                                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                                <div class="progress-bar" style="width: {{ $pilier->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(0) }};"></div>
                                                         </div>
                                                     </div>
                                                        </div>
                                                    </div>
                                                    
                                                <!-- FLÃˆCHE HIéRARCHIQUE -->
                                                <div class="hierarchy-arrow-container">
                                                    <div class="hierarchy-arrow-line"></div>
                                                    <div class="hierarchy-arrow-head">
                                                        <i class="fas fa-chevron-down"></i>
                                                         </div>
                                                         </div>
                                                
                                                <!-- OBJECTIF STRATEGIQUE PARENT (NIVEAU 1) -->
                                                <div class="hierarchy-card parent-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(1) }};">
                                                    <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }};">
                                                        <div class="hierarchy-icon">
                                                            <i class="fas fa-bullseye"></i>
                                                     </div>
                                                        <div class="hierarchy-info">
                                                            <h6 class="mb-0">Objectif Stratégique
</h6>
                                                            <small>{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }} - {{ $selectedObjectifStrategique->libelle }}</small>
                                                 </div>
                                                        <div class="hierarchy-arrow">
                                                            <i class="fas fa-chevron-down"></i>
                                                         </div>
                                                         </div>
                                                    <div class="hierarchy-body">
                                                        <div class="hierarchy-stats">
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ number_format($selectedObjectifStrategique->taux_avancement, 1) }}%</div>
                                                                <div class="stat-label">Progression</div>
                                                    </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifStrategique->objectifsSpecifiques->count() }}</div>
                                                                <div class="stat-label">OSP</div>
                                                </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifStrategique->objectifsSpecifiques->sum(function($ospec) { return $ospec->actions->count(); }) }}</div>
                                                                <div class="stat-label">Actions</div>
                                                            </div>
                                                        </div>
                                                        <div class="hierarchy-progress">
                                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                                <div class="progress-bar" style="width: {{ $selectedObjectifStrategique->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(1) }};"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- FLÃˆCHE HIéRARCHIQUE -->
                                                <div class="hierarchy-arrow-container">
                                                    <div class="hierarchy-arrow-line"></div>
                                                    <div class="hierarchy-arrow-head">
                                                        <i class="fas fa-chevron-down"></i>
                                            </div>
                                        </div>
                                             
                                                                                           <!-- OBJECTIF SPÃ‰CIFIQUE COURANT (NIVEAU 2) -->
                                                <div class="hierarchy-card current-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(2) }};">
                                                  <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                                                      <div class="hierarchy-icon">
                                                          <i class="fas fa-bullseye"></i>
                                                     </div>
                                                      <div class="hierarchy-info">
                                                          <h6 class="mb-0">Objectif Spécifique</h6>
                                                          <small>{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }} - {{ $selectedObjectifSpecifique->libelle }}</small>
                                                 </div>
                                                      <div class="hierarchy-arrow">
                                                          <i class="fas fa-chevron-down"></i>
                                                         </div>
                                                         </div>
                                                  <div class="hierarchy-body">
                                                      <div class="hierarchy-stats">
                                                          <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ number_format($selectedObjectifSpecifique->taux_avancement, 1) }}%</div>
                                                              <div class="stat-label">Progression</div>
                                                          </div>
                                                          <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifSpecifique->actions->count() }}</div>
                                                              <div class="stat-label">Actions</div>
                                                          </div>
                                                          <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifSpecifique->actions->sum(function($action) { return $action->sousActions->count(); }) }}</div>
                                                              <div class="stat-label">Sous-Actions</div>
                                                          </div>
                                                      </div>
                                                      <div class="hierarchy-progress">
                                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                                <div class="progress-bar" style="width: {{ $selectedObjectifSpecifique->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(2) }};"></div>
                                                            </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                         </div>
                                    </div>
                                </div>
                            
                            
                            <!-- Vue Action -->
                            @elseif($currentView === 'action')
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h4 class="mb-0">
                                                    <i class="fas fa-list-check me-2" style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                                                    Sous-actions
                                                </h4>
                                                <button type="button" class="btn btn-sm" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}; border-color: {{ $pilier->getHierarchicalColor(4) }};" wire:click="openModalCreateSousAction" onclick="console.log('ðŸ–±ï¸ Clic sur bouton Créer une sous-action')">
                                                    <i class="fas fa-plus me-2"></i>Créer une Sous-action
                                                </button>
                                              
                                                         </div>
                                            
                                            @if($selectedAction->sousActions->count() > 0)
                                                 <div class="table-responsive">
                                                     <table class="table table-hover table-striped table-sous-actions">
                                                         <thead style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                                                             <tr>
                                                                 <th style="width: 10%;">
                                                                    <i class="fas fa-code me-1"></i>Code
                                                                </th>
                                                                <th style="width: 20%;">
                                                                    <i class="fas fa-tasks me-1"></i>Libellé
                                                                </th>
                                                                <th style="width: 8%;">
                                                                    <i class="fas fa-tag me-1"></i>Type
                                                                </th>
                                                                <th style="width: 18%;">
                                                                    <i class="fas fa-percentage me-1"></i>Progression
                                                                </th>
                                                                <th style="width: 15%;">
                                                                    <i class="fas fa-user me-1"></i>Owner
                                                                </th>
                                                                <th style="width: 17%;">
                                                                    Echéance
                                                                </th>
                                                                <th style="width: 12%;">
                                                                    <i class="fas fa-cogs me-1"></i>Actions
                                                                </th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                    @foreach($selectedAction->sousActions as $sousAction)
                                                             <tr>
                                                                 <td>
                                                                        <span class="badge fw-bold" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                                                                            {{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }}.{{ $selectedAction->code }}.{{ $sousAction->code }}
                                                                        </span>
                                                                 </td>
                                                                 <td>
                                                         <div>
                                                                     <strong class="text-dark">{{ $sousAction->libelle }}</strong>
                                                                     @if($sousAction->description)
                                                                                <br><small class="text-muted">{{ Str::limit($sousAction->description, 80) }}</small>
                                                                     @endif
                                                         </div>
                                                                 </td>
                                                                 <td>
                                                                        @if($sousAction->type === 'normal')
                                                                            <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}">
                                                                                <i class="fas fa-tasks me-1"></i>Normal
                                                                            </span>
                                                                        @elseif($sousAction->type === 'projet')
                                                                            <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}">
                                                                                <i class="fas fa-project-diagram me-1"></i>Projet
                                                                         </span>
                                                                     @else
                                                                            <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}">
                                                                                <i class="fas fa-question me-1"></i>Non défini
                                                                            </span>
                                                                     @endif
                                                                 </td>
                                                                 <td>
                                                                        <div class="d-flex flex-column align-items-start">
                                                                            <div class="d-flex justify-content-between align-items-center w-100 mb-1">
                                                                                <small class="text-muted">Progression</small>
                                                                                <span class="badge fw-bold" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                                                                                    {{ number_format($sousAction->taux_avancement, 1) }}%
                                                                                </span>
                                                     </div>
                                                                            <div class="progress mb-2 progress-compact" style="width: 100%; background: #e9ecef;">
                                                                                <div class="progress-bar" 
                                                                                     style="width: {{ $sousAction->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(4) }};"
                                                                                  role="progressbar" 
                                                                                  aria-valuenow="{{ $sousAction->taux_avancement }}" 
                                                                                  aria-valuemin="0" 
                                                                                  aria-valuemax="100">
                                                 </div>
                                                         </div>
                                                                            
                                                                           
                                                </div>
                                                                 </td>
                                                                 <td>
                                                                        @if($sousAction->owner)
                                                                            <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}">
                                                                                <i class="fas fa-user me-1"></i>
                                                                                {{ Str::limit($sousAction->owner->name, 15) }}
                                                                            </span>
                                                                        @else
                                                                            <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}">Non assigné</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex flex-column align-items-start">
                                                                            @if($sousAction->date_echeance)
                                                                                <div class="mb-1">
                                                                                    <i class="fas fa-calendar-alt "style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                                                                                    <span class="badge bg-info badge-enhanced">{{ \Carbon\Carbon::parse($sousAction->date_echeance)->format('d/m/Y') }}</span>
                                            </div>
                                                                            @else
                                                                                <div class="mb-1">
                                                                                    <i class="fas fa-calendar-alt text-muted column-icon"></i>
                                                                                    <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}">Non définie</span>
                                        </div>
                                                                            @endif
                                                                            
                                                                            @if($sousAction->taux_avancement == 100 && $sousAction->date_realisation)
                                                                                <div>
                                                                                    <i class="fas fa-check-circle "style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                                                                                    <small style="color: {{ $pilier->getHierarchicalColor(4) }};">Réalisé le {{ \Carbon\Carbon::parse($sousAction->date_realisation)->format('d/m/Y') }}</small>
                                    </div>
                                                                            @endif
                                </div>
                                                                 </td>
                                                                    <td>
                                                                        <div class="d-flex gap-1">
                                                                            <!-- Bouton Voir -->
                                                                            <button type="button" 
                                                                                class="btn btn-sm btn-outline-primary btn-action" 
                                                                                title="Voir les Détails"
                                                                                wire:click="naviguerVersSousAction({{ $sousAction->id }})" onclick="console.log('ðŸ'ï¸ Voir Détails sous-action {{ $sousAction->id }}')">
                                                                             <i class="fas fa-eye"></i>
                                                                         </button>
                                                                            
                                                                            <!-- Bouton Modifier -->
                                                                         <button type="button" 
                                                                                class="btn btn-sm btn-outline-warning btn-action" 
                                                                                 wire:click="setActionToEditSousAction({{ $sousAction->id }})"
                                                                                 title="Modifier">
                                                                             <i class="fas fa-edit"></i>
                                                                         </button>
                                                                            
                                                                            <!-- Boutons Spécifiques au type Projet -->
                                                                            @if($sousAction->type === 'projet')
                                                                         <button type="button" 
                                                                                    class="btn btn-sm btn-outline-info btn-action btn-projet" 
                                                                                    title="Calendrier d'activités"
                                                                                    onclick="console.log('ðŸ"… Ouvrir calendrier pour sous-action {{ $sousAction->id }}')">
                                                                                    <i class="fas fa-calendar-alt"></i>
                                                                                </button>
                                                                                
                                                                                <button type="button" 
                                                                                    class="btn btn-sm btn-outline-secondary btn-action btn-projet" 
                                                                                    title="Diagramme de Gantt"
                                                                                    onclick="console.log('ðŸ"Š Ouvrir Gantt pour sous-action {{ $sousAction->id }}')">
                                                                                    <i class="fas fa-chart-bar"></i>
                                                                                </button>
                                                                            @endif
                                                                            
                                                                            <!-- Bouton Supprimer -->
                                                                            <button type="button" 
                                                                                class="btn btn-sm btn-outline-danger btn-action" 
                                                                                 wire:click="deleteSousAction({{ $sousAction->id }})"
                                                                                 onclick="if(!confirm('ÃŠtes-vous sÃ»r de vouloir supprimer cette sous-action ?')) return false;"
                                                                                 title="Supprimer">
                                                                             <i class="fas fa-trash"></i>
                                                                         </button>
                                    </div>
                                                                 </td>
                                                             </tr>
                                                    @endforeach
                                                         </tbody>
                                                     </table>
                                                </div>
                                            @else
                                                <div class="text-center py-5">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Aucune sous-action</h5>
                                                    <p class="text-muted">Commencez par créer votre première sous-action</p>
                                                                            <button type="button" class="btn style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}; border-color: {{ $pilier->getHierarchicalColor(4) }};" wire:click="openCreateSousActionModal">
                            <i class="fas fa-plus me-2"></i>Créer la première sous-action
                        </button>
                                                </div>
                                            @endif
                                </div>
                                        
                                        <div class="col-md-4">
                                             <!-- ðŸŽ¯ HIERARCHIE COMPLETE DES STATISTIQUES PARENT -->
                                            <div class="hierarchy-stats-container">
                                                
                                                <!-- PILIER PARENT (NIVEAU 0) -->
                                                <div class="hierarchy-card parent-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(0) }};">
                                                    <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(0) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(0)) }};">
                                                        <div class="hierarchy-icon">
                                                            <i class="fas fa-layer-group"></i>
                                                </div>
                                                        <div class="hierarchy-info">
                                                            <h6 class="mb-0">Pilier Parent</h6>
                                                            <small>{{ $pilier->code }} - {{ $pilier->libelle }}</small>
                                                        </div>
                                                        <div class="hierarchy-arrow">
                                                            <i class="fas fa-chevron-down"></i>
                                                     </div>
                                                 </div>
                                                    <div class="hierarchy-body">
                                                        <div class="hierarchy-stats">
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ number_format($pilier->taux_avancement, 1) }}%</div>
                                                                <div class="stat-label">Progression</div>
                                                         </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $pilier->objectifsStrategiques->count() }}</div>
                                                                <div class="stat-label">OS</div>
                                                         </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $pilier->objectifsStrategiques->sum(function($os) { return $os->objectifsSpecifiques->count(); }) }}</div>
                                                                <div class="stat-label">OSP</div>
                                                            </div>
                                                        </div>
                                                        <div class="hierarchy-progress">
                                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                                <div class="progress-bar" style="width: {{ $pilier->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(0) }};"></div>
                                                         </div>
                                                     </div>
                                                        </div>
                                                    </div>
                                                    
                                                <!-- FLÃˆCHE HIéRARCHIQUE -->
                                                <div class="hierarchy-arrow-container">
                                                    <div class="hierarchy-arrow-line"></div>
                                                    <div class="hierarchy-arrow-head">
                                                        <i class="fas fa-chevron-down"></i>
                                                         </div>
                                                         </div>
                                                
                                                <!-- OBJECTIF STRATEGIQUE PARENT (NIVEAU 1) -->
                                                <div class="hierarchy-card parent-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(1) }};">
                                                    <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }};">
                                                        <div class="hierarchy-icon">
                                                            <i class="fas fa-bullseye"></i>
                                                     </div>
                                                        <div class="hierarchy-info">
                                                            <h6 class="mb-0">Objectif Stratégique
                                                            </h6>
                                                            <small>{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }} - {{ $selectedObjectifStrategique->libelle }}</small>
                                                 </div>
                                                        <div class="hierarchy-arrow">
                                                            <i class="fas fa-chevron-down"></i>
                                                         </div>
                                                         </div>
                                                    <div class="hierarchy-body">
                                                        <div class="hierarchy-stats">
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ number_format($selectedObjectifStrategique->taux_avancement, 1) }}%</div>
                                                                <div class="stat-label">Progression</div>
                                                    </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifStrategique->objectifsSpecifiques->count() }}</div>
                                                                <div class="stat-label">OSP</div>
                                                </div>
                                                            <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifStrategique->objectifsSpecifiques->sum(function($ospec) { return $ospec->actions->count(); }) }}</div>
                                                                <div class="stat-label">Actions</div>
                                                            </div>
                                                        </div>
                                                        <div class="hierarchy-progress">
                                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                                <div class="progress-bar" style="width: {{ $selectedObjectifStrategique->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(1) }};"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- FLÃˆCHE HIéRARCHIQUE -->
                                                <div class="hierarchy-arrow-container">
                                                    <div class="hierarchy-arrow-line"></div>
                                                    <div class="hierarchy-arrow-head">
                                                        <i class="fas fa-chevron-down"></i>
                                            </div>
                                        </div>
                                             
                                                                                           <!-- OBJECTIF SPéCIFIQUE COURANT (NIVEAU 2) -->
                                                <div class="hierarchy-card current-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(2) }};">
                                                  <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                                                      <div class="hierarchy-icon">
                                                          <i class="fas fa-bullseye"></i>
                                                     </div>
                                                      <div class="hierarchy-info">
                                                          <h6 class="mb-0">Objectif Spécifique</h6>
                                                          <small>{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }} - {{ $selectedObjectifSpecifique->libelle }}</small>
                                                 </div>
                                                      <div class="hierarchy-arrow">
                                                          <i class="fas fa-chevron-down"></i>
                                                         </div>
                                                         </div>
                                                  <div class="hierarchy-body">
                                                      <div class="hierarchy-stats">
                                                          <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ number_format($selectedObjectifSpecifique->taux_avancement, 1) }}%</div>
                                                              <div class="stat-label">Progression</div>
                                                         </div>
                                                          <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifSpecifique->actions->count() }}</div>
                                                              <div class="stat-label">Actions</div>
                                                          </div>
                                                          <div class="stat-item">
                                                                <div class="stat-value" style="color: #333;">{{ $selectedObjectifSpecifique->actions->sum(function($action) { return $action->sousActions->count(); }) }}</div>
                                                              <div class="stat-label">Sous-Actions</div>
                                                          </div>
                                                      </div>
                                                      <div class="hierarchy-progress">
                                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                                <div class="progress-bar" style="width: {{ $selectedObjectifSpecifique->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(2) }};"></div>
                                                            </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                         </div>
                                    </div>
                                </div>
                                
                                <!-- FLÃˆCHE HIéRARCHIQUE -->
                                <div class="hierarchy-arrow-container">
                                    <div class="hierarchy-arrow-line"></div>
                                    <div class="hierarchy-arrow-head">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                                
                                <!-- ACTION COURANTE (NIVEAU 3) -->
                                <div class="hierarchy-card current-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(3) }};">
                                    <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">
                                        <div class="hierarchy-icon">
                                            <i class="fas fa-tasks"></i>
                                        </div>
                                        <div class="hierarchy-info">
                                            <h6 class="mb-0">Action</h6>
                                            <small>{{ $pilier->code }}.{{ $selectedObjectifStrategique->code }}.{{ $selectedObjectifSpecifique->code }}.{{ $selectedAction->code }} - {{ $selectedAction->libelle }}</small>
                                        </div>
                                        <div class="hierarchy-arrow">
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </div>
                                    <div class="hierarchy-body">
                                        <div class="hierarchy-stats">
                                            <div class="stat-item">
                                                <div class="stat-value" style="color: #333;">{{ number_format($selectedAction->taux_avancement, 1) }}%</div>
                                                <div class="stat-label">Progression</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value" style="color: #333;">{{ $selectedAction->sousActions->count() }}</div>
                                                <div class="stat-label">Sous-Actions</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value" style="color: #333;">{{ $selectedAction->owner ? $selectedAction->owner->name : 'Non assigné' }}</div>
                                                <div class="stat-label">Owner</div>
                                            </div>
                                        </div>
                                        <div class="hierarchy-progress">
                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                <div class="progress-bar" style="width: {{ $selectedAction->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(3) }};"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- FLÃˆCHE HIéRARCHIQUE -->
                                <div class="hierarchy-arrow-container">
                                    <div class="hierarchy-arrow-line"></div>
                                    <div class="hierarchy-arrow-head">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                                
                                <!-- SOUS-ACTIONS (NIVEAU 4) -->
                                <div class="hierarchy-card current-level" style="background: white; border-color: {{ $pilier->getHierarchicalColor(4) }};">
                                    <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                                        <div class="hierarchy-icon">
                                            <i class="fas fa-list-check"></i>
                                        </div>
                                        <div class="hierarchy-info">
                                            <h6 class="mb-0">Sous-Actions</h6>
                                            <small>{{ $selectedAction->sousActions->count() }} sous-action(s) au total</small>
                                        </div>
                                    </div>
                                    <div class="hierarchy-body">
                                        <div class="hierarchy-stats">
                                            <div class="stat-item">
                                                <div class="stat-value" style="color: #333;">{{ number_format($selectedAction->sousActions->avg('taux_avancement'), 1) }}%</div>
                                                <div class="stat-label">Moyenne</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value" style="color: #333;">{{ $selectedAction->sousActions->where('type', 'normal')->count() }}</div>
                                                <div class="stat-label">Normal</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value" style="color: #333;">{{ $selectedAction->sousActions->where('type', 'projet')->count() }}</div>
                                                <div class="stat-label">Projet</div>
                                            </div>
                                        </div>
                                        <div class="hierarchy-progress">
                                            <div class="progress" style="height: 6px; background: #e9ecef;">
                                                <div class="progress-bar" style="width: {{ $selectedAction->sousActions->avg('taux_avancement') }}%; background: {{ $pilier->getHierarchicalColor(4) }};"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                            
                            
                            <!-- Vue Sous-Action -->
                            @elseif($currentView === 'sousAction')
                                <div class="p-4">
                                    <!-- En-tÃªte avec navigation -->
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div>
                                            <h4 class="mb-1">
                                                <i class="fas fa-list-check me-2" style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                                                        Détails de la Sous-action
                                            </h4>
                                            <nav aria-label="breadcrumb">
                                                <ol class="breadcrumb mb-0">
                                                    <li class="breadcrumb-item">
                                                        <a href="#" wire:click="retourListeActions" class="text-decoration-none">
                                                            <i class="fas fa-arrow-left me-1"></i>Retour aux Actions
                                                        </a>
                                                    </li>
                                                </ol>
                                            </nav>
                                                </div>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm" wire:click="setActionToEditSousAction({{ $selectedSousAction->id }})">
                                                <i class="fas fa-edit me-1"></i>Modifier
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" wire:click="deleteSousAction({{ $selectedSousAction->id }})" onclick="return confirm('ÃŠtes-vous sÃ»r de vouloir supprimer cette sous-action ?')">
                                                <i class="fas fa-trash me-1"></i>Supprimer
                                            </button>
                                        </div>
                                    </div>

                                                    <div class="row">
                                        <!-- Informations principales -->
                                        <div class="col-12">
                                            <!-- Carte principale de la sous-action -->
                                            <div class="hierarchy-card current-level mb-4" style="background: white; border-color: {{ $pilier->getHierarchicalColor(4) }};">
                                                <div class="hierarchy-header" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                                                    <div class="hierarchy-icon">
                                                        <i class="fas fa-list-check"></i>
                                                        </div>
                                                    <div class="hierarchy-info">
                                                        <h6 class="mb-0">{{ $selectedSousAction->code }}</h6>
                                                        <small>{{ $selectedSousAction->libelle }}</small>
                                                    </div>
                                                    <div class="hierarchy-arrow">
                                                        <i class="fas fa-chevron-down"></i>
                                                    </div>
                                                </div>
                                                <div class="hierarchy-body">
                                                    @if($selectedSousAction->description)
                                                            <div class="mb-3">
                                                            <h6 class="text-muted mb-2">Description</h6>
                                                            <p class="text-dark mb-0">{{ $selectedSousAction->description }}</p>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- ContrÃ´le de progression en temps réel -->
                                                    <div class="mb-4">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="text-muted mb-0">Progression</h6>
                                                            
                                                        </div>
                                                        
                                                        
                                                        @if($selectedSousAction->type === 'normal')
                                                            <!-- Boutons de progression pour les sous-actions normales -->
                                                            <div class="progression-buttons mb-3">
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <button type="button" 
                                                                            class="btn btn-outline-secondary btn-sm progression-btn {{ ($selectedSousAction->taux_avancement ?? 0) == 0 ? 'active' : '' }}"
                                                                            wire:click="updateProgressionBouton({{ $selectedSousAction->id ?? 0 }}, 0)"
                                                                            style="min-width: 60px; transition: all 0.3s ease;">
                                                                        0%
                                                                    </button>
                                                                    <button type="button" 
                                                                            class="btn btn-outline-secondary btn-sm progression-btn {{ ($selectedSousAction->taux_avancement ?? 0) >= 25 && ($selectedSousAction->taux_avancement ?? 0) < 50 ? 'active' : '' }}"
                                                                            wire:click="updateProgressionBouton({{ $selectedSousAction->id ?? 0 }}, 25)"
                                                                            style="min-width: 60px; transition: all 0.3s ease;">
                                                                        25%
                                                                    </button>
                                                                    <button type="button" 
                                                                            class="btn btn-outline-secondary btn-sm progression-btn {{ ($selectedSousAction->taux_avancement ?? 0) >= 50 && ($selectedSousAction->taux_avancement ?? 0) < 75 ? 'active' : '' }}"
                                                                            wire:click="updateProgressionBouton({{ $selectedSousAction->id ?? 0 }}, 50)"
                                                                            style="min-width: 60px; transition: all 0.3s ease;">
                                                                        50%
                                                                    </button>
                                                                    <button type="button" 
                                                                            class="btn btn-outline-secondary btn-sm progression-btn {{ ($selectedSousAction->taux_avancement ?? 0) >= 75 && ($selectedSousAction->taux_avancement ?? 0) < 100 ? 'active' : '' }}"
                                                                            wire:click="updateProgressionBouton({{ $selectedSousAction->id ?? 0 }}, 75)"
                                                                            style="min-width: 60px; transition: all 0.3s ease;">
                                                                        75%
                                                                    </button>
                                                                    <button type="button" 
                                                                            class="btn btn-outline-secondary btn-sm progression-btn {{ ($selectedSousAction->taux_avancement ?? 0) == 100 ? 'active' : '' }}"
                                                                            wire:click="updateProgressionBouton({{ $selectedSousAction->id ?? 0 }}, 100)"
                                                                            style="min-width: 60px; transition: all 0.3s ease;">
                                                                        100%
                                                                    </button>
                                                                </div>
                                                                
                                                                <!-- Barre de progression visuelle -->
                                                                <div class="progress mb-2" style="height: 20px; border-radius: 10px;">
                                                                    <div class="progress-bar bg-gradient-primary" 
                                                                 role="progressbar" 
                                                                         style="width: {{ $selectedSousAction->taux_avancement ?? 0 }}%; background: {{ $pilier->getHierarchicalColor(4) }}; transition: width 0.5s ease;"
                                                                         aria-valuenow="{{ $selectedSousAction->taux_avancement ?? 0 }}" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="100">
                                                                        <strong class="text-white">{{ $selectedSousAction->taux_avancement ?? 0 }}%</strong>
                                                            </div>
                                                        </div>
                                                                </div>
                                                        @else
                                                            <!-- Progression automatique pour les projets -->
                                                            <div class="text-center py-2">
                                                                <div class="alert alert-info mb-0">
                                                                    <i class="fas fa-info-circle me-2"></i>
                                                                    <strong>Progression automatique</strong><br>
                                                                    <small>Le taux d'avancement est calculé automatiquement Ã  partir des activités du projet</small>
                                                            </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Informations Détaillées -->
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="info-item mb-3">
                                                                <h6 class="text-muted mb-1">Owner</h6>
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-user me-2" style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                                                                    <span class="fw-bold">{{ $selectedSousAction->owner->name ?? 'Non assigné' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="info-item mb-3">
                                                                <h6 class="text-muted mb-1">Statut</h6>
                                                                <span class="badge bg-{{ $this->getProgressStatus($selectedSousAction->taux_avancement) }}">
                                                                    @if($selectedSousAction->taux_avancement >= 100)
                                                                        <i class="fas fa-check me-1"></i>Terminé
                                                                    @elseif($selectedSousAction->taux_avancement >= 75)
                                                                        <i class="fas fa-clock me-1"></i>En cours
                                                                    @elseif($selectedSousAction->taux_avancement >= 50)
                                                                        <i class="fas fa-pause me-1"></i>En pause
                                                                    @else
                                                                        <i class="fas fa-play me-1"></i>Ã€ démarrer
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Dates -->
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="info-item mb-3">
                                                                <h6 class="text-muted mb-1">Date d'échéance</h6>
                                                                @if($selectedSousAction->date_echeance)
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fas fa-calendar me-2" style="color: {{ $pilier->getHierarchicalColor(4) }};"></i>
                                                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($selectedSousAction->date_echeance)->format('d/m/Y') }}</span>
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">Non définie</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="info-item mb-3">
                                                                <h6 class="text-muted mb-1">Date de réalisation</h6>
                                                                @if($selectedSousAction->date_realisation)
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fas fa-check-circle me-2" style="color: #28a745;"></i>
                                                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($selectedSousAction->date_realisation)->format('d/m/Y') }}</span>
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">Non terminée</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                            @if($selectedSousAction->date_echeance)
                                                        <div class="info-item">
                                                            <h6 class="text-muted mb-1">écart</h6>
                                                            @php
                                                                $ecart = $this->calculateEcart($selectedSousAction->date_echeance, $selectedSousAction->date_realisation);
                                                                $ecartClass = $ecart ? 'success' : 'warning';
                                                                $ecartIcon = $ecart ? 'check' : 'exclamation-triangle';
                                                                $ecartText = $ecart ? 'Dans les délais' : 'En retard';
                                                            @endphp
                                                            <span class="badge bg-{{ $ecartClass }}">
                                                                <i class="fas fa-{{ $ecartIcon }} me-1"></i>{{ $ecartText }}
                                                                    </span>
                                                        </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                            <!-- Carte redondante supprimée - Les informations sont déjà affichées dans la carte principale ci-dessus -->
                                                            
                                                                                                                                                                                    <!-- SECTION DES ACTIVITÉS SUPPRIMéE TEMPORAIREMENT - TOUTES LES SOUS-ACTIONS SONT DE TYPE NORMAL -->
                                    </div>
                                                                            
                                                                            </div>
                                                                            
                                </div>
                            @endif
                        </div>
                </div>

            </div>
                    @endif
                </div>

                
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9998;"></div>
    @endif

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }
        
        .hierarchical-card {
            transition: all 0.3s ease;
        }

        /* Styles pour la page Détail sous-action */
        .hierarchy-impact-item {
            padding: 12px;
            border-radius: 8px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .hierarchy-impact-item:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        .info-item {
            padding: 16px;
            border-radius: 8px;
            background: #f8f9fa;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: #e9ecef;
            border-left-color: var(--bs-primary);
        }

        .stat-item {
            padding: 16px;
            border-radius: 8px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            background: #e9ecef;
            transform: scale(1.05);
        }

        .breadcrumb-item a:hover {
            color: var(--bs-primary) !important;
        }

        /* Animation pour les barres de progression */
        .progress-bar {
            transition: width 0.6s ease;
        }

        /* Style pour le slider de progression */
        .form-range::-webkit-slider-thumb {
            background: var(--bs-primary);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .form-range::-moz-range-thumb {
            background: var(--bs-primary);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
            cursor: pointer;
            border: 1px solid #e9ecef;
        }
        
        .hierarchical-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-color: #007bff;
        }
        
        .hierarchical-content {
            transition: transform 0.3s ease;
        }
        
        .breadcrumb .btn-link {
            text-decoration: none;
            color: #6c757d;
        }
        
        .breadcrumb .btn-link:hover {
            color: #007bff;
        }
        
        .progress-bar {
            transition: width 0.8s ease;
        }
        
        /* Styles pour le plein écran */
        .fullscreen-content {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9999;
            background: white;
            overflow: auto;
        }
        
        /* Styles pour les cartes hiérarchiques */
        .hierarchy-stats-container {
            position: relative;
        }
        
        .hierarchy-card {
            border-radius: 12px;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .hierarchy-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        .hierarchy-header {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }
        
        .hierarchy-icon {
            width: 35px;
            height: 35px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }
        
        .hierarchy-info {
            flex: 1;
        }
        
        .hierarchy-info h6 {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
        }
        
        .hierarchy-info small {
            font-size: 11px;
            opacity: 0.9;
        }
        
        .hierarchy-arrow {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.7;
        }
        
        .hierarchy-body {
            padding: 16px;
        }
        
        .hierarchy-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        
        .stat-item {
            text-align: center;
            flex: 1;
        }
        
        .stat-value {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        /* Styles pour les cartes de sous-actions */
            .sous-action-card {
        transition: all 0.3s ease;
        border-radius: 12px;
    }
    
    .sous-action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    /* Styles pour le tableau des sous-actions */
    .table-sous-actions {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .table-sous-actions thead th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 12px 8px;
        vertical-align: middle;
    }
    
    .table-sous-actions tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-sous-actions tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
    }
    
    .table-sous-actions tbody td {
        padding: 12px 8px;
        vertical-align: middle;
        border-top: 1px solid #f1f3f4;
    }
    
    /* Boutons de pourcentage compacts */
    .btn-xs {
        padding: 2px 6px;
        font-size: 11px;
        line-height: 1.2;
        border-radius: 4px;
        min-width: 32px;
    }
    
    /* Barre de progression compacte */
    .progress-compact {
        height: 6px !important;
        border-radius: 3px;
    }
    
    /* Badges améliorés */
    .badge-enhanced {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 500;
    }
    
    /* Boutons d'action */
    .btn-action {
        transition: all 0.2s ease;
        border-radius: 6px;
        padding: 6px 8px;
        font-size: 0.875rem;
    }
    
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    /* IcÃ´nes dans les colonnes */
    .column-icon {
        color: #6c757d;
        margin-right: 6px;
    }
    
    /* Date de réalisation */
    .realisation-date {
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    /* Badges de type */
    .type-badge {
        font-size: 0.7rem;
        padding: 3px 6px;
        border-radius: 4px;
        font-weight: 500;
    }
    
    /* Indicateurs de calcul */
    .calc-indicator {
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    /* Boutons d'action projet */
    .btn-projet {
        transition: all 0.2s ease;
        border-radius: 4px;
        padding: 4px 6px;
        font-size: 0.8rem;
    }
    
    .btn-projet:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
        
        .percentage-btn {
            min-width: 45px;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .percentage-btn:hover {
            transform: scale(1.05);
        }
        
        .percentage-btn.btn-primary {
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .fs-7 {
            font-size: 0.75rem !important;
        }
        
        .stat-label {
            font-size: 11px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .hierarchy-progress {
            margin-top: 8px;
        }
        
        /* Flèches hiérarchiques */
        .hierarchy-arrow-container {
            position: relative;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 5px 0;
        }
        
        .hierarchy-arrow-line {
            position: absolute;
            width: 2px;
            height: 15px;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.1));
            border-radius: 1px;
        }
        
        .hierarchy-arrow-head {
            position: absolute;
            width: 20px;
            height: 20px;
            background: rgba(255,255,255,0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            color: #666;
            font-size: 10px;
        }
        
        /* Animations */
        .hierarchy-card {
            animation: slideInUp 0.4s ease-out;
        }
        
        .hierarchy-card:nth-child(1) { animation-delay: 0.1s; }
        .hierarchy-card:nth-child(3) { animation-delay: 0.2s; }
        .hierarchy-card:nth-child(5) { animation-delay: 0.3s; }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Niveaux hiérarchiques */
        .root-level {
            border-left: 4px solid;
        }
        
        .parent-level {
            border-left: 3px solid;
        }
        
        .current-level {
            border-left: 4px solid;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
    </style>

    <script>
        // Variables pour le modal hiérarchique uniquement
        let hierarchiqueCurrentZoom = 100;
        let hierarchiqueIsFullscreen = false;
        
        function resetZoom() {
            hierarchiqueCurrentZoom = 100;
            updateHierarchiqueZoom();
        }
        
        function updateHierarchiqueZoom() {
            const content = document.getElementById('hierarchiqueContent');
            if (content) {
                content.style.transform = `scale(${hierarchiqueCurrentZoom / 100})`;
                content.style.transformOrigin = 'top left';
            }
        }
        
        function toggleFullscreen() {
            const content = document.getElementById('hierarchiqueContent');
            
            if (!hierarchiqueIsFullscreen) {
                content.classList.add('fullscreen-content');
                hierarchiqueIsFullscreen = true;
            } else {
                content.classList.remove('fullscreen-content');
                hierarchiqueIsFullscreen = false;
            }
        }
    </script>

    <!-- SCRIPT POUR FORCER LE RE-RENDU -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Écouter l'événement de forçage CSS automatique
            Livewire.on('force-modal-css', (data) => {
                console.log('🎯 [AUTO] Événement force-modal-css reçu:', data);
                
                // Attendre que le DOM soit mis à jour
                setTimeout(() => {
                    console.log('🎯 [AUTO] Forçage automatique de l\'affichage du modal...');
                    forceModalDisplay();
                }, 100);
            });
            
            Livewire.on('console.log', (data) => {
                console.log('📝 [LIVEWIRE]', data);
            });
        });
        
        // ===== NOUVEAU MODAL INDÉPENDANT =====
        
        // Ouvrir le nouveau modal
        function openNewOSModal() {
            console.log('🚀 [NEW MODAL] Ouverture du nouveau modal de création d\'OS...');
            const modal = document.getElementById('newCreateOSModal');
            if (modal) {
                modal.style.display = 'block';
                console.log('✅ [NEW MODAL] Modal affiché avec succès !');
                
                // Focus sur le premier champ
                setTimeout(() => {
                    const firstInput = modal.querySelector('input[name="code"]');
                    if (firstInput) firstInput.focus();
                }, 100);
            } else {
                console.error('❌ [NEW MODAL] Modal non trouvé !');
            }
        }
        
        // Fermer le nouveau modal
        function closeNewOSModal() {
            console.log('🔒 [NEW MODAL] Fermeture du nouveau modal...');
            const modal = document.getElementById('newCreateOSModal');
            if (modal) {
                modal.style.display = 'none';
                console.log('✅ [NEW MODAL] Modal fermé avec succès !');
                
                // Réinitialiser le formulaire
                const form = document.getElementById('newCreateOSForm');
                if (form) form.reset();
            }
        }
        
        // Configuration du nouveau formulaire
        function setupNewOSForm() {
            console.log('🔄 [NEW MODAL] Configuration du nouveau formulaire...');
            
            const form = document.getElementById('newCreateOSForm');
            if (form) {
                console.log('✅ [NEW MODAL] Formulaire trouvé, ajout de l\'écouteur d\'événement...');
                
                // Supprimer l'ancien écouteur s'il existe
                form.removeEventListener('submit', handleNewOSSubmit);
                
                // Ajouter le nouvel écouteur
                form.addEventListener('submit', handleNewOSSubmit);
                
                console.log('✅ [NEW MODAL] Écouteur d\'événement ajouté avec succès !');
            } else {
                console.warn('⚠️ [NEW MODAL] Formulaire non trouvé, nouvelle tentative dans 100ms...');
                setTimeout(setupNewOSForm, 100);
            }
        }
        
        // Gestion de la soumission du nouveau formulaire
        function handleNewOSSubmit(e) {
            e.preventDefault();
            console.log('🚀 [NEW MODAL] Soumission du nouveau formulaire...');
            
            const formData = new FormData(e.target);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            console.log('📋 [NEW MODAL] Données du formulaire:', data);
            
            // Appeler la méthode Livewire
            const wireId = document.querySelector('[wire\\:id]').getAttribute('wire:id');
            console.log('🔄 [NEW MODAL] Appel de la méthode Livewire saveNewOS...');
            window.Livewire.find(wireId).call('saveNewOS', data);
            
            // Fermer le modal immédiatement
            closeNewOSModal();
            
            console.log('✅ [NEW MODAL] Formulaire soumis avec succès !');
        }
        
        // Fermer le modal en cliquant à l'extérieur
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('newCreateOSModal');
            if (modal && e.target === modal) {
                closeNewOSModal();
            }
        });
        
        // Initialisation du nouveau formulaire
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupNewOSForm);
        } else {
            setupNewOSForm();
        }
        
        // Écouter les événements Livewire
        document.addEventListener('livewire:init', setupNewOSForm);
        
        // ===== ANCIEN MODAL BOOTSTRAP (GARDÉ POUR COMPATIBILITÉ) =====
        
        // Configuration du formulaire de création d'objectif stratégique
        function setupObjectifStrategiqueForm() {
            console.log('🔄 [BOOTSTRAP] Configuration du formulaire de création d\'OS...');
            
            const form = document.getElementById('createObjectifStrategiqueForm');
            if (form) {
                console.log('✅ [BOOTSTRAP] Formulaire trouvé, ajout de l\'écouteur d\'événement...');
                
                // Supprimer l'ancien écouteur s'il existe
                form.removeEventListener('submit', handleObjectifStrategiqueSubmit);
                
                // Ajouter le nouvel écouteur
                form.addEventListener('submit', handleObjectifStrategiqueSubmit);
                
                console.log('✅ [BOOTSTRAP] Écouteur d\'événement ajouté avec succès !');
            } else {
                console.warn('⚠️ [BOOTSTRAP] Formulaire non trouvé, nouvelle tentative dans 100ms...');
                setTimeout(setupObjectifStrategiqueForm, 100);
            }
        }
        
        // Gestion de la soumission du formulaire
        function handleObjectifStrategiqueSubmit(e) {
            e.preventDefault();
            console.log('🚀 [BOOTSTRAP] Soumission du formulaire de création d\'OS...');
            
            const formData = new FormData(e.target);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            console.log('📋 [BOOTSTRAP] Données du formulaire:', data);
            
            // Appeler la méthode Livewire
            const wireId = document.querySelector('[wire\\:id]').getAttribute('wire:id');
            console.log('🔄 [BOOTSTRAP] Appel de la méthode Livewire saveNewOS...');
            window.Livewire.find(wireId).call('saveNewOS', data);
            
            // Fermer le modal après soumission (méthode sécurisée avec délai)
            setTimeout(() => {
                console.log('🔄 [BOOTSTRAP] Tentative de fermeture du modal après délai...');
                
                try {
                    const modalElement = document.getElementById('createObjectifStrategiqueModal');
                    if (modalElement) {
                        // Méthode 1: Via l'instance Bootstrap
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) {
                            modalInstance.hide();
                            console.log('✅ [BOOTSTRAP] Modal fermé via instance Bootstrap');
                        } else {
                            // Méthode 2: Via l'API Bootstrap directement
                            const newModal = new bootstrap.Modal(modalElement);
                            newModal.hide();
                            console.log('✅ [BOOTSTRAP] Modal fermé via nouvelle instance');
                        }
                    } else {
                        console.warn('⚠️ [BOOTSTRAP] Élément modal non trouvé');
                    }
                } catch (modalError) {
                    console.warn('⚠️ [BOOTSTRAP] Erreur lors de la fermeture du modal:', modalError);
                    // Méthode 3: Fermeture manuelle via CSS
                    const modalElement = document.getElementById('createObjectifStrategiqueModal');
                    if (modalElement) {
                        modalElement.style.display = 'none';
                        modalElement.classList.remove('show');
                        document.body.classList.remove('modal-open');
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.remove();
                        console.log('✅ [BOOTSTRAP] Modal fermé via instance Bootstrap');
                    }
                }
            }, 500); // Attendre 500ms pour que Bootstrap soit prêt
            
            console.log('✅ [BOOTSTRAP] Formulaire soumis avec succès !');
        }
        
        // Initialisation
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupObjectifStrategiqueForm);
        } else {
            setupObjectifStrategiqueForm();
        }
        
                // Écouter les événements Livewire
        document.addEventListener('livewire:init', setupObjectifStrategiqueForm);
    </script>

    <!-- ===== MODALS DE CRéATION ET D'éDITION ===== -->
    
    <!-- MODAL BOOTSTRAP PUR (TEST) -->
    <div class="modal fade" id="bootstrapTestModal" tabindex="-1" aria-labelledby="bootstrapTestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: #007bff; color: white;">
                    <h5 class="modal-title" id="bootstrapTestModalLabel">
                        <i class="fas fa-rocket me-2"></i>
                        Test Modal Bootstrap
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>🎉 <strong>SUCCÈS !</strong> Ce modal Bootstrap fonctionne parfaitement !</p>
                    <p>Si vous voyez ce message, le problème n'est PAS avec Bootstrap, mais avec Livewire.</p>
                    <hr>
                    <p><strong>Diagnostic :</strong></p>
                    <ul>
                        <li>✅ Bootstrap : Fonctionne</li>
                        <li>❌ Livewire : Ne re-rend pas le composant</li>
                        <li>🔧 Solution : Forcer le re-rendu Livewire</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" onclick="console.log('🎯 Modal Bootstrap fonctionne !')">Confirmer</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Créer Objectif Stratégique (BOOTSTRAP PUR) -->
    <!-- NOUVEAU MODAL CRÉER OBJECTIF STRATÉGIQUE (INDÉPENDANT) -->
    @if($pilier)
    <div id="newCreateOSModal" class="custom-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="modal-content-custom" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px; min-width: 500px; max-width: 600px; max-height: 80vh; overflow-y: auto;">
            
            <!-- HEADER -->
            <div class="modal-header-custom" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid {{ $pilier->getHierarchicalColor(1) }};">
                <h4 style="margin: 0; color: {{ $pilier->getHierarchicalColor(1) }}; font-weight: bold;">
                    <i class="fas fa-bullseye me-2"></i>
                    Créer un Objectif Stratégique
                </h4>
                <button type="button" onclick="closeNewOSModal()" style="background: none; border: none; font-size: 24px; color: #666; cursor: pointer; padding: 5px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- FORMULAIRE -->
            <form id="newCreateOSForm">
                <input type="hidden" name="pilier_id" value="{{ $pilier->id }}">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Code *</label>
                    <input type="text" name="code" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; transition: border-color 0.3s;" placeholder="Ex: OS1">
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Libellé *</label>
                    <input type="text" name="libelle" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; transition: border-color 0.3s;" placeholder="Ex: Croissance des activités">
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Description</label>
                    <textarea name="description" rows="4" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; transition: border-color 0.3s; resize: vertical;" placeholder="Description optionnelle..."></textarea>
                </div>
                
                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Owner</label>
                    <select name="owner_id" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px; transition: border-color 0.3s;">
                        <option value="">Sélectionner un owner</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- BOUTONS -->
                <div class="modal-footer-custom" style="display: flex; justify-content: flex-end; gap: 15px; padding-top: 20px; border-top: 1px solid #eee;">
                    <button type="button" onclick="closeNewOSModal()" style="padding: 12px 24px; border: 2px solid #6c757d; background: white; color: #6c757d; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" style="padding: 12px 24px; border: none; background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }}; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                        <i class="submit-btn-text">Créer</i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal éditer Objectif Stratégique
 -->
    @if($showEditObjectifStrategiqueForm)
    <div class="modal fade show" style="display: block; z-index: 10000;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }};">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Modifier l'Objectif Stratégique

                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="hideEditObjectifForm"></button>
                </div>
                <form wire:submit.prevent="updateObjectifStrategique">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" wire:model="editingObjectifStrategique.code" required>
                            @error('editingObjectifStrategique.code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Libellé *</label>
                            <input type="text" class="form-control" wire:model="editingObjectifStrategique.libelle" required>
                            @error('editingObjectifStrategique.libelle') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" wire:model="editingObjectifStrategique.description" rows="3"></textarea>
                            @error('editingObjectifStrategique.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Owner</label>
                            <select class="form-select" wire:model="editingObjectifStrategique.owner_id">
                                <option value="">Sélectionner un owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('editingObjectifStrategique.owner_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="hideEditObjectifForm">Annuler</button>
                        <button type="submit" class="btn" style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }}; border-color: {{ $pilier->getHierarchicalColor(1) }};">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif

    <!-- Modal Créer Objectif Spécifique -->
    @if($showCreateObjectifSpecifiqueForm)
    <div class="modal fade show" style="display: block; z-index: 10000;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                    <h5 class="modal-title">
                        <i class="fas fa-tasks me-2"></i>
                        Créer un Objectif Spécifique
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeCreateOSPModal"></button>
                </div>
                <form wire:submit.prevent="saveNewOSP">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" wire:model="newObjectifSpecifique.code" required>
                            @error('newObjectifSpecifique.code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Libellé *</label>
                            <input type="text" class="form-control" wire:model="newObjectifSpecifique.libelle" required>
                            @error('newObjectifSpecifique.libelle') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" wire:model="newObjectifSpecifique.description" rows="3"></textarea>
                            @error('newObjectifSpecifique.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Owner</label>
                            <select class="form-select" wire:model="newObjectifSpecifique.owner_id">
                                <option value="">Sélectionner un owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('newObjectifSpecifique.owner_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeCreateOSPModal">Annuler</button>
                        <button type="submit" class="btn" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }}; border-color: {{ $pilier->getHierarchicalColor(2) }};">
                            <i class="fas fa-save me-2"></i>Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif

    <!-- Modal éditer Objectif Spécifique -->
    @if($showEditObjectifSpecifiqueForm)
    <div class="modal fade show" style="display: block; z-index: 10000;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Modifier l'Objectif Spécifique
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="hideEditObjectifSpecifiqueForm"></button>
                </div>
                <form wire:submit.prevent="updateObjectifSpecifique">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" wire:model="editingObjectifSpecifique.code" required>
                            @error('editingObjectifSpecifique.code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Libellé *</label>
                            <input type="text" class="form-control" wire:model="editingObjectifSpecifique.libelle" required>
                            @error('editingObjectifSpecifique.libelle') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" wire:model="editingObjectifSpecifique.description" rows="3"></textarea>
                            @error('editingObjectifSpecifique.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Owner</label>
                            <select class="form-select" wire:model="editingObjectifSpecifique.owner_id">
                                <option value="">Sélectionner un owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('editingObjectifSpecifique.owner_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="hideEditObjectifSpecifiqueForm">Annuler</button>
                        <button type="submit" class="btn" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }}; border-color: {{ $pilier->getHierarchicalColor(2) }};">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif

    <!-- Modal Créer Action -->
    @if($showCreateActionForm)
    <div class="modal fade show" style="display: block; z-index: 10000;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">
                    <h5 class="modal-title">
                        <i class="fas fa-list-check me-2"></i>
                        Créer une Action
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeCreateActionModal"></button>
                </div>
                <form wire:submit.prevent="saveNewAction">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" wire:model="newAction.code" required>
                            @error('newAction.code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Libellé *</label>
                            <input type="text" class="form-control" wire:model="newAction.libelle" required>
                            @error('newAction.libelle') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" wire:model="newAction.description" rows="3"></textarea>
                            @error('newAction.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Owner</label>
                            <select class="form-select" wire:model="newAction.owner_id">
                                <option value="">Sélectionner un owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('newAction.owner_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="hideCreateActionForm">Annuler</button>
                        <button type="submit" class="btn" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }}; border-color: {{ $pilier->getHierarchicalColor(3) }};">
                            <i class="fas fa-save me-2"></i>Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif

    <!-- Modal éditer Action -->
    @if($showEditActionForm)
    <div class="modal fade show" style="display: block; z-index: 10000;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Modifier l'Action
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="hideEditActionForm"></button>
                </div>
                <form wire:submit.prevent="updateAction">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" wire:model="editingAction.code" required>
                            @error('editingAction.code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Libellé *</label>
                            <input type="text" class="form-control" wire:model="editingAction.libelle" required>
                            @error('editingAction.libelle') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" wire:model="editingAction.description" rows="3"></textarea>
                            @error('editingAction.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Owner</label>
                            <select class="form-select" wire:model="editingAction.owner_id">
                                <option value="">Sélectionner un owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('editingAction.owner_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="hideEditActionForm">Annuler</button>
                        <button type="submit" class="btn" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }}; border-color: {{ $pilier->getHierarchicalColor(3) }};">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif

    

   

    <!-- Modal Créer Objectif Spécifique -->
    @if($showCreateObjectifSpecifiqueForm)
    <div class="modal fade show" style="display: block; z-index: 10000;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>
                        Créer un Objectif Spécifique
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeCreateOSPModal"></button>
                </div>
                <form wire:submit.prevent="createObjectifSpecifique">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" wire:model="newObjectifSpecifique.code" required>
                            @error('newObjectifSpecifique.code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Libellé *</label>
                            <input type="text" class="form-control" wire:model="newObjectifSpecifique.libelle" required>
                            @error('newObjectifSpecifique.libelle') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" wire:model="newObjectifSpecifique.description" rows="3"></textarea>
                            @error('newObjectifSpecifique.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Owner</label>
                            <select class="form-select" wire:model="newObjectifSpecifique.owner_id">
                                <option value="">Sélectionner un owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('newObjectifSpecifique.owner_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeCreateOSPModal">Annuler</button>
                        <button type="submit" class="btn" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }}; border-color: {{ $pilier->getHierarchicalColor(2) }};">
                            <i class="fas fa-save me-2"></i>Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif

    <!-- Modal éditer Objectif Spécifique -->
    @if($showEditObjectifSpecifiqueForm)
    <div class="modal fade show" style="display: block; z-index: 10000;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Modifier l'Objectif Spécifique
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="hideEditObjectifSpecifiqueForm"></button>
                </div>
                <form wire:submit.prevent="updateObjectifSpecifique">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" wire:model="editingObjectifSpecifique.code" required>
                            @error('editingObjectifSpecifique.code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Libellé *</label>
                            <input type="text" class="form-control" wire:model="editingObjectifSpecifique.libelle" required>
                            @error('editingObjectifSpecifique.libelle') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" wire:model="editingObjectifSpecifique.description" rows="3"></textarea>
                            @error('editingObjectifSpecifique.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Owner</label>
                            <select class="form-select" wire:model="editingObjectifSpecifique.owner_id">
                                <option value="">Sélectionner un owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('editingObjectifSpecifique.owner_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="hideEditObjectifSpecifiqueForm">Annuler</button>
                        <button type="submit" class="btn" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }}; border-color: {{ $pilier->getHierarchicalColor(2) }};">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif

    <!-- Modal Créer Action -->
    @if($showCreateActionForm)
    <div class="modal fade show" style="display: block; z-index: 10000;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>
                        Créer une Action
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeCreateActionModal"></button>
                </div>
                <form wire:submit.prevent="createAction">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" wire:model="newAction.code" required>
                            @error('newAction.code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Libellé *</label>
                            <input type="text" class="form-control" wire:model="newAction.libelle" required>
                            @error('newAction.libelle') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" wire:model="newAction.description" rows="3"></textarea>
                            @error('newAction.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Owner</label>
                            <select class="form-select" wire:model="newAction.owner_id">
                                <option value="">Sélectionner un owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('newAction.owner_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeCreateActionModal">Annuler</button>
                        <button type="submit" class="btn" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }}; border-color: {{ $pilier->getHierarchicalColor(3) }};">
                            <i class="fas fa-save me-2"></i>Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif

    <!-- Modal éditer Action -->
    @if($showEditActionForm)
    <div class="modal fade show" style="display: block; z-index: 10000;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }};">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Modifier l'Action
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="hideEditActionForm"></button>
                </div>
                <form wire:submit.prevent="updateAction">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" wire:model="editingAction.code" required>
                            @error('editingAction.code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Libellé *</label>
                            <input type="text" class="form-control" wire:model="editingAction.libelle" required>
                            @error('editingAction.libelle') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" wire:model="editingAction.description" rows="3"></textarea>
                            @error('editingAction.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Owner</label>
                            <select class="form-select" wire:model="editingAction.owner_id">
                                <option value="">Sélectionner un owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('editingAction.owner_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="hideEditActionForm">Annuler</button>
                        <button type="submit" class="btn" style="background: {{ $pilier->getHierarchicalColor(3) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(3)) }}; border-color: {{ $pilier->getHierarchicalColor(3) }};">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif

    <!-- Modal Créer Sous-Action -->
    @if($showCreateSousActionForm)
    <div class="modal fade show" style="display: block; z-index: 10000;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>
                        Créer une Sous-Action
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModalCreateSousAction"></button>
                </div>
                <form wire:submit.prevent="saveSousAction">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" wire:model="newSousAction.code" required>
                            @error('newSousAction.code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Libellé *</label>
                            <input type="text" class="form-control" wire:model="newSousAction.libelle" required>
                            @error('newSousAction.libelle') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" wire:model="newSousAction.description" rows="3"></textarea>
                            @error('newSousAction.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Sélection du type de sous-action -->
                        <div class="mb-3">
                            <label class="form-label">Type de sous-action *</label>
                            <select class="form-select" wire:model.live="newSousAction.type" required>
                                <option value="normal" selected>Normal</option>
                                <option value="projet">Projet</option>
                            </select>
                            @error('newSousAction.type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Owner</label>
                            <select class="form-select" wire:model="newSousAction.owner_id">
                                <option value="">Sélectionner un owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('newSousAction.owner_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Date d'échéance</label>
                            <input type="date" class="form-control" wire:model="newSousAction.date_echeance">
                            @error('newSousAction.date_echeance') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Champs conditionnels selon le type -->
                        @if($newSousAction['type'] === 'normal')
                            <div class="mb-3">
                                <label class="form-label">Taux d'avancement initial *</label>
                                <input type="number" class="form-control" wire:model="newSousAction.taux_avancement" min="0" max="100" value="0" required>
                                @error('newSousAction.taux_avancement') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        @elseif($newSousAction['type'] === 'projet')
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Type Projet :</strong> Cette sous-action aura un calendrier d'activités, un diagramme de Gantt et des notifications automatiques.
                                <br><small>Le taux d'avancement sera calculé automatiquement Ã  partir des activités.</small>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModalCreateSousAction">Annuler</button>
                        
                        <!-- Bouton de test pour diagnostiquer -->
                        <button type="button" class="btn btn-info me-2" wire:click="testSaveSousAction">
                            <i class="fas fa-vial me-2"></i>Test
                        </button>
                        
                        <button type="submit" class="btn" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}; border-color: {{ $pilier->getHierarchicalColor(4) }};">
                            <i class="fas fa-save me-2"></i>Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif

    <!-- Modal éditer Sous-Action -->
    @if($showEditSousActionForm)
    <div class="modal fade show" style="display: block; z-index: 10000;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }};">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Modifier la Sous-Action
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="hideEditSousActionForm"></button>
                </div>
                <form wire:submit.prevent="updateSousAction">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Code *</label>
                            <input type="text" class="form-control" wire:model="editingSousAction.code" required>
                            @error('editingSousAction.code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Libellé *</label>
                            <input type="text" class="form-control" wire:model="editingSousAction.libelle" required>
                            @error('editingSousAction.libelle') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" wire:model="editingSousAction.description" rows="3"></textarea>
                            @error('editingSousAction.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Type de sous-action (lecture seule en édition) -->
                        <div class="mb-3">
                            <label class="form-label">Type de sous-action</label>
                            <div class="form-control-plaintext">
                                @if($editingSousAction['type'] === 'normal')
                                    <span class="badge bg-primary">
                                        <i class="fas fa-tasks me-1"></i>Normal
                                    </span>
                                @elseif($editingSousAction['type'] === 'projet')
                                    <span class="badge bg-success">
                                        <i class="fas fa-project-diagram me-1"></i>Projet
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Owner</label>
                            <select class="form-select" wire:model="editingSousAction.owner_id">
                                <option value="">Sélectionner un owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('editingSousAction.owner_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Date d'échéance</label>
                            <input type="date" class="form-control" wire:model="editingSousAction.date_echeance">
                            @error('editingSousAction.date_echeance') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Champs conditionnels selon le type -->
                        @if($editingSousAction['type'] === 'normal')
                            <div class="mb-3">
                                <label class="form-label">Taux d'avancement *</label>
                                <input type="number" class="form-control" wire:model="editingSousAction.taux_avancement" min="0" max="100" required>
                                @error('editingSousAction.taux_avancement') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        @elseif($editingSousAction['type'] === 'projet')
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Type Projet :</strong> Le taux d'avancement est calculé automatiquement Ã  partir des activités.
                                <br><small>Utilisez le calendrier d'activités pour gérer la progression.</small>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="hideEditSousActionForm">Annuler</button>
                        <button type="submit" class="btn" style="background: {{ $pilier->getHierarchicalColor(4) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(4)) }}; border-color: {{ $pilier->getHierarchicalColor(4) }};">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
                <!-- Script pour capturer les logs de débogage et gérer les mises Ã  jour en temps réel -->
                <script>
                    document.addEventListener('livewire:init', () => {
                        // Capture des logs de débogage
                        Livewire.on('console.log', (message, data) => {
                            console.log(message, data);
                        });

                                    // Gestion des mises Ã  jour en temps réel des taux
            Livewire.on('tauxMisAJour', (data) => {
                console.log('ðŸ"„ événement tauxMisAJour reÃ§u:', data);
                
                if (data.cascadeComplete) {
                    // Mise Ã  jour instantanée de tous les pourcentages
                    updateAllPercentages(data);
                    
                    // Notification de succès
                    showSuccessNotification(data);
                }
            });
                    });

                            // FONCTION - Mise Ã  jour instantanée de tous les pourcentages
        function updateAllPercentages(data) {
            console.log('ðŸ"„ Mise Ã  jour instantanée de tous les pourcentages');
            
            // Mettre Ã  jour le pourcentage de l'action parente
            if (data.tauxAction !== null) {
                updateProgressBar('action', data.tauxAction);
            }

            // Mettre Ã  jour le pourcentage de l'Objectif Spécifique
            if (data.tauxOS !== null) {
                updateProgressBar('objectif-specifique', data.tauxOS);
            }

            // Mettre Ã  jour le pourcentage de l'Objectif Stratégique

            if (data.tauxOST !== null) {
                updateProgressBar('objectif-strategique', data.tauxOST);
            }

            // Mettre Ã  jour le pourcentage du pilier
            if (data.tauxPilier !== null) {
                updateProgressBar('pilier', data.tauxPilier);
            }

            console.log('âœ… Tous les pourcentages mis Ã  jour instantanément');
        }

        // FONCTION - Mise Ã  jour d'une barre de progression spécifique
        function updateProgressBar(type, newPercentage) {
            const progressBar = document.querySelector(`[data-progress-type="${type}"] .progress-bar`);
            if (progressBar) {
                progressBar.style.width = `${newPercentage}%`;
                progressBar.textContent = `${newPercentage}%`;
                
                // Mettre Ã  jour aussi le texte du pourcentage
                const percentageText = progressBar.closest('.progress-container').querySelector('.percentage-text');
                if (percentageText) {
                    percentageText.textContent = `${newPercentage}%`;
                }
            }
        }

        // Fonction pour mettre Ã  jour les barres de progression (ancienne version)
        function updateProgressBars(data) {
            // Mise Ã  jour de la barre de progression de l'action
            const actionProgress = document.querySelector('.hierarchy-impact-item:nth-child(1) .progress-bar');
            if (actionProgress) {
                actionProgress.style.width = data.actionTaux + '%';
                actionProgress.parentElement.nextElementSibling.textContent = data.actionTaux + '%';
            }

            // Mise Ã  jour de la barre de progression de l'Objectif Spécifique
            const osProgress = document.querySelector('.hierarchy-impact-item:nth-child(2) .progress-bar');
            if (osProgress) {
                osProgress.style.width = data.objectifSpecifiqueTaux + '%';
                osProgress.parentElement.nextElementSibling.textContent = data.objectifSpecifiqueTaux + '%';
            }

            // Mise Ã  jour de la barre de progression de l'Objectif Stratégique

            const ostProgress = document.querySelector('.hierarchy-impact-item:nth-child(3) .progress-bar');
            if (ostProgress) {
                ostProgress.style.width = data.objectifStrategiqueTaux + '%';
                ostProgress.parentElement.nextElementSibling.textContent = data.objectifStrategiqueTaux + '%';
            }

            // Mise Ã  jour de la barre de progression du pilier
            const pilierProgress = document.querySelector('.hierarchy-impact-item:nth-child(4) .progress-bar');
            if (pilierProgress) {
                pilierProgress.style.width = data.pilierTaux + '%';
                pilierProgress.parentElement.nextElementSibling.textContent = data.pilierTaux + '%';
            }

            // Mise Ã  jour des statistiques
            const actionStat = document.querySelector('.stat-item:nth-child(2) h4');
            if (actionStat) {
                actionStat.textContent = data.actionTaux + '%';
            }
        }

                            // Fonction pour afficher une notification de succès
        function showSuccessNotification(data) {
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="toast show" role="alert">
                    <div class="toast-header" style="background: {{ $pilier->getHierarchicalColor(0) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(0)) }};">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong class="me-auto">âœ… Mise Ã  jour réussie !</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        <p class="mb-1">Sous-action mise Ã  jour : <strong>${data.nouveauTaux}%</strong></p>
                        <small class="text-muted">
                            ðŸš€ Tous les taux parents ont été recalculés automatiquement !
                        </small>
                    </div>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);
        }

        // Fonction pour afficher une notification de mise Ã  jour (ancienne version)
        function showUpdateNotification(data) {
            // Créer une notification toast personnalisée
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="toast show" role="alert">
                    <div class="toast-header" style="background: {{ $pilier->getHierarchicalColor(0) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(0)) }};">
                        <i class="fas fa-sync-alt me-2"></i>
                        <strong class="me-auto">Mise Ã  jour en temps réel</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        <p class="mb-1">Sous-action mise Ã  jour : <strong>${data.newTaux}%</strong></p>
                        <small class="text-muted">
                            Impact sur la hiérarchie : Action (${data.actionTaux}%), OS (${data.objectifSpecifiqueTaux}%), 
                            OSt (${data.objectifStrategiqueTaux}%), Pilier (${data.pilierTaux}%)
                        </small>
                    </div>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Supprimer la notification après 5 secondes
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);
        }

                    // Animation des barres de progression au chargement
                    document.addEventListener('DOMContentLoaded', function() {
                        const progressBars = document.querySelectorAll('.progress-bar');
                        progressBars.forEach(bar => {
                            const width = bar.style.width;
                            bar.style.width = '0%';
                            setTimeout(() => {
                                bar.style.width = width;
                            }, 300);
                        });
                    });
                </script>
            </div>
        </div>
    </div>

    <!-- ======================================== -->
    <!-- MODALS POUR LA GESTION DES ACTIVITéS -->
    <!-- ======================================== -->

    <!-- Modal de création d'activité -->
    @if($showCreateActivityModal)
        <div class="modal fade show d-block" style="z-index: 10000;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle me-2 style="color: {{ $pilier->getHierarchicalColor(1) }};"></i>
                            Nouvelle activité
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeCreateActivityModal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="activity-form" wire:submit.prevent="saveActivity">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="titre" class="form-label">Titre de l'activité *</label>
                                        <input type="text" class="form-control" id="titre" wire:model="newActivity.titre" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="priorite" class="form-label">Priorité *</label>
                                        <select class="form-select" id="priorite" wire:model="newActivity.priorite" required>
                                            <option value="basse">Basse</option>
                                            <option value="moyenne">Moyenne</option>
                                            <option value="haute">Haute</option>
                                            <option value="critique">Critique</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" wire:model="newActivity.description" rows="3"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_debut" class="form-label">Date de début *</label>
                                        <input type="datetime-local" class="form-control" id="date_debut" wire:model="newActivity.date_debut" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_fin" class="form-label">Date de fin *</label>
                                        <input type="datetime-local" class="form-control" id="date_fin" wire:model="newActivity.date_fin" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="duree_estimee" class="form-label">Durée estimée (heures) *</label>
                                        <input type="number" class="form-control" id="duree_estimee" wire:model="newActivity.duree_estimee" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="statut" class="form-label">Statut *</label>
                                        <select class="form-select" id="statut" wire:model="newActivity.statut" required>
                                            <option value="a_faire">Ã€ faire</option>
                                            <option value="en_cours">En cours</option>
                                            <option value="termine">Terminé</option>
                                            <option value="bloque">Bloqué</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="taux_avancement" class="form-label">Taux d'avancement *</label>
                                        <input type="number" class="form-control" id="taux_avancement" wire:model="newActivity.taux_avancement" min="0" max="100" step="5" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="owner_id" class="form-label">Assigné Ã </label>
                                        <select class="form-select" id="owner_id" wire:model="newActivity.owner_id">
                                            <option value="">Sélectionner un utilisateur</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tags" class="form-label">Tags</label>
                                        <input type="text" class="form-control" id="tags" wire:model="newActivity.tags" placeholder="tag1, tag2, tag3">
                                        <small class="form-text text-muted">Séparez les tags par des virgules</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" wire:model="newActivity.notes" rows="2"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeCreateActivityModal">Annuler</button>
                        <button type="submit" class="btn btn-primary" form="activity-form">
                            <i class="fas fa-save me-1"></i>Créer l'activité
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal d'édition d'activité -->
    @if($showEditActivityModal && $editingActivity)
        <div class="modal fade show d-block" style="z-index: 10000;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2 style="color: {{ $pilier->getHierarchicalColor(1) }};"></i>
                            Modifier l'activité
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeEditActivityModal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-activity-form" wire:submit.prevent="updateActivity">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="edit_titre" class="form-label">Titre de l'activité *</label>
                                        <input type="text" class="form-control" id="edit_titre" wire:model="editingActivity.titre" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="edit_priorite" class="form-label">Priorité *</label>
                                        <select class="form-select" id="edit_priorite" wire:model="editingActivity.priorite" required>
                                            <option value="basse">Basse</option>
                                            <option value="moyenne">Moyenne</option>
                                            <option value="haute">Haute</option>
                                            <option value="critique">Critique</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_description" class="form-label">Description</label>
                                <textarea class="form-control" id="edit_description" wire:model="editingActivity.description" rows="3"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_date_debut" class="form-label">Date de début *</label>
                                        <input type="datetime-local" class="form-control" id="edit_date_debut" wire:model="editingActivity.date_debut" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_date_fin" class="form-label">Date de fin *</label>
                                        <input type="datetime-local" class="form-control" id="edit_date_fin" wire:model="editingActivity.date_fin" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="edit_duree_estimee" class="form-label">Durée estimée (heures) *</label>
                                        <input type="number" class="form-control" id="edit_duree_estimee" wire:model="editingActivity.duree_estimee" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="edit_statut" class="form-label">Statut *</label>
                                            <select class="form-select" id="edit_statut" wire:model="editingActivity.statut" required>
                                                <option value="a_faire">Ã€ faire</option>
                                                <option value="en_cours">En cours</option>
                                                <option value="termine">Terminé</option>
                                                <option value="bloque">Bloqué</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="edit_taux_avancement" class="form-label">Taux d'avancement *</label>
                                        <input type="number" class="form-control" id="edit_titre" wire:model="editingActivity.taux_avancement" min="0" max="100" step="5" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_owner_id" class="form-label">Assigné à </label>
                                        <select class="form-select" id="edit_owner_id" wire:model="editingActivity.owner_id">
                                            <option value="">Sélectionner un utilisateur</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_tags" class="form-label">Tags</label>
                                        <input type="text" class="form-control" id="edit_tags" wire:model="editingActivity.tags" placeholder="tag1, tag2, tag3">
                                        <small class="form-text text-muted">Séparez les tags par des virgules</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="edit_notes" wire:model="editingActivity.notes" rows="2"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeEditActivityModal">Annuler</button>
                        <button type="submit" class="btn btn-primary" form="edit-activity-form">
                            <i class="fas fa-save me-1"></i>Mettre Ã  jour
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif
</div> 



