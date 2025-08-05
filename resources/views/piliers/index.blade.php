@extends('layouts.app')

@section('title', 'Gestion des Piliers')

@section('content')
<style>
    .progress {
        height: 25px;
        border-radius: 12px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        overflow: hidden;
        box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
    }
    .progress-bar {
        line-height: 25px;
        font-size: 13px;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0,0,0,.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .progress-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    .progress-bar.bg-success {
        background: linear-gradient(45deg, #28a745, #20c997);
    }
    .progress-bar.bg-info {
        background: linear-gradient(45deg, #17a2b8, #6f42c1);
    }
    .progress-bar.bg-warning {
        background: linear-gradient(45deg, #ffc107, #fd7e14);
    }
    .progress-bar.bg-danger {
        background: linear-gradient(45deg, #dc3545, #e83e8c);
    }
    .taux-avancement-display {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        padding: 0.5rem 0;
    }
    .text-success { color: #28a745 !important; }
    .text-info { color: #17a2b8 !important; }
    .text-warning { color: #ffc107 !important; }
    .text-danger { color: #dc3545 !important; }
    .card {
        transition: all 0.3s ease;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,.1);
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,.15);
    }
    .pilier-card, .objectif-strategique-card, .objectif-specifique-card, .action-card {
        position: relative;
    }
    .pilier-card::before, .objectif-strategique-card::before, 
    .objectif-specifique-card::before, .action-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #007bff, #6610f2);
        border-radius: 12px 12px 0 0;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-layer-group me-2 text-success"></i>
                    Gestion des Piliers
                </h2>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-info" onclick="openVueGeneraleModal()">
                        <i class="fas fa-chart-line me-2"></i>Vue Générale
                    </button>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createPilierModal">
                        <i class="fas fa-plus me-2"></i>
                        Nouveau Pilier
                    </button>
                </div>
            </div>

            <!-- Tableau des piliers -->
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-list me-2"></i>
                    Liste des Piliers ({{ $piliers->count() }})
                </div>
                <div class="card-body">
                    @if($piliers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Libellé</th>
                                        <th>Description</th>
                                        <th>Progression</th>
                                        <th>Objectifs Stratégiques</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($piliers as $pilier)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary fs-6">{{ $pilier->code }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $pilier->libelle }}</strong>
                                        </td>
                                        <td>
                                            @if($pilier->description)
                                                {{ Str::limit($pilier->description, 50) }}
                                            @else
                                                <span class="text-muted">Aucune description</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="progress pilier-card" data-pilier-id="{{ $pilier->id }}" style="height: 25px;">
                                                <div class="progress-bar pilier-progress-bar"
                                                     role="progressbar" 
                                                     style="width: {{ $pilier->taux_avancement }}%"
                                                     aria-valuenow="{{ $pilier->taux_avancement }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ number_format($pilier->taux_avancement, 2) }}%
                                                </div>
                                            </div>
                                            <div class="taux-avancement-display text-center mt-1 taux-avancement" data-pilier-id="{{ $pilier->id }}">
                                                {{ number_format($pilier->taux_avancement, 2) }}%
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $pilier->objectifsStrategiques->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary"
                                                        onclick="openPilierModal({{ $pilier->id }})"
                                                        title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-info"
                                                        onclick="openPilierHierarchiqueModal({{ $pilier->id }})"
                                                        title="Vue hiérarchique">
                                                    <i class="fas fa-sitemap"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                        title="Modifier"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editPilierModal"
                                                        data-pilier-id="{{ $pilier->id }}"
                                                        data-pilier-code="{{ $pilier->code }}"
                                                        data-pilier-libelle="{{ $pilier->libelle }}"
                                                        data-pilier-description="{{ $pilier->description ?? '' }}"
                                                        onclick="chargerDonneesPilierEdit(this)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        title="Supprimer"
                                                        onclick="supprimerPilier({{ $pilier->id }}, '{{ addslashes($pilier->libelle) }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun pilier</h5>
                            <p class="text-muted">Commencez par créer votre premier pilier stratégique.</p>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createPilierModal">
                                <i class="fas fa-plus me-2"></i>
                                Créer le premier pilier
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Créer Pilier -->
<div class="modal fade" id="createPilierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau Pilier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createPilierForm">
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Éditer Pilier -->
<div class="modal fade" id="editPilierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier le Pilier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPilierForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="pilier_id" id="editPilierId">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" id="editPilierCode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" id="editPilierLibelle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="editPilierDescription" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Modal Créer Objectif Stratégique -->
<div class="modal fade" id="createObjectifStrategiqueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvel Objectif Stratégique</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createObjectifStrategiqueForm">
                <div class="modal-body">
                    <input type="hidden" name="pilier_id" id="createObjectifStrategiquePilierId">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" id="createObjectifStrategiqueCode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" name="owner_id">
                            <option value="">Sélectionner un owner</option>
                            @foreach(App\Models\User::whereHas('role', function($query) {
                                $query->whereIn('nom', ['admin_general', 'owner_os']);
                            })->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Éditer Objectif Stratégique -->
<div class="modal fade" id="editObjectifStrategiqueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier l'Objectif Stratégique</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editObjectifStrategiqueForm">
                <div class="modal-body">
                    <input type="hidden" name="objectif_id" id="editObjectifStrategiqueId">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" id="editObjectifStrategiqueCode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" id="editObjectifStrategiqueLibelle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="editObjectifStrategiqueDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilier *</label>
                        <select class="form-select" name="pilier_id" id="editObjectifStrategiquePilierId" required>
                            <option value="">Sélectionner un pilier</option>
                            @foreach(App\Models\Pilier::all() as $pilier)
                                <option value="{{ $pilier->id }}">{{ $pilier->code }} - {{ $pilier->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" name="owner_id" id="editObjectifStrategiqueOwnerId">
                            <option value="">Sélectionner un owner</option>
                            @foreach(App\Models\User::whereHas('role', function($query) {
                                $query->whereIn('nom', ['admin_general', 'owner_os']);
                            })->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Créer Objectif Spécifique -->
<div class="modal fade" id="createObjectifSpecifiqueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvel Objectif Spécifique</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createObjectifSpecifiqueForm">
                <div class="modal-body">
                    <input type="hidden" name="objectif_strategique_id" id="createObjectifSpecifiqueObjectifStrategiqueId">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" id="createObjectifSpecifiqueCode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" name="owner_id">
                            <option value="">Sélectionner un owner</option>
                            @foreach(App\Models\User::whereHas('role', function($query) {
                                $query->whereIn('nom', ['admin_general', 'owner_os', 'owner_pil']);
                            })->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Éditer Objectif Spécifique -->
<div class="modal fade" id="editObjectifSpecifiqueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier l'Objectif Spécifique</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editObjectifSpecifiqueForm">
                <div class="modal-body">
                    <input type="hidden" name="objectif_id" id="editObjectifSpecifiqueId">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" id="editObjectifSpecifiqueCode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" id="editObjectifSpecifiqueLibelle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="editObjectifSpecifiqueDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Objectif Stratégique *</label>
                        <select class="form-select" name="objectif_strategique_id" id="editObjectifSpecifiqueObjectifStrategiqueId" required>
                            <option value="">Sélectionner un objectif stratégique</option>
                            @foreach(App\Models\ObjectifStrategique::all() as $objectifStrategique)
                                <option value="{{ $objectifStrategique->id }}">{{ $objectifStrategique->code_complet }} - {{ $objectifStrategique->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" name="owner_id" id="editObjectifSpecifiqueOwnerId">
                            <option value="">Sélectionner un owner</option>
                            @foreach(App\Models\User::whereHas('role', function($query) {
                                $query->whereIn('nom', ['admin_general', 'owner_os', 'owner_pil']);
                            })->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Créer Action -->
<div class="modal fade" id="createActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createActionForm">
                <div class="modal-body">
                    <input type="hidden" name="objectif_specifique_id" id="createActionObjectifSpecifiqueId">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" id="createActionCode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" name="owner_id">
                            <option value="">Sélectionner un owner</option>
                            @foreach(App\Models\User::whereHas('role', function($query) {
                                $query->whereIn('nom', ['admin_general', 'owner_os', 'owner_pil', 'owner_action']);
                            })->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Éditer Action -->
<div class="modal fade" id="editActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier l'Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editActionForm">
                <div class="modal-body">
                    <input type="hidden" name="action_id" id="editActionId">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" id="editActionCode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" id="editActionLibelle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="editActionDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Objectif Spécifique *</label>
                        <select class="form-select" name="objectif_specifique_id" id="editActionObjectifSpecifiqueId" required>
                            <option value="">Sélectionner un objectif spécifique</option>
                            @foreach(App\Models\ObjectifSpecifique::all() as $objectifSpecifique)
                                <option value="{{ $objectifSpecifique->id }}">{{ $objectifSpecifique->code_complet }} - {{ $objectifSpecifique->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" name="owner_id" id="editActionOwnerId">
                            <option value="">Sélectionner un owner</option>
                            @foreach(App\Models\User::whereHas('role', function($query) {
                                $query->whereIn('nom', ['admin_general', 'owner_os', 'owner_pil', 'owner_action']);
                            })->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Créer Sous-Action -->
<div class="modal fade" id="createSousActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Sous-Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createSousActionForm">
                <div class="modal-body">
                    <input type="hidden" name="action_id" id="createSousActionActionId">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" id="createSousActionCode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Taux d'avancement (%) *</label>
                        <input type="number" class="form-control" name="taux_avancement" min="0" max="100" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date d'échéance</label>
                        <input type="date" class="form-control" name="date_echeance">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" name="owner_id">
                            <option value="">Sélectionner un owner</option>
                            @foreach(App\Models\User::whereHas('role', function($query) {
                                $query->whereIn('nom', ['admin_general', 'owner_os', 'owner_pil', 'owner_action', 'owner_sous_action']);
                            })->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Éditer Sous-Action -->
<div class="modal fade" id="editSousActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la Sous-Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSousActionForm">
                <div class="modal-body">
                    <input type="hidden" name="sous_action_id" id="editSousActionId">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" id="editSousActionCode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" name="libelle" id="editSousActionLibelle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="editSousActionDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Taux d'avancement (%) *</label>
                        <input type="number" class="form-control" name="taux_avancement" id="editSousActionTauxAvancement" min="0" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date d'échéance</label>
                        <input type="date" class="form-control" name="date_echeance" id="editSousActionDateEcheance">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Action *</label>
                        <select class="form-select" name="action_id" id="editSousActionActionId" required>
                            <option value="">Sélectionner une action</option>
                            @foreach(App\Models\Action::all() as $action)
                                <option value="{{ $action->id }}">{{ $action->code_complet }} - {{ $action->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" name="owner_id" id="editSousActionOwnerId">
                            <option value="">Sélectionner un owner</option>
                            @foreach(App\Models\User::whereHas('role', function($query) {
                                $query->whereIn('nom', ['admin_general', 'owner_os', 'owner_pil', 'owner_action', 'owner_sous_action']);
                            })->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Vue Générale Améliorée -->
<div class="modal fade" id="vueGeneraleModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <!-- Header avec navigation et filtres -->
            <div class="modal-header bg-gradient-primary text-white border-0">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-grow-1">
                        <h4 class="modal-title mb-0">
                            <i class="fas fa-chart-network me-2"></i>
                            Vue Générale - Pilotage Stratégique
                        </h4>
                        <small class="text-white-50">Vue d'ensemble complète de la hiérarchie stratégique</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="toggleViewMode()">
                            <i class="fas fa-th-large me-1"></i>
                            <span id="viewModeText">Vue Tableau</span>
                        </button>
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="exportVueGenerale()">
                            <i class="fas fa-download me-1"></i>
                            Exporter
                        </button>
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Barre d'outils avec filtres -->
            <div class="modal-toolbar bg-light border-bottom p-3">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchVueGenerale" placeholder="Rechercher...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterStatus">
                            <option value="">Tous les statuts</option>
                            <option value="success">Terminé</option>
                            <option value="warning">En cours</option>
                            <option value="danger">En retard</option>
                            <option value="secondary">Non démarré</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterOwner">
                            <option value="">Tous les owners</option>
                            <!-- Options chargées dynamiquement -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshVueGenerale()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleFilters()">
                                <i class="fas fa-filter"></i>
                                Filtres
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="modal-body p-0" style="height: calc(100vh - 200px);">
                <!-- Vue Tableau (par défaut) -->
                <div id="tableView" class="h-100">
                    <div class="table-responsive h-100">
                        <table class="table table-hover mb-0" id="tableauVueGenerale">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th class="text-center" style="width: 15%;">
                                        <i class="fas fa-bullseye me-1"></i>
                                        COMITÉ STRATÉGIQUE
                                    </th>
                                    <th class="text-center" style="width: 15%;">
                                        <i class="fas fa-list-check me-1"></i>
                                        COMITÉ DE PILOTAGE
                                    </th>
                                    <th class="text-center" style="width: 15%;">
                                        <i class="fas fa-tasks me-1"></i>
                                        ACTIONS
                                    </th>
                                    <th class="text-center" style="width: 15%;">
                                        <i class="fas fa-check-circle me-1"></i>
                                        SOUS-ACTIONS
                                    </th>
                                    <th class="text-center" style="width: 8%;">
                                        <i class="fas fa-percentage me-1"></i>
                                        PROGRESSION
                                    </th>
                                    <th class="text-center" style="width: 12%;">
                                        <i class="fas fa-user me-1"></i>
                                        OWNER
                                    </th>
                                    <th class="text-center" style="width: 10%;">
                                        <i class="fas fa-calendar me-1"></i>
                                        ÉCHÉANCE
                                    </th>
                                    <th class="text-center" style="width: 10%;">
                                        <i class="fas fa-info-circle me-1"></i>
                                        STATUT
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tbodyVueGenerale">
                                <!-- Le contenu sera chargé dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Vue Cartes (alternative) -->
                <div id="cardView" class="h-100 d-none">
                    <div class="container-fluid h-100 p-4">
                        <div class="row" id="cardContainer">
                            <!-- Les cartes seront chargées dynamiquement -->
                        </div>
                    </div>
                </div>

                <!-- Indicateur de chargement -->
                <div id="loadingIndicator" class="d-none">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            <h5 class="text-muted">Chargement de la vue générale...</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer avec statistiques -->
            <div class="modal-footer bg-light border-top">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="d-flex gap-3">
                        <div class="text-center">
                            <div class="h5 mb-0 text-success" id="statsCompleted">0</div>
                            <small class="text-muted">Terminés</small>
                        </div>
                        <div class="text-center">
                            <div class="h5 mb-0 text-warning" id="statsInProgress">0</div>
                            <small class="text-muted">En cours</small>
                        </div>
                        <div class="text-center">
                            <div class="h5 mb-0 text-danger" id="statsOverdue">0</div>
                            <small class="text-muted">En retard</small>
                        </div>
                        <div class="text-center">
                            <div class="h5 mb-0 text-secondary" id="statsNotStarted">0</div>
                            <small class="text-muted">Non démarrés</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success me-2">Terminé</span>
                        <span class="badge bg-warning me-2">En cours</span>
                        <span class="badge bg-danger me-2">En retard</span>
                        <span class="badge bg-secondary me-2">Non démarré</span>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                            <i class="fas fa-check me-2"></i>
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Listener pour les toasts Livewire
        document.addEventListener('livewire:init', () => {
            Livewire.on('showToast', (event) => {
                console.log('🔍 [DEBUG] Toast event reçu:', event);
                // L'événement est un tableau avec un objet
                const toastData = event[0];
                showToast(toastData.type, toastData.message);
            });
            
            // Écouter les mises à jour de taux pour rafraîchir la page
            Livewire.on('refreshPilierList', () => {
                console.log('🔄 Rafraîchissement de la liste des piliers...');
                window.location.reload();
            });
            

        });

        // Fonction pour ouvrir le modal Livewire
        function openPilierModal(pilierId) {
            console.log('🔍 [DEBUG] Tentative d\'ouverture du modal pour le pilier:', pilierId);
            if (typeof Livewire !== 'undefined') {
                console.log('✅ [DEBUG] Livewire est disponible, dispatch de l\'événement');
                Livewire.dispatch('openPilierModal', { pilierId: pilierId });
            } else {
                console.error('❌ [ERROR] Livewire not initialized');
                alert('Erreur: Livewire n\'est pas initialisé');
            }
        }

        // Fonction pour ouvrir la Vue Générale
        function openVueGeneraleModal() {
            console.log('🔍 [DEBUG] Ouverture de la Vue Générale');
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('openVueGeneraleModal');
            } else {
                console.error('❌ [ERROR] Livewire not initialized');
                alert('Erreur: Livewire n\'est pas initialisé');
            }
        }


// Variables globales pour la navigation hiérarchique
let niveauActuel = 'pilier';
let historiqueNavigation = [];

// Fonction utilitaire pour échapper les caractères spéciaux dans les chaînes JavaScript
function escapeJsString(str) {
    if (!str) return '';
    return str.replace(/\\/g, '\\\\')
              .replace(/'/g, "\\'")
              .replace(/"/g, '\\"')
              .replace(/\n/g, '\\n')
              .replace(/\r/g, '\\r')
              .replace(/\t/g, '\\t');
}

// Fonction pour ouvrir la modale hiérarchique du pilier
function ouvrirModalPilier(button) {
    const pilierId = button.getAttribute('data-pilier-id');
    const code = button.getAttribute('data-pilier-code');
    const libelle = button.getAttribute('data-pilier-libelle');
    const description = button.getAttribute('data-pilier-description');
    const tauxAvancement = button.getAttribute('data-pilier-taux');
    const nbObjectifsStrategiques = button.getAttribute('data-pilier-count');

    // Réinitialiser la navigation
    historiqueNavigation = [];
    niveauActuel = 'pilier';

    // Charger les données du pilier
            chargerDonneesPilier(pilierId, code, libelle);
    
    // Afficher la modale
    const modal = new bootstrap.Modal(document.getElementById('hierarchieModal'));
    modal.show();
}

// Fonction pour charger les données du pilier dans la modale hiérarchique
function chargerDonneesPilier(pilierId, code, libelle, isRetour = false) {
    console.log('🚀 [DEBUG] chargerDonneesPilier appelée avec:', { pilierId, code, libelle, isRetour });
    
    const modalTitle = document.getElementById('hierarchieModalTitle');
    const modalBody = document.getElementById('hierarchieModalBody');
    const btnRetour = document.getElementById('btnRetour');

    // Ne modifier le titre que si ce n'est pas un retour
    if (!isRetour) {
    modalTitle.textContent = `Détails du Pilier ${code}`;
    }
    btnRetour.style.display = 'none';

    // Afficher un indicateur de chargement
    modalBody.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <h5 class="text-muted">Chargement des données du pilier...</h5>
        </div>
    `;

    console.log('🌐 [DEBUG] Appel AJAX vers:', `/api/piliers/${pilierId}/objectifs-strategiques`);

    // Charger les objectifs stratégiques via AJAX
    fetch(`/api/piliers/${pilierId}/objectifs-strategiques`)
        .then(response => {
            console.log('📡 [DEBUG] Réponse reçue:', { status: response.status, statusText: response.statusText });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📊 [DEBUG] Données reçues:', data);
            let objectifsStrategiquesHtml = '';
            if (data.objectifs_strategiques && data.objectifs_strategiques.length > 0) {
                data.objectifs_strategiques.forEach(objectif => {
                    objectifsStrategiquesHtml += `
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <span class="badge bg-info me-2">${code}.${objectif.code}</span>
                                    </h6>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="voirObjectifStrategique(${objectif.id}, '${escapeJsString(objectif.code)}', '${escapeJsString(objectif.libelle)}', ${pilierId}, '${escapeJsString(code)}', '${escapeJsString(libelle)}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                onclick="modifierObjectifStrategique(${objectif.id})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Objectif:</strong>
                                        <p class="mb-0 text-dark">${escapeJsString(objectif.libelle)}</p>
                                    </div>
                                    ${objectif.description ? `
                                    <div class="mb-3">
                                        <strong>Description:</strong>
                                        <p class="mb-0 text-muted small">${escapeJsString(objectif.description)}</p>
                                    </div>
                                    ` : ''}
                                    <div class="mb-3">
                                        <strong>Owner:</strong>
                                        <div class="mt-1">
                                        ${objectif.owner ? `<span class="badge bg-success"><i class="fas fa-user me-1"></i>${objectif.owner.name || objectif.owner}</span>` : '<span class="text-muted small">Non assigné</span>'}
                                    </div>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Pourcentage:</strong>
                                        <div class="progress mt-1" style="height: 12px;">
                                            <div class="progress-bar bg-success" style="width: ${objectif.taux_avancement}%" role="progressbar" aria-valuenow="${objectif.taux_avancement}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="text-center mt-1">
                                            <span class="badge bg-success fs-6">${objectif.taux_avancement}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                objectifsStrategiquesHtml = `
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-bullseye fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun objectif stratégique</h5>
                            <p class="text-muted">Ce pilier n'a pas encore d'objectifs stratégiques.</p>
                            <button type="button" class="btn btn-primary" onclick="ajouterObjectifStrategique(${pilierId})">
                                <i class="fas fa-plus me-2"></i>
                                Créer le premier objectif
                            </button>
                        </div>
                    </div>
                `;
            }

            console.log('🔨 [DEBUG] Génération du HTML pour le modal...');
            
            const modalHtml = `
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-info-circle me-2"></i>
                                Détails du Pilier
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Code:</strong>
                                        <span class="badge bg-primary fs-6">${code}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Libellé:</strong>
                                        <h5 class="text-dark">${libelle}</h5>
                                    </div>
                                </div>
                                ${data.description ? `<div class="mb-3"><strong>Description:</strong><p class="mb-0 text-muted">${escapeJsString(data.description)}</p></div>` : ''}
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Progression:</strong>
                                        <div class="progress mt-1" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: ${data.taux_avancement || 0}%" aria-valuenow="${data.taux_avancement || 0}" aria-valuemin="0" aria-valuemax="100">${data.taux_avancement || 0}%</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Objectifs Stratégiques:</strong> ${data.objectifs_strategiques ? data.objectifs_strategiques.length : 0}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-chart-line me-2"></i>
                                Statistiques
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="text-success mb-3">${data.taux_avancement || 0}%</h2>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h4 class="text-muted">${data.objectifs_strategiques ? data.objectifs_strategiques.length : 0}</h4>
                                            <small class="text-muted">Objectifs Stratégiques</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success">${data.objectifs_strategiques ? data.objectifs_strategiques.filter(o => o.taux_avancement == 100).length : 0}</h4>
                                            <small class="text-muted">Terminés</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-success">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-bullseye me-2"></i>
                            Objectifs Stratégiques (${data.objectifs_strategiques ? data.objectifs_strategiques.length : 0})
                        </div>
                        <button type="button" class="btn btn-light btn-sm" onclick="ajouterObjectifStrategique(${pilierId})">
                            <i class="fas fa-plus me-1"></i>
                            Ajouter un Objectif
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            ${objectifsStrategiquesHtml}
                        </div>
                    </div>
                </div>
            `;
            
            console.log('📝 [DEBUG] HTML généré, longueur:', modalHtml.length);
            console.log('📝 [DEBUG] Premiers 500 caractères du HTML:', modalHtml.substring(0, 500));
            
            // Assigner le HTML au modal
            modalBody.innerHTML = modalHtml;
            
            console.log('✅ [DEBUG] HTML assigné au modal avec succès');
        })
        .catch(error => {
            console.error('💥 [DEBUG] Erreur lors du chargement:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Erreur lors du chargement des données</h5>
                    <p class="mb-0">Détails: ${error.message}</p>
                    <button type="button" class="btn btn-primary mt-3" onclick="chargerDonneesPilier(${pilierId}, '${code}', '${libelle}')">
                        <i class="fas fa-redo me-2"></i>Réessayer
                    </button>
                </div>
            `;
        });
}



// Fonction pour retourner au niveau précédent (nouvelle approche)
function retourNiveauPrecedent() {
    console.log('🔄 [DEBUG] Retour au niveau précédent');
    
    if (currentAction) {
        // Retour vers l'objectif spécifique
        voirObjectifSpecifique(currentObjectifSpecifique.id, currentObjectifSpecifique.code, currentObjectifSpecifique.libelle);
    } else if (currentObjectifSpecifique) {
        // Retour vers l'objectif stratégique
        if (currentObjectifStrategique && currentPilier) {
            voirObjectifStrategique(currentObjectifStrategique.id, currentObjectifStrategique.code, currentObjectifStrategique.libelle, currentPilier.id, currentPilier.code, currentPilier.libelle);
        } else {
            retourPiliers();
        }
    } else if (currentObjectifStrategique) {
        // Retour vers le pilier
        if (currentPilier) {
            voirPilier(currentPilier.id, currentPilier.code, currentPilier.libelle);
        } else {
            retourPiliers();
        }
    } else {
        // Retour vers la liste des piliers
        retourPiliers();
    }
}

// Gestion des formulaires avec indicateurs de chargement
document.getElementById('createPilierForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Afficher l'indicateur de chargement
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Création...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    
    fetch('{{ route("piliers.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Pilier créé avec succès !');
            window.location.reload();
        } else {
            showToast('error', 'Erreur lors de la création : ' + data.message);
            // Restaurer le bouton
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('error', 'Erreur lors de la création.');
        // Restaurer le bouton
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

document.getElementById('editPilierForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Afficher l'indicateur de chargement
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sauvegarde...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    const pilierId = document.getElementById('editPilierId').value;
    
    // Ajouter la méthode PUT pour Laravel
    formData.append('_method', 'PUT');
    
    fetch(`/piliers/${pilierId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Pilier modifié avec succès !');
            window.location.reload();
        } else {
            showToast('error', 'Erreur lors de la modification : ' + data.message);
            // Restaurer le bouton
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('error', 'Erreur lors de la modification.');
        // Restaurer le bouton
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

document.getElementById('createObjectifStrategiqueForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('🚀 [DEBUG] Soumission du formulaire de création d\'objectif stratégique');
    
    // Récupérer les données du formulaire
    const code = document.getElementById('createObjectifStrategiqueCode').value;
    const libelle = document.getElementById('createObjectifStrategiqueLibelle').value;
    const description = document.getElementById('createObjectifStrategiqueDescription').value;
    const pilierId = document.getElementById('createObjectifStrategiquePilierId').value;
    const ownerId = document.getElementById('createObjectifStrategiqueOwnerId').value;
    
    console.log('📊 [DEBUG] Données du formulaire:', {
        code, libelle, description, pilierId, ownerId
    });
    
    // Préparer les données JSON
    const data = {
        code: code,
        libelle: libelle,
        description: description,
        pilier_id: pilierId,
        owner_id: ownerId || null
    };
    
    console.log('📤 [DEBUG] Données à envoyer:', data);
    
    fetch(`/api/objectifs-strategiques`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('📡 [DEBUG] Réponse reçue:', { status: response.status, statusText: response.statusText });
        return response.json();
    })
    .then(data => {
        console.log('📊 [DEBUG] Données de réponse:', data);
        
        if (data.success) {
            // Fermer la modale de création
            const modal = bootstrap.Modal.getInstance(document.getElementById('createObjectifStrategiqueModal'));
            modal.hide();
            
            // Recharger les données du pilier pour mettre à jour la liste
            const button = document.querySelector(`button[data-pilier-id="${pilierId}"]`);
            if (button) {
                ouvrirModalPilier(button);
            }
            
            showToast('success', 'Objectif stratégique créé avec succès !');
        } else {
            // Gestion spécifique des erreurs de validation
            if (data.errors) {
                let errorMessage = 'Erreurs de validation :\n';
                Object.keys(data.errors).forEach(field => {
                    errorMessage += `- ${field}: ${data.errors[field][0]}\n`;
                });
                alert(errorMessage);
            } else if (data.message) {
            alert('Erreur lors de la création : ' + data.message);
            } else {
                alert('Erreur lors de la création.');
            }
        }
    })
    .catch(error => {
        console.error('💥 [DEBUG] Erreur:', error);
        alert('Erreur lors de la création : ' + error.message);
    });
});

document.getElementById('editObjectifStrategiqueForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('🚀 [DEBUG] Soumission du formulaire d\'édition d\'objectif stratégique');
    
    // Récupérer les données du formulaire
    const objectifId = document.getElementById('editObjectifStrategiqueId').value;
    const code = document.getElementById('editObjectifStrategiqueCode').value;
    const libelle = document.getElementById('editObjectifStrategiqueLibelle').value;
    const description = document.getElementById('editObjectifStrategiqueDescription').value;
    const pilierId = document.getElementById('editObjectifStrategiquePilierId').value;
    const ownerId = document.getElementById('editObjectifStrategiqueOwnerId').value;
    
    console.log('📊 [DEBUG] Données du formulaire:', {
        objectifId, code, libelle, description, pilierId, ownerId
    });
    
    // Préparer les données JSON
    const data = {
        code: code,
        libelle: libelle,
        description: description,
        pilier_id: pilierId,
        owner_id: ownerId || null
    };
    
    console.log('📤 [DEBUG] Données à envoyer:', data);
    
    fetch(`/api/objectifs-strategiques/${objectifId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('📡 [DEBUG] Réponse reçue:', { status: response.status, statusText: response.statusText });
        return response.json();
    })
    .then(data => {
        console.log('📊 [DEBUG] Données de réponse:', data);
        
        if (data.success) {
            // Fermer la modale d'édition
            const modal = bootstrap.Modal.getInstance(document.getElementById('editObjectifStrategiqueModal'));
            modal.hide();
            
            // Recharger les données du pilier pour mettre à jour la liste
            const button = document.querySelector(`button[data-pilier-id="${pilierId}"]`);
            if (button) {
                ouvrirModalPilier(button);
            }
            
            showToast('success', 'Objectif stratégique modifié avec succès !');
        } else {
            // Gestion spécifique des erreurs de validation
            if (data.errors) {
                let errorMessage = 'Erreurs de validation :\n';
                Object.keys(data.errors).forEach(field => {
                    errorMessage += `- ${field}: ${data.errors[field][0]}\n`;
                });
                alert(errorMessage);
            } else if (data.message) {
            alert('Erreur lors de la modification : ' + data.message);
            } else {
                alert('Erreur lors de la modification.');
            }
        }
    })
    .catch(error => {
        console.error('💥 [DEBUG] Erreur:', error);
        alert('Erreur lors de la modification : ' + error.message);
    });
});

document.getElementById('createObjectifSpecifiqueForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');
    const objectifStrategiqueId = document.getElementById('createObjectifSpecifiqueObjectifStrategiqueId').value;
    
    fetch(`/api/objectifs-specifiques`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer la modale de création
            const modal = bootstrap.Modal.getInstance(document.getElementById('createObjectifSpecifiqueModal'));
            modal.hide();
            
            // Recharger dynamiquement le contenu de l'objectif stratégique
            // Extraire les informations de l'objectif stratégique depuis le titre de la modale
            const modalTitle = document.getElementById('hierarchieModalTitle').textContent;
            const codeMatch = modalTitle.match(/OS\d+/);
            const code = codeMatch ? codeMatch[0] : 'OS1';
            
            // Extraire le libellé depuis le titre
            const libelleMatch = modalTitle.match(/Détails de l'Objectif Stratégique (.+)/);
            const libelle = libelleMatch ? libelleMatch[1] : '';
            
            rechargerObjectifStrategique(objectifStrategiqueId, code, libelle);
        } else {
            // Gestion spécifique des erreurs de validation
            if (data.errors && data.errors.code) {
                alert('Erreur de code : ' + data.errors.code[0]);
            } else if (data.message) {
            alert('Erreur lors de la création : ' + data.message);
            } else {
                alert('Erreur lors de la création.');
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la création.');
    });
});

document.getElementById('editObjectifSpecifiqueForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const objectifId = document.getElementById('editObjectifSpecifiqueId').value;
    const objectifStrategiqueId = document.getElementById('editObjectifSpecifiqueObjectifStrategiqueId').value;
    
    // Collecter les données du formulaire
    const formData = {
        code: document.getElementById('editObjectifSpecifiqueCode').value,
        libelle: document.getElementById('editObjectifSpecifiqueLibelle').value,
        description: document.getElementById('editObjectifSpecifiqueDescription').value,
        objectif_strategique_id: objectifStrategiqueId,
        owner_id: document.getElementById('editObjectifSpecifiqueOwnerId').value
    };
    
    fetch(`/api/objectifs-specifiques/${objectifId}`, {
        method: 'PUT',
        body: JSON.stringify(formData),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer la modale d'édition
            const modal = bootstrap.Modal.getInstance(document.getElementById('editObjectifSpecifiqueModal'));
            modal.hide();
            
            // Recharger dynamiquement le contenu de l'objectif stratégique
            const modalTitle = document.getElementById('hierarchieModalTitle').textContent;
            const codeMatch = modalTitle.match(/OS\d+/);
            const code = codeMatch ? codeMatch[0] : 'OS1';
            
            // Extraire le libellé depuis le titre
            const libelleMatch = modalTitle.match(/Détails de l'Objectif Stratégique (.+)/);
            const libelle = libelleMatch ? libelleMatch[1] : '';
            
            rechargerObjectifStrategique(objectifStrategiqueId, code, libelle);
        } else {
            // Gestion spécifique des erreurs de validation
            if (data.errors && data.errors.code) {
                alert('Erreur de code : ' + data.errors.code[0]);
            } else if (data.message) {
            alert('Erreur lors de la modification : ' + data.message);
            } else {
                alert('Erreur lors de la modification.');
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification.');
    });
});

document.getElementById('createActionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');
    const objectifSpecifiqueId = document.getElementById('createActionObjectifSpecifiqueId').value;
    
    fetch(`/api/actions`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer la modale de création
            const modal = bootstrap.Modal.getInstance(document.getElementById('createActionModal'));
            modal.hide();
            
            // Recharger dynamiquement le contenu de l'objectif spécifique
            // Extraire les informations de l'objectif spécifique depuis le titre de la modale
            const modalTitle = document.getElementById('hierarchieModalTitle').textContent;
            const codeMatch = modalTitle.match(/PIL\d+/);
            const code = codeMatch ? codeMatch[0] : 'PIL1';
            
            // Extraire le libellé depuis le titre
            const libelleMatch = modalTitle.match(/Détails de l'Objectif Spécifique (.+)/);
            const libelle = libelleMatch ? libelleMatch[1] : '';
            
            rechargerObjectifSpecifique(objectifSpecifiqueId, code, libelle);
        } else {
            // Gestion spécifique des erreurs de validation
            if (data.errors && data.errors.code) {
                alert('Erreur de code : ' + data.errors.code[0]);
            } else if (data.message) {
            alert('Erreur lors de la création : ' + data.message);
            } else {
                alert('Erreur lors de la création.');
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la création.');
    });
});

document.getElementById('editActionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const actionId = document.getElementById('editActionId').value;
    const objectifSpecifiqueId = document.getElementById('editActionObjectifSpecifiqueId').value;
    
    // Collecter les données du formulaire
    const formData = {
        code: document.getElementById('editActionCode').value,
        libelle: document.getElementById('editActionLibelle').value,
        description: document.getElementById('editActionDescription').value,
        objectif_specifique_id: objectifSpecifiqueId,
        owner_id: document.getElementById('editActionOwnerId').value
    };
    
    fetch(`/api/actions/${actionId}`, {
        method: 'PUT',
        body: JSON.stringify(formData),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer la modale d'édition
            const modal = bootstrap.Modal.getInstance(document.getElementById('editActionModal'));
            modal.hide();
            
            // Recharger dynamiquement le contenu de l'objectif spécifique
            const modalTitle = document.getElementById('hierarchieModalTitle').textContent;
            const codeMatch = modalTitle.match(/PIL\d+/);
            const code = codeMatch ? codeMatch[0] : 'PIL1';
            
            // Extraire le libellé depuis le titre
            const libelleMatch = modalTitle.match(/Détails de l'Objectif Spécifique (.+)/);
            const libelle = libelleMatch ? libelleMatch[1] : '';
            
            rechargerObjectifSpecifique(objectifSpecifiqueId, code, libelle);
        } else {
            // Gestion spécifique des erreurs de validation
            if (data.errors && data.errors.code) {
                alert('Erreur de code : ' + data.errors.code[0]);
            } else if (data.message) {
            alert('Erreur lors de la modification : ' + data.message);
            } else {
                alert('Erreur lors de la modification.');
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification.');
    });
});

document.getElementById('createSousActionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('🚀 [DEBUG] Début de la création de sous-action');
    
    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');
    const actionId = document.getElementById('createSousActionActionId').value;
    
    console.log('📋 [DEBUG] Données du formulaire:');
    console.log('- Action ID:', actionId);
    console.log('- Code:', formData.get('code'));
    console.log('- Libellé:', formData.get('libelle'));
    console.log('- Description:', formData.get('description'));
    console.log('- Taux d\'avancement:', formData.get('taux_avancement'));
    console.log('- Date d\'échéance:', formData.get('date_echeance'));
    console.log('- Owner ID:', formData.get('owner_id'));
    console.log('- CSRF Token:', formData.get('_token'));
    
    console.log('🌐 [DEBUG] Envoi de la requête POST vers /api/sous-actions');
    
    fetch(`/api/sous-actions`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('📡 [DEBUG] Réponse reçue:');
        console.log('- Status:', response.status);
        console.log('- Status Text:', response.statusText);
        console.log('- Headers:', response.headers);
        
        if (!response.ok) {
            console.error('❌ [DEBUG] Erreur HTTP:', response.status, response.statusText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('📊 [DEBUG] Données reçues:', data);
        
        if (data.success) {
            console.log('✅ [DEBUG] Création réussie, fermeture de la modale');
            
            // Fermer la modale de création
            const modal = bootstrap.Modal.getInstance(document.getElementById('createSousActionModal'));
            modal.hide();
            
            console.log('🔄 [DEBUG] Rechargement de l\'action...');
            
            // Recharger dynamiquement le contenu de l'action
            // Extraire les informations de l'action depuis le titre de la modale
            const modalTitle = document.getElementById('hierarchieModalTitle').textContent;
            console.log('📝 [DEBUG] Titre de la modale:', modalTitle);
            
            const codeMatch = modalTitle.match(/ACT\d+/);
            const code = codeMatch ? codeMatch[0] : 'ACT1';
            console.log('🔍 [DEBUG] Code extrait:', code);
            
            // Extraire le libellé depuis le titre
            const libelleMatch = modalTitle.match(/Détails de l'Action (.+)/);
            const libelle = libelleMatch ? libelleMatch[1] : '';
            console.log('🔍 [DEBUG] Libellé extrait:', libelle);
            
            console.log('🔄 [DEBUG] Appel de rechargerAction avec:', { actionId, code, libelle });
            rechargerAction(actionId, code, libelle);
            
            console.log('✅ [DEBUG] Processus de création terminé avec succès');
        } else {
            console.error('❌ [DEBUG] Erreur de création:', data);
            
            // Gestion spécifique des erreurs de validation
            if (data.errors && data.errors.code) {
                console.error('❌ [DEBUG] Erreur de code:', data.errors.code[0]);
                alert('Erreur de code : ' + data.errors.code[0]);
            } else if (data.errors && data.errors.libelle) {
                console.error('❌ [DEBUG] Erreur de libellé:', data.errors.libelle[0]);
                alert('Erreur de libellé : ' + data.errors.libelle[0]);
            } else if (data.errors && data.errors.taux_avancement) {
                console.error('❌ [DEBUG] Erreur de taux d\'avancement:', data.errors.taux_avancement[0]);
                alert('Erreur de taux d\'avancement : ' + data.errors.taux_avancement[0]);
            } else if (data.message) {
                console.error('❌ [DEBUG] Message d\'erreur:', data.message);
            alert('Erreur lors de la création : ' + data.message);
            } else {
                console.error('❌ [DEBUG] Erreur inconnue');
                alert('Erreur lors de la création.');
            }
        }
    })
    .catch(error => {
        console.error('💥 [DEBUG] Erreur fatale:', error);
        console.error('💥 [DEBUG] Stack trace:', error.stack);
        alert('Erreur lors de la création: ' + error.message);
    });
});

document.getElementById('editSousActionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');
    const sousActionId = document.getElementById('editSousActionId').value;
    const actionId = document.getElementById('editSousActionActionId').value;
    
    fetch(`/api/sous-actions/${sousActionId}`, {
        method: 'PUT',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer la modale d'édition
            const modal = bootstrap.Modal.getInstance(document.getElementById('editSousActionModal'));
            modal.hide();
            
            // Recharger dynamiquement le contenu de l'action
            const modalTitle = document.getElementById('hierarchieModalTitle').textContent;
            const codeMatch = modalTitle.match(/ACT\d+/);
            const code = codeMatch ? codeMatch[0] : 'ACT1';
            
            // Extraire le libellé depuis le titre
            const libelleMatch = modalTitle.match(/Détails de l'Action (.+)/);
            const libelle = libelleMatch ? libelleMatch[1] : '';
            
            rechargerAction(actionId, code, libelle);
        } else {
            // Gestion spécifique des erreurs de validation
            if (data.errors && data.errors.code) {
                alert('Erreur de code : ' + data.errors.code[0]);
            } else if (data.message) {
            alert('Erreur lors de la modification : ' + data.message);
            } else {
                alert('Erreur lors de la modification.');
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification.');
    });
});

function supprimerPilier(pilierId, libelle) {
    // Utiliser SweetAlert2 ou une confirmation plus moderne
    if (confirm(`Êtes-vous sûr de vouloir supprimer le pilier "${libelle}" ?\n\nCette action est irréversible.`)) {
        // Trouver le bouton de suppression et le désactiver
        const deleteBtn = event.target.closest('button');
        const originalHTML = deleteBtn.innerHTML;
        
        // Afficher l'indicateur de chargement
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        deleteBtn.disabled = true;
        
        // Créer un formulaire temporaire pour la suppression
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch(`/piliers/${pilierId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Pilier supprimé avec succès !');
                window.location.reload();
            } else {
                showToast('error', 'Erreur lors de la suppression : ' + data.message);
                // Restaurer le bouton
                deleteBtn.innerHTML = originalHTML;
                deleteBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('error', 'Erreur lors de la suppression.');
            // Restaurer le bouton
            deleteBtn.innerHTML = originalHTML;
            deleteBtn.disabled = false;
        });
    }
}

// Fonction pour ajouter une sous-action
function ajouterSousAction(actionId) {
    console.log('🚀 [DEBUG] ajouterSousAction appelée avec actionId:', actionId);
    
    // Afficher la modale de création de sous-action
    const modal = new bootstrap.Modal(document.getElementById('createSousActionModal'));
    
    // Pré-remplir l'action sélectionnée
    document.getElementById('createSousActionActionId').value = actionId;
    console.log('📝 [DEBUG] Action ID pré-rempli:', actionId);
    
    // Suggérer un code basé sur les sous-actions existantes
    console.log('🔍 [DEBUG] Récupération des codes existants...');
    fetch(`/api/sous-actions/codes/${actionId}`)
        .then(response => response.json())
        .then(data => {
            console.log('📊 [DEBUG] Codes existants reçus:', data);
            const codeInput = document.getElementById('createSousActionCode');
            const existingCodes = data.codes;
            let nextCode = 'SA1';
            
            if (existingCodes.length > 0) {
                // Trouver le prochain numéro disponible
                const numbers = existingCodes.map(code => parseInt(code.replace('SA', ''))).filter(n => !isNaN(n));
                if (numbers.length > 0) {
                    const maxNumber = Math.max(...numbers);
                    nextCode = `SA${maxNumber + 1}`;
                }
            }
            
            console.log('🔢 [DEBUG] Code suggéré:', nextCode);
            codeInput.value = nextCode;
        })
        .catch(error => {
            console.error('❌ [DEBUG] Erreur lors de la récupération des codes:', error);
        });
    
    console.log('📋 [DEBUG] Affichage de la modale');
    modal.show();
}

// Fonction pour modifier une sous-action
function modifierSousAction(sousActionId) {
    alert('Fonction de modification de sous-action à implémenter');
}

// Fonction pour recharger dynamiquement le contenu d'un objectif stratégique
function rechargerObjectifStrategique(objectifStrategiqueId, code, libelle, pilierId) {
    fetch(`/api/objectifs-strategiques/${objectifStrategiqueId}/objectifs-specifiques`)
        .then(response => response.json())
        .then(data => {
            let objectifsSpecifiquesHtml = '';
            if (data.objectifs_specifiques && data.objectifs_specifiques.length > 0) {
                data.objectifs_specifiques.forEach(objectif => {
                    objectifsSpecifiquesHtml += `
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <span class="badge bg-info me-2">${objectif.code}</span>
                                        ${objectif.libelle}
                                    </h6>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="voirObjectifSpecifique(${objectif.id}, '${escapeJsString(objectif.code)}', '${escapeJsString(objectif.libelle)}', ${objectifStrategiqueId})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                onclick="modifierObjectifSpecifique(${objectif.id})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    ${objectif.description ? `<p class="card-text small">${escapeJsString(objectif.description)}</p>` : ''}
                                    <div class="mb-2">
                                        <small class="text-muted">Owner:</small>
                                        ${objectif.owner ? `<span class="badge bg-success"><i class="fas fa-user me-1"></i>${objectif.owner.name || objectif.owner}</span>` : '<span class="text-muted small">Non assigné</span>'}
                                    </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: ${objectif.taux_avancement}%"></div>
                                    </div>
                                    <span class="badge bg-success">${objectif.taux_avancement}%</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                objectifsSpecifiquesHtml = `
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-list-check fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun objectif spécifique</h5>
                            <p class="text-muted">Cet objectif stratégique n'a pas encore d'objectifs spécifiques.</p>
                            <button type="button" class="btn btn-primary" onclick="ajouterObjectifSpecifique(${objectifStrategiqueId})">
                                <i class="fas fa-plus me-2"></i>
                                Créer le premier objectif
                            </button>
                        </div>
                    </div>
                `;
            }

            // Mettre à jour spécifiquement la section des objectifs spécifiques
            // Chercher la carte qui contient le bouton "Ajouter un Objectif" pour identifier la bonne section
            const cards = document.querySelectorAll('.card.border-success');
            let targetCard = null;
            
            for (let card of cards) {
                const addButton = card.querySelector('button[onclick*="ajouterObjectifSpecifique"]');
                if (addButton) {
                    targetCard = card;
                    break;
                }
            }
            
            if (targetCard) {
                // Mettre à jour le contenu de la section des objectifs spécifiques
                const objectifsSpecifiquesSection = targetCard.querySelector('.card-body .row');
            if (objectifsSpecifiquesSection) {
                objectifsSpecifiquesSection.innerHTML = objectifsSpecifiquesHtml;
            }
            
            // Mettre à jour le compteur dans le header de la section
                const headerCount = targetCard.querySelector('.card-header .d-flex div:first-child');
            if (headerCount) {
                headerCount.innerHTML = `<i class="fas fa-list-check me-2"></i> Objectifs Spécifiques (${data.objectifs_specifiques ? data.objectifs_specifiques.length : 0})`;
                }
            }
            
            // Mettre à jour les statistiques dans la carte de droite
            const statsCount = document.querySelector('.col-md-4 .card-body .text-center .row .col-6 h4.text-muted');
            if (statsCount) {
                statsCount.textContent = data.objectifs_specifiques ? data.objectifs_specifiques.length : 0;
            }
            
            const statsCompleted = document.querySelector('.col-md-4 .card-body .text-center .row .col-6 h4.text-success');
            if (statsCompleted) {
                statsCompleted.textContent = data.objectifs_specifiques ? data.objectifs_specifiques.filter(os => os.taux_avancement == 100).length : 0;
            }
            
            // Mettre à jour la barre de progression dans la carte de détails
            const progressBar = document.querySelector('.col-md-8 .card-body .progress .progress-bar');
            if (progressBar) {
                progressBar.style.width = `${data.taux_avancement || 0}%`;
                progressBar.textContent = `${data.taux_avancement || 0}%`;
                progressBar.setAttribute('aria-valuenow', data.taux_avancement || 0);
            }
            
            // Mettre à jour le pourcentage dans les statistiques
            const percentageDisplay = document.querySelector('.col-md-4 .card-body .text-center h2.text-success');
            if (percentageDisplay) {
                percentageDisplay.textContent = `${data.taux_avancement || 0}%`;
            }
            
            // Mettre à jour le compteur dans la carte de détails
            const detailsCount = document.querySelector('.col-md-8 .card-body .row .col-md-6:last-child strong:last-child');
            if (detailsCount) {
                detailsCount.nextSibling.textContent = ` ${data.objectifs_specifiques ? data.objectifs_specifiques.length : 0}`;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
}

// Fonctions pour les actions et sous-actions
function ajouterAction(objectifSpecifiqueId) {
    // Afficher la modale de création d'action
    const modal = new bootstrap.Modal(document.getElementById('createActionModal'));
    
    // Pré-remplir l'objectif spécifique sélectionné
    document.getElementById('createActionObjectifSpecifiqueId').value = objectifSpecifiqueId;
    
    // Suggérer un code basé sur les actions existantes
    fetch(`/api/actions/codes/${objectifSpecifiqueId}`)
        .then(response => response.json())
        .then(data => {
            const codeInput = document.getElementById('createActionCode');
            const existingCodes = data.codes;
            let nextCode = 'ACT1';
            
            if (existingCodes.length > 0) {
                // Trouver le prochain numéro disponible
                const numbers = existingCodes.map(code => parseInt(code.replace('ACT', ''))).filter(n => !isNaN(n));
                if (numbers.length > 0) {
                    const maxNumber = Math.max(...numbers);
                    nextCode = `ACT${maxNumber + 1}`;
                }
            }
            
            codeInput.value = nextCode;
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des codes:', error);
        });
    
    modal.show();
}

// Fonction pour modifier une action
function modifierAction(actionId) {
    // Charger les données de l'action
    fetch(`/api/actions/${actionId}`)
        .then(response => response.json())
        .then(data => {
            // Remplir le formulaire d'édition
            document.getElementById('editActionId').value = actionId;
            document.getElementById('editActionCode').value = data.action.code;
            document.getElementById('editActionLibelle').value = data.action.libelle;
            document.getElementById('editActionDescription').value = data.action.description || '';
            document.getElementById('editActionObjectifSpecifiqueId').value = data.action.objectif_specifique_id;
            document.getElementById('editActionOwnerId').value = data.action.owner_id || '';
            
            // Afficher la modale d'édition
            const modal = new bootstrap.Modal(document.getElementById('editActionModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement des données.');
        });
}

// Fonction pour ajouter une sous-action
function ajouterSousAction(actionId) {
    console.log('🚀 [DEBUG] ajouterSousAction appelée avec actionId:', actionId);
    
    // Afficher la modale de création de sous-action
    const modal = new bootstrap.Modal(document.getElementById('createSousActionModal'));
    
    // Pré-remplir l'action sélectionnée
    document.getElementById('createSousActionActionId').value = actionId;
    console.log('📝 [DEBUG] Action ID pré-rempli:', actionId);
    
    // Suggérer un code basé sur les sous-actions existantes
    console.log('🔍 [DEBUG] Récupération des codes existants...');
    fetch(`/api/sous-actions/codes/${actionId}`)
        .then(response => response.json())
        .then(data => {
            console.log('📊 [DEBUG] Codes existants reçus:', data);
            const codeInput = document.getElementById('createSousActionCode');
            const existingCodes = data.codes;
            let nextCode = 'SA1';
            
            if (existingCodes.length > 0) {
                // Trouver le prochain numéro disponible
                const numbers = existingCodes.map(code => parseInt(code.replace('SA', ''))).filter(n => !isNaN(n));
                if (numbers.length > 0) {
                    const maxNumber = Math.max(...numbers);
                    nextCode = `SA${maxNumber + 1}`;
                }
            }
            
            console.log('🔢 [DEBUG] Code suggéré:', nextCode);
            codeInput.value = nextCode;
        })
        .catch(error => {
            console.error('❌ [DEBUG] Erreur lors de la récupération des codes:', error);
        });
    
    console.log('📋 [DEBUG] Affichage de la modale');
    modal.show();
}

// Fonction pour modifier une sous-action
function modifierSousAction(sousActionId) {
    // Charger les données de la sous-action
    fetch(`/api/sous-actions/${sousActionId}`)
        .then(response => response.json())
        .then(data => {
            // Remplir le formulaire d'édition
            document.getElementById('editSousActionId').value = sousActionId;
            document.getElementById('editSousActionCode').value = data.sous_action.code;
            document.getElementById('editSousActionLibelle').value = data.sous_action.libelle;
            document.getElementById('editSousActionDescription').value = data.sous_action.description || '';
            document.getElementById('editSousActionTauxAvancement').value = data.sous_action.taux_avancement;
            document.getElementById('editSousActionDateEcheance').value = data.sous_action.date_echeance || '';
            document.getElementById('editSousActionActionId').value = data.sous_action.action_id;
            document.getElementById('editSousActionOwnerId').value = data.sous_action.owner_id || '';
            
            // Afficher la modale d'édition
            const modal = new bootstrap.Modal(document.getElementById('editSousActionModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement des données.');
        });
}

// Fonction pour recharger dynamiquement le contenu d'une action
function rechargerAction(actionId, code, libelle) {
    console.log('🔄 [DEBUG] rechargerAction appelée avec:', { actionId, code, libelle });
    
    fetch(`/api/actions/${actionId}/sous-actions`)
        .then(response => {
            console.log('📡 [DEBUG] Réponse rechargerAction:');
            console.log('- Status:', response.status);
            console.log('- Status Text:', response.statusText);
            
            if (!response.ok) {
                console.error('❌ [DEBUG] Erreur HTTP dans rechargerAction:', response.status, response.statusText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('📊 [DEBUG] Données rechargerAction reçues:', data);
            let sousActionsHtml = '';
            if (data.sous_actions && data.sous_actions.length > 0) {
                data.sous_actions.forEach(sousAction => {
                    sousActionsHtml += `
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <span class="badge bg-info me-2">${sousAction.code}</span>
                                        ${sousAction.libelle}
                                    </h6>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                onclick="modifierSousAction(${sousAction.id})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    ${sousAction.description ? `<p class="card-text small">${escapeJsString(sousAction.description)}</p>` : ''}
                                    <div class="mb-2">
                                        <small class="text-muted">Owner:</small>
                                        ${sousAction.owner ? `<span class="badge bg-success"><i class="fas fa-user me-1"></i>${sousAction.owner.name || sousAction.owner}</span>` : '<span class="text-muted small">Non assigné</span>'}
                                    </div>
                                    
                                    <!-- Curseur interactif pour le taux d'avancement -->
                                    <div class="mb-3">
                                        <label class="form-label small mb-1">
                                            <i class="fas fa-sliders-h me-1"></i>Taux d'avancement: <span class="badge bg-primary" id="taux-display-${sousAction.id}">${sousAction.taux_avancement}%</span>
                                        </label>
                                        <input type="range" 
                                               class="form-range" 
                                               id="taux-slider-${sousAction.id}"
                                               min="0" 
                                               max="100" 
                                               value="${sousAction.taux_avancement}"
                                               oninput="updateTauxDisplay(${sousAction.id}, this.value)"
                                               onchange="updateSousActionTaux(${sousAction.id}, this.value)">
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span>0%</span>
                                            <span>100%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: ${sousAction.taux_avancement}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                sousActionsHtml = `
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune sous-action</h5>
                            <p class="text-muted">Cette action n'a pas encore de sous-actions.</p>
                            <button type="button" class="btn btn-primary" onclick="ajouterSousAction(${actionId})">
                                <i class="fas fa-plus me-2"></i>
                                Créer la première sous-action
                            </button>
                        </div>
                    </div>
                `;
            }

            // Mettre à jour spécifiquement la section des sous-actions
            // Chercher la carte qui contient le bouton "Ajouter une Sous-action" pour identifier la bonne section
            const cards = document.querySelectorAll('.card.border-success');
            let targetCard = null;
            
            for (let card of cards) {
                const addButton = card.querySelector('button[onclick*="ajouterSousAction"]');
                if (addButton) {
                    targetCard = card;
                    break;
                }
            }
            
            if (targetCard) {
                // Mettre à jour le contenu de la section des sous-actions
                const sousActionsSection = targetCard.querySelector('.card-body .row');
            if (sousActionsSection) {
                sousActionsSection.innerHTML = sousActionsHtml;
            }
            
            // Mettre à jour le compteur dans le header de la section
                const headerCount = targetCard.querySelector('.card-header .d-flex div:first-child');
            if (headerCount) {
                headerCount.innerHTML = `<i class="fas fa-list me-2"></i> Sous-actions (${data.sous_actions ? data.sous_actions.length : 0})`;
                }
            }
            
            // Mettre à jour les statistiques dans la carte de droite
            const statsCount = document.querySelector('.col-md-4 .card-body .text-center .row .col-6 h4.text-muted');
            if (statsCount) {
                statsCount.textContent = data.sous_actions ? data.sous_actions.length : 0;
            }
            
            const statsCompleted = document.querySelector('.col-md-4 .card-body .text-center .row .col-6 h4.text-success');
            if (statsCompleted) {
                statsCompleted.textContent = data.sous_actions ? data.sous_actions.filter(sa => sa.taux_avancement == 100).length : 0;
            }
            
            // Mettre à jour la barre de progression dans la carte de détails
            const progressBar = document.querySelector('.col-md-8 .card-body .progress .progress-bar');
            if (progressBar) {
                progressBar.style.width = `${data.taux_avancement || 0}%`;
                progressBar.textContent = `${data.taux_avancement || 0}%`;
                progressBar.setAttribute('aria-valuenow', data.taux_avancement || 0);
            }
            
            // Mettre à jour le pourcentage dans les statistiques
            const percentageDisplay = document.querySelector('.col-md-4 .card-body .text-center h2.text-success');
            if (percentageDisplay) {
                percentageDisplay.textContent = `${data.taux_avancement || 0}%`;
            }
            
            // Mettre à jour le compteur dans la carte de détails
            const detailsCount = document.querySelector('.col-md-8 .card-body .row .col-md-6:last-child strong:last-child');
            if (detailsCount) {
                detailsCount.nextSibling.textContent = ` ${data.sous_actions ? data.sous_actions.length : 0}`;
            }
            
            console.log('✅ [DEBUG] rechargerAction terminée avec succès');
            console.log('📊 [DEBUG] Nombre de sous-actions:', data.sous_actions ? data.sous_actions.length : 0);
        })
        .catch(error => {
            console.error('💥 [DEBUG] Erreur dans rechargerAction:', error);
            console.error('💥 [DEBUG] Stack trace:', error.stack);
        });
}

// Fonction pour voir les détails d'un objectif spécifique
function voirObjectifSpecifique(objectifId, code, libelle, parentId) {
    // Sauvegarder l'état actuel avec plus d'informations
    historiqueNavigation.push({
        niveau: niveauActuel,
        titre: document.getElementById('hierarchieModalTitle').textContent,
        contenu: document.getElementById('hierarchieModalBody').innerHTML,
        objectifId: objectifId,
        code: code,
        libelle: libelle,
        parentId: parentId
    });
    
    niveauActuel = 'objectif_specifique';
    
    // Charger les données de l'objectif spécifique
    chargerDonneesObjectifSpecifique(objectifId, code, libelle, parentId);
    
    // Afficher le bouton retour
    document.getElementById('btnRetour').style.display = 'block';
}

// Fonction pour charger les données d'un objectif spécifique
function chargerDonneesObjectifSpecifique(objectifId, code, libelle, parentId) {
    const modalTitle = document.getElementById('hierarchieModalTitle');
    
    // Charger les actions via AJAX
    fetch(`/api/objectifs-specifiques/${objectifId}/actions`)
        .then(response => response.json())
        .then(data => {
            // Mettre à jour le titre avec le code concaténé
            modalTitle.textContent = `Détails de l'Objectif Spécifique ${data.pilier_code}.${data.objectif_strategique_code}.${code}`;
            
            let actionsHtml = '';
            if (data.actions && data.actions.length > 0) {
                data.actions.forEach(action => {
                    actionsHtml += `
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <span class="badge bg-info me-2">${data.pilier_code}.${data.objectif_strategique_code}.${code}.${action.code}</span>
                                    </h6>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="voirAction(${action.id}, '${escapeJsString(action.code)}', '${escapeJsString(action.libelle)}', ${objectifId})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                onclick="modifierAction(${action.id})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Action:</strong>
                                        <p class="mb-0 text-dark">${escapeJsString(action.libelle)}</p>
                                    </div>
                                    ${action.description ? `
                                    <div class="mb-3">
                                        <strong>Description:</strong>
                                        <p class="mb-0 text-muted small">${escapeJsString(action.description)}</p>
                                    </div>
                                    ` : ''}
                                    <div class="mb-3">
                                        <strong>Owner:</strong>
                                        <div class="mt-1">
                                        ${action.owner ? `<span class="badge bg-success"><i class="fas fa-user me-1"></i>${action.owner.name || action.owner}</span>` : '<span class="text-muted small">Non assigné</span>'}
                                    </div>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Pourcentage:</strong>
                                        <div class="progress mt-1" style="height: 12px;">
                                            <div class="progress-bar bg-success" style="width: ${action.taux_avancement}%" role="progressbar" aria-valuenow="${action.taux_avancement}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="text-center mt-1">
                                            <span class="badge bg-success fs-6">${action.taux_avancement}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                actionsHtml = `
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune action</h5>
                            <p class="text-muted">Cet objectif spécifique n'a pas encore d'actions.</p>
                            <button type="button" class="btn btn-primary" onclick="ajouterAction(${objectifId})">
                                <i class="fas fa-plus me-2"></i>
                                Créer la première action
                            </button>
                        </div>
                    </div>
                `;
            }

            document.getElementById('hierarchieModalBody').innerHTML = `
                <!-- Carte de contexte du Pilier parent -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <i class="fas fa-layer-group me-2"></i>
                                Contexte - Pilier Parent
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Code Pilier:</strong>
                                        <span class="badge bg-primary fs-6">${data.pilier_code || 'N/A'}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Libellé Pilier:</strong>
                                        <h6 class="text-dark mb-0">${data.pilier_libelle || 'N/A'}</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Progression Pilier:</strong>
                                        <div class="progress mt-1" style="height: 15px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: ${data.taux_avancement || 0}%" aria-valuenow="${data.taux_avancement || 0}" aria-valuemin="0" aria-valuemax="100">${data.taux_avancement || 0}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte de contexte de l'Objectif Stratégique parent -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <i class="fas fa-bullseye me-2"></i>
                                Contexte - Objectif Stratégique Parent
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Code OS:</strong>
                                        <span class="badge bg-warning text-dark fs-6">${data.objectif_strategique_code || 'N/A'}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Libellé OS:</strong>
                                        <h6 class="text-dark mb-0">${data.objectif_strategique_libelle || 'N/A'}</h6>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Owner OS:</strong>
                                        <div class="mt-1">
                                            ${data.objectif_strategique_owner ? `<span class="badge bg-success">${data.objectif_strategique_owner}</span>` : '<span class="text-muted small">Non assigné</span>'}
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Progression:</strong>
                                        <div class="progress mt-1" style="height: 15px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: ${data.taux_avancement || 0}%" aria-valuenow="${data.taux_avancement || 0}" aria-valuemin="0" aria-valuemax="100">${data.taux_avancement || 0}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-info-circle me-2"></i>
                                Détails de l'Objectif Spécifique
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <strong>Code:</strong>
                                        <span class="badge bg-primary fs-6">${data.pilier_code}.${data.objectif_strategique_code}.${code}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Libellé:</strong>
                                        <h5 class="text-dark">${libelle}</h5>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Owner:</strong>
                                        <div class="mt-1">
                                            ${data.objectif_specifique_owner ? `<span class="badge bg-success"><i class="fas fa-user me-1"></i>${data.objectif_specifique_owner}</span>` : '<span class="text-muted small">Non assigné</span>'}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Progression:</strong>
                                        <div class="progress mt-1" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: ${data.taux_avancement || 0}%" aria-valuenow="${data.taux_avancement || 0}" aria-valuemin="0" aria-valuemax="100">${data.taux_avancement || 0}%</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Actions:</strong> ${data.actions ? data.actions.length : 0}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-chart-line me-2"></i>
                                Statistiques
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="text-success mb-3">${data.taux_avancement || 0}%</h2>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h4 class="text-muted">${data.actions ? data.actions.length : 0}</h4>
                                            <small class="text-muted">Actions</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success">${data.actions ? data.actions.filter(a => a.taux_avancement == 100).length : 0}</h4>
                                            <small class="text-muted">Terminées</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-success">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-tasks me-2"></i>
                            Actions (${data.actions ? data.actions.length : 0})
                        </div>
                        <button type="button" class="btn btn-light btn-sm" onclick="ajouterAction(${objectifId})">
                            <i class="fas fa-plus me-1"></i>
                            Ajouter une Action
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            ${actionsHtml}
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('hierarchieModalBody').innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des données.</div>';
        });
    
    // Afficher le bouton retour
    document.getElementById('btnRetour').style.display = 'block';
}

// Fonction pour voir les détails d'une action
function voirAction(actionId, code, libelle, objectifId) {
    // Sauvegarder l'état actuel avec plus d'informations
    historiqueNavigation.push({
        niveau: niveauActuel,
        titre: document.getElementById('hierarchieModalTitle').textContent,
        contenu: document.getElementById('hierarchieModalBody').innerHTML,
        actionId: actionId,
        code: code,
        libelle: libelle,
        objectifId: objectifId
    });
    
    niveauActuel = 'action';
    
    // Charger les données de l'action
    chargerDonneesAction(actionId, code, libelle, objectifId);
    
    // Afficher le bouton retour
    document.getElementById('btnRetour').style.display = 'block';
}

// Fonction pour charger les données d'une action
function chargerDonneesAction(actionId, code, libelle, objectifId) {
    const modalTitle = document.getElementById('hierarchieModalTitle');
    
    // Charger les sous-actions via AJAX
    fetch(`/api/actions/${actionId}/sous-actions`)
        .then(response => response.json())
        .then(data => {
            // Mettre à jour le titre avec le code concaténé
            modalTitle.textContent = `Détails de l'Action ${data.pilier_code}.${data.objectif_strategique_code}.${data.objectif_specifique_code}.${code}`;
            
            let sousActionsHtml = '';
            if (data.sous_actions && data.sous_actions.length > 0) {
                data.sous_actions.forEach(sousAction => {
                    sousActionsHtml += `
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <span class="badge bg-info me-2">${data.pilier_code}.${data.objectif_strategique_code}.${data.objectif_specifique_code}.${code}.${sousAction.code}</span>
                                    </h6>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                onclick="modifierSousAction(${sousAction.id})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Sous-Action:</strong>
                                        <p class="mb-0 text-dark">${escapeJsString(sousAction.libelle)}</p>
                                    </div>
                                    ${sousAction.description ? `
                                    <div class="mb-3">
                                        <strong>Description:</strong>
                                        <p class="mb-0 text-muted small">${escapeJsString(sousAction.description)}</p>
                                    </div>
                                    ` : ''}
                                    <div class="mb-3">
                                        <strong>Owner:</strong>
                                        <div class="mt-1">
                                        ${sousAction.owner ? `<span class="badge bg-success"><i class="fas fa-user me-1"></i>${sousAction.owner.name || sousAction.owner}</span>` : '<span class="text-muted small">Non assigné</span>'}
                                        </div>
                                    </div>
                                    
                                    <!-- Curseur interactif pour le taux d'avancement -->
                                    <div class="mb-3">
                                        <label class="form-label small mb-1">
                                            <i class="fas fa-sliders-h me-1"></i>Taux d'avancement: <span class="badge bg-primary" id="taux-display-${sousAction.id}">${sousAction.taux_avancement}%</span>
                                        </label>
                                        <input type="range" 
                                               class="form-range" 
                                               id="taux-slider-${sousAction.id}"
                                               min="0" 
                                               max="100" 
                                               value="${sousAction.taux_avancement}"
                                               oninput="updateTauxDisplay(${sousAction.id}, this.value)"
                                               onchange="updateSousActionTaux(${sousAction.id}, this.value)">
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span>0%</span>
                                            <span>100%</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <strong>Pourcentage:</strong>
                                        <div class="progress mt-1" style="height: 12px;">
                                            <div class="progress-bar bg-success" style="width: ${sousAction.taux_avancement}%" role="progressbar" aria-valuenow="${sousAction.taux_avancement}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="text-center mt-1">
                                            <span class="badge bg-success fs-6">${sousAction.taux_avancement}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                sousActionsHtml = `
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune sous-action</h5>
                            <p class="text-muted">Cette action n'a pas encore de sous-actions.</p>
                            <button type="button" class="btn btn-primary" onclick="ajouterSousAction(${actionId})">
                                <i class="fas fa-plus me-2"></i>
                                Créer la première sous-action
                            </button>
                        </div>
                    </div>
                `;
            }

            document.getElementById('hierarchieModalBody').innerHTML = `
                <!-- Carte de contexte du Pilier parent -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <i class="fas fa-layer-group me-2"></i>
                                Contexte - Pilier Parent
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Code Pilier:</strong>
                                        <span class="badge bg-primary fs-6">${data.pilier_code || 'N/A'}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Libellé Pilier:</strong>
                                        <h6 class="text-dark mb-0">${data.pilier_libelle || 'N/A'}</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Progression Pilier:</strong>
                                        <div class="progress mt-1" style="height: 15px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: ${data.taux_avancement || 0}%" aria-valuenow="${data.taux_avancement || 0}" aria-valuemin="0" aria-valuemax="100">${data.taux_avancement || 0}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte de contexte de l'Objectif Stratégique parent -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <i class="fas fa-bullseye me-2"></i>
                                Contexte - Objectif Stratégique Parent
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Code OS:</strong>
                                        <span class="badge bg-warning text-dark fs-6">${data.objectif_strategique_code || 'N/A'}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Libellé OS:</strong>
                                        <h6 class="text-dark mb-0">${data.objectif_strategique_libelle || 'N/A'}</h6>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Owner OS:</strong>
                                        <div class="mt-1">
                                            ${data.objectif_strategique_owner ? `<span class="badge bg-success">${data.objectif_strategique_owner}</span>` : '<span class="text-muted small">Non assigné</span>'}
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Progression:</strong>
                                        <div class="progress mt-1" style="height: 15px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: ${data.taux_avancement || 0}%" aria-valuenow="${data.taux_avancement || 0}" aria-valuemin="0" aria-valuemax="100">${data.taux_avancement || 0}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte de contexte de l'Objectif Spécifique parent -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-list-check me-2"></i>
                                Contexte - Objectif Spécifique Parent
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Code OS:</strong>
                                        <span class="badge bg-success fs-6">${data.objectif_specifique_code || 'N/A'}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Libellé OS:</strong>
                                        <h6 class="text-dark mb-0">${data.objectif_specifique_libelle || 'N/A'}</h6>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Owner OS:</strong>
                                        <div class="mt-1">
                                            ${data.objectif_specifique_owner ? `<span class="badge bg-success">${data.objectif_specifique_owner}</span>` : '<span class="text-muted small">Non assigné</span>'}
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Progression:</strong>
                                        <div class="progress mt-1" style="height: 15px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: ${data.taux_avancement || 0}%" aria-valuenow="${data.taux_avancement || 0}" aria-valuemin="0" aria-valuemax="100">${data.taux_avancement || 0}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-info-circle me-2"></i>
                                Détails de l'Action
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <strong>Code:</strong>
                                        <span class="badge bg-primary fs-6">${data.pilier_code}.${data.objectif_strategique_code}.${data.objectif_specifique_code}.${code}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Libellé:</strong>
                                        <h5 class="text-dark">${libelle}</h5>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Owner:</strong>
                                        <div class="mt-1">
                                            ${data.action_owner ? `<span class="badge bg-success"><i class="fas fa-user me-1"></i>${data.action_owner}</span>` : '<span class="text-muted small">Non assigné</span>'}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Progression:</strong>
                                        <div class="progress mt-1" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: ${data.taux_avancement || 0}%" aria-valuenow="${data.taux_avancement || 0}" aria-valuemin="0" aria-valuemax="100">${data.taux_avancement || 0}%</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Sous-actions:</strong> ${data.sous_actions ? data.sous_actions.length : 0}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-chart-line me-2"></i>
                                Statistiques
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="text-success mb-3">${data.taux_avancement || 0}%</h2>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h4 class="text-muted">${data.sous_actions ? data.sous_actions.length : 0}</h4>
                                            <small class="text-muted">Sous-actions</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success">${data.sous_actions ? data.sous_actions.filter(sa => sa.taux_avancement == 100).length : 0}</h4>
                                            <small class="text-muted">Terminées</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-success">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-list me-2"></i>
                            Sous-actions (${data.sous_actions ? data.sous_actions.length : 0})
                        </div>
                        <button type="button" class="btn btn-light btn-sm" onclick="ajouterSousAction(${actionId})">
                            <i class="fas fa-plus me-1"></i>
                            Ajouter une Sous-action
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            ${sousActionsHtml}
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('hierarchieModalBody').innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des données.</div>';
        });
    
    // Afficher le bouton retour
    document.getElementById('btnRetour').style.display = 'block';
}

// Fonction pour recharger un objectif stratégique après modification
function rechargerObjectifStrategique(objectifId, code, libelle) {
    // Recharger les données de l'objectif stratégique
    chargerDonneesObjectifStrategique(objectifId, code, libelle, null);
    
    // Afficher une notification de succès
    showToast('success', 'Objectif spécifique créé avec succès !', 'Succès');
}

// Fonction pour recharger un objectif spécifique après modification
function rechargerObjectifSpecifique(objectifId, code, libelle) {
    // Recharger les données de l'objectif spécifique
    chargerDonneesObjectifSpecifique(objectifId, code, libelle, null);
    
    // Afficher une notification de succès
    showToast('success', 'Action créée avec succès !', 'Succès');
}

// Fonction pour recharger une action après modification
function rechargerAction(actionId, code, libelle) {
    // Recharger les données de l'action
    chargerDonneesAction(actionId, code, libelle, null);
    
    // Afficher une notification de succès
    showToast('success', 'Sous-action créée avec succès !', 'Succès');
}

// Fonction pour recharger une sous-action après modification
function rechargerSousAction(sousActionId, code, libelle) {
    // Recharger les données de la sous-action
    chargerDonneesSousAction(sousActionId, code, libelle, null);
    
    // Afficher une notification de succès
    showToast('success', 'Sous-action modifiée avec succès !', 'Succès');
}

// Fonction pour mettre à jour l'affichage du taux en temps réel
function updateTauxDisplay(sousActionId, value) {
    const displayElement = document.getElementById(`taux-display-${sousActionId}`);
    if (displayElement) {
        displayElement.textContent = `${value}%`;
    }
    
    // Mettre à jour aussi la barre de progression
    const progressBar = document.querySelector(`#taux-slider-${sousActionId}`).closest('.card-body').querySelector('.progress-bar');
    if (progressBar) {
        progressBar.style.width = `${value}%`;
    }
}

// Fonction pour mettre à jour le taux d'une sous-action et recalculer tous les parents
function updateSousActionTaux(sousActionId, newTaux) {
    console.log('🔄 [DEBUG] Mise à jour du taux de sous-action:', { sousActionId, newTaux });
    console.log('📡 [DEBUG] Envoi de la requête PUT vers /api/sous-actions/' + sousActionId + '/taux-avancement');
    
    const requestData = {
        taux_avancement: newTaux
    };
    
    console.log('📋 [DEBUG] Données envoyées:', requestData);
    console.log('🔑 [DEBUG] CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch(`/api/sous-actions/${sousActionId}/taux-avancement`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log('📡 [DEBUG] Réponse reçue:');
        console.log('- Status:', response.status);
        console.log('- Status Text:', response.statusText);
        console.log('- Headers:', response.headers);
        
        if (!response.ok) {
            console.error('❌ [DEBUG] Erreur HTTP:', response.status, response.statusText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('📊 [DEBUG] Données reçues:', data);
        
        if (data.success) {
            console.log('✅ [DEBUG] Taux mis à jour avec succès:', data);
            console.log('📈 [DEBUG] Taux mis à jour:', data.updated_taux);
            
            // Mettre à jour tous les taux parents dans l'interface
            updateAllParentTaux(data.updated_taux);
            
            // Mettre à jour les taux sur toutes les étapes de navigation
            updateTauxOnAllNavigationSteps();
            
            // Recharger automatiquement les données de la page actuelle après 1 seconde
            setTimeout(() => {
                rechargerDonneesActuelles();
            }, 1000);
            
            // Recharger aussi la page principale si on est sur la page principale
            if (niveauActuel === 'pilier') {
                setTimeout(() => {
                    rechargerPagePrincipale();
                }, 1500);
            }
            
            // Afficher une notification de succès
            showToast('success', `Taux d'avancement mis à jour à ${newTaux}%`);
        } else {
            console.error('❌ [DEBUG] Erreur lors de la mise à jour:', data);
            showToast('error', 'Erreur lors de la mise à jour du taux: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('💥 [DEBUG] Erreur fatale:', error);
        console.error('💥 [DEBUG] Stack trace:', error.stack);
        showToast('error', 'Erreur lors de la mise à jour du taux: ' + error.message);
    });
}

// Fonction pour mettre à jour tous les taux parents dans l'interface avec un affichage amélioré
function updateAllParentTaux(updatedTaux) {
    console.log('🔄 [DEBUG] Mise à jour des taux parents:', updatedTaux);
    
    // Mettre à jour le taux de l'action dans la section "Détails de l'Action"
    if (updatedTaux.action !== undefined) {
        console.log('📊 [DEBUG] Mise à jour du taux d\'action:', updatedTaux.action);
        
        // Mettre à jour la barre de progression de l'action
        const actionProgressBar = document.querySelector('.modal-body .row .col-md-8 .card-body .progress .progress-bar');
        if (actionProgressBar) {
            actionProgressBar.style.width = `${updatedTaux.action}%`;
            actionProgressBar.textContent = `${updatedTaux.action.toFixed(1)}%`;
            actionProgressBar.setAttribute('aria-valuenow', updatedTaux.action);
            
            // Améliorer l'apparence de la barre de progression
            actionProgressBar.className = 'progress-bar';
            if (updatedTaux.action >= 100) {
                actionProgressBar.classList.add('bg-success');
            } else if (updatedTaux.action >= 75) {
                actionProgressBar.classList.add('bg-info');
            } else if (updatedTaux.action >= 50) {
                actionProgressBar.classList.add('bg-warning');
            } else {
                actionProgressBar.classList.add('bg-danger');
            }
            
            console.log('✅ [DEBUG] Barre de progression d\'action mise à jour');
        } else {
            console.warn('⚠️ [DEBUG] Barre de progression d\'action non trouvée');
        }
        
        // Mettre à jour l'affichage du pourcentage dans les statistiques
        const actionPercentageDisplay = document.querySelector('.modal-body .row .col-md-4 .card-body .text-center h2');
        if (actionPercentageDisplay) {
            actionPercentageDisplay.textContent = `${updatedTaux.action.toFixed(1)}%`;
            actionPercentageDisplay.className = 'text-center h2';
            if (updatedTaux.action >= 100) {
                actionPercentageDisplay.classList.add('text-success');
            } else if (updatedTaux.action >= 75) {
                actionPercentageDisplay.classList.add('text-info');
            } else if (updatedTaux.action >= 50) {
                actionPercentageDisplay.classList.add('text-warning');
            } else {
                actionPercentageDisplay.classList.add('text-danger');
            }
            console.log('✅ [DEBUG] Pourcentage d\'action dans les statistiques mis à jour');
        } else {
            console.warn('⚠️ [DEBUG] Pourcentage d\'action dans les statistiques non trouvé');
        }
    }
    
    // Mettre à jour le taux de l'objectif spécifique dans le contexte
    if (updatedTaux.objectif_specifique !== undefined) {
        console.log('📊 [DEBUG] Mise à jour du taux d\'objectif spécifique:', updatedTaux.objectif_specifique);
        
        // Chercher la carte de contexte de l'objectif spécifique par son contenu
        const contextCards = document.querySelectorAll('.modal-body .card');
        let objectifSpecifiqueCard = null;
        
        for (let card of contextCards) {
            const header = card.querySelector('.card-header');
            if (header && header.textContent.includes('Objectif Spécifique Parent')) {
                objectifSpecifiqueCard = card;
                break;
            }
        }
        
        if (objectifSpecifiqueCard) {
            const progressBar = objectifSpecifiqueCard.querySelector('.progress .progress-bar');
            if (progressBar) {
                progressBar.style.width = `${updatedTaux.objectif_specifique}%`;
                progressBar.textContent = `${updatedTaux.objectif_specifique.toFixed(1)}%`;
                progressBar.setAttribute('aria-valuenow', updatedTaux.objectif_specifique);
                
                // Améliorer l'apparence de la barre de progression
                progressBar.className = 'progress-bar';
                if (updatedTaux.objectif_specifique >= 100) {
                    progressBar.classList.add('bg-success');
                } else if (updatedTaux.objectif_specifique >= 75) {
                    progressBar.classList.add('bg-info');
                } else if (updatedTaux.objectif_specifique >= 50) {
                    progressBar.classList.add('bg-warning');
                } else {
                    progressBar.classList.add('bg-danger');
                }
                
                console.log('✅ [DEBUG] Barre de progression d\'objectif spécifique mise à jour');
            } else {
                console.warn('⚠️ [DEBUG] Barre de progression d\'objectif spécifique non trouvée');
            }
        } else {
            console.warn('⚠️ [DEBUG] Carte d\'objectif spécifique non trouvée');
        }
    }
    
    // Mettre à jour le taux de l'objectif stratégique dans le contexte
    if (updatedTaux.objectif_strategique !== undefined) {
        console.log('📊 [DEBUG] Mise à jour du taux d\'objectif stratégique:', updatedTaux.objectif_strategique);
        
        // Chercher la carte de contexte de l'objectif stratégique par son contenu
        const contextCards = document.querySelectorAll('.modal-body .card');
        let objectifStrategiqueCard = null;
        
        for (let card of contextCards) {
            const header = card.querySelector('.card-header');
            if (header && header.textContent.includes('Objectif Stratégique Parent')) {
                objectifStrategiqueCard = card;
                break;
            }
        }
        
        if (objectifStrategiqueCard) {
            const progressBar = objectifStrategiqueCard.querySelector('.progress .progress-bar');
            if (progressBar) {
                progressBar.style.width = `${updatedTaux.objectif_strategique}%`;
                progressBar.textContent = `${updatedTaux.objectif_strategique.toFixed(1)}%`;
                progressBar.setAttribute('aria-valuenow', updatedTaux.objectif_strategique);
                
                // Améliorer l'apparence de la barre de progression
                progressBar.className = 'progress-bar';
                if (updatedTaux.objectif_strategique >= 100) {
                    progressBar.classList.add('bg-success');
                } else if (updatedTaux.objectif_strategique >= 75) {
                    progressBar.classList.add('bg-info');
                } else if (updatedTaux.objectif_strategique >= 50) {
                    progressBar.classList.add('bg-warning');
                } else {
                    progressBar.classList.add('bg-danger');
                }
                
                console.log('✅ [DEBUG] Barre de progression d\'objectif stratégique mise à jour');
            } else {
                console.warn('⚠️ [DEBUG] Barre de progression d\'objectif stratégique non trouvée');
            }
        } else {
            console.warn('⚠️ [DEBUG] Carte d\'objectif stratégique non trouvée');
        }
    }
    
    // Mettre à jour le taux du pilier dans le contexte
    if (updatedTaux.pilier !== undefined) {
        console.log('📊 [DEBUG] Mise à jour du taux de pilier:', updatedTaux.pilier);
        
        // Chercher la carte de contexte du pilier par son contenu
        const contextCards = document.querySelectorAll('.modal-body .card');
        let pilierCard = null;
        
        for (let card of contextCards) {
            const header = card.querySelector('.card-header');
            if (header && header.textContent.includes('Pilier Parent')) {
                pilierCard = card;
                break;
            }
        }
        
        if (pilierCard) {
            const progressBar = pilierCard.querySelector('.progress .progress-bar');
            if (progressBar) {
                progressBar.style.width = `${updatedTaux.pilier}%`;
                progressBar.textContent = `${updatedTaux.pilier.toFixed(1)}%`;
                progressBar.setAttribute('aria-valuenow', updatedTaux.pilier);
                
                // Améliorer l'apparence de la barre de progression
                progressBar.className = 'progress-bar';
                if (updatedTaux.pilier >= 100) {
                    progressBar.classList.add('bg-success');
                } else if (updatedTaux.pilier >= 75) {
                    progressBar.classList.add('bg-info');
                } else if (updatedTaux.pilier >= 50) {
                    progressBar.classList.add('bg-warning');
                } else {
                    progressBar.classList.add('bg-danger');
                }
                
                console.log('✅ [DEBUG] Barre de progression de pilier mise à jour');
            } else {
                console.warn('⚠️ [DEBUG] Barre de progression de pilier non trouvée');
            }
        } else {
            console.warn('⚠️ [DEBUG] Carte de pilier non trouvée');
        }
    }
    
    console.log('✅ [DEBUG] Mise à jour des taux parents terminée');
}

// Fonction pour mettre à jour les taux sur toutes les étapes de navigation
function updateTauxOnAllNavigationSteps() {
    console.log('🔄 [DEBUG] Mise à jour des taux sur toutes les étapes de navigation');
    
    // Mettre à jour les taux sur la page principale (niveau 0 - Piliers)
    updateTauxOnPiliersPage();
    
    // Mettre à jour les taux sur la page des objectifs stratégiques (niveau 1)
    updateTauxOnObjectifsStrategiquesPage();
    
    // Mettre à jour les taux sur la page des objectifs spécifiques (niveau 2)
    updateTauxOnObjectifsSpecifiquesPage();
    
    // Mettre à jour les taux sur la page des actions (niveau 3)
    updateTauxOnActionsPage();
    
    console.log('✅ [DEBUG] Mise à jour des taux sur toutes les étapes terminée');
}

// Fonction pour mettre à jour les taux sur la page des piliers
function updateTauxOnPiliersPage() {
    const pilierCards = document.querySelectorAll('.pilier-card');
    pilierCards.forEach(card => {
        const pilierId = card.getAttribute('data-pilier-id');
        if (pilierId) {
            fetch(`/api/piliers/${pilierId}/taux-avancement`)
        .then(response => response.json())
        .then(data => {
                    if (data.success) {
                        const progressBar = card.querySelector('.progress .progress-bar');
                        const percentageDisplay = card.querySelector('.taux-avancement-display');
                        
                        if (progressBar) {
                            progressBar.style.width = `${data.taux}%`;
                            progressBar.textContent = `${data.taux.toFixed(1)}%`;
                            progressBar.className = 'progress-bar';
                            if (data.taux >= 100) progressBar.classList.add('bg-success');
                            else if (data.taux >= 75) progressBar.classList.add('bg-info');
                            else if (data.taux >= 50) progressBar.classList.add('bg-warning');
                            else progressBar.classList.add('bg-danger');
                        }
                        
                        if (percentageDisplay) {
                            percentageDisplay.textContent = `${data.taux.toFixed(1)}%`;
                        }
                    }
                })
                .catch(error => console.error('Erreur lors de la mise à jour du taux du pilier:', error));
        }
    });
}

// Fonction pour mettre à jour les taux sur la page des objectifs stratégiques
function updateTauxOnObjectifsStrategiquesPage() {
    const objectifStrategiqueCards = document.querySelectorAll('.objectif-strategique-card');
    objectifStrategiqueCards.forEach(card => {
        const objectifStrategiqueId = card.getAttribute('data-objectif-strategique-id');
        if (objectifStrategiqueId) {
            fetch(`/api/objectifs-strategiques/${objectifStrategiqueId}/taux-avancement`)
        .then(response => response.json())
        .then(data => {
                    if (data.success) {
                        const progressBar = card.querySelector('.progress .progress-bar');
                        const percentageDisplay = card.querySelector('.taux-avancement-display');
                        
                        if (progressBar) {
                            progressBar.style.width = `${data.taux}%`;
                            progressBar.textContent = `${data.taux.toFixed(1)}%`;
                            progressBar.className = 'progress-bar';
                            if (data.taux >= 100) progressBar.classList.add('bg-success');
                            else if (data.taux >= 75) progressBar.classList.add('bg-info');
                            else if (data.taux >= 50) progressBar.classList.add('bg-warning');
                            else progressBar.classList.add('bg-danger');
                        }
                        
                        if (percentageDisplay) {
                            percentageDisplay.textContent = `${data.taux.toFixed(1)}%`;
                        }
                    }
                })
                .catch(error => console.error('Erreur lors de la mise à jour du taux de l\'objectif stratégique:', error));
        }
    });
}

// Fonction pour mettre à jour les taux sur la page des objectifs spécifiques
function updateTauxOnObjectifsSpecifiquesPage() {
    const objectifSpecifiqueCards = document.querySelectorAll('.objectif-specifique-card');
    objectifSpecifiqueCards.forEach(card => {
        const objectifSpecifiqueId = card.getAttribute('data-objectif-specifique-id');
        if (objectifSpecifiqueId) {
            fetch(`/api/objectifs-specifiques/${objectifSpecifiqueId}/taux-avancement`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const progressBar = card.querySelector('.progress .progress-bar');
                        const percentageDisplay = card.querySelector('.taux-avancement-display');
                        
                        if (progressBar) {
                            progressBar.style.width = `${data.taux}%`;
                            progressBar.textContent = `${data.taux.toFixed(1)}%`;
                            progressBar.className = 'progress-bar';
                            if (data.taux >= 100) progressBar.classList.add('bg-success');
                            else if (data.taux >= 75) progressBar.classList.add('bg-info');
                            else if (data.taux >= 50) progressBar.classList.add('bg-warning');
                            else progressBar.classList.add('bg-danger');
                        }
                        
                        if (percentageDisplay) {
                            percentageDisplay.textContent = `${data.taux.toFixed(1)}%`;
                        }
                    }
                })
                .catch(error => console.error('Erreur lors de la mise à jour du taux de l\'objectif spécifique:', error));
        }
    });
}

// Fonction pour mettre à jour les taux sur la page des actions
function updateTauxOnActionsPage() {
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        const actionId = card.getAttribute('data-action-id');
        if (actionId) {
            fetch(`/api/actions/${actionId}/taux-avancement`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const progressBar = card.querySelector('.progress .progress-bar');
                        const percentageDisplay = card.querySelector('.taux-avancement-display');
                        
                        if (progressBar) {
                            progressBar.style.width = `${data.taux}%`;
                            progressBar.textContent = `${data.taux.toFixed(1)}%`;
                            progressBar.className = 'progress-bar';
                            if (data.taux >= 100) progressBar.classList.add('bg-success');
                            else if (data.taux >= 75) progressBar.classList.add('bg-info');
                            else if (data.taux >= 50) progressBar.classList.add('bg-warning');
                            else progressBar.classList.add('bg-danger');
                        }
                        
                        if (percentageDisplay) {
                            percentageDisplay.textContent = `${data.taux.toFixed(1)}%`;
                        }
                    }
                })
                .catch(error => console.error('Erreur lors de la mise à jour du taux de l\'action:', error));
        }
    });
}

// Fonction pour recharger les données de la page actuelle
function rechargerDonneesActuelles() {
    console.log('🔄 [DEBUG] Rechargement des données actuelles pour le niveau:', niveauActuel);
    
    const titre = document.getElementById('hierarchieModalTitle').textContent;
    
    if (niveauActuel === 'pilier') {
        // Recharger la page principale
        console.log('🔄 [DEBUG] Rechargement de la page principale');
        rechargerPagePrincipale();
    } else if (niveauActuel === 'action') {
        // Extraire l'ID de l'action du titre
        const match = titre.match(/Détails de l'Action (\w+)/);
        if (match) {
            const code = match[1];
            // Chercher l'ID de l'action dans l'historique ou les données
            const actionId = getActionIdByCode(code);
            if (actionId) {
                console.log('🔄 [DEBUG] Rechargement des données de l\'action:', actionId);
                chargerDonneesAction(actionId, code, '', null);
            } else {
                console.warn('⚠️ [DEBUG] ID de l\'action non trouvé pour le code:', code);
            }
        }
    } else if (niveauActuel === 'objectif_specifique') {
        // Extraire l'ID de l'objectif spécifique du titre
        const match = titre.match(/Détails de l'Objectif Spécifique (\w+)/);
        if (match) {
            const code = match[1];
            // Chercher l'ID de l'objectif spécifique dans l'historique ou les données
            const objectifId = getObjectifSpecifiqueIdByCode(code);
            if (objectifId) {
                console.log('🔄 [DEBUG] Rechargement des données de l\'objectif spécifique:', objectifId);
                chargerDonneesObjectifSpecifique(objectifId, code, '', null);
            } else {
                console.warn('⚠️ [DEBUG] ID de l\'objectif spécifique non trouvé pour le code:', code);
                // Essayer de recharger depuis la page principale
                rechargerDepuisPagePrincipale();
            }
        }
    } else if (niveauActuel === 'objectif_strategique') {
        // Extraire l'ID de l'objectif stratégique du titre
        const match = titre.match(/Détails de l'Objectif Stratégique (\w+)/);
        if (match) {
            const code = match[1];
            // Chercher l'ID de l'objectif stratégique dans l'historique ou les données
            const objectifId = getObjectifStrategiqueIdByCode(code);
            if (objectifId) {
                console.log('🔄 [DEBUG] Rechargement des données de l\'objectif stratégique:', objectifId);
                chargerDonneesObjectifStrategique(objectifId, code, '', null);
            } else {
                console.warn('⚠️ [DEBUG] ID de l\'objectif stratégique non trouvé pour le code:', code);
                // Essayer de recharger depuis la page principale
                rechargerDepuisPagePrincipale();
            }
        }
    }
}

// Fonctions utilitaires pour récupérer les IDs par code
function getActionIdByCode(code) {
    // Chercher dans l'historique de navigation
    for (let i = historiqueNavigation.length - 1; i >= 0; i--) {
        const niveau = historiqueNavigation[i];
        if (niveau.niveau === 'action' && niveau.code === code) {
            return niveau.actionId;
        }
    }
    return null;
}

function getObjectifSpecifiqueIdByCode(code) {
    // Chercher dans l'historique de navigation
    for (let i = historiqueNavigation.length - 1; i >= 0; i--) {
        const niveau = historiqueNavigation[i];
        if (niveau.niveau === 'objectif_specifique' && niveau.code === code) {
            return niveau.objectifId;
        }
    }
    return null;
}

function getObjectifStrategiqueIdByCode(code) {
    // Chercher dans l'historique de navigation
    for (let i = historiqueNavigation.length - 1; i >= 0; i--) {
        const niveau = historiqueNavigation[i];
        if (niveau.niveau === 'objectif_strategique' && niveau.code === code) {
            return niveau.objectifId;
        }
    }
    return null;
}

// Fonction pour recharger depuis la page principale
function rechargerDepuisPagePrincipale() {
    console.log('🔄 [DEBUG] Tentative de rechargement depuis la page principale');
    
    // Recharger la page complète
    window.location.reload();
}

// Fonction pour recharger la page principale
function rechargerPagePrincipale() {
    console.log('🔄 [DEBUG] Rechargement de la page principale');
    
    // Recharger les données des piliers
    fetch('/api/piliers')
        .then(response => response.json())
        .then(data => {
            console.log('🔄 [DEBUG] Données des piliers rechargées:', data);
            
            // Mettre à jour chaque pilier
            data.forEach(pilier => {
                const pilierCard = document.querySelector(`[data-pilier-id="${pilier.id}"]`);
                if (pilierCard) {
                    // Mettre à jour la barre de progression
                    const progressBar = pilierCard.querySelector('.progress-bar');
                    if (progressBar) {
                        const taux = pilier.taux_avancement || 0;
                        progressBar.style.width = `${taux}%`;
                        progressBar.setAttribute('aria-valuenow', taux);
                    }
                    
                    // Mettre à jour le pourcentage
                    const percentageDisplay = pilierCard.querySelector('.text-center h2');
                    if (percentageDisplay) {
                        const taux = pilier.taux_avancement || 0;
                        percentageDisplay.textContent = `${taux.toFixed(2)}%`;
                    }
                    
                    // Mettre à jour les objectifs stratégiques
                    const objectifsStrategiquesContainer = pilierCard.querySelector('.objectifs-strategiques-container');
                    if (objectifsStrategiquesContainer) {
                        rechargerObjectifsStrategiques(pilier.id, objectifsStrategiquesContainer);
                    }
                }
            });
        })
        .catch(error => {
            console.error('❌ [DEBUG] Erreur lors du rechargement de la page principale:', error);
        });
}

// Fonction pour recharger les données d'un pilier avec les taux mis à jour
function rechargerDonneesPilier(pilierId, code, libelle) {
    console.log('🔄 [DEBUG] Rechargement des données du pilier:', pilierId, code, libelle);
    
    fetch(`/api/piliers/${pilierId}/objectifs-strategiques`)
        .then(response => response.json())
        .then(data => {
            console.log('🔄 [DEBUG] Données du pilier rechargées:', data);
            
            // Mettre à jour le taux d'avancement du pilier dans les détails
            const pilierTaux = data.taux_avancement || 0;
            
            // Mettre à jour la barre de progression du pilier
            const pilierProgressBar = document.querySelector('.modal-body .row .col-md-8 .card-body .progress .progress-bar');
            if (pilierProgressBar) {
                pilierProgressBar.style.width = `${pilierTaux}%`;
                pilierProgressBar.textContent = `${pilierTaux.toFixed(2)}%`;
                pilierProgressBar.setAttribute('aria-valuenow', pilierTaux);
                console.log('✅ [DEBUG] Barre de progression du pilier mise à jour:', pilierTaux);
            }
            
            // Mettre à jour le pourcentage dans les statistiques
            const statistiquesPercentage = document.querySelector('.modal-body .row .col-md-4 .card-body .text-center h2');
            if (statistiquesPercentage) {
                statistiquesPercentage.textContent = `${pilierTaux.toFixed(2)}%`;
                console.log('✅ [DEBUG] Pourcentage des statistiques mis à jour:', pilierTaux);
            }
            
            // Mettre à jour les objectifs stratégiques
            const objectifsStrategiquesContainer = document.querySelector('.objectifs-strategiques-container');
            if (objectifsStrategiquesContainer) {
                rechargerObjectifsStrategiques(pilierId, objectifsStrategiquesContainer);
            }
        })
        .catch(error => {
            console.error('❌ [DEBUG] Erreur lors du rechargement des données du pilier:', error);
        });
}





// Fonction pour afficher des notifications toast
function showToast(type, message) {
    // Créer un élément toast Bootstrap
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    // Créer un conteneur toast s'il n'existe pas
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Ajouter le toast au conteneur
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Récupérer le dernier toast ajouté
    const toastElement = toastContainer.lastElementChild;
    
    // Initialiser et afficher le toast
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 3000
    });
    
    toast.show();
    
    // Supprimer le toast du DOM après qu'il soit caché
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

// Fonction pour ajouter un objectif spécifique
function ajouterObjectifSpecifique(objectifStrategiqueId) {
    console.log('🚀 [DEBUG] ajouterObjectifSpecifique appelée avec objectifStrategiqueId:', objectifStrategiqueId);
    
    // Pré-remplir l'objectif stratégique sélectionné
    document.getElementById('createObjectifSpecifiqueObjectifStrategiqueId').value = objectifStrategiqueId;
    
    // Suggérer un code basé sur les objectifs spécifiques existants
    fetch(`/api/objectifs-specifiques/codes/${objectifStrategiqueId}`)
        .then(response => response.json())
        .then(data => {
            console.log('📊 [DEBUG] Codes existants reçus:', data);
            const codeInput = document.getElementById('createObjectifSpecifiqueCode');
            const existingCodes = data.codes;
            let nextCode = 'PIL1';
            
            if (existingCodes.length > 0) {
                // Trouver le prochain numéro disponible
                const numbers = existingCodes.map(code => parseInt(code.replace('PIL', ''))).filter(n => !isNaN(n));
                if (numbers.length > 0) {
                    const maxNumber = Math.max(...numbers);
                    nextCode = `PIL${maxNumber + 1}`;
                }
            }
            
            console.log('🔢 [DEBUG] Code suggéré:', nextCode);
            codeInput.value = nextCode;
        })
        .catch(error => {
            console.error('❌ [DEBUG] Erreur lors de la récupération des codes:', error);
        });
    
    // Afficher la modale de création
    const modal = new bootstrap.Modal(document.getElementById('createObjectifSpecifiqueModal'));
    modal.show();
}

// Fonction pour modifier un objectif spécifique
function modifierObjectifSpecifique(objectifSpecifiqueId) {
    console.log('🚀 [DEBUG] modifierObjectifSpecifique appelée avec objectifSpecifiqueId:', objectifSpecifiqueId);
    
    // Charger les données de l'objectif spécifique
    fetch(`/api/objectifs-specifiques/${objectifSpecifiqueId}`)
        .then(response => response.json())
        .then(data => {
            console.log('📊 [DEBUG] Données de l\'objectif spécifique reçues:', data);
            
            // Remplir le formulaire d'édition
            document.getElementById('editObjectifSpecifiqueId').value = objectifSpecifiqueId;
            document.getElementById('editObjectifSpecifiqueCode').value = data.objectif_specifique.code;
            document.getElementById('editObjectifSpecifiqueLibelle').value = data.objectif_specifique.libelle;
            document.getElementById('editObjectifSpecifiqueDescription').value = data.objectif_specifique.description || '';
            document.getElementById('editObjectifSpecifiqueObjectifStrategiqueId').value = data.objectif_specifique.objectif_strategique_id;
            document.getElementById('editObjectifSpecifiqueOwnerId').value = data.objectif_specifique.owner_id || '';
            
            // Afficher la modale d'édition
            const modal = new bootstrap.Modal(document.getElementById('editObjectifSpecifiqueModal'));
            modal.show();
        })
        .catch(error => {
            console.error('❌ [DEBUG] Erreur lors du chargement des données:', error);
            showToast('error', 'Erreur lors du chargement des données de l\'objectif spécifique');
        });
}

// Fonction pour ajouter une action
function ajouterAction(objectifSpecifiqueId) {
    console.log('🚀 [DEBUG] ajouterAction appelée avec objectifSpecifiqueId:', objectifSpecifiqueId);
    
    // Pré-remplir l'objectif spécifique sélectionné
    document.getElementById('createActionObjectifSpecifiqueId').value = objectifSpecifiqueId;
    
    // Suggérer un code basé sur les actions existantes
    fetch(`/api/actions/codes/${objectifSpecifiqueId}`)
        .then(response => response.json())
        .then(data => {
            console.log('📊 [DEBUG] Codes existants reçus:', data);
            const codeInput = document.getElementById('createActionCode');
            const existingCodes = data.codes;
            let nextCode = 'A1';
            
            if (existingCodes.length > 0) {
                // Trouver le prochain numéro disponible
                const numbers = existingCodes.map(code => parseInt(code.replace('A', ''))).filter(n => !isNaN(n));
                if (numbers.length > 0) {
                    const maxNumber = Math.max(...numbers);
                    nextCode = `A${maxNumber + 1}`;
                }
            }
            
            console.log('🔢 [DEBUG] Code suggéré:', nextCode);
            codeInput.value = nextCode;
        })
        .catch(error => {
            console.error('❌ [DEBUG] Erreur lors de la récupération des codes:', error);
        });
    
    // Afficher la modale de création
    const modal = new bootstrap.Modal(document.getElementById('createActionModal'));
    modal.show();
}

// Fonction pour modifier une action
function modifierAction(actionId) {
    console.log('🚀 [DEBUG] modifierAction appelée avec actionId:', actionId);
    
    // Charger les données de l'action
    fetch(`/api/actions/${actionId}`)
        .then(response => response.json())
        .then(data => {
            console.log('📊 [DEBUG] Données de l\'action reçues:', data);
            
            // Remplir le formulaire d'édition
            document.getElementById('editActionId').value = actionId;
            document.getElementById('editActionCode').value = data.action.code;
            document.getElementById('editActionLibelle').value = data.action.libelle;
            document.getElementById('editActionDescription').value = data.action.description || '';
            document.getElementById('editActionObjectifSpecifiqueId').value = data.action.objectif_specifique_id;
            document.getElementById('editActionOwnerId').value = data.action.owner_id || '';
            
            // Afficher la modale d'édition
            const modal = new bootstrap.Modal(document.getElementById('editActionModal'));
            modal.show();
        })
        .catch(error => {
            console.error('❌ [DEBUG] Erreur lors du chargement des données:', error);
            showToast('error', 'Erreur lors du chargement des données de l\'action');
        });
}

// Fonction pour modifier une sous-action
function modifierSousAction(sousActionId) {
    console.log('🚀 [DEBUG] modifierSousAction appelée avec sousActionId:', sousActionId);
    
    // Charger les données de la sous-action
    fetch(`/api/sous-actions/${sousActionId}`)
        .then(response => response.json())
        .then(data => {
            console.log('📊 [DEBUG] Données de la sous-action reçues:', data);
            
            // Remplir le formulaire d'édition
            document.getElementById('editSousActionId').value = sousActionId;
            document.getElementById('editSousActionCode').value = data.sous_action.code;
            document.getElementById('editSousActionLibelle').value = data.sous_action.libelle;
            document.getElementById('editSousActionDescription').value = data.sous_action.description || '';
            document.getElementById('editSousActionActionId').value = data.sous_action.action_id;
            document.getElementById('editSousActionTauxAvancement').value = data.sous_action.taux_avancement;
            document.getElementById('editSousActionDateEcheance').value = data.sous_action.date_echeance || '';
            document.getElementById('editSousActionOwnerId').value = data.sous_action.owner_id || '';
            
            // Afficher la modale d'édition
            const modal = new bootstrap.Modal(document.getElementById('editSousActionModal'));
            modal.show();
        })
        .catch(error => {
            console.error('❌ [DEBUG] Erreur lors du chargement des données:', error);
            showToast('error', 'Erreur lors du chargement des données de la sous-action');
        });
}

// Fonction pour ajouter un objectif stratégique
function ajouterObjectifStrategique(pilierId) {
    console.log('🚀 [DEBUG] ajouterObjectifStrategique appelée avec pilierId:', pilierId);
    
    // Pré-remplir le pilier sélectionné
    document.getElementById('createObjectifStrategiquePilierId').value = pilierId;
    
    // Suggérer un code basé sur les objectifs stratégiques existants
    fetch(`/api/objectifs-strategiques/codes/${pilierId}`)
        .then(response => response.json())
        .then(data => {
            console.log('📊 [DEBUG] Codes existants reçus:', data);
            const codeInput = document.getElementById('createObjectifStrategiqueCode');
            const existingCodes = data.codes;
            let nextCode = 'OS1';
            
            if (existingCodes.length > 0) {
                // Trouver le prochain numéro disponible
                const numbers = existingCodes.map(code => parseInt(code.replace('OS', ''))).filter(n => !isNaN(n));
                if (numbers.length > 0) {
                    const maxNumber = Math.max(...numbers);
                    nextCode = `OS${maxNumber + 1}`;
                }
            }
            
            console.log('🔢 [DEBUG] Code suggéré:', nextCode);
            codeInput.value = nextCode;
        })
        .catch(error => {
            console.error('❌ [DEBUG] Erreur lors de la récupération des codes:', error);
        });
    
    // Afficher la modale de création
    const modal = new bootstrap.Modal(document.getElementById('createObjectifStrategiqueModal'));
    modal.show();
}

// Fonction pour modifier un objectif stratégique
function modifierObjectifStrategique(objectifStrategiqueId) {
    console.log('🚀 [DEBUG] modifierObjectifStrategique appelée avec objectifStrategiqueId:', objectifStrategiqueId);
    
    // Charger les données de l'objectif stratégique
    fetch(`/api/objectifs-strategiques/${objectifStrategiqueId}`)
        .then(response => response.json())
        .then(data => {
            console.log('📊 [DEBUG] Données de l\'objectif stratégique reçues:', data);
            
            // Remplir le formulaire d'édition
            document.getElementById('editObjectifStrategiqueId').value = objectifStrategiqueId;
            document.getElementById('editObjectifStrategiqueCode').value = data.objectif_strategique.code;
            document.getElementById('editObjectifStrategiqueLibelle').value = data.objectif_strategique.libelle;
            document.getElementById('editObjectifStrategiqueDescription').value = data.objectif_strategique.description || '';
            document.getElementById('editObjectifStrategiquePilierId').value = data.objectif_strategique.pilier_id;
            document.getElementById('editObjectifStrategiqueOwnerId').value = data.objectif_strategique.owner_id || '';
            
            // Afficher la modale d'édition
            const modal = new bootstrap.Modal(document.getElementById('editObjectifStrategiqueModal'));
            modal.show();
        })
        .catch(error => {
            console.error('❌ [DEBUG] Erreur lors du chargement des données:', error);
            showToast('error', 'Erreur lors du chargement des données de l\'objectif stratégique');
        });
}

// Fonction pour charger les données d'un pilier pour l'édition
function chargerDonneesPilierEdit(button) {
    console.log('🚀 [DEBUG] chargerDonneesPilierEdit appelée');
    
    const pilierId = button.getAttribute('data-pilier-id');
    const code = button.getAttribute('data-pilier-code');
    const libelle = button.getAttribute('data-pilier-libelle');
    const description = button.getAttribute('data-pilier-description');
    
    console.log('📊 [DEBUG] Données du pilier:', { pilierId, code, libelle, description });
    
    // Remplir le formulaire d'édition
    document.getElementById('editPilierId').value = pilierId;
    document.getElementById('editPilierCode').value = code;
    document.getElementById('editPilierLibelle').value = libelle;
    document.getElementById('editPilierDescription').value = description || '';
    
    // Afficher la modale d'édition
    const modal = new bootstrap.Modal(document.getElementById('editPilierModal'));
    modal.show();
}

// Fonction pour ouvrir la vue générale
function ouvrirVueGenerale() {
    console.log('🚀 [DEBUG] ouvrirVueGenerale appelée');
    
    // Rediriger vers la page de vue générale
    window.location.href = '{{ route("vue-generale") }}';
}

// Fonction pour voir les détails d'un pilier
function voirPilier(pilierId, code, libelle) {
    console.log('🚀 [DEBUG] Voir pilier:', { pilierId, code, libelle });
    
    currentPilier = { id: pilierId, code, libelle };
    currentObjectifStrategique = null;
    currentObjectifSpecifique = null;
    currentAction = null;
    
    const modalTitle = document.getElementById('hierarchieModalTitle');
    const modalBody = document.getElementById('hierarchieModalBody');
    const btnRetour = document.getElementById('btnRetour');
    
    modalTitle.textContent = `Détails du Pilier ${code}`;
    btnRetour.style.display = 'block';
    btnRetour.onclick = retourPiliers;
    
    // Afficher un indicateur de chargement
    modalBody.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <p class="mt-3 text-muted">Chargement des données du pilier...</p>
        </div>
    `;
    
    // Charger les objectifs stratégiques via AJAX
    fetch(`/api/piliers/${pilierId}/objectifs-strategiques`)
        .then(response => response.json())
        .then(data => {
            console.log('📊 [DEBUG] Données du pilier reçues:', data);
            
            let objectifsStrategiquesHtml = '';
            if (data.objectifs_strategiques && data.objectifs_strategiques.length > 0) {
                data.objectifs_strategiques.forEach(objectif => {
                    objectifsStrategiquesHtml += `
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm hover-lift">
                                <div class="card-header bg-gradient-success text-white d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <span class="badge bg-light text-dark me-2">${code}.${objectif.code}</span>
                                        ${objectif.libelle}
                                    </h6>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-light"
                                                onclick="voirObjectifStrategique(${objectif.id}, '${escapeJsString(objectif.code)}', '${escapeJsString(objectif.libelle)}', ${pilierId}, '${escapeJsString(code)}', '${escapeJsString(libelle)}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-light"
                                                onclick="modifierObjectifStrategique(${objectif.id})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Description:</strong>
                                        <p class="mb-0 text-muted">${escapeJsString(objectif.description || 'Aucune description')}</p>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Owner:</strong>
                                        <div class="mt-1">
                                            ${objectif.owner ? `<span class="badge bg-success"><i class="fas fa-user me-1"></i>${objectif.owner.name || objectif.owner}</span>` : '<span class="text-muted small">Non assigné</span>'}
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Progression:</strong>
                                        <div class="progress mt-2" style="height: 15px;">
                                            <div class="progress-bar bg-success" style="width: ${objectif.taux_avancement}%" 
                                                 role="progressbar" aria-valuenow="${objectif.taux_avancement}" aria-valuemin="0" aria-valuemax="100">
                                                ${objectif.taux_avancement}%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-info">
                                            <i class="fas fa-list-check me-1"></i>
                                            ${objectif.objectifs_specifiques_count || 0} objectifs spécifiques
                                        </span>
                                        <button type="button" class="btn btn-success btn-sm" 
                                                onclick="voirObjectifStrategique(${objectif.id}, '${escapeJsString(objectif.code)}', '${escapeJsString(objectif.libelle)}', ${pilierId}, '${escapeJsString(code)}', '${escapeJsString(libelle)}')">
                                            <i class="fas fa-arrow-right me-1"></i>
                                            Voir détails
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                objectifsStrategiquesHtml = `
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-bullseye fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun objectif stratégique</h5>
                            <p class="text-muted">Ce pilier n'a pas encore d'objectifs stratégiques.</p>
                            <button type="button" class="btn btn-success" onclick="ajouterObjectifStrategique(${pilierId})">
                                <i class="fas fa-plus me-2"></i>
                                Créer le premier objectif
                            </button>
                        </div>
                    </div>
                `;
            }
            
            modalBody.innerHTML = `
                <!-- Informations du pilier -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-info-circle me-2"></i>
                                Détails du Pilier
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Code:</strong>
                                    <span class="badge bg-primary ms-2">${code}</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Libellé:</strong>
                                    <p class="mb-0">${escapeJsString(libelle)}</p>
                                </div>
                                <div class="mb-3">
                                    <strong>Progression:</strong>
                                    <div class="progress mt-2" style="height: 20px;">
                                        <div class="progress-bar bg-success" style="width: ${data.taux_avancement}%" 
                                             role="progressbar" aria-valuenow="${data.taux_avancement}" aria-valuemin="0" aria-valuemax="100">
                                            <strong>${data.taux_avancement}%</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-chart-line me-2"></i>
                                Statistiques
                            </div>
                            <div class="card-body text-center">
                                <div class="display-4 text-success mb-2">${data.taux_avancement}%</div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <div class="h4 text-primary">${data.objectifs_strategiques ? data.objectifs_strategiques.length : 0}</div>
                                            <small class="text-muted">Objectifs Stratégiques</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h4 text-success">0</div>
                                        <small class="text-muted">Terminés</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Objectifs Stratégiques -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-bullseye me-2 text-success"></i>
                                Objectifs Stratégiques (${data.objectifs_strategiques ? data.objectifs_strategiques.length : 0})
                            </h5>
                            <button type="button" class="btn btn-success" onclick="ajouterObjectifStrategique(${pilierId})">
                                <i class="fas fa-plus me-2"></i>
                                Ajouter un Objectif
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    ${objectifsStrategiquesHtml}
                </div>
            `;
        })
        .catch(error => {
            console.error('❌ [ERROR] Erreur lors du chargement du pilier:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Erreur lors du chargement des données du pilier. Veuillez réessayer.
                </div>
            `;
        });
}

// Fonction pour retourner à la liste des piliers
function retourPiliers() {
    console.log('🔄 [DEBUG] Retour à la liste des piliers');
    currentPilier = null;
    currentObjectifStrategique = null;
    currentObjectifSpecifique = null;
    currentAction = null;
    chargerListePiliers();
}

// Fonction pour ouvrir le modal Vue Générale
function openVueGeneraleModal() {
    console.log('🔄 [VUE-GENERALE] Fonction openVueGeneraleModal appelée');
    
    // Utiliser directement le dispatch d'événement Livewire
    console.log('🔄 [VUE-GENERALE] Dispatch de l\'événement openVueGeneraleModal...');
    Livewire.dispatch('openVueGeneraleModal');
}

// Fonction pour ouvrir le modal hiérarchique
function openPilierHierarchiqueModal(pilierId) {
    console.log('🔄 [HIERARCHIQUE] Fonction openPilierHierarchiqueModal appelée avec pilierId:', pilierId);
    
    // Utiliser directement le dispatch d'événement Livewire
    console.log('🔄 [HIERARCHIQUE] Dispatch de l\'événement openPilierHierarchiqueModal...');
    
    // Vérifier que Livewire est disponible
    if (typeof Livewire !== 'undefined') {
        // Essayer de cibler spécifiquement le composant
        const components = Livewire.all();
        let componentFound = false;
        
        console.log('🔍 [HIERARCHIQUE] Composants Livewire disponibles:', Object.keys(components));
        
        // Lister tous les composants pour le débogage
        for (let componentId in components) {
            const component = components[componentId];
            console.log('🔍 [HIERARCHIQUE] Composant:', componentId, 'Propriétés:', Object.keys(component.$wire || {}));
        }
        
        for (let componentId in components) {
            const component = components[componentId];
            if (component.$wire && typeof component.$wire.openModal === 'function') {
                                // Vérifier si c'est le bon composant en cherchant la propriété componentType
                const isHierarchiqueComponent = component.$wire.componentType === 'hierarchique';
                
                if (isHierarchiqueComponent) {
                    console.log('🔍 [HIERARCHIQUE] Composant hiérarchique trouvé:', componentId);
                    try {
                        component.$wire.openModal({ pilierId: pilierId });
                        componentFound = true;
                        console.log('✅ [HIERARCHIQUE] Méthode openModal appelée directement');
                        break;
                    } catch (e) {
                        console.log('❌ [HIERARCHIQUE] Erreur lors de l\'appel direct:', e);
                    }
                } else {
                    console.log('🔍 [HIERARCHIQUE] Composant ignoré (pas hiérarchique):', componentId);
                }
            }
        }
        
        if (!componentFound) {
            // Essayer de cibler le composant par ID
            const hierarchiqueComponent = document.getElementById('pilier-hierarchique-modal');
            if (hierarchiqueComponent && hierarchiqueComponent.__livewire) {
                console.log('🔍 [HIERARCHIQUE] Composant trouvé par ID');
                try {
                    hierarchiqueComponent.__livewire.openModal({ pilierId: pilierId });
                    console.log('✅ [HIERARCHIQUE] Méthode openModal appelée via ID');
                } catch (e) {
                    console.log('❌ [HIERARCHIQUE] Erreur lors de l\'appel via ID:', e);
                    // Fallback vers le dispatch d'événement
                    Livewire.dispatch('openPilierHierarchiqueModal', { pilierId: pilierId });
                    console.log('✅ [HIERARCHIQUE] Événement dispatché (fallback final)');
                }
            } else {
                // Fallback vers le dispatch d'événement
                Livewire.dispatch('openPilierHierarchiqueModal', { pilierId: pilierId });
                console.log('✅ [HIERARCHIQUE] Événement dispatché (fallback)');
            }
        }
    } else {
        console.error('❌ [HIERARCHIQUE] Livewire n\'est pas disponible');
    }
}

// Écouteur pour synchroniser les taux en temps réel sur la page principale
document.addEventListener('livewire:init', () => {
    Livewire.on('tauxUpdated', (event) => {
        console.log('🔄 [SYNC-PRINCIPAL] Événement tauxUpdated reçu:', event);
        
        const tauxData = event[0];
        
        // Mettre à jour les taux des piliers sur la page principale
        if (tauxData.pilier) {
            const pilierId = tauxData.pilier.id;
            const pilierTaux = tauxData.pilier.taux;
            
            // Mettre à jour la barre de progression du pilier
            const pilierProgressBar = document.querySelector(`[data-pilier-id="${pilierId}"] .progress-bar`);
            if (pilierProgressBar) {
                pilierProgressBar.style.width = pilierTaux + '%';
                pilierProgressBar.setAttribute('aria-valuenow', pilierTaux);
                pilierProgressBar.innerHTML = `<strong>${pilierTaux.toFixed(2)}%</strong>`;
            }
            
            // Mettre à jour le pourcentage affiché
            const pilierTauxElement = document.querySelector(`[data-pilier-id="${pilierId}"] .taux-avancement`);
            if (pilierTauxElement) {
                pilierTauxElement.textContent = pilierTaux.toFixed(2) + '%';
            }
            
            console.log('✅ [SYNC-PRINCIPAL] Taux du pilier mis à jour:', pilierId, pilierTaux);
        }
    });
    
    // Écouteur pour vérifier que l'événement hiérarchique est bien reçu
    Livewire.on('openPilierHierarchiqueModal', (event) => {
        console.log('🔄 [HIERARCHIQUE] Événement reçu par l\'écouteur:', event);
        
        // Essayer de trouver le composant hiérarchique et l'appeler
        const components = Livewire.all();
        for (let componentId in components) {
            const component = components[componentId];
            if (component.$wire && component.$wire.showModal !== undefined) {
                console.log('🔍 [HIERARCHIQUE] Composant modal trouvé:', componentId);
                try {
                    component.$wire.openModal(event[0]);
                    console.log('✅ [HIERARCHIQUE] Modal ouvert via écouteur');
                    break;
                } catch (e) {
                    console.log('❌ [HIERARCHIQUE] Erreur lors de l\'ouverture via écouteur:', e);
                }
            }
        }
    });
});

</script>
@endpush

<!-- Composant Livewire pour le modal des détails du pilier -->
        <livewire:pilier-details-modal-new />
        <livewire:vue-generale-modal />
                            <livewire:pilier-hierarchique-modal wire:key="pilier-hierarchique-modal" id="pilier-hierarchique-modal" />