<!-- Vue détail du pilier -->
@if(!$pilier)
    <div class="alert alert-warning m-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Aucun pilier sélectionné</strong><br>
        Veuillez sélectionner un pilier pour afficher ses détails.
    </div>
@else
<div class="pilier-detail-container">
    <!-- Informations du pilier -->
    <div class="pilier-info bg-white p-4 border-bottom">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    <div class="pilier-icon me-3" style="background: {{ $pilier->getHierarchicalColor(1) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(1)) }}; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">{{ $pilier->libelle }}</h3>
                        <p class="text-muted mb-0">
                            <strong>Code:</strong> {{ $pilier->code }} | 
                            <strong>Propriétaire:</strong> {{ $pilier->owner ? $pilier->owner->name : 'Non assigné' }}
                        </p>
                        <p class="text-muted mb-0">
                            @php
                                $maxEcheancePilier = $pilier->getMaxEcheanceDate();
                            @endphp
                            @if($maxEcheancePilier)
                                <strong>Échéance 
:</strong> 
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($maxEcheancePilier)->format('d/m/Y') }}
                                </span>
                            @else
                                <strong>Échéance 
:</strong> 
                                <span class="badge bg-light text-muted">
                                    <i class="fas fa-calendar-times me-1"></i>
                                    Aucune échéance
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
                
                @if($pilier->description)
                    <p class="text-muted">{{ $pilier->description }}</p>
                @endif
            </div>
            
            <div class="col-md-4 text-end">
                <div class="progress-circle pilier-level mb-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="progress-ring" style="width: 80px; height: 80px; position: relative;">
                            <svg width="80" height="80" viewBox="0 0 80 80">
                                <circle cx="40" cy="40" r="35" stroke="#e9ecef" stroke-width="6" fill="none"/>
                                <circle cx="40" cy="40" r="35" stroke="{{ $pilier->getHierarchicalColor(1) }}" stroke-width="6" fill="none" 
                                        stroke-dasharray="{{ 2 * pi() * 35 }}" 
                                        stroke-dashoffset="{{ 2 * pi() * 35 * (1 - $pilier->getTauxAvancementAttribute() / 100) }}"
                                        transform="rotate(-90 40 40)"/>
                            </svg>
                            <div class="progress-text" style="position: absolute !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; text-align: center !important; width: 100% !important; pointer-events: none !important; z-index: 1000 !important;">
                                <span class="progress-percentage" style="text-align: center !important; display: block !important; width: 100% !important; font-weight: 700 !important; color: #2c3e50 !important; font-size: 18px !important; line-height: 1.2 !important; text-shadow: 0 2px 4px rgba(0,0,0,0.1) !important; letter-spacing: -0.5px !important;">{{ number_format($pilier->getTauxAvancementAttribute(), 1) }}%</span>
                            </div>
                        </div>
                        <div class="progress-label">Progression globale</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des objectifs stratégiques -->
    <div class="objectifs-strategiques-container p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-bullseye me-2" style="color: {{ $pilier->getHierarchicalColor(2) }};"></i>
                Objectifs Stratégiques
            </h4>
            
            @if($canCreateObjectifStrategique)
                <button type="button" 
                        class="btn btn-primary" 
                        style="background: {{ $pilier->getHierarchicalColor(2) }}; border-color: {{ $pilier->getHierarchicalColor(2) }};"
                        wire:click="openCreateOSModal">
                    <i class="fas fa-plus me-2"></i>Créer un Objectif Stratégique
                </button>
            @endif
        </div>

        @if($pilier->objectifsStrategiques->count() > 0)
            <div class="table-responsive objectifs-strategiques-table">
                <table class="table table-hover table-striped">
                    <thead style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                        <tr>
                            <th class="col-code" style="width: 10%;">
                                <i class="fas fa-code me-1"></i><span>Code</span>
                            </th>
                            <th class="col-libelle" style="width: 20%;">
                                <i class="fas fa-bullseye me-1"></i><span>Libellé</span>
                            </th>
                            <th class="col-progression" style="width: 15%;">
                                <i class="fas fa-percentage me-1"></i><span>Progression</span>
                            </th>
                            <th class="col-echeance" style="width: 15%;">
                                <i class="fas fa-calendar-alt me-1"></i><span>Date d'échéance</span>
                            </th>
                            <th class="col-proprietaire" style="width: 15%;">
                                <i class="fas fa-user me-1"></i><span>Propriétaire</span>
                            </th>
                            <th class="col-osp" style="width: 10%;">
                                <i class="fas fa-list me-1"></i><span>Objectifs Spécifiques</span>
                            </th>
                            <th class="col-actions" style="width: 15%;">
                                <i class="fas fa-cogs me-1"></i><span>Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pilier->objectifsStrategiques as $objectifStrategique)
                            <tr>
                                <td>
                                    <span class="badge fw-bold" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                                        {{ $pilier->code }}.{{ $objectifStrategique->code }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-dark">{{ $objectifStrategique->libelle }}</strong>
                                        @if($objectifStrategique->description)
                                            <br><small class="text-muted">{{ Str::limit($objectifStrategique->description, 80) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-start">
                                        <div class="d-flex justify-content-between align-items-center w-100 mb-1">
                                            <small class="text-muted">Progression</small>
                                            <span class="badge fw-bold" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }};">
                                                {{ number_format($objectifStrategique->taux_avancement, 2) }}%
                                            </span>
                                        </div>
                                        <div class="progress mb-2 progress-compact" style="width: 100%; background: #e9ecef;">
                                            <div class="progress-bar" 
                                                 style="width: {{ $objectifStrategique->taux_avancement }}%; background: {{ $pilier->getHierarchicalColor(2) }};"
                                                 role="progressbar" 
                                                 aria-valuenow="{{ $objectifStrategique->taux_avancement }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $maxEcheanceOS = $objectifStrategique->getMaxEcheanceDate();
                                    @endphp
                                    @if($maxEcheanceOS)
                                        <span class="badge bg-warning text-dark" title="Date d'échéance maximale des sous-actions">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ \Carbon\Carbon::parse($maxEcheanceOS)->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted">
                                            <i class="fas fa-calendar-times me-1"></i>
                                            Aucune échéance
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($objectifStrategique->owner)
                                        <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }}">
                                            <i class="fas fa-user me-1"></i>
                                            {{ Str::limit($objectifStrategique->owner->name, 15) }}
                                        </span>
                                    @else
                                        <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }}">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-enhanced" style="background: {{ $pilier->getHierarchicalColor(2) }}; color: {{ $pilier->getTextColor($pilier->getHierarchicalColor(2)) }}">
                                        <i class="fas fa-list me-1"></i>
                                        {{ $objectifStrategique->objectifsSpecifiques->count() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- Bouton Voir -->
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary btn-action" 
                                                title="Voir les détails"
                                                wire:click="naviguerVersObjectifStrategique({{ $objectifStrategique->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        @if($canEditObjectifStrategique($objectifStrategique))
                                            <!-- Bouton Modifier -->
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-warning btn-action" 
                                                    title="Modifier"
                                                    wire:click="setObjectifStrategiqueToEdit({{ $objectifStrategique->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <!-- Bouton Supprimer -->
                                            @if($canDeleteObjectifStrategique($objectifStrategique))
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger btn-action" 
                                                        title="Supprimer"
                                                        wire:click="deleteObjectifStrategique({{ $objectifStrategique->id }})"
                                                        onclick="if(!confirm('Êtes-vous sûr de vouloir supprimer cet objectif stratégique ?')) return false;">
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
                <i class="fas fa-bullseye fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucun objectif stratégique</h5>
                <p class="text-muted">Commencez par créer votre premier objectif stratégique</p>
                
                @if($canCreateObjectifStrategique)
                    <button type="button" 
                            class="btn btn-primary" 
                            style="background: {{ $pilier->getHierarchicalColor(2) }}; border-color: {{ $pilier->getHierarchicalColor(2) }};"
                            wire:click="openCreateOSModal">
                        <i class="fas fa-plus me-2"></i>Créer le premier objectif
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Inclure les modals pour les objectifs stratégiques -->
@include('livewire.pilier-hierarchique-v2.components.modals')

<style>
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

.progress-compact {
    height: 8px;
    border-radius: 4px;
}

.badge-enhanced {
    padding: 0.5em 0.75em;
    font-size: 0.875em;
}

.btn-action {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endif
