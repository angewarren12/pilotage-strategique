@extends('layouts.app')

@section('title', 'Sous-Actions - Plateforme de Stratelia')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-list-check me-2 text-primary"></i>
                Sous-Actions
            </h1>
            <p class="text-muted">Gestion et suivi des sous-actions avec taux d'avancement</p>
        </div>
        <div>
            @if(Auth::user()->canCreateSousAction())
            <a href="{{ route('sous-actions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Sous-Action
            </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('sous-actions.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="statut" class="form-label">Statut</label>
                    <select name="statut" id="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                        <option value="en_retard" {{ request('statut') == 'en_retard' ? 'selected' : '' }}>En retard</option>
                    </select>
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label for="avancement" class="form-label">Avancement</label>
                    <select name="avancement" id="avancement" class="form-select">
                        <option value="">Tous</option>
                        <option value="0-25" {{ request('avancement') == '0-25' ? 'selected' : '' }}>0-25%</option>
                        <option value="25-50" {{ request('avancement') == '25-50' ? 'selected' : '' }}>25-50%</option>
                        <option value="50-75" {{ request('avancement') == '50-75' ? 'selected' : '' }}>50-75%</option>
                        <option value="75-100" {{ request('avancement') == '75-100' ? 'selected' : '' }}>75-100%</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filtrer
                        </button>
                        <a href="{{ route('sous-actions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sous-Actions Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-table me-2"></i>
                Tableau des Sous-Actions ({{ $sousActions->count() }} éléments)
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="sousActionsTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Libellé</th>
                            <th>Action Parente</th>
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
                        <tr data-id="{{ $sousAction->id }}">
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
                                <div>
                                    <span class="badge bg-info">{{ $sousAction->action->code_complet }}</span>
                                    <br><small>{{ Str::limit($sousAction->action->libelle, 40) }}</small>
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
                                @if(Auth::user()->canUpdateSousAction())
                                <small>
                                    <a href="#" class="text-primary edit-taux" data-id="{{ $sousAction->id }}" data-taux="{{ $sousAction->taux_avancement }}">
                                        <i class="fas fa-edit me-1"></i>Modifier
                                    </a>
                                </small>
                                @endif
                            </td>
                            <td>
                                @if($sousAction->date_echeance)
                                    <div>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($sousAction->date_echeance)->format('d/m/Y') }}
                                        </span>
                                        @if($sousAction->date_realisation)
                                            <br><small class="text-success">
                                                <i class="fas fa-check me-1"></i>Réalisé le {{ \Carbon\Carbon::parse($sousAction->date_realisation)->format('d/m/Y') }}
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
                                    @if(Auth::user()->canUpdateSousAction())
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="deleteSousAction({{ $sousAction->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Aucune sous-action trouvée</p>
                                    @if(Auth::user()->canCreateSousAction())
                                    <a href="{{ route('sous-actions.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Créer la première sous-action
                                    </a>
                                    @endif
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

<!-- Modal pour modifier le taux d'avancement -->
<div class="modal fade" id="editTauxModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-percentage me-2"></i>
                    Modifier le taux d'avancement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTauxForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="taux_avancement" class="form-label">Taux d'avancement (%)</label>
                        <input type="range" class="form-range" id="taux_avancement" name="taux_avancement" min="0" max="100" step="5">
                        <div class="d-flex justify-content-between">
                            <span>0%</span>
                            <span id="taux_value">50%</span>
                            <span>100%</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="taux_input" class="form-label">Valeur exacte</label>
                        <input type="number" class="form-control" id="taux_input" name="taux_input" min="0" max="100" step="0.01">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form pour suppression -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .min-w-50 {
        min-width: 50px;
    }
    
    .progress {
        border-radius: 10px;
    }
    
    .progress-bar {
        border-radius: 10px;
        transition: width 0.3s ease;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .font-monospace {
        font-family: 'Courier New', monospace;
    }
</style>
@endpush

@push('scripts')
<script>
    // Gestion du modal de modification du taux
    let currentSousActionId = null;
    
    // Afficher le modal pour modifier le taux
    $(document).on('click', '.edit-taux', function(e) {
        e.preventDefault();
        currentSousActionId = $(this).data('id');
        const currentTaux = $(this).data('taux');
        
        $('#taux_avancement').val(currentTaux);
        $('#taux_input').val(currentTaux);
        $('#taux_value').text(currentTaux + '%');
        
        $('#editTauxModal').modal('show');
    });
    
    // Synchroniser le slider et l'input
    $('#taux_avancement').on('input', function() {
        const value = $(this).val();
        $('#taux_input').val(value);
        $('#taux_value').text(value + '%');
    });
    
    $('#taux_input').on('input', function() {
        const value = $(this).val();
        $('#taux_avancement').val(value);
        $('#taux_value').text(value + '%');
    });
    
    // Soumettre le formulaire de modification
    $('#editTauxForm').on('submit', function(e) {
        e.preventDefault();
        
        const taux = $('#taux_input').val();
        
        $.ajax({
            url: `/sous-actions/${currentSousActionId}/taux-avancement`,
            method: 'PATCH',
            data: {
                taux_avancement: taux,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Mettre à jour l'affichage
                const row = $(`tr[data-id="${currentSousActionId}"]`);
                row.find('.progress-bar').css('width', response.taux_avancement + '%');
                row.find('.fw-bold').text(response.taux_avancement + '%');
                
                // Mettre à jour le statut
                const statutCell = row.find('td:nth-child(8) .badge');
                statutCell.removeClass().addClass(`badge badge-${response.statut_color}`);
                statutCell.html(`<i class="fas fa-${response.statut === 'termine' ? 'check' : (response.statut === 'en_retard' ? 'exclamation-triangle' : 'clock')} me-1"></i>${response.statut}`);
                
                // Fermer le modal
                $('#editTauxModal').modal('hide');
                
                // Afficher un message de succès
                showAlert('Taux d\'avancement mis à jour avec succès !', 'success');
            },
            error: function(xhr) {
                showAlert('Erreur lors de la mise à jour du taux d\'avancement', 'danger');
            }
        });
    });
    
    // Fonction pour supprimer une sous-action
    function deleteSousAction(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette sous-action ?')) {
            const form = $('#deleteForm');
            form.attr('action', `/sous-actions/${id}`);
            form.submit();
        }
    }
    
    // Fonction pour afficher des alertes
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.container-fluid').prepend(alertHtml);
        
        // Auto-hide après 3 secondes
        setTimeout(function() {
            $('.alert').alert('close');
        }, 3000);
    }
    
    // Initialisation des tooltips
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endpush 