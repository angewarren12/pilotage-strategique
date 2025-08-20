@extends('layouts.app')

@section('title', 'Test Validations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Test des Validations
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Statistiques -->
                    @if(!empty($stats))
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $stats['total'] ?? 0 }}</h4>
                                        <small>Total</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $stats['pending'] ?? 0 }}</h4>
                                        <small>En attente</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $stats['approved'] ?? 0 }}</h4>
                                        <small>Approuvées</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h4>{{ $stats['rejected'] ?? 0 }}</h4>
                                        <small>Rejetées</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($validations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Élément</th>
                                        <th>Type</th>
                                        <th>Demandeur</th>
                                        <th>Statut</th>
                                        <th>Date demande</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($validations as $validation)
                                        <tr class="{{ $validation->isPending() ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>{{ $validation->element_name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $validation->element_code }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $validation->element_type }}</span>
                                            </td>
                                            <td>
                                                {{ $validation->requestedBy->name ?? 'Utilisateur inconnu' }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $validation->status_color }}">
                                                    <i class="{{ $validation->status_icon }}"></i>
                                                    {{ ucfirst($validation->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $validation->formatted_requested_at }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="showValidationDetails({{ $validation->id }})">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if($validation->isPending())
                                                        <button class="btn btn-sm btn-outline-success" 
                                                                onclick="approveValidation({{ $validation->id }})">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                onclick="rejectValidation({{ $validation->id }})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shield-alt text-muted fs-1"></i>
                            <p class="text-muted mt-2">Aucune validation trouvée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showValidationDetails(validationId) {
    // Ici vous pouvez ajouter une requête AJAX pour afficher les détails
    alert('Détails de la validation ' + validationId);
}

function approveValidation(validationId) {
    if (confirm('Approuver cette validation ?')) {
        // Ici vous pouvez ajouter une requête AJAX pour approuver
        location.reload();
    }
}

function rejectValidation(validationId) {
    const reason = prompt('Raison du rejet :');
    if (reason) {
        // Ici vous pouvez ajouter une requête AJAX pour rejeter
        location.reload();
    }
}
</script>
@endsection 