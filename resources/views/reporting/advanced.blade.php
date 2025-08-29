@extends('layouts.app')

@section('title', 'Reporting Avancé - Analyses & Prédictions')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line me-2 text-primary"></i>
                Reporting Avancé
            </h1>
            <p class="text-muted">Analyses approfondies et prédictions intelligentes</p>
        </div>
        <div>
            <button class="btn btn-success me-2" onclick="exportAdvancedReport()">
                <i class="fas fa-file-excel me-2"></i>Export Excel Pro
            </button>
            <button class="btn btn-danger me-2" onclick="exportToPDF()">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </button>
            <button class="btn btn-info" onclick="exportToPowerPoint()">
                <i class="fas fa-file-powerpoint me-2"></i>PowerPoint
            </button>
        </div>
    </div>

    <!-- Score de Santé Global -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-1">
                                <i class="fas fa-heartbeat me-2 text-success"></i>
                                Score de Santé Global du Projet
                            </h4>
                            <p class="text-muted mb-0">Évaluation globale basée sur la progression, le respect des échéances et l'activité</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="health-score-circle" data-score="{{ $advancedKPIs['global_health_score'] }}">
                                <svg width="120" height="120" viewBox="0 0 120 120">
                                    <circle cx="60" cy="60" r="45" stroke="#e9ecef" stroke-width="8" fill="none"/>
                                    <circle cx="60" cy="60" r="45" stroke="#28a745" stroke-width="8" fill="none" 
                                            stroke-dasharray="283" 
                                            stroke-dashoffset="{{ 283 - (283 * $advancedKPIs['global_health_score'] / 100) }}"
                                            transform="rotate(-90 60 60)"
                                            style="transition: stroke-dashoffset 2s ease-in-out;"/>
                                </svg>
                                <div class="health-score-text">
                                    <span class="score-number">{{ $advancedKPIs['global_health_score'] }}</span>
                                    <span class="score-label">/ 100</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Avancés -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="kpi-card velocity">
                <div class="kpi-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-number">
                        @php
                            $currentVelocity = collect($advancedKPIs['velocity_trend'])->last()['velocity'] ?? 0;
                        @endphp
                        {{ $currentVelocity > 0 ? '+' : '' }}{{ $currentVelocity }}%
                    </div>
                    <div class="kpi-label">Vélocité Actuelle</div>
                    <div class="kpi-trend {{ $currentVelocity > 0 ? 'positive' : ($currentVelocity < 0 ? 'negative' : 'neutral') }}">
                        <i class="fas fa-arrow-{{ $currentVelocity > 0 ? 'up' : ($currentVelocity < 0 ? 'down' : 'right') }}"></i>
                        Progression hebdomadaire
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="kpi-card alerts">
                <div class="kpi-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-number">{{ count($advancedKPIs['critical_alerts']) }}</div>
                    <div class="kpi-label">Alertes Critiques</div>
                    <div class="kpi-trend">
                        <i class="fas fa-bell"></i>
                        Nécessitent une attention
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="kpi-card bottlenecks">
                <div class="kpi-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-number">{{ count($advancedKPIs['bottlenecks']) }}</div>
                    <div class="kpi-label">Goulots d'Étranglement</div>
                    <div class="kpi-trend">
                        <i class="fas fa-search"></i>
                        Points de blocage identifiés
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="kpi-card predictions">
                <div class="kpi-icon">
                    <i class="fas fa-crystal-ball"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-number">{{ count($advancedKPIs['completion_prediction']) }}</div>
                    <div class="kpi-label">Prédictions IA</div>
                    <div class="kpi-trend">
                        <i class="fas fa-robot"></i>
                        Analyses prédictives
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques Interactifs -->
    <div class="row mb-4">
        <!-- Graphique Vélocité -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Tendance de Vélocité
                    </h5>
                    <small class="text-muted">Évolution de la progression par semaine</small>
                </div>
                <div class="card-body">
                    <canvas id="velocityChart" width="400" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Performance par Pilier -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        Performance par Pilier
                    </h5>
                    <small class="text-muted">Score de santé et progression par pilier</small>
                </div>
                <div class="card-body">
                    <canvas id="pilierPerformanceChart" width="400" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques Avancés -->
    <div class="row mb-4">
        <!-- Heatmap Performance Équipe -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-th me-2"></i>
                        Heatmap Performance des Équipes
                    </h5>
                    <small class="text-muted">Visualisation des performances par responsable</small>
                </div>
                <div class="card-body">
                    <div id="teamHeatmap" class="heatmap-container">
                        <!-- Généré par JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        Top Performers
                    </h5>
                    <small class="text-muted">Classement des meilleures performances</small>
                </div>
                <div class="card-body">
                    <div class="top-performers-list">
                        @foreach($advancedKPIs['team_performance']->take(5) as $index => $performer)
                        <div class="performer-item rank-{{ $index + 1 }}">
                            <div class="rank-badge">{{ $index + 1 }}</div>
                            <div class="performer-info">
                                <div class="performer-name">{{ $performer['nom'] }}</div>
                                <div class="performer-score">{{ $performer['score_performance'] }} pts</div>
                            </div>
                            <div class="performer-progress">
                                <div class="progress">
                                    <div class="progress-bar bg-success" 
                                         style="width: {{ $performer['progression_moyenne'] }}%"
                                         title="{{ $performer['progression_moyenne'] }}% progression"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes et Prédictions -->
    <div class="row mb-4">
        <!-- Alertes Critiques -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                        Alertes Critiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alerts-container">
                        @foreach($advancedKPIs['critical_alerts']->take(6) as $alert)
                        <div class="alert-item severity-{{ $alert['severity'] }}">
                            <div class="alert-icon">
                                @switch($alert['type'])
                                    @case('retard')
                                        <i class="fas fa-clock text-danger"></i>
                                        @break
                                    @case('echeance_proche')
                                        <i class="fas fa-calendar-exclamation text-warning"></i>
                                        @break
                                    @case('sans_progression')
                                        <i class="fas fa-pause-circle text-info"></i>
                                        @break
                                @endswitch
                            </div>
                            <div class="alert-content">
                                <div class="alert-title">{{ $alert['title'] }}</div>
                                <div class="alert-message">{{ $alert['message'] }}</div>
                                <div class="alert-meta">
                                    @if(isset($alert['days_overdue']))
                                        <span class="badge bg-danger">{{ $alert['days_overdue'] }} jours de retard</span>
                                    @endif
                                    @if(isset($alert['days_remaining']))
                                        <span class="badge bg-warning">{{ abs($alert['days_remaining']) }} jours restants</span>
                                    @endif
                                    @if(isset($alert['days_inactive']))
                                        <span class="badge bg-info">{{ $alert['days_inactive'] }} jours inactif</span>
                                    @endif
                                    <span class="badge bg-secondary">{{ $alert['progress'] }}% complété</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Prédictions IA -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-robot me-2 text-primary"></i>
                        Prédictions IA
                    </h5>
                    <small class="text-muted">Analyse prédictive des dates de fin</small>
                </div>
                <div class="card-body">
                    <div class="predictions-container">
                        @foreach($advancedKPIs['completion_prediction']->take(6) as $prediction)
                        <div class="prediction-item risk-{{ $prediction['risque'] }}">
                            <div class="prediction-header">
                                <div class="prediction-code">{{ $prediction['code'] }}</div>
                                <div class="prediction-risk">
                                    <span class="risk-badge risk-{{ $prediction['risque'] }}">
                                        @switch($prediction['risque'])
                                            @case('critical')
                                                <i class="fas fa-exclamation-triangle"></i> Critique
                                                @break
                                            @case('high')
                                                <i class="fas fa-exclamation-circle"></i> Élevé
                                                @break
                                            @case('medium')
                                                <i class="fas fa-info-circle"></i> Moyen
                                                @break
                                            @default
                                                <i class="fas fa-check-circle"></i> Faible
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                            <div class="prediction-content">
                                <div class="prediction-title">{{ Str::limit($prediction['libelle'], 50) }}</div>
                                <div class="prediction-dates">
                                    <div class="date-item">
                                        <span class="date-label">Échéance:</span>
                                        <span class="date-value">{{ Carbon\Carbon::parse($prediction['date_echeance'])->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="date-item">
                                        <span class="date-label">Fin prévue:</span>
                                        <span class="date-value prediction-date">{{ $prediction['date_fin_prevue']->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                <div class="prediction-progress">
                                    <div class="progress">
                                        <div class="progress-bar" 
                                             style="width: {{ $prediction['progression_actuelle'] }}%"
                                             title="{{ $prediction['progression_actuelle'] }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $prediction['jours_restants'] }} jours estimés</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau de Reporting Détaillé -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-table me-2"></i>
                Tableau de Reporting Détaillé
            </h5>
            <div class="card-tools">
                <button class="btn btn-sm btn-outline-secondary" onclick="toggleTableView()">
                    <i class="fas fa-expand-arrows-alt"></i> Vue complète
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="reportingTable">
                    <thead>
                        <tr>
                            <th>Code Complet</th>
                            <th>Libellé</th>
                            <th>Responsable</th>
                            <th>Progression</th>
                            <th>Échéance</th>
                            <th>Statut</th>
                            <th>Prédiction</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sousActions as $sousAction)
                        <tr class="sous-action-row" data-id="{{ $sousAction->id }}">
                            <td>
                                <span class="code-badge">
                                    {{ $sousAction->action->objectifSpecifique->objectifStrategique->pilier->code }}.{{ $sousAction->action->objectifSpecifique->objectifStrategique->code }}.{{ $sousAction->action->objectifSpecifique->code }}.{{ $sousAction->action->code }}.{{ $sousAction->code }}
                                </span>
                            </td>
                            <td>
                                <div class="sous-action-title">{{ $sousAction->libelle }}</div>
                                @if($sousAction->description)
                                    <small class="text-muted">{{ Str::limit($sousAction->description, 60) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($sousAction->owner)
                                    <span class="badge bg-primary">{{ $sousAction->owner->name }}</span>
                                @else
                                    <span class="badge bg-secondary">Non assigné</span>
                                @endif
                            </td>
                            <td>
                                <div class="progress-container">
                                    <div class="progress">
                                        <div class="progress-bar bg-success" 
                                             style="width: {{ $sousAction->taux_avancement }}%"
                                             title="{{ $sousAction->taux_avancement }}%"></div>
                                    </div>
                                    <span class="progress-text">{{ $sousAction->taux_avancement }}%</span>
                                </div>
                            </td>
                            <td>
                                @if($sousAction->date_echeance)
                                    @php
                                        $isOverdue = Carbon\Carbon::parse($sousAction->date_echeance)->isPast() && $sousAction->taux_avancement < 100;
                                        $isUrgent = Carbon\Carbon::parse($sousAction->date_echeance)->diffInDays(now(), false) <= 7 && !$isOverdue;
                                    @endphp
                                    <span class="badge {{ $isOverdue ? 'bg-danger' : ($isUrgent ? 'bg-warning' : 'bg-info') }}">
                                        {{ Carbon\Carbon::parse($sousAction->date_echeance)->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark">Pas d'échéance</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statut = 'en_cours';
                                    if ($sousAction->taux_avancement >= 100) {
                                        $statut = 'termine';
                                    } elseif ($sousAction->date_echeance && Carbon\Carbon::parse($sousAction->date_echeance)->isPast()) {
                                        $statut = 'en_retard';
                                    }
                                @endphp
                                <span class="badge status-{{ $statut }}">
                                    @switch($statut)
                                        @case('termine')
                                            <i class="fas fa-check-circle"></i> Terminé
                                            @break
                                        @case('en_retard')
                                            <i class="fas fa-exclamation-triangle"></i> En retard
                                            @break
                                        @default
                                            <i class="fas fa-clock"></i> En cours
                                    @endswitch
                                </span>
                            </td>
                            <td>
                                @php
                                    $prediction = collect($advancedKPIs['completion_prediction'])->firstWhere('sous_action_id', $sousAction->id);
                                @endphp
                                @if($prediction)
                                    <div class="prediction-mini">
                                        <div class="prediction-date">{{ $prediction['date_fin_prevue']->format('d/m') }}</div>
                                        <div class="prediction-risk risk-{{ $prediction['risque'] }}">
                                            {{ ucfirst($prediction['risque']) }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="showSousActionDetails({{ $sousAction->id }})" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" onclick="showProgressHistory({{ $sousAction->id }})" title="Historique">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- CSS Personnalisé -->
<style>
/* Score de Santé */
.health-score-circle {
    position: relative;
    display: inline-block;
}

.health-score-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.score-number {
    font-size: 2rem;
    font-weight: bold;
    color: #28a745;
}

.score-label {
    font-size: 0.9rem;
    color: #6c757d;
}

/* KPI Cards */
.kpi-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-left: 4px solid #00AE9E;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.kpi-card.velocity { border-left-color: #28a745; }
.kpi-card.alerts { border-left-color: #dc3545; }
.kpi-card.bottlenecks { border-left-color: #ffc107; }
.kpi-card.predictions { border-left-color: #6f42c1; }

.kpi-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.kpi-card.velocity .kpi-icon { color: #28a745; }
.kpi-card.alerts .kpi-icon { color: #dc3545; }
.kpi-card.bottlenecks .kpi-icon { color: #ffc107; }
.kpi-card.predictions .kpi-icon { color: #6f42c1; }

.kpi-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.kpi-label {
    font-size: 1rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.kpi-trend {
    font-size: 0.85rem;
    color: #6c757d;
}

.kpi-trend.positive { color: #28a745; }
.kpi-trend.negative { color: #dc3545; }
.kpi-trend.neutral { color: #6c757d; }

/* Top Performers */
.top-performers-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.performer-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: background 0.2s ease;
}

.performer-item:hover {
    background: #e9ecef;
}

.rank-badge {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    margin-right: 1rem;
}

.rank-1 .rank-badge { background: #ffd700; color: #333; }
.rank-2 .rank-badge { background: #c0c0c0; color: #333; }
.rank-3 .rank-badge { background: #cd7f32; color: white; }
.rank-4 .rank-badge, .rank-5 .rank-badge { background: #6c757d; }

.performer-info {
    flex: 1;
}

.performer-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.performer-score {
    font-size: 0.85rem;
    color: #6c757d;
}

.performer-progress {
    width: 100px;
}

/* Alertes */
.alerts-container {
    max-height: 400px;
    overflow-y: auto;
}

.alert-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    border-left: 4px solid #dee2e6;
    margin-bottom: 1rem;
    background: #f8f9fa;
    border-radius: 0 8px 8px 0;
}

.alert-item.severity-critical { border-left-color: #dc3545; }
.alert-item.severity-warning { border-left-color: #ffc107; }
.alert-item.severity-info { border-left-color: #17a2b8; }

.alert-icon {
    margin-right: 1rem;
    font-size: 1.2rem;
}

.alert-content {
    flex: 1;
}

.alert-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.alert-message {
    font-size: 0.9rem;
    color: #495057;
    margin-bottom: 0.5rem;
}

.alert-meta {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* Prédictions */
.predictions-container {
    max-height: 400px;
    overflow-y: auto;
}

.prediction-item {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 1rem;
    background: white;
}

.prediction-item.risk-critical { border-left: 4px solid #dc3545; }
.prediction-item.risk-high { border-left: 4px solid #fd7e14; }
.prediction-item.risk-medium { border-left: 4px solid #ffc107; }
.prediction-item.risk-low { border-left: 4px solid #28a745; }

.prediction-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.prediction-code {
    font-family: monospace;
    font-size: 0.85rem;
    background: #e9ecef;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.risk-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-weight: 600;
}

.risk-badge.risk-critical { background: #dc3545; color: white; }
.risk-badge.risk-high { background: #fd7e14; color: white; }
.risk-badge.risk-medium { background: #ffc107; color: #333; }
.risk-badge.risk-low { background: #28a745; color: white; }

.prediction-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.prediction-dates {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.date-item {
    display: flex;
    flex-direction: column;
}

.date-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
}

.date-value {
    font-weight: 600;
}

.prediction-date {
    color: #00AE9E;
}

/* Heatmap */
.heatmap-container {
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 8px;
}

/* Tableau */
.code-badge {
    font-family: monospace;
    font-size: 0.8rem;
    background: #e9ecef;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.sous-action-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.progress-container {
    min-width: 120px;
}

.progress-text {
    font-size: 0.85rem;
    font-weight: 600;
    margin-left: 0.5rem;
}

.status-termine { background: #28a745 !important; }
.status-en_retard { background: #dc3545 !important; }
.status-en_cours { background: #ffc107 !important; color: #333 !important; }

.prediction-mini {
    text-align: center;
}

.prediction-date {
    font-size: 0.8rem;
    font-weight: 600;
}

.prediction-risk {
    font-size: 0.7rem;
    text-transform: uppercase;
}

.prediction-risk.risk-critical { color: #dc3545; }
.prediction-risk.risk-high { color: #fd7e14; }
.prediction-risk.risk-medium { color: #ffc107; }
.prediction-risk.risk-low { color: #28a745; }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Données pour les graphiques
const velocityData = @json($advancedKPIs['velocity_trend']);
const pilierPerformanceData = @json($advancedKPIs['performance_by_pilier']);
const teamPerformanceData = @json($advancedKPIs['team_performance']);

// Graphique de vélocité
const velocityCtx = document.getElementById('velocityChart').getContext('2d');
new Chart(velocityCtx, {
    type: 'line',
    data: {
        labels: velocityData.map(item => item.date),
        datasets: [{
            label: 'Progression (%)',
            data: velocityData.map(item => item.progress),
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Vélocité',
            data: velocityData.map(item => item.velocity),
            borderColor: '#00AE9E',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Progression (%)' }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: { display: true, text: 'Vélocité' },
                grid: { drawOnChartArea: false }
            }
        },
        plugins: {
            tooltip: {
                mode: 'index',
                intersect: false
            },
            legend: {
                position: 'top'
            }
        }
    }
});

// Graphique performance par pilier
const pilierCtx = document.getElementById('pilierPerformanceChart').getContext('2d');
new Chart(pilierCtx, {
    type: 'radar',
    data: {
        labels: pilierPerformanceData.map(item => item.libelle),
        datasets: [{
            label: 'Progression',
            data: pilierPerformanceData.map(item => item.progression_moyenne),
            borderColor: '#00AE9E',
            backgroundColor: 'rgba(0, 123, 255, 0.2)',
            pointBackgroundColor: '#00AE9E'
        }, {
            label: 'Score Santé',
            data: pilierPerformanceData.map(item => item.score_sante),
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.2)',
            pointBackgroundColor: '#28a745'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            r: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});

// Générer la heatmap des équipes
function generateTeamHeatmap() {
    const container = document.getElementById('teamHeatmap');
    
    if (teamPerformanceData.length === 0) {
        container.innerHTML = '<p class="text-center text-muted">Aucune donnée disponible</p>';
        return;
    }
    
    let html = '<div class="heatmap-grid">';
    html += '<div class="heatmap-header">Performance des Équipes</div>';
    
    teamPerformanceData.forEach(team => {
        const intensity = Math.round(team.score_performance / 10);
        html += `
            <div class="heatmap-cell intensity-${intensity}" 
                 title="${team.nom}: ${team.score_performance} pts">
                <div class="team-name">${team.nom}</div>
                <div class="team-score">${team.score_performance}</div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

// Fonctions d'export
function exportAdvancedReport() {
    alert('Export Excel Pro en cours de développement avec graphiques intégrés');
}

function exportToPDF() {
    alert('Export PDF professionnel en cours de développement');
}

function exportToPowerPoint() {
    alert('Export PowerPoint automatique en cours de développement');
}

// Fonctions du tableau
function toggleTableView() {
    const table = document.getElementById('reportingTable');
    table.classList.toggle('table-expanded');
}

function showSousActionDetails(id) {
    alert(`Affichage des détails de la sous-action ${id}`);
}

function showProgressHistory(id) {
    alert(`Historique de progression de la sous-action ${id}`);
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    generateTeamHeatmap();
    
    // Animation du score de santé
    setTimeout(() => {
        const healthScore = document.querySelector('.health-score-circle circle:last-child');
        if (healthScore) {
            healthScore.style.strokeDashoffset = healthScore.getAttribute('stroke-dashoffset');
        }
    }, 500);
});
</script>

<!-- Styles additionnels pour la heatmap -->
<style>
.heatmap-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 10px;
    padding: 20px;
}

.heatmap-header {
    grid-column: 1 / -1;
    text-align: center;
    font-weight: bold;
    margin-bottom: 10px;
}

.heatmap-cell {
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    color: white;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.heatmap-cell:hover {
    transform: scale(1.05);
}

.team-name {
    font-size: 0.9rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.team-score {
    font-size: 1.2rem;
    font-weight: bold;
}

/* Intensités de couleur basées sur le score */
.intensity-1, .intensity-2 { background: #ff4444; }
.intensity-3, .intensity-4 { background: #ff8800; }
.intensity-5, .intensity-6 { background: #ffbb33; }
.intensity-7, .intensity-8 { background: #88cc88; }
.intensity-9, .intensity-10 { background: #44aa44; }

.table-expanded {
    font-size: 0.85rem;
}

.table-expanded .progress {
    height: 8px;
}
</style>
@endpush
