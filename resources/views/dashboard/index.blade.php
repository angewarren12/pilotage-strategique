@extends('layouts.app')

@section('title', 'Dashboard - Plateforme de Pilotage Stratégique')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                Dashboard
            </h1>
            <p class="text-muted">Vue d'ensemble de votre plateforme de pilotage stratégique</p>
        </div>
        <div>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimer
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="number">{{ $stats['total_piliers'] }}</div>
                <div class="label">Piliers</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="number">{{ $stats['total_objectifs_strategiques'] }}</div>
                <div class="label">Objectifs Stratégiques</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="icon">
                    <i class="fas fa-crosshairs"></i>
                </div>
                <div class="number">{{ $stats['total_objectifs_specifiques'] }}</div>
                <div class="label">Objectifs Spécifiques</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
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
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="icon">
                    <i class="fas fa-list-check"></i>
                </div>
                <div class="number">{{ $stats['total_sous_actions'] }}</div>
                <div class="label">Sous-Actions</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="icon">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div class="number">{{ $stats['sous_actions_terminees'] }}</div>
                <div class="label">Terminées</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="icon">
                    <i class="fas fa-clock text-warning"></i>
                </div>
                <div class="number">{{ $stats['sous_actions_en_cours'] }}</div>
                <div class="label">En Cours</div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
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
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('sous-actions.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-2"></i>
                                Nouvelle Sous-Action
                            </a>
                        </div>
                        @endif
                        
                        @if(Auth::user()->canCreateAction())
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('actions.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-plus me-2"></i>
                                Nouvelle Action
                            </a>
                        </div>
                        @endif
                        
                        @if(Auth::user()->canCreateObjectifSpecifique())
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('objectifs-specifiques.create') }}" class="btn btn-info w-100">
                                <i class="fas fa-plus me-2"></i>
                                Nouvel Objectif Spécifique
                            </a>
                        </div>
                        @endif
                        
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('reporting') }}" class="btn btn-warning w-100">
                                <i class="fas fa-chart-bar me-2"></i>
                                Voir le Reporting
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh dashboard every 5 minutes
    setInterval(function() {
        location.reload();
    }, 300000);
</script>
@endpush 