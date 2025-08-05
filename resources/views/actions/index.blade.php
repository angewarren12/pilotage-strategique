@extends('layouts.app')

@section('title', 'Actions')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tasks me-2"></i>
                Actions
            </h1>
            <p class="text-muted">Gestion des actions de pilotage</p>
        </div>
        
        @if(Auth::user()->canCreateAction())
        <a href="{{ route('actions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Nouvelle Action
        </a>
        @endif
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="owner" class="form-label">Owner</label>
                    <select name="owner" id="owner" class="form-select">
                        <option value="">Tous</option>
                        @foreach($actions->pluck('owner')->unique()->filter() as $owner)
                            <option value="{{ $owner->id }}" {{ request('owner') == $owner->id ? 'selected' : '' }}>
                                {{ $owner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="objectif_specifique" class="form-label">Objectif Spécifique</label>
                    <select name="objectif_specifique" id="objectif_specifique" class="form-select">
                        <option value="">Tous</option>
                        @foreach($actions->pluck('objectifSpecifique')->unique()->filter() as $os)
                            <option value="{{ $os->id }}" {{ request('objectif_specifique') == $os->id ? 'selected' : '' }}>
                                {{ $os->code_complet }} - {{ $os->libelle }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="avancement" class="form-label">Taux d'Avancement</label>
                    <select name="avancement" id="avancement" class="form-select">
                        <option value="">Tous</option>
                        <option value="0-25" {{ request('avancement') == '0-25' ? 'selected' : '' }}>0-25%</option>
                        <option value="25-50" {{ request('avancement') == '25-50' ? 'selected' : '' }}>25-50%</option>
                        <option value="50-75" {{ request('avancement') == '50-75' ? 'selected' : '' }}>50-75%</option>
                        <option value="75-100" {{ request('avancement') == '75-100' ? 'selected' : '' }}>75-100%</option>
                    </select>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-search me-1"></i>
                        Filtrer
                    </button>
                    <a href="{{ route('actions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des actions -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Liste des Actions
                <span class="badge bg-primary ms-2">{{ $actions->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($actions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Libellé</th>
                            <th>Objectif Spécifique</th>
                            <th>Owner</th>
                            <th>Taux d'Avancement</th>
                            <th>Sous-Actions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($actions as $action)
                        <tr>
                            <td>
                                <span class="badge bg-info">{{ $action->code_complet }}</span>
                            </td>
                            <td>
                                <strong>{{ $action->libelle }}</strong>
                                @if($action->description)
                                    <br><small class="text-muted">{{ Str::limit($action->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $action->objectifSpecifique->code_complet ?? 'N/A' }}</span>
                                <br><small>{{ $action->objectifSpecifique->libelle ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @if($action->owner)
                                    <span class="badge bg-success">{{ $action->owner->name }}</span>
                                @else
                                    <span class="text-muted">Non assigné</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $action->statut_color }}" 
                                             style="width: {{ $action->taux_avancement }}%"></div>
                                    </div>
                                    <span class="badge bg-{{ $action->statut_color }}">
                                        {{ $action->taux_avancement }}%
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $action->sousActions->count() }}</span>
                                <small class="text-muted">sous-actions</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-info" 
                                            title="Vue hiérarchique"
                                            wire:click="$dispatch('openHierarchicalModal', { pilierId: {{ $action->objectifSpecifique->objectifStrategique->pilier->id }} })">
                                        <i class="fas fa-sitemap"></i>
                                    </button>
                                    <a href="{{ route('actions.show', $action) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(Auth::user()->canCreateAction())
                                    <a href="{{ route('actions.edit', $action) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('actions.destroy', $action) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette action ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucune action trouvée</h5>
                <p class="text-muted">Commencez par créer votre première action.</p>
                @if(Auth::user()->canCreateAction())
                <a href="{{ route('actions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Créer la première action
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit des filtres
    const filterSelects = document.querySelectorAll('#owner, #objectif_specifique, #avancement');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endpush
    <!-- Composant Livewire pour le modal hiérarchique global -->
    <livewire:pilier-details-modal-new />
@endsection 