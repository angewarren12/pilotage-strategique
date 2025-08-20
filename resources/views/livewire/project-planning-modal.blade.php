<div>
    @if($showModal)
    <div class="modal fade show" style="display: block; z-index: 10000;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="z-index: 10000;">
                <!-- Header du Modal -->
                <div class="modal-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-calendar-alt fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0">
                                <strong>Planning Global du Projet</strong>
                            </h5>
                            <small class="text-white-75">Vue d'ensemble et gestion du planning stratégique</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-danger btn-sm me-2" wire:click="forceCloseModal" style="z-index: 10002;">
                            <i class="fas fa-times"></i> Fermer
                        </button>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal" style="z-index: 10002;"></button>
                    </div>
                </div>

                <!-- Contenu du Modal -->
                <div class="modal-body p-0">
                    @if($isLoading)
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            <p class="mt-2">Chargement du planning...</p>
                        </div>
                    @else
                        <!-- Barre d'outils et filtres -->
                        <div class="p-3 bg-light border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               placeholder="Rechercher..."
                                               wire:model.live="searchTerm">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" wire:model.live="selectedPilier">
                                        <option value="">Tous les piliers</option>
                                        @foreach($piliers as $pilier)
                                            <option value="{{ $pilier->id }}">{{ $pilier->code }} - {{ $pilier->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" wire:model.live="selectedOwner">
                                        <option value="">Tous les owners</option>
                                        @foreach($owners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" wire:model.live="selectedStatus">
                                        @foreach($statuses as $key => $status)
                                            <option value="{{ $key }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" wire:model.live="dateRange">
                                        <option value="all">Toutes les dates</option>
                                        <option value="this_month">Ce mois</option>
                                        <option value="this_quarter">Ce trimestre</option>
                                        <option value="this_year">Cette année</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="zoomOut" title="Zoom arrière">
                                            <i class="fas fa-search-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="resetZoom" title="Zoom par défaut">
                                            <i class="fas fa-expand-arrows-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="zoomIn" title="Zoom avant">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mode de vue -->
                        <div class="p-3 bg-light border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                                class="btn {{ $viewMode === 'gantt' ? 'btn-primary' : 'btn-outline-primary' }}" 
                                                wire:click="$set('viewMode', 'gantt')">
                                            <i class="fas fa-chart-bar me-2"></i>Gantt
                                        </button>
                                        <button type="button" 
                                                class="btn {{ $viewMode === 'timeline' ? 'btn-primary' : 'btn-outline-primary' }}" 
                                                wire:click="$set('viewMode', 'timeline')">
                                            <i class="fas fa-clock me-2"></i>Timeline
                                        </button>
                                        <button type="button" 
                                                class="btn {{ $viewMode === 'hierarchy' ? 'btn-primary' : 'btn-outline-primary' }}" 
                                                wire:click="$set('viewMode', 'hierarchy')">
                                            <i class="fas fa-sitemap me-2"></i>Hiérarchie
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-success btn-sm" wire:click="exportPDF" title="Export PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm" wire:click="exportExcel" title="Export Excel">
                                            <i class="fas fa-file-excel"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm" wire:click="exportImage" title="Export Image">
                                            <i class="fas fa-file-image"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contenu principal avec zoom -->
                        <div class="planning-content" style="transform: scale({{ $zoomLevel }}); transform-origin: top left;">
                            <!-- Vue Gantt -->
                            @if($viewMode === 'gantt')
                                <div class="p-4">
                                    <div class="gantt-container">
                                        <div class="gantt-header">
                                            <div class="gantt-timeline">
                                                <!-- Timeline header sera généré par JavaScript -->
                                            </div>
                                        </div>
                                        <div class="gantt-body">
                                            @foreach($ganttData as $pilierData)
                                                <div class="gantt-row pilier-row" style="border-left: 4px solid {{ $pilierData['color'] }};">
                                                    <div class="gantt-label">
                                                        <strong>{{ $pilierData['text'] }}</strong>
                                                        <small class="text-muted d-block">{{ number_format($pilierData['progress'] * 100, 1) }}%</small>
                                                    </div>
                                                    <div class="gantt-bars">
                                                        <div class="gantt-bar" 
                                                             style="left: {{ $this->calculateBarPosition($pilierData['start_date']) }}%; 
                                                                    width: {{ $this->calculateBarWidth($pilierData['start_date'], $pilierData['end_date']) }}%; 
                                                                    background: {{ $pilierData['color'] }};">
                                                            <div class="progress-overlay" style="width: {{ $pilierData['progress'] * 100 }}%;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                @foreach($pilierData['children'] as $osData)
                                                    <div class="gantt-row os-row" style="border-left: 4px solid {{ $osData['color'] }};">
                                                        <div class="gantt-label">
                                                            <span class="ms-3">{{ $osData['text'] }}</span>
                                                            <small class="text-muted d-block ms-3">{{ number_format($osData['progress'] * 100, 1) }}%</small>
                                                        </div>
                                                        <div class="gantt-bars">
                                                            <div class="gantt-bar" 
                                                                 style="left: {{ $this->calculateBarPosition($osData['start_date']) }}%; 
                                                                        width: {{ $this->calculateBarWidth($osData['start_date'], $osData['end_date']) }}%; 
                                                                        background: {{ $osData['color'] }};">
                                                                <div class="progress-overlay" style="width: {{ $osData['progress'] * 100 }}%;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    @foreach($osData['children'] as $ospData)
                                                        <div class="gantt-row osp-row" style="border-left: 4px solid {{ $ospData['color'] }};">
                                                            <div class="gantt-label">
                                                                <span class="ms-4">{{ $ospData['text'] }}</span>
                                                                <small class="text-muted d-block ms-4">{{ number_format($ospData['progress'] * 100, 1) }}%</small>
                                                            </div>
                                                            <div class="gantt-bars">
                                                                <div class="gantt-bar" 
                                                                     style="left: {{ $this->calculateBarPosition($ospData['start_date']) }}%; 
                                                                            width: {{ $this->calculateBarWidth($ospData['start_date'], $ospData['end_date']) }}%; 
                                                                            background: {{ $ospData['color'] }};">
                                                                    <div class="progress-overlay" style="width: {{ $ospData['progress'] * 100 }}%;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        @foreach($ospData['children'] as $actionData)
                                                            <div class="gantt-row action-row" style="border-left: 4px solid {{ $actionData['color'] }};">
                                                                <div class="gantt-label">
                                                                    <span class="ms-5">{{ $actionData['text'] }}</span>
                                                                    <small class="text-muted d-block ms-5">{{ number_format($actionData['progress'] * 100, 1) }}%</small>
                                                                </div>
                                                                <div class="gantt-bars">
                                                                    <div class="gantt-bar" 
                                                                         style="left: {{ $this->calculateBarPosition($actionData['start_date']) }}%; 
                                                                                width: {{ $this->calculateBarWidth($actionData['start_date'], $actionData['end_date']) }}%; 
                                                                                background: {{ $actionData['color'] }};">
                                                                        <div class="progress-overlay" style="width: {{ $actionData['progress'] * 100 }}%;"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            <!-- Vue Timeline -->
                            @elseif($viewMode === 'timeline')
                                <div class="p-4">
                                    <div class="timeline-container">
                                        <div class="timeline-header">
                                            <h5>Timeline des Actions</h5>
                                        </div>
                                        <div class="timeline-body">
                                            @foreach($timelineData as $event)
                                                <div class="timeline-item" style="border-left: 4px solid {{ $event['color'] }};">
                                                    <div class="timeline-marker" style="background: {{ $event['color'] }};"></div>
                                                    <div class="timeline-content">
                                                        <div class="timeline-header">
                                                            <h6>{{ $event['title'] }}</h6>
                                                            <span class="badge" style="background: {{ $event['color'] }};">
                                                                {{ $event['pilier'] }} > {{ $event['os'] }} > {{ $event['osp'] }}
                                                                @if(isset($event['action']))
                                                                    > {{ $event['action'] }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <div class="timeline-body">
                                                            <p class="mb-1">
                                                                <strong>Période :</strong> 
                                                                {{ \Carbon\Carbon::parse($event['start'])->format('d/m/Y') }} 
                                                                @if($event['start'] !== $event['end'])
                                                                    - {{ \Carbon\Carbon::parse($event['end'])->format('d/m/Y') }}
                                                                @endif
                                                            </p>
                                                            <div class="progress" style="height: 8px;">
                                                                <div class="progress-bar" 
                                                                     style="width: {{ $event['taux'] }}%; background: {{ $event['color'] }};">
                                                                </div>
                                                            </div>
                                                            <small class="text-muted">{{ number_format($event['taux'], 1) }}% terminé</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            <!-- Vue Hiérarchie -->
                            @else
                                <div class="p-4">
                                    <div class="hierarchy-container">
                                        @foreach($planningData as $pilierData)
                                            <div class="hierarchy-card pilier-card" style="border-color: {{ $pilierData['color'] }};">
                                                <div class="hierarchy-header" style="background: {{ $pilierData['color'] }}; color: white;">
                                                    <div class="hierarchy-icon">
                                                        <i class="fas fa-layer-group"></i>
                                                    </div>
                                                    <div class="hierarchy-info">
                                                        <h6 class="mb-0">{{ $pilierData['code'] }} - {{ $pilierData['libelle'] }}</h6>
                                                        <small>Pilier stratégique</small>
                                                    </div>
                                                    <div class="hierarchy-progress">
                                                        <div class="progress" style="height: 8px; background: rgba(255,255,255,0.3);">
                                                            <div class="progress-bar bg-white" style="width: {{ $pilierData['taux_avancement'] }}%;"></div>
                                                        </div>
                                                        <small>{{ number_format($pilierData['taux_avancement'], 1) }}%</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="hierarchy-body">
                                                    @foreach($pilierData['objectifs_strategiques'] as $osData)
                                                        <div class="hierarchy-subcard os-card" style="border-color: {{ $pilierData['color'] }};">
                                                            <div class="hierarchy-subheader" style="background: {{ $pilierData['color'] }}; color: white;">
                                                                <div class="hierarchy-icon">
                                                                    <i class="fas fa-bullseye"></i>
                                                                </div>
                                                                <div class="hierarchy-info">
                                                                    <h6 class="mb-0">{{ $osData['code'] }} - {{ $osData['libelle'] }}</h6>
                                                                    <small>Objectif stratégique</small>
                                                                </div>
                                                                <div class="hierarchy-progress">
                                                                    <div class="progress" style="height: 6px; background: rgba(255,255,255,0.3);">
                                                                        <div class="progress-bar bg-white" style="width: {{ $osData['taux_avancement'] }}%;"></div>
                                                                    </div>
                                                                    <small>{{ number_format($osData['taux_avancement'], 1) }}%</small>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="hierarchy-subbody">
                                                                @foreach($osData['objectifs_specifiques'] as $ospData)
                                                                    <div class="hierarchy-subsubcard osp-card" style="border-color: {{ $pilierData['color'] }};">
                                                                        <div class="hierarchy-subsubheader" style="background: {{ $pilierData['color'] }}; color: white;">
                                                                            <div class="hierarchy-icon">
                                                                                <i class="fas fa-target"></i>
                                                                            </div>
                                                                            <div class="hierarchy-info">
                                                                                <h6 class="mb-0">{{ $ospData['code'] }} - {{ $ospData['libelle'] }}</h6>
                                                                                <small>Objectif spécifique</small>
                                                                            </div>
                                                                            <div class="hierarchy-progress">
                                                                                <div class="progress" style="height: 4px; background: rgba(255,255,255,0.3);">
                                                                                    <div class="progress-bar bg-white" style="width: {{ $ospData['taux_avancement'] }}%;"></div>
                                                                                </div>
                                                                                <small>{{ number_format($ospData['taux_avancement'], 1) }}%</small>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <div class="hierarchy-subsubbody">
                                                                            @foreach($ospData['actions'] as $actionData)
                                                                                <div class="action-item" style="border-left: 3px solid {{ $pilierData['color'] }};">
                                                                                    <div class="action-header">
                                                                                        <h6 class="mb-1">{{ $actionData['code'] }} - {{ $actionData['libelle'] }}</h6>
                                                                                        <div class="action-meta">
                                                                                            @if($actionData['date_echeance'])
                                                                                                <span class="badge bg-warning">
                                                                                                    <i class="fas fa-calendar me-1"></i>
                                                                                                    {{ \Carbon\Carbon::parse($actionData['date_echeance'])->format('d/m/Y') }}
                                                                                                </span>
                                                                                            @endif
                                                                                            @if($actionData['date_realisation'])
                                                                                                <span class="badge bg-success">
                                                                                                    <i class="fas fa-check me-1"></i>
                                                                                                    Terminé
                                                                                                </span>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="action-progress">
                                                                                        <div class="progress" style="height: 6px;">
                                                                                            <div class="progress-bar" 
                                                                                                 style="width: {{ $actionData['taux_avancement'] }}%; background: {{ $pilierData['color'] }};">
                                                                                            </div>
                                                                                        </div>
                                                                                        <small class="text-muted">{{ number_format($actionData['taux_avancement'], 1) }}%</small>
                                                                                    </div>
                                                                                    
                                                                                    @if(count($actionData['sous_actions']) > 0)
                                                                                        <div class="sous-actions-list">
                                                                                            @foreach($actionData['sous_actions'] as $sousActionData)
                                                                                                <div class="sous-action-item" style="border-left: 2px solid {{ $pilierData['color'] }};">
                                                                                                    <small class="text-muted">{{ $sousActionData['code'] }} - {{ $sousActionData['libelle'] }}</small>
                                                                                                    <div class="progress" style="height: 4px;">
                                                                                                        <div class="progress-bar" 
                                                                                                             style="width: {{ $sousActionData['taux_avancement'] }}%; background: {{ $pilierData['color'] }};">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.5);"></div>
    @endif

    <style>
        .modal.show {
            pointer-events: auto !important;
        }
        
        .modal-content {
            pointer-events: auto !important;
            position: relative;
            z-index: 10001 !important;
        }
        
        .modal-backdrop {
            pointer-events: none !important;
        }
        
        .planning-content {
            transition: transform 0.3s ease;
        }
        
        .gantt-container {
            position: relative;
            overflow-x: auto;
        }
        
        .gantt-header {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            border-bottom: 2px solid #dee2e6;
        }
        
        .gantt-row {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .gantt-label {
            width: 300px;
            padding: 0 15px;
            font-size: 0.9rem;
        }
        
        .gantt-bars {
            flex: 1;
            position: relative;
            height: 30px;
            background: #f8f9fa;
        }
        
        .gantt-bar {
            position: absolute;
            height: 20px;
            border-radius: 3px;
            top: 5px;
            opacity: 0.8;
        }
        
        .progress-overlay {
            height: 100%;
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .pilier-row { background: rgba(0,123,255,0.1); }
        .os-row { background: rgba(40,167,69,0.1); }
        .osp-row { background: rgba(255,193,7,0.1); }
        .action-row { background: rgba(220,53,69,0.1); }
        
        .timeline-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .timeline-item {
            position: relative;
            padding: 20px 0 20px 30px;
            margin-bottom: 20px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -8px;
            top: 25px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
        }
        
        .timeline-content {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .hierarchy-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .hierarchy-card {
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .hierarchy-header {
            padding: 15px;
            display: flex;
            align-items: center;
        }
        
        .hierarchy-icon {
            margin-right: 15px;
            font-size: 1.5rem;
        }
        
        .hierarchy-info {
            flex: 1;
        }
        
        .hierarchy-progress {
            text-align: right;
            min-width: 100px;
        }
        
        .hierarchy-body {
            padding: 15px;
        }
        
        .hierarchy-subcard {
            margin: 10px 0;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .hierarchy-subheader {
            padding: 12px;
            display: flex;
            align-items: center;
        }
        
        .hierarchy-subbody {
            padding: 12px;
        }
        
        .hierarchy-subsubcard {
            margin: 8px 0;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .hierarchy-subsubheader {
            padding: 10px;
            display: flex;
            align-items: center;
        }
        
        .hierarchy-subsubbody {
            padding: 10px;
        }
        
        .action-item {
            padding: 10px;
            margin: 8px 0;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .action-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .action-meta {
            display: flex;
            gap: 5px;
        }
        
        .action-progress {
            margin-bottom: 8px;
        }
        
        .sous-actions-list {
            margin-top: 8px;
            padding-left: 15px;
        }
        
        .sous-action-item {
            padding: 5px 10px;
            margin: 3px 0;
            background: white;
            border-radius: 3px;
        }
    </style>

    <script>
        // Fonctions JavaScript pour le planning
        function calculateBarPosition(startDate) {
            // Logique pour calculer la position de la barre
            return 0; // À implémenter
        }
        
        function calculateBarWidth(startDate, endDate) {
            // Logique pour calculer la largeur de la barre
            return 20; // À implémenter
        }
    </script>
</div> 