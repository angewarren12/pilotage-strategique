@extends('layouts.app')

@section('title', 'Dashboard - Plateforme de Stratelia')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                Dashboard
            </h1>
            <p class="text-muted mb-0">Vue d'ensemble de votre plateforme de pilotage stratégique</p>
        </div>
        <div>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print me-2"></i><span class="d-none d-sm-inline">Imprimer</span><span class="d-sm-none">Print</span>
            </button>
        </div>
    </div>

    <!-- Message de bienvenue personnalisé -->
    <div class="alert alert-success border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #00AE9E 0%, #33C2B5 100%); color: white;">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-hand-wave fa-2x me-3 d-none d-sm-inline"></i>
                <i class="fas fa-hand-wave fa-lg me-2 d-sm-none"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-1">
                    <span class="d-none d-sm-inline">Bienvenue, {{ Auth::user()->name }} ! 👋</span>
                    <span class="d-sm-none">Bonjour {{ Auth::user()->name }} ! 👋</span>
                </h5>
                <p class="mb-0">
                    <span class="d-none d-md-inline">
                        Nous sommes le {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}. 
                        Voici un aperçu de votre plateforme Stratelia.
                    </span>
                    <span class="d-md-none d-sm-inline">
                        {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMM') }} - 
                        Aperçu Stratelia
                    </span>
                    <span class="d-sm-none">
                        {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('DD/MM') }} - Stratelia
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-4 mb-2 mb-md-3">
            <div class="stats-card stats-card-compact">
                <div class="icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="number">{{ $stats['total_piliers'] }}</div>
                <div class="label">Piliers</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-4 mb-2 mb-md-3">
            <div class="stats-card stats-card-compact">
                <div class="icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="number">{{ $stats['total_objectifs_strategiques'] }}</div>
                <div class="label">Objectifs Stratégiques</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-4 mb-2 mb-md-3">
            <div class="stats-card stats-card-compact">
                <div class="icon">
                    <i class="fas fa-crosshairs"></i>
                </div>
                <div class="number">{{ $stats['total_objectifs_specifiques'] }}</div>
                <div class="label">Objectifs Spécifiques</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-4 mb-2 mb-md-3">
            <div class="stats-card stats-card-compact">
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="number">{{ $stats['total_actions'] }}</div>
                <div class="label">Actions</div>
            </div>
        </div>
    </div>

    <!-- Sous-Actions Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-4 mb-2 mb-md-3">
            <div class="stats-card stats-card-compact">
                <div class="icon">
                    <i class="fas fa-list-check"></i>
                </div>
                <div class="number">{{ $stats['total_sous_actions'] }}</div>
                <div class="label">Sous-Actions</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-4 mb-2 mb-md-3">
            <div class="stats-card stats-card-compact">
                <div class="icon">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div class="number">{{ $stats['sous_actions_terminees'] }}</div>
                <div class="label">Terminées</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-4 mb-2 mb-md-3">
            <div class="stats-card stats-card-compact">
                <div class="icon">
                    <i class="fas fa-clock text-warning"></i>
                </div>
                <div class="number">{{ $stats['sous_actions_en_cours'] }}</div>
                <div class="label">En Cours</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-4 mb-2 mb-md-3">
            <div class="stats-card stats-card-compact">
                <div class="icon">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                </div>
                <div class="number">{{ $stats['sous_actions_en_retard'] }}</div>
                <div class="label">En Retard</div>
            </div>
        </div>
    </div>

    <!-- Hierarchical View -->
    @if($piliers->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-sitemap me-2"></i>
                        Vue Hiérarchique
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Libellé</th>
                                    <th>Owner</th>
                                    <th>Avancement</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($piliers as $pilier)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $pilier->code }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $pilier->libelle }}</strong>
                                        @if($pilier->description)
                                            <br><small class="text-muted">{{ Str::limit($pilier->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pilier->owner)
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-user me-1"></i>{{ $pilier->owner->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar" style="width: {{ $pilier->taux_avancement }}%"></div>
                                            </div>
                                            <span class="fw-bold">{{ $pilier->taux_avancement }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $pilier->statut_color }}">
                                            @if($pilier->taux_avancement >= 100)
                                                <i class="fas fa-check me-1"></i>Terminé
                                            @elseif($pilier->taux_avancement >= 75)
                                                <i class="fas fa-clock me-1"></i>En cours
                                            @elseif($pilier->taux_avancement >= 50)
                                                <i class="fas fa-pause me-1"></i>En pause
                                            @else
                                                <i class="fas fa-play me-1"></i>Démarré
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('piliers.show', $pilier) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(Auth::user()->isAdminGeneral())
                                            <a href="{{ route('piliers.edit', $pilier) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
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
    </div>
    @endif

    <!-- Recent Sous-Actions -->
    @if($sousActions->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list-check me-2"></i>
                        Sous-Actions Récentes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Libellé</th>
                                    <th>Action Parente</th>
                                    <th>Owner</th>
                                    <th>Avancement</th>
                                    <th>Échéance</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sousActions->take(10) as $sousAction)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $sousAction->code_complet }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $sousAction->libelle }}</strong>
                                        @if($sousAction->description)
                                            <br><small class="text-muted">{{ Str::limit($sousAction->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $sousAction->action->code_complet }}</span>
                                        <br><small>{{ $sousAction->action->libelle }}</small>
                                    </td>
                                    <td>
                                        @if($sousAction->owner)
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-user me-1"></i>{{ $sousAction->owner->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar" style="width: {{ $sousAction->taux_avancement }}%"></div>
                                            </div>
                                            <span class="fw-bold">{{ $sousAction->taux_avancement }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($sousAction->date_echeance)
                                            <span class="badge bg-light text-dark">
                                                {{ \Carbon\Carbon::parse($sousAction->date_echeance)->format('d/m/Y') }}
                                            </span>
                                            @if($sousAction->ecart_jours !== null)
                                                <br><small class="text-{{ $sousAction->ecart_jours < 0 ? 'danger' : 'success' }}">
                                                    {{ $sousAction->ecart_libelle }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">Non définie</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $sousAction->statut_color }}">
                                            <i class="fas fa-{{ $sousAction->statut === 'termine' ? 'check' : ($sousAction->statut === 'en_retard' ? 'exclamation-triangle' : 'clock') }} me-1"></i>
                                            {{ $sousAction->statut_libelle }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('sous-actions.show', $sousAction) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(Auth::user()->canUpdateSousAction())
                                            <a href="{{ route('sous-actions.edit', $sousAction) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
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
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Actions Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(Auth::user()->canCreateSousAction())
                        <div class="col-lg-3 col-md-4 col-sm-4 mb-2 mb-md-3">
                            <a href="{{ route('sous-actions.create') }}" class="btn btn-primary w-100 btn-action-compact">
                                <i class="fas fa-plus me-1 me-sm-2"></i>
                                <span class="d-none d-sm-inline">Nouvelle Sous-Action</span>
                                <span class="d-sm-none">Sous-Action</span>
                            </a>
                        </div>
                        @endif
                        
                        @if(Auth::user()->canCreateAction())
                        <div class="col-lg-3 col-md-4 col-sm-4 mb-2 mb-md-3">
                            <a href="{{ route('actions.create') }}" class="btn btn-success w-100 btn-action-compact">
                                <i class="fas fa-plus me-1 me-sm-2"></i>
                                <span class="d-none d-sm-inline">Nouvelle Action</span>
                                <span class="d-sm-none">Action</span>
                            </a>
                        </div>
                        @endif
                        
                        @if(Auth::user()->canCreateObjectifSpecifique())
                        <div class="col-lg-3 col-md-4 col-sm-4 mb-2 mb-md-3">
                            <a href="{{ route('objectifs-specifiques.create') }}" class="btn btn-info w-100 btn-action-compact">
                                <i class="fas fa-plus me-1 me-sm-2"></i>
                                <span class="d-none d-sm-inline">Nouvel Objectif Spécifique</span>
                                <span class="d-sm-none">Objectif</span>
                            </a>
                        </div>
                        @endif
                        
                        <div class="col-lg-3 col-md-4 col-sm-4 mb-2 mb-md-3">
                            <a href="{{ route('reporting') }}" class="btn btn-warning w-100 btn-action-compact">
                                <i class="fas fa-chart-bar me-1 me-sm-2"></i>
                                <span class="d-none d-sm-inline">Reporting Standard</span>
                                <span class="d-sm-none">Standard</span>
                            </a>
                        </div>
                        
                        <div class="col-lg-3 col-md-4 col-sm-4 mb-2 mb-md-3">
                            <a href="{{ route('reporting.advanced') }}" class="btn btn-primary w-100 btn-action-compact">
                                <i class="fas fa-chart-line me-1 me-sm-2"></i>
                                <span class="d-none d-sm-inline">Reporting Avancé</span>
                                <span class="d-sm-none">Avancé</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles responsifs pour le dashboard */
.stats-card-compact {
    padding: 1.5rem 1rem !important;
    min-height: auto !important;
}

.stats-card-compact .icon {
    font-size: 2rem !important;
    margin-bottom: 0.5rem !important;
}

.stats-card-compact .number {
    font-size: 2rem !important;
    margin-bottom: 0.25rem !important;
}

    .stats-card-compact .label {
        font-size: 0.875rem !important;
        margin-bottom: 0 !important;
    }
    
    .btn-action-compact {
        padding: 0.5rem 0.75rem !important;
        font-size: 0.875rem !important;
        min-height: 38px !important;
    }

    @media (max-width: 768px) {
        .stats-card-compact {
            padding: 1rem 0.75rem !important;
            margin-bottom: 0.75rem !important;
        }
        
        .stats-card-compact .icon {
            font-size: 1.5rem !important;
            margin-bottom: 0.375rem !important;
        }
        
        .stats-card-compact .number {
            font-size: 1.5rem !important;
            margin-bottom: 0.25rem !important;
        }
        
        .stats-card-compact .label {
            font-size: 0.8rem !important;
        }
        
        /* Message de bienvenue responsive sur tablette */
        .alert .fa-hand-wave {
            font-size: 1.75rem !important;
            margin-right: 1rem !important;
        }
        
        .alert-heading {
            font-size: 1.1rem !important;
        }
        
        .alert p {
            font-size: 0.9rem !important;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .table th,
        .table td {
            padding: 0.5rem !important;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem !important;
        }
        
        .card-header h5 {
            font-size: 1rem !important;
        }
        
        .progress {
            height: 6px !important;
        }
        
        .badge {
            font-size: 0.75rem !important;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding: 0.5rem !important;
        }
        
        .stats-card-compact {
            padding: 0.75rem 0.5rem !important;
        }
        
        .stats-card-compact .icon {
            font-size: 1.25rem !important;
        }
        
        .stats-card-compact .number {
            font-size: 1.25rem !important;
        }
        
        .btn-action-compact {
            padding: 0.375rem 0.5rem !important;
            font-size: 0.8rem !important;
            min-height: 32px !important;
        }
        
        /* Message de bienvenue compact sur mobile */
        .alert .fa-hand-wave {
            font-size: 1.5rem !important;
            margin-right: 0.75rem !important;
        }
        
        .alert-heading {
            font-size: 1rem !important;
        }
        
        .alert p {
            font-size: 0.85rem !important;
        }
        
        .table-responsive {
            font-size: 0.8rem;
        }
        
        .table th,
        .table td {
            padding: 0.375rem !important;
        }
        
        .btn {
            font-size: 0.875rem !important;
            padding: 0.375rem 0.75rem !important;
        }
        
        .card {
            margin-bottom: 1rem !important;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        .card-header {
            padding: 0.75rem 1rem !important;
        }
        
        /* Masquer certaines colonnes sur très petits écrans */
        .table th:nth-child(3),
        .table td:nth-child(3) {
            display: none;
        }
        
        .table th:nth-child(5),
        .table td:nth-child(5) {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .stats-card-compact .label {
            font-size: 0.75rem !important;
        }
        
        .stats-card-compact .icon {
            font-size: 1.125rem !important;
        }
        
        .stats-card-compact .number {
            font-size: 1.125rem !important;
        }
        
        .btn-action-compact {
            padding: 0.25rem 0.375rem !important;
            font-size: 0.75rem !important;
            min-height: 28px !important;
        }
        
        .table th:nth-child(4),
        .table td:nth-child(4) {
            display: none;
        }
        
        .container-fluid {
            padding: 0.25rem !important;
        }
        
        .card-body {
            padding: 0.75rem !important;
        }
        
        .card-header {
            padding: 0.5rem 0.75rem !important;
        }
        
        /* Message de bienvenue ultra-compact sur très petits écrans */
        .alert .fa-hand-wave {
            font-size: 1.5rem !important;
            margin-right: 0.75rem !important;
        }
        
        .alert-heading {
            font-size: 1rem !important;
        }
        
        .alert p {
            font-size: 0.875rem !important;
        }
        
        /* Masquer le message de bienvenue sur très petits écrans si nécessaire */
        .alert .d-none-mobile {
            display: none !important;
        }
    }
</style>

@endsection

@push('scripts')
<script>
    // Auto-refresh dashboard every 5 minutes
    setInterval(function() {
        location.reload();
    }, 300000);
</script>
@endpush 