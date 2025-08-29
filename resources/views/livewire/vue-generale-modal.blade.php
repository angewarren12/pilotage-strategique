<div>
    @if($showModal)
    <div class="modal fade show" style="display: block; z-index: 9999;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <!-- Header du Modal -->
                <div class="modal-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-chart-line fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0">
                                <strong>Vue Générale Hiérarchique</strong>
                            </h5>
                            <small class="text-white-75">Vue d'ensemble interactive de tous les piliers et leurs éléments</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>

                <!-- Contenu du Modal -->
                <div class="modal-body p-0">
                    <!-- Barre d'outils interactive -->
                    <div class="p-3 bg-light border-bottom">
                        <div class="row align-items-center">
                            <!-- Recherche -->
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           placeholder="Rechercher par pilier, objectif stratégique..."
                                           wire:model.live="searchTerm"
                                           id="searchInput">
                                </div>
                            </div>
                            
                            <!-- Contrôles de zoom -->
                            <div class="col-md-3">
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="zoomOut()" title="Zoom arrière">
                                        <i class="fas fa-search-minus"></i>
                                    </button>
                                    <span class="badge bg-primary" id="zoomLevel">100%</span>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="zoomIn()" title="Zoom avant">
                                        <i class="fas fa-search-plus"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetZoom()" title="Zoom par défaut">
                                        <i class="fas fa-expand-arrows-alt"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Filtres avancés -->
                            <div class="col-md-3">
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-filter me-1"></i>Filtres
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="filterByProgress('all')">Tous les progrès</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="filterByProgress('completed')">Terminés (100%)</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="filterByProgress('in-progress')">En cours (25-75%)</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="filterByProgress('not-started')">Non commencés (0%)</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="filterByOwner()">Par propriétaire</a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="col-md-2 text-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="exportToExcel()" title="Exporter Excel">
                                        <i class="fas fa-file-excel"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleFullscreen()" title="Plein écran">
                                        <i class="fas fa-expand"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="clearFilters" title="Effacer filtres">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Statistiques en temps réel -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex gap-4">
                                        <div class="text-center">
                                            <div class="h5 mb-0 text-primary" id="totalPiliers">{{ $piliers->count() }}</div>
                                            <small class="text-muted">Piliers</small>
                                        </div>
                                        <div class="text-center">
                                            <div class="h5 mb-0 text-info" id="totalOS">{{ $piliers->sum(function($p) { return $p->objectifsStrategiques->count(); }) }}</div>
                                            <small class="text-muted">Objectifs Stratégiques</small>
                                        </div>
                                        <div class="text-center">
                                            <div class="h5 mb-0 text-warning" id="totalOSpec">{{ $piliers->sum(function($p) { return $p->objectifsStrategiques->sum(function($os) { return $os->objectifsSpecifiques->count(); }); }) }}</div>
                                            <small class="text-muted">Objectifs Spécifiques</small>
                                        </div>
                                        <div class="text-center">
                                            <div class="h5 mb-0 text-success" id="totalActions">{{ $piliers->sum(function($p) { return $p->objectifsStrategiques->sum(function($os) { return $os->objectifsSpecifiques->sum(function($ospec) { return $ospec->actions->count(); }); }); }) }}</div>
                                            <small class="text-muted">Actions</small>
                                        </div>
                                        <div class="text-center">
                                            <div class="h5 mb-0 text-secondary" id="totalSousActions">{{ $piliers->sum(function($p) { return $p->objectifsStrategiques->sum(function($os) { return $os->objectifsSpecifiques->sum(function($ospec) { return $ospec->actions->sum(function($action) { return $action->sousActions->count(); }); }); }); }) }}</div>
                                            <small class="text-muted">Sous-actions</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="progress" style="width: 200px; height: 8px;">
                                            @php
                                                $globalProgress = $piliers->avg('taux_avancement');
                                            @endphp
                                            <div class="progress-bar bg-success" style="width: {{ $globalProgress }}%"></div>
                                        </div>
                                        <small class="text-muted">Progression globale: {{ number_format($globalProgress, 0) }}%</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau hiérarchique avec zoom -->
                    <div class="table-responsive vue-generale-table" style="max-height: 70vh; overflow-y: auto;" id="vueGeneraleTable">
                        @if($isLoading)
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <p class="mt-2">Chargement de la vue générale...</p>
                            </div>
                        @else
                            <table class="table table-bordered table-hover mb-0" id="hierarchicalTable">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th style="width: 15%;" class="text-center sortable" onclick="sortTable(0)">
                                            <i class="fas fa-layer-group me-1"></i>
                                            PILIER
                                            <i class="fas fa-sort ms-1"></i>
                                        </th>
                                        <th style="width: 15%;" class="text-center sortable" onclick="sortTable(1)">
                                            <i class="fas fa-bullseye me-1"></i>
                                            COMITÉ STRATÉGIQUE
                                            <i class="fas fa-sort ms-1"></i>
                                        </th>
                                        <th style="width: 15%;" class="text-center sortable" onclick="sortTable(2)">
                                            <i class="fas fa-compass me-1"></i>
                                            COMITÉ DE PILOTAGE
                                            <i class="fas fa-sort ms-1"></i>
                                        </th>
                                        <th style="width: 15%;" class="text-center sortable" onclick="sortTable(3)">
                                            <i class="fas fa-tasks me-1"></i>
                                            ACTIONS
                                            <i class="fas fa-sort ms-1"></i>
                                        </th>
                                        <th style="width: 15%;" class="text-center sortable" onclick="sortTable(4)">
                                            <i class="fas fa-list-ul me-1"></i>
                                            SOUS-ACTIONS
                                            <i class="fas fa-sort ms-1"></i>
                                        </th>
                                        <th style="width: 25%;" class="text-center sortable" onclick="sortTable(5)">
                                            <i class="fas fa-calendar-check me-1"></i>
                                            EXÉCUTION
                                            <i class="fas fa-sort ms-1"></i>
                                        </th>
                                    </tr>
                                    <tr class="table-secondary">
                                        <th class="text-center">Code | Libellé | % | Owner</th>
                                        <th class="text-center">Code | Libellé | % | Owner</th>
                                        <th class="text-center">Code | Libellé | % | Owner</th>
                                        <th class="text-center">Code | Libellé | % | Owner</th>
                                        <th class="text-center">Code | Libellé | % | Owner</th>
                                        <th class="text-center">
                                            <div class="row">
                                                <div class="col-3">Échéance</div>
                                                <div class="col-3">Date Réalisation</div>
                                                <div class="col-3">Écart</div>
                                                <div class="col-3">Progression</div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($piliers as $pilier)
                                        @php
                                            // Calculer le nombre total de lignes pour ce pilier
                                            $pilierRowspan = 0;
                                            foreach($pilier->objectifsStrategiques as $os) {
                                                foreach($os->objectifsSpecifiques as $ospec) {
                                                    foreach($ospec->actions as $action) {
                                                        $pilierRowspan += max($action->sousActions->count(), 1);
                                                    }
                                                }
                                            }
                                            $pilierRowspan = max($pilierRowspan, 1);
                                        @endphp

                                        @if($pilier->objectifsStrategiques->count() > 0)
                                            @foreach($pilier->objectifsStrategiques as $objectifStrategique)
                                                @php
                                                    // Calculer le nombre de lignes pour cet objectif stratégique
                                                    $osRowspan = 0;
                                                    foreach($objectifStrategique->objectifsSpecifiques as $ospec) {
                                                        foreach($ospec->actions as $action) {
                                                            $osRowspan += max($action->sousActions->count(), 1);
                                                        }
                                                    }
                                                    $osRowspan = max($osRowspan, 1);
                                                @endphp

                                                @foreach($objectifStrategique->objectifsSpecifiques as $objectifSpecifique)
                                                    @php
                                                        // Calculer le nombre de lignes pour cet objectif spécifique
                                                        $ospecRowspan = 0;
                                                        foreach($objectifSpecifique->actions as $action) {
                                                            $ospecRowspan += max($action->sousActions->count(), 1);
                                                        }
                                                        $ospecRowspan = max($ospecRowspan, 1);
                                                    @endphp

                                                    @foreach($objectifSpecifique->actions as $action)
                                                        @php
                                                            $actionRowspan = max($action->sousActions->count(), 1);
                                                        @endphp

                                                        @if($action->sousActions->count() > 0)
                                                            @foreach($action->sousActions as $sousAction)
                                                                <tr class="hierarchical-row" data-pilier="{{ $pilier->code }}" data-os="{{ $objectifStrategique->code }}" data-ospec="{{ $objectifSpecifique->code }}" data-action="{{ $action->code }}" data-sousaction="{{ $sousAction->code }}" data-progress="{{ $sousAction->taux_avancement }}">
                                                                    @if($loop->first && $action === $objectifSpecifique->actions->first() && $objectifSpecifique === $objectifStrategique->objectifsSpecifiques->first() && $objectifStrategique === $pilier->objectifsStrategiques->first())
                                                                        <td class="hierarchical-cell" rowspan="{{ $pilierRowspan }}" data-level="pilier" 
                                                                            style="background-color: {{ $pilier->color }}; color: white; border-left: 4px solid {{ $pilier->color }};">
                                                                            <div class="fw-bold">{{ $pilier->code }}</div>
                                                                            <div>{{ $pilier->libelle }}</div>
                                                                            <div class="progress mt-1" style="height: 6px; background: rgba(255,255,255,0.3);">
                                                                                <div class="progress-bar bg-white" 
                                                                                     style="width: {{ $pilier->taux_avancement }}%"></div>
                                                                            </div>
                                                                            <small class="text-white-75">{{ number_format($pilier->taux_avancement, 0) }}%</small>
                                                                            <div class="mt-1">
                                                                                <span class="badge bg-white text-dark">{{ $pilier->owner->name ?? 'Non assigné' }}</span>
                                                                            </div>
                                                                        </td>
                                                                    @endif

                                                                    @if($loop->first && $action === $objectifSpecifique->actions->first() && $objectifSpecifique === $objectifStrategique->objectifsSpecifiques->first())
                                                                        <td class="hierarchical-cell" rowspan="{{ $osRowspan }}" data-level="os"
                                                                            style="background-color: {{ $pilier->getHierarchicalColor(1) }}; color: white; border-left: 4px solid {{ $pilier->color }};">
                                                                            <div class="fw-bold">{{ $pilier->code }}.{{ $objectifStrategique->code }}</div>
                                                                            <div>{{ $objectifStrategique->libelle }}</div>
                                                                            <div class="progress mt-1" style="height: 6px; background: rgba(255,255,255,0.3);">
                                                                                <div class="progress-bar bg-white" 
                                                                                     style="width: {{ $objectifStrategique->taux_avancement }}%"></div>
                                                                            </div>
                                                                            <small class="text-white-75">{{ number_format($objectifStrategique->taux_avancement, 0) }}%</small>
                                                                            <div class="mt-1">
                                                                                <span class="badge bg-white text-dark">{{ $objectifStrategique->owner->name ?? 'Non assigné' }}</span>
                                                                            </div>
                                                                        </td>
                                                                    @endif

                                                                    @if($loop->first && $action === $objectifSpecifique->actions->first())
                                                                        <td class="hierarchical-cell" rowspan="{{ $ospecRowspan }}" data-level="ospec"
                                                                            style="background-color: {{ $pilier->getHierarchicalColor(2) }}; color: white; border-left: 4px solid {{ $pilier->color }};">
                                                                            <div class="fw-bold">{{ $pilier->code }}.{{ $objectifStrategique->code }}.{{ $objectifSpecifique->code }}</div>
                                                                            <div>{{ $objectifSpecifique->libelle }}</div>
                                                                            <div class="progress mt-1" style="height: 6px; background: rgba(255,255,255,0.3);">
                                                                                <div class="progress-bar bg-white" 
                                                                                     style="width: {{ $objectifSpecifique->taux_avancement }}%"></div>
                                                                            </div>
                                                                            <small class="text-white-75">{{ number_format($objectifSpecifique->taux_avancement, 0) }}%</small>
                                                                            <div class="mt-1">
                                                                                <span class="badge bg-white text-dark">{{ $objectifSpecifique->owner->name ?? 'Non assigné' }}</span>
                                                                            </div>
                                                                        </td>
                                                                    @endif

                                                                    @if($loop->first)
                                                                        <td class="hierarchical-cell" rowspan="{{ $actionRowspan }}" data-level="action" 
                                                                            style="cursor: pointer; background-color: {{ $pilier->getHierarchicalColor(3) }}; color: white; border-left: 4px solid {{ $pilier->color }};" 
                                                                            onclick="openActionComments({{ $action->id }})"
                                                                            title="Cliquer pour ouvrir la discussion">
                                                                            <div class="fw-bold">{{ $pilier->code }}.{{ $objectifStrategique->code }}.{{ $objectifSpecifique->code }}.{{ $action->code }}</div>
                                                                            <div>{{ $action->libelle }}</div>
                                                                            <div class="progress mt-1" style="height: 6px; background: rgba(255,255,255,0.3);">
                                                                                <div class="progress-bar bg-white" 
                                                                                     style="width: {{ $action->taux_avancement }}%"></div>
                                                                            </div>
                                                                            <small class="text-white-75">{{ number_format($action->taux_avancement, 0) }}%</small>
                                                                            <div class="mt-1 d-flex align-items-center justify-content-between">
                                                                                <span class="badge bg-white text-dark">{{ $action->owner->name ?? 'Non assigné' }}</span>
                                                                                <i class="fas fa-comments text-white" style="font-size: 0.8em;"></i>
                                                                            </div>
                                                                        </td>
                                                                    @endif

                                                                    <td class="hierarchical-cell" data-level="sousaction"
                                                                        style="background-color: {{ $pilier->getHierarchicalColor(4) }}; color: white; border-left: 4px solid {{ $pilier->color }};">
                                                                        <div class="fw-bold">{{ $pilier->code }}.{{ $objectifStrategique->code }}.{{ $objectifSpecifique->code }}.{{ $action->code }}.{{ $sousAction->code }}</div>
                                                                        <div>{{ $sousAction->libelle }}</div>
                                                                        <div class="progress mt-1" style="height: 6px; background: rgba(255,255,255,0.3);">
                                                                            <div class="progress-bar bg-white" 
                                                                                 style="width: {{ $sousAction->taux_avancement }}%"></div>
                                                                        </div>
                                                                        <small class="text-white-75">{{ number_format($sousAction->taux_avancement, 0) }}%</small>
                                                                        <div class="mt-1">
                                                                            <span class="badge bg-white text-dark">{{ $sousAction->owner->name ?? 'Non assigné' }}</span>
                                                                        </div>
                                                                    </td>

                                                                    <td>
                                                                        <div class="row">
                                                                            <div class="col-3">
                                                                                <small class="text-muted">Échéance</small>
                                                                                <div>{{ $sousAction->date_echeance ? Carbon\Carbon::parse($sousAction->date_echeance)->format('d/m/Y') : 'Non définie' }}</div>
                                                                            </div>
                                                                            <div class="col-3">
                                                                                <small class="text-muted">Date Réalisation</small>
                                                                                <div>{{ $sousAction->date_realisation ? Carbon\Carbon::parse($sousAction->date_realisation)->format('d/m/Y') : '-' }}</div>
                                                                            </div>
                                                                            <div class="col-3">
                                                                                <small class="text-muted">Écart</small>
                                                                                <div>
                                                                                    @php
                                                                                        $ecart = $this->calculateEcart($sousAction->date_echeance, $sousAction->date_realisation);
                                                                                    @endphp
                                                                                    @if($ecart)
                                                                                        <span class="badge bg-{{ $sousAction->date_realisation ? 'success' : 'warning' }}">
                                                                                            {{ $ecart }}
                                                                                        </span>
                                                                                    @else
                                                                                        <span class="badge bg-secondary">-</span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">
                                                                                <small class="text-muted">Progression</small>
                                                                                <div>
                                                                                    <span class="badge bg-primary">{{ number_format($sousAction->taux_avancement, 0) }}%</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr class="hierarchical-row" data-pilier="{{ $pilier->code }}" data-os="{{ $objectifStrategique->code }}" data-ospec="{{ $objectifSpecifique->code }}" data-action="{{ $action->code }}" data-progress="{{ $action->taux_avancement }}">
                                                                @if($action === $objectifSpecifique->actions->first() && $objectifSpecifique === $objectifStrategique->objectifsSpecifiques->first() && $objectifStrategique === $pilier->objectifsStrategiques->first())
                                                                    <td class="hierarchical-cell" rowspan="{{ $pilierRowspan }}" data-level="pilier"
                                                                        style="background-color: {{ $pilier->color }}; color: white; border-left: 4px solid {{ $pilier->color }};">
                                                                        <div class="fw-bold">{{ $pilier->code }}</div>
                                                                        <div>{{ $pilier->libelle }}</div>
                                                                        <div class="progress mt-1" style="height: 6px; background: rgba(255,255,255,0.3);">
                                                                            <div class="progress-bar bg-white" 
                                                                                 style="width: {{ $pilier->taux_avancement }}%"></div>
                                                                        </div>
                                                                        <small class="text-white-75">{{ number_format($pilier->taux_avancement, 0) }}%</small>
                                                                        <div class="mt-1">
                                                                            <span class="badge bg-white text-dark">{{ $pilier->owner->name ?? 'Non assigné' }}</span>
                                                                        </div>
                                                                    </td>
                                                                @endif

                                                                @if($action === $objectifSpecifique->actions->first() && $objectifSpecifique === $objectifStrategique->objectifsSpecifiques->first())
                                                                    <td class="hierarchical-cell" rowspan="{{ $osRowspan }}" data-level="os"
                                                                        style="background-color: {{ $pilier->getHierarchicalColor(1) }}; color: white; border-left: 4px solid {{ $pilier->color }};">
                                                                        <div class="fw-bold">{{ $pilier->code }}.{{ $objectifStrategique->code }}</div>
                                                                        <div>{{ $objectifStrategique->libelle }}</div>
                                                                        <div class="progress mt-1" style="height: 6px; background: rgba(255,255,255,0.3);">
                                                                            <div class="progress-bar bg-white" 
                                                                                 style="width: {{ $objectifStrategique->taux_avancement }}%"></div>
                                                                        </div>
                                                                        <small class="text-white-75">{{ number_format($objectifStrategique->taux_avancement, 0) }}%</small>
                                                                        <div class="mt-1">
                                                                            <span class="badge bg-white text-dark">{{ $objectifStrategique->owner->name ?? 'Non assigné' }}</span>
                                                                        </div>
                                                                    </td>
                                                                @endif

                                                                @if($action === $objectifSpecifique->actions->first())
                                                                    <td class="hierarchical-cell" rowspan="{{ $ospecRowspan }}" data-level="ospec"
                                                                        style="background-color: {{ $pilier->getHierarchicalColor(2) }}; color: white; border-left: 4px solid {{ $pilier->color }};">
                                                                        <div class="fw-bold">{{ $pilier->code }}.{{ $objectifStrategique->code }}.{{ $objectifSpecifique->code }}</div>
                                                                        <div>{{ $objectifSpecifique->libelle }}</div>
                                                                        <div class="progress mt-1" style="height: 6px; background: rgba(255,255,255,0.3);">
                                                                            <div class="progress-bar bg-white" 
                                                                                 style="width: {{ $objectifSpecifique->taux_avancement }}%"></div>
                                                                        </div>
                                                                        <small class="text-white-75">{{ number_format($objectifSpecifique->taux_avancement, 0) }}%</small>
                                                                        <div class="mt-1">
                                                                            <span class="badge bg-white text-dark">{{ $objectifSpecifique->owner->name ?? 'Non assigné' }}</span>
                                                                        </div>
                                                                    </td>
                                                                @endif

                                                                <td class="hierarchical-cell" data-level="action"
                                                                    style="cursor: pointer; background-color: {{ $pilier->getHierarchicalColor(3) }}; color: white; border-left: 4px solid {{ $pilier->color }};" 
                                                                    onclick="openActionComments({{ $action->id }})"
                                                                    title="Cliquer pour ouvrir la discussion">
                                                                    <div class="fw-bold">{{ $pilier->code }}.{{ $objectifStrategique->code }}.{{ $objectifSpecifique->code }}.{{ $action->code }}</div>
                                                                    <div>{{ $action->libelle }}</div>
                                                                    <div class="progress mt-1" style="height: 6px; background: rgba(255,255,255,0.3);">
                                                                        <div class="progress-bar bg-white" 
                                                                             style="width: {{ $action->taux_avancement }}%"></div>
                                                                    </div>
                                                                    <small class="text-white-75">{{ number_format($action->taux_avancement, 0) }}%</small>
                                                                    <div class="mt-1 d-flex align-items-center justify-content-between">
                                                                        <span class="badge bg-white text-dark">{{ $action->owner->name ?? 'Non assigné' }}</span>
                                                                        <i class="fas fa-comments text-white" style="font-size: 0.8em;"></i>
                                                                    </div>
                                                                </td>

                                                                <td class="table-light hierarchical-cell" data-level="sousaction">
                                                                    <div class="text-muted">Aucune sous-action</div>
                                                                </td>

                                                                <td>
                                                                    <div class="row">
                                                                        <div class="col-3">
                                                                            <small class="text-muted">Échéance</small>
                                                                            <div>-</div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <small class="text-muted">Date Réalisation</small>
                                                                            <div>-</div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <small class="text-muted">Écart</small>
                                                                            <div>
                                                                                <span class="badge bg-secondary">-</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <small class="text-muted">Progression</small>
                                                                            <div>
                                                                                <span class="badge bg-primary">{{ number_format($action->taux_avancement, 0) }}%</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        @else
                                            <!-- Pilier sans Objectifs Stratégiques -->
                                            <tr class="hierarchical-row" data-pilier="{{ $pilier->code }}" data-progress="{{ $pilier->taux_avancement }}">
                                                <td class="hierarchical-cell" data-level="pilier" 
                                                    style="background-color: {{ $pilier->color }}; color: white; border-left: 4px solid {{ $pilier->color }};">
                                                    <div class="fw-bold">{{ $pilier->code }}</div>
                                                    <div>{{ $pilier->libelle }}</div>
                                                    <div class="progress mt-1" style="height: 6px; background: rgba(255,255,255,0.3);">
                                                        <div class="progress-bar bg-white" 
                                                             style="width: {{ $pilier->taux_avancement }}%"></div>
                                                    </div>
                                                    <small class="text-white-75">{{ number_format($pilier->taux_avancement, 0) }}%</small>
                                                    <div class="mt-1">
                                                        <span class="badge bg-white text-dark">{{ $pilier->owner->name ?? 'Non assigné' }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center text-muted" colspan="5">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Aucun Objectif Stratégique défini
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                <!-- Footer du Modal -->
                <div class="modal-footer">
                    <div class="d-flex justify-content-between w-100">
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Total: {{ $piliers->count() }} pilier(s) affiché(s) | 
                                <span id="visibleRows">0</span> lignes visibles
                            </small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">
                                <i class="fas fa-times me-1"></i>
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9998;"></div>
    @endif

    <!-- Modal de commentaires des actions -->
    <livewire:action-comments-modal />

    <style>
        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        
        .table-responsive::-webkit-scrollbar {
            width: 8px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Styles pour le zoom */
        .vue-generale-table {
            transition: transform 0.3s ease;
        }
        
        /* Styles pour les cellules hiérarchiques */
        .hierarchical-cell {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .hierarchical-cell:hover {
            transform: scale(1.02);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            z-index: 10;
        }
        
        /* Styles pour le tri */
        .sortable {
            cursor: pointer;
            user-select: none;
        }
        
        .sortable:hover {
            background-color: rgba(255,255,255,0.1);
        }
        
        /* Animation pour les lignes */
        .hierarchical-row {
            transition: all 0.3s ease;
        }
        
        .hierarchical-row:hover {
            background-color: rgba(0,123,255,0.05) !important;
            transform: translateX(2px);
        }
        
        /* Styles pour les filtres */
        .filtered-out {
            display: none !important;
        }
        
        /* Styles pour le plein écran */
        .fullscreen-table {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9999;
            background: white;
        }
        
        /* Animation de chargement */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hierarchical-row {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        /* Styles pour les barres de progression animées */
        .progress-bar {
            transition: width 0.8s ease;
        }
        
        /* Styles pour les badges interactifs */
        .badge {
            transition: all 0.3s ease;
        }
        
        .badge:hover {
            transform: scale(1.1);
        }
    </style>

    <script>
        let currentZoom = 100;
        let currentSortColumn = -1;
        let currentSortDirection = 'asc';
        let isFullscreen = false;
        
        // Fonction de zoom
        function zoomIn() {
            if (currentZoom < 200) {
                currentZoom += 25;
                updateZoom();
            }
        }
        
        function zoomOut() {
            if (currentZoom > 50) {
                currentZoom -= 25;
                updateZoom();
            }
        }
        
        function resetZoom() {
            currentZoom = 100;
            updateZoom();
        }
        
        function updateZoom() {
            const table = document.getElementById('vueGeneraleTable');
            const zoomLevel = document.getElementById('zoomLevel');
            
            table.style.transform = `scale(${currentZoom / 100})`;
            table.style.transformOrigin = 'top left';
            zoomLevel.textContent = currentZoom + '%';
            
            // Ajuster la hauteur du conteneur
            const container = table.parentElement;
            container.style.height = `${70 * (currentZoom / 100)}vh`;
        }
        
        // Fonction de tri
        function sortTable(columnIndex) {
            const table = document.getElementById('hierarchicalTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // Changer la direction de tri
            if (currentSortColumn === columnIndex) {
                currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                currentSortColumn = columnIndex;
                currentSortDirection = 'asc';
            }
            
            // Mettre à jour les icônes de tri
            updateSortIcons(columnIndex);
            
            // Trier les lignes
            rows.sort((a, b) => {
                const aValue = getCellValue(a, columnIndex);
                const bValue = getCellValue(b, columnIndex);
                
                if (currentSortDirection === 'asc') {
                    return aValue.localeCompare(bValue);
                } else {
                    return bValue.localeCompare(aValue);
                }
            });
            
            // Réorganiser les lignes
            rows.forEach(row => tbody.appendChild(row));
        }
        
        function getCellValue(row, columnIndex) {
            const cell = row.cells[columnIndex];
            if (!cell) return '';
            
            // Extraire le texte de la cellule
            return cell.textContent.trim();
        }
        
        function updateSortIcons(activeColumn) {
            const headers = document.querySelectorAll('.sortable i.fa-sort');
            headers.forEach((icon, index) => {
                if (index === activeColumn) {
                    icon.className = currentSortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
                } else {
                    icon.className = 'fas fa-sort';
                }
            });
        }
        
        // Fonctions de filtrage
        function filterByProgress(type) {
            const rows = document.querySelectorAll('.hierarchical-row');
            
            rows.forEach(row => {
                const progress = parseFloat(row.dataset.progress) || 0;
                let show = true;
                
                switch(type) {
                    case 'completed':
                        show = progress === 100;
                        break;
                    case 'in-progress':
                        show = progress >= 25 && progress <= 75;
                        break;
                    case 'not-started':
                        show = progress === 0;
                        break;
                    default:
                        show = true;
                }
                
                if (show) {
                    row.classList.remove('filtered-out');
                } else {
                    row.classList.add('filtered-out');
                }
            });
            
            updateVisibleRows();
        }
        
        function filterByOwner() {
            const owner = prompt('Entrez le nom du propriétaire à filtrer:');
            if (!owner) return;
            
            const rows = document.querySelectorAll('.hierarchical-row');
            
            rows.forEach(row => {
                const badges = row.querySelectorAll('.badge');
                let hasOwner = false;
                
                badges.forEach(badge => {
                    if (badge.textContent.toLowerCase().includes(owner.toLowerCase())) {
                        hasOwner = true;
                    }
                });
                
                if (hasOwner) {
                    row.classList.remove('filtered-out');
                } else {
                    row.classList.add('filtered-out');
                }
            });
            
            updateVisibleRows();
        }
        
        // Fonction d'export Excel
        function exportToExcel() {
            const table = document.getElementById('hierarchicalTable');
            const rows = Array.from(table.querySelectorAll('tr'));
            
            let csv = '';
            
            rows.forEach(row => {
                const cells = Array.from(row.querySelectorAll('th, td'));
                const rowData = cells.map(cell => {
                    // Nettoyer le contenu HTML et extraire le texte
                    const text = cell.textContent.replace(/\s+/g, ' ').trim();
                    return `"${text}"`;
                });
                csv += rowData.join(',') + '\n';
            });
            
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'vue_generale_hierarchique.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        // Fonction plein écran
        function toggleFullscreen() {
            const table = document.getElementById('vueGeneraleTable');
            
            if (!isFullscreen) {
                table.classList.add('fullscreen-table');
                isFullscreen = true;
            } else {
                table.classList.remove('fullscreen-table');
                isFullscreen = false;
            }
        }
        
        // Fonction de mise à jour du nombre de lignes visibles
        function updateVisibleRows() {
            const visibleRows = document.querySelectorAll('.hierarchical-row:not(.filtered-out)').length;
            const visibleRowsElement = document.getElementById('visibleRows');
            if (visibleRowsElement) {
                visibleRowsElement.textContent = visibleRows;
            }
        }
        
        // Recherche en temps réel
        document.addEventListener('livewire:init', () => {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('.hierarchical-row');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            row.classList.remove('filtered-out');
                        } else {
                            row.classList.add('filtered-out');
                        }
                    });
                    
                    updateVisibleRows();
                });
            }
        });
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            updateVisibleRows();
            
            // Ajouter des événements de clic pour les cellules hiérarchiques
            document.querySelectorAll('.hierarchical-cell').forEach(cell => {
                cell.addEventListener('click', function() {
                    const level = this.dataset.level;
                    console.log(`Cellule ${level} cliquée:`, this.textContent.trim());
                    
                    // Ajouter un effet visuel temporaire
                    this.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 200);
                });
            });
        });
        
        // Raccourcis clavier
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case '=':
                    case '+':
                        e.preventDefault();
                        zoomIn();
                        break;
                    case '-':
                        e.preventDefault();
                        zoomOut();
                        break;
                    case '0':
                        e.preventDefault();
                        resetZoom();
                        break;
                    case 'f':
                        e.preventDefault();
                        document.getElementById('searchInput').focus();
                        break;
                }
            }
        });

        // Fonction pour ouvrir le modal de commentaires
        function openActionComments(actionId) {
            console.log('🔍 [DEBUG] Ouverture du modal de commentaires pour l\'action:', actionId);
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('openActionCommentsModal', { actionId: actionId });
            } else {
                console.error('❌ [ERROR] Livewire not initialized');
                alert('Erreur: Livewire n\'est pas initialisé');
            }
        }
    </script>
</div> 