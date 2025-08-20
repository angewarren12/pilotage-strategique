@extends('layouts.app')

@section('title', 'Objectifs Stratégiques')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-bullseye me-2"></i>
                Objectifs Stratégiques
            </h1>
            <p class="text-muted">Gestion des objectifs stratégiques</p>
        </div>
        
        @if(Auth::user()->canCreateObjectifStrategique())
        <a href="{{ route('objectifs-strategiques.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Nouvel Objectif Stratégique
        </a>
        @endif
    </div>

    <!-- Tableau des objectifs stratégiques -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Liste des Objectifs Stratégiques
                <span class="badge bg-primary ms-2">{{ $objectifsStrategiques->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($objectifsStrategiques->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Libellé</th>
                            <th>Pilier</th>
                            <th>Owner</th>
                            <th>Taux d'Avancement</th>
                            <th>Objectifs Spécifiques</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($objectifsStrategiques as $objectifStrategique)
                        <tr>
                            <td>
                                <span class="badge bg-info">{{ $objectifStrategique->code_complet }}</span>
                            </td>
                            <td>
                                <strong>{{ $objectifStrategique->libelle }}</strong>
                                @if($objectifStrategique->description)
                                    <br><small class="text-muted">{{ Str::limit($objectifStrategique->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $objectifStrategique->pilier->code ?? 'N/A' }}</span>
                                <br><small>{{ $objectifStrategique->pilier->libelle ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @if($objectifStrategique->owner)
                                    <span class="badge bg-success">{{ $objectifStrategique->owner->name }}</span>
                                @else
                                    <span class="text-muted">Non assigné</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $objectifStrategique->statut_color }}" 
                                             style="width: {{ $objectifStrategique->taux_avancement }}%"></div>
                                    </div>
                                    <span class="badge bg-{{ $objectifStrategique->statut_color }}">
                                        {{ $objectifStrategique->taux_avancement }}%
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $objectifStrategique->objectifsSpecifiques->count() }}</span>
                                <small class="text-muted">objectifs</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-info" 
                                            title="Vue hiérarchique"
                                            wire:click="$dispatch('openHierarchicalModal', { pilierId: {{ $objectifStrategique->pilier->id }} })">
                                        <i class="fas fa-sitemap"></i>
                                    </button>
                                    <a href="{{ route('objectifs-strategiques.show', $objectifStrategique) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(Auth::user()->canCreateObjectifStrategique())
                                    <a href="{{ route('objectifs-strategiques.edit', $objectifStrategique) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('objectifs-strategiques.destroy', $objectifStrategique) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet objectif stratégique ?')">
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
                <i class="fas fa-bullseye fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucun objectif stratégique trouvé</h5>
                <p class="text-muted">Commencez par créer votre premier objectif stratégique.</p>
                @if(Auth::user()->canCreateObjectifStrategique())
                <a href="{{ route('objectifs-strategiques.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Créer le premier objectif stratégique
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
    <!-- Composant Livewire pour le modal hiérarchique global -->
    <!-- Composant PilierDetailsModalNew supprimé -->
@endsection 