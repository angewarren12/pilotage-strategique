@extends('layouts.app')

@section('title', 'Reporting - Plateforme de Pilotage Stratégique')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-bar me-2 text-primary"></i>
                Reporting
            </h1>
            <p class="text-muted">Analyse et suivi détaillé des sous-actions</p>
        </div>
        <div>
            <button class="btn btn-success me-2" onclick="exportToExcel()">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </button>
            <button class="btn btn-danger" onclick="exportToPDF()">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </button>
        </div>
    </div>

    <!-- Filtres Avancés -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>
                Filtres Avancés
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reporting') }}" id="reportingFilters">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="owner" class="form-label">Owner</label>
                        <select name="owner" id="owner" class="form-select">
                            <option value="">Tous les owners</option>
                            @foreach($sousActions->pluck('owner')->unique()->filter() as $owner)
                                <option value="{{ $owner->id }}" {{ request('owner') == $owner->id ? 'selected' : '' }}>
                                    {{ $owner->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select name="statut" id="statut" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                            <option value="en_retard" {{ request('statut') == 'en_retard' ? 'selected' : '' }}>En retard</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="periode" class="form-label">Période</label>
                        <select name="periode" id="periode" class="form-select">
                            <option value="">Toutes les périodes</option>
                            <option value="semaine" {{ request('periode') == 'semaine' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="mois" {{ request('periode') == 'mois' ? 'selected' : '' }}>Ce mois</option>
                            <option value="trimestre" {{ request('periode') == 'trimestre' ? 'selected' : '' }}>Ce trimestre</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="avancement" class="form-label">Avancement</label>
                        <select name="avancement" id="avancement" class="form-select">
                            <option value="">Tous</option>
                            <option value="0-25" {{ request('avancement') == '0-25' ? 'selected' : '' }}>0-25%</option>
                            <option value="25-50" {{ request('avancement') == '25-50' ? 'selected' : '' }}>25-50%</option>
                            <option value="50-75" {{ request('avancement') == '50-75' ? 'selected' : '' }}>50-75%</option>
                            <option value="75-100" {{ request('avancement') == '75-100' ? 'selected' : '' }}>75-100%</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date_debut" class="form-label">Date de début</label>
                        <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ request('date_debut') }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="date_fin" class="form-label">Date de fin</label>
                        <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ request('date_fin') }}">
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Appliquer les filtres
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon">
                    <i class="fas fa-list-check"></i>
                </div>
                <div class="number">{{ $sousActions->count() }}</div>
                <div class="label">Total Sous-Actions</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <div class="number">{{ $sousActions->where('statut', 'termine')->count() }}</div>
                <div class="label">Terminées</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                </div>
                <div class="number">{{ $sousActions->where('statut', 'en_retard')->count() }}</div>
                <div class="label">En Retard</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon">
                    <i class="fas fa-percentage text-info"></i>
                </div>
                <div class="number">{{ round($sousActions->avg('taux_avancement'), 1) }}%</div>
                <div class="label">Moyenne Avancement</div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Répartition par Statut
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="statutChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Avancement par Owner
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="ownerChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau de Reporting -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-table me-2"></i>
                Tableau de Reporting ({{ $sousActions->count() }} éléments)
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="reportingTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Libellé</th>
                            <th>Hiérarchie</th>
                            <th>Owner</th>
                            <th>Avancement</th>
                            <th>Échéance</th>
                            <th>Écart</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sousActions as $sousAction)
                        <tr>
                            <td>
                                <span class="badge bg-secondary font-monospace">{{ $sousAction->code_complet }}</span>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $sousAction->libelle }}</strong>
                                    @if($sousAction->description)
                                        <br><small class="text-muted">{{ Str::limit($sousAction->description, 60) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-layer-group me-1 text-primary"></i>
                                        <span>{{ $sousAction->action->objectifSpecifique->objectifStrategique->pilier->code }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-bullseye me-1 text-info"></i>
                                        <span>{{ $sousAction->action->objectifSpecifique->objectifStrategique->code }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-crosshairs me-1 text-warning"></i>
                                        <span>{{ $sousAction->action->objectifSpecifique->code }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-tasks me-1 text-success"></i>
                                        <span>{{ $sousAction->action->code }}</span>
                                    </div>
                                </div>
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
                                    <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                        <div class="progress-bar" style="width: {{ $sousAction->taux_avancement }}%"></div>
                                    </div>
                                    <span class="fw-bold min-w-50">{{ $sousAction->taux_avancement }}%</span>
                                </div>
                            </td>
                            <td>
                                @if($sousAction->date_echeance)
                                    <div>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-calendar me-1"></i>{{ $sousAction->date_echeance->format('d/m/Y') }}
                                        </span>
                                        @if($sousAction->date_realisation)
                                            <br><small class="text-success">
                                                <i class="fas fa-check me-1"></i>Réalisé le {{ $sousAction->date_realisation->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">Non définie</span>
                                @endif
                            </td>
                            <td>
                                @if($sousAction->ecart_jours !== null)
                                    <span class="badge bg-{{ $sousAction->ecart_jours < 0 ? 'danger' : ($sousAction->ecart_jours > 0 ? 'success' : 'info') }}">
                                        <i class="fas fa-{{ $sousAction->ecart_jours < 0 ? 'exclamation-triangle' : ($sousAction->ecart_jours > 0 ? 'check' : 'clock') }} me-1"></i>
                                        {{ $sousAction->ecart_libelle }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
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
                                    <a href="{{ route('sous-actions.show', $sousAction) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(Auth::user()->canUpdateSousAction())
                                    <a href="{{ route('sous-actions.edit', $sousAction) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                    <p>Aucune donnée trouvée avec les filtres actuels</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .min-w-50 {
        min-width: 50px;
    }
    
    .font-monospace {
        font-family: 'Courier New', monospace;
    }
    
    .progress {
        border-radius: 10px;
    }
    
    .progress-bar {
        border-radius: 10px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Données pour les graphiques
    const statutData = {
        en_cours: {{ $sousActions->where('statut', 'en_cours')->count() }},
        termine: {{ $sousActions->where('statut', 'termine')->count() }},
        en_retard: {{ $sousActions->where('statut', 'en_retard')->count() }}
    };
    
    const ownerData = @json($sousActions->groupBy('owner_id')->map(function($group) {
        return [
            'name' => $group->first()->owner->name ?? 'Non assigné',
            'avg' => round($group->avg('taux_avancement'), 1)
        ];
    })->values());
    
    // Graphique en camembert pour les statuts
    const statutCtx = document.getElementById('statutChart').getContext('2d');
    new Chart(statutCtx, {
        type: 'doughnut',
        data: {
            labels: ['En cours', 'Terminé', 'En retard'],
            datasets: [{
                data: [statutData.en_cours, statutData.termine, statutData.en_retard],
                backgroundColor: ['#FFC107', '#28A745', '#DC3545'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Graphique en barres pour les owners
    const ownerCtx = document.getElementById('ownerChart').getContext('2d');
    new Chart(ownerCtx, {
        type: 'bar',
        data: {
            labels: ownerData.map(item => item.name),
            datasets: [{
                label: 'Moyenne d\'avancement (%)',
                data: ownerData.map(item => item.avg),
                backgroundColor: '#4CAF50',
                borderColor: '#45a049',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    
    // Fonctions d'export
    function exportToExcel() {
        // Simulation d'export Excel
        alert('Fonctionnalité d\'export Excel en cours de développement');
    }
    
    function exportToPDF() {
        // Simulation d'export PDF
        alert('Fonctionnalité d\'export PDF en cours de développement');
    }
    
    function resetFilters() {
        document.getElementById('reportingFilters').reset();
        window.location.href = '{{ route("reporting") }}';
    }
    
    // Initialisation des tooltips
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endpush 