@extends('layouts.app')

@section('title', 'Détails du Pilier')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building me-2"></i>
                {{ $pilier->code }} - {{ $pilier->libelle }}
            </h1>
            <p class="text-muted">Détails du pilier stratégique</p>
        </div>
        <div>
            @if(Auth::user()->canCreatePilier())
            <a href="{{ route('piliers.edit', $pilier) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>
                Modifier
            </a>
            @endif
            <a href="{{ route('piliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Informations du pilier -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations générales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Code</label>
                            <p class="mb-0">
                                <span class="badge bg-primary fs-6">{{ $pilier->code }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Libellé</label>
                        <p class="mb-0">{{ $pilier->libelle }}</p>
                    </div>
                    @if($pilier->description)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <p class="mb-0">{{ $pilier->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Progression
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="progress mb-2" style="height: 20px;">
                            <div class="progress-bar bg-{{ $pilier->statut_color }}" 
                                 style="width: {{ $pilier->taux_avancement }}%">
                                {{ $pilier->taux_avancement }}%
                            </div>
                        </div>
                        <small class="text-muted">Taux d'avancement global</small>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-0">{{ $pilier->objectifsStrategiques->count() }}</h4>
                                <small class="text-muted">Objectifs Stratégiques</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-0">
                                {{ $pilier->objectifsStrategiques->where('taux_avancement', 100)->count() }}
                            </h4>
                            <small class="text-muted">Terminés</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Objectifs Stratégiques avec Cards -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-bullseye me-2"></i>
                Objectifs Stratégiques
                <span class="badge bg-primary ms-2">{{ $pilier->objectifsStrategiques->count() }}</span>
            </h5>
            @if(Auth::user()->canCreateObjectifStrategique())
            <button type="button" class="btn btn-primary btn-sm" onclick="ajouterCardObjectif()">
                <i class="fas fa-plus me-2"></i>
                Ajouter un Objectif
            </button>
            @endif
        </div>
        <div class="card-body">
            <div id="objectifs-container" class="row">
                @foreach($pilier->objectifsStrategiques as $index => $objectifStrategique)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card objectif-card h-100" data-objectif-id="{{ $objectifStrategique->id }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <span class="badge bg-info me-2">{{ $objectifStrategique->code }}</span>
                                Objectif #{{ $index + 1 }}
                            </h6>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        title="Voir" 
                                        data-objectif-id="{{ $objectifStrategique->id }}"
                                        data-objectif-code="{{ $objectifStrategique->code }}"
                                        data-objectif-libelle="{{ $objectifStrategique->libelle }}"
                                        data-objectif-description="{{ $objectifStrategique->description ?? '' }}"
                                        data-objectif-owner="{{ $objectifStrategique->owner ? $objectifStrategique->owner->name : 'Non assigné' }}"
                                        data-objectif-taux="{{ $objectifStrategique->taux_avancement }}"
                                        data-objectif-count="{{ $objectifStrategique->objectifsSpecifiques->count() }}"
                                        onclick="ouvrirModalDetails(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('objectifs-strategiques.show', $objectifStrategique->id) }}" 
                                   class="btn btn-sm btn-outline-info" title="Page complète">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                @if(Auth::user()->canUpdateObjectifStrategique())
                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                        title="Modifier" onclick="ouvrirModalEdit({{ $objectifStrategique->id }}, '{{ $objectifStrategique->code }}', '{{ $objectifStrategique->libelle }}', '{{ $objectifStrategique->description ?? '' }}', {{ $objectifStrategique->owner_id ?? 'null' }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">{{ $objectifStrategique->libelle }}</h6>
                            @if($objectifStrategique->description)
                                <p class="card-text small text-muted">{{ Str::limit($objectifStrategique->description, 100) }}</p>
                            @endif
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Owner</label>
                                <p class="mb-0">
                                    @if($objectifStrategique->owner)
                                        <span class="badge bg-success">{{ $objectifStrategique->owner->name }}</span>
                                    @else
                                        <span class="text-muted small">Non assigné</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Progression</label>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $objectifStrategique->statut_color }}" 
                                             style="width: {{ $objectifStrategique->taux_avancement }}%"></div>
                                    </div>
                                    <span class="badge bg-{{ $objectifStrategique->statut_color }} small">
                                        {{ $objectifStrategique->taux_avancement }}%
                                    </span>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-warning">
                                    {{ $objectifStrategique->objectifsSpecifiques->count() }} objectifs spécifiques
                                </span>
                                <small class="text-muted">{{ $objectifStrategique->created_at->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
                <!-- Template pour nouvelle card -->
                <div class="col-lg-6 col-xl-4 mb-4" id="template-nouvelle-card" style="display: none;">
                    <div class="card objectif-card h-100 border-dashed">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <span class="badge bg-info me-2" id="template-code">OS{{ $pilier->objectifsStrategiques->count() + 1 }}</span>
                                Nouvel Objectif
                            </h6>
                        </div>
                        <div class="card-body">
                            <form class="form-nouvel-objectif">
                                <input type="hidden" name="pilier_id" value="{{ $pilier->id }}">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Code</label>
                                    <input type="text" class="form-control form-control-sm" name="code" 
                                           value="OS{{ $pilier->objectifsStrategiques->count() + 1 }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Libellé <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" name="libelle" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Description</label>
                                    <textarea class="form-control form-control-sm" name="description" rows="2"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Owner</label>
                                    <select class="form-select form-select-sm" name="owner_id">
                                        <option value="">Sélectionner un owner</option>
                                        @foreach(App\Models\User::whereHas('role', function($query) {
                                            $query->whereIn('nom', ['admin_general', 'owner_os']);
                                        })->get() as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="annulerNouvelleCard()">
                                        Annuler
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save me-1"></i>
                                        Créer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($pilier->objectifsStrategiques->count() == 0)
            <div class="text-center py-5">
                <i class="fas fa-bullseye fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucun objectif stratégique</h5>
                <p class="text-muted">Ce pilier n'a pas encore d'objectifs stratégiques.</p>
                @if(Auth::user()->canCreateObjectifStrategique())
                <button type="button" class="btn btn-primary" onclick="ajouterCardObjectif()">
                    <i class="fas fa-plus me-2"></i>
                    Créer le premier objectif
                </button>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Éditer Objectif Stratégique -->
<div class="modal fade" id="editObjectifModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Éditer Objectif Stratégique</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editObjectifForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_objectif_id" name="objectif_id">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" id="edit_code" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Libellé *</label>
                        <input type="text" class="form-control" id="edit_libelle" name="libelle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner</label>
                        <select class="form-select" id="edit_owner_id" name="owner_id">
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

<!-- Modal Détails Objectif Stratégique -->
<div class="modal fade" id="objectifDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="objectifDetailsModalTitle">Détails de l'Objectif Stratégique</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="objectifDetailsModalBody">
                <!-- Le contenu sera rempli dynamiquement par JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let objectifIndex = {{ $pilier->objectifsStrategiques->count() }};

function ajouterCardObjectif() {
    const container = document.getElementById('objectifs-container');
    const template = document.getElementById('template-nouvelle-card');
    
    // Créer une copie du template
    const nouvelleCard = template.cloneNode(true);
    nouvelleCard.id = 'nouvelle-card-' + objectifIndex;
    nouvelleCard.style.display = 'block';
    
    // Calculer le prochain code disponible
    const existingCodes = Array.from(document.querySelectorAll('.objectif-card:not([id^="nouvelle-card-"]) .code-badge'))
        .map(el => el.textContent.trim())
        .filter(code => code.startsWith('OS'));
    
    let nextCodeNumber = 1;
    while (existingCodes.includes('OS' + nextCodeNumber)) {
        nextCodeNumber++;
    }
    
    // Mettre à jour le code suggéré
    const codeInput = nouvelleCard.querySelector('input[name="code"]');
    codeInput.value = 'OS' + nextCodeNumber;
    
    // Ajouter la card au container
    container.appendChild(nouvelleCard);
    
    // Gérer la soumission du formulaire
    const form = nouvelleCard.querySelector('.form-nouvel-objectif');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        creerObjectifStrategique(this, nouvelleCard);
    });
    
    // Masquer le message "aucun objectif"
    const emptyMessage = container.querySelector('.text-center');
    if (emptyMessage) {
        emptyMessage.style.display = 'none';
    }
    
    objectifIndex++;
}

function annulerNouvelleCard() {
    const nouvelleCard = document.querySelector('#nouvelle-card-' + (objectifIndex - 1));
    if (nouvelleCard) {
        nouvelleCard.remove();
    }
    
    // Vérifier s'il reste des objectifs
    const objectifsCards = document.querySelectorAll('.objectif-card:not([id^="nouvelle-card-"])');
    if (objectifsCards.length === 0) {
        const emptyMessage = document.querySelector('.text-center');
        if (emptyMessage) {
            emptyMessage.style.display = 'block';
        }
    }
}

function creerObjectifStrategique(form, cardElement) {
    const formData = new FormData(form);
    formData.append('_token', '{{ csrf_token() }}');
    
    console.log('Envoi des données:', Object.fromEntries(formData));
    
    fetch('{{ route("objectifs-strategiques.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        console.log('Response ok:', response.ok);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Response text:', text);
                throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            // Supprimer la card de formulaire
            cardElement.remove();
            
            // Afficher un message de succès
            alert('Objectif stratégique créé avec succès !');
            
            // Recharger la page pour afficher le nouvel objectif
            window.location.reload();
        } else {
            alert('Erreur lors de la création : ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur complète:', error);
        console.error('Stack trace:', error.stack);
        alert('Erreur lors de la création de l\'objectif stratégique. Vérifiez la console pour plus de détails.');
    });
}

function editerCardObjectif(objectifId) {
    // Rediriger vers la page d'édition
    window.location.href = `/objectifs-strategiques/${objectifId}/edit`;
}

function ouvrirModalEdit(objectifId, code, libelle, description, ownerId) {
    // Remplir le modal avec les données
    document.getElementById('edit_objectif_id').value = objectifId;
    document.getElementById('edit_code').value = code;
    document.getElementById('edit_libelle').value = libelle;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_owner_id').value = ownerId || '';
    
    // Ouvrir le modal
    const modal = new bootstrap.Modal(document.getElementById('editObjectifModal'));
    modal.show();
}

function ouvrirModalDetails(button) {
    const modal = new bootstrap.Modal(document.getElementById('objectifDetailsModal'));
    const modalBody = document.getElementById('objectifDetailsModalBody');
    const modalTitle = document.getElementById('objectifDetailsModalTitle');

    // Récupérer les données depuis les attributs data-
    const objectifId = button.getAttribute('data-objectif-id');
    const code = button.getAttribute('data-objectif-code');
    const libelle = button.getAttribute('data-objectif-libelle');
    const description = button.getAttribute('data-objectif-description');
    const owner = button.getAttribute('data-objectif-owner');
    const tauxAvancement = parseInt(button.getAttribute('data-objectif-taux'));
    const nbObjectifsSpecifiques = parseInt(button.getAttribute('data-objectif-count'));

    // Déterminer la couleur de la barre de progression
    let progressColor = 'bg-success';
    if (tauxAvancement < 30) progressColor = 'bg-danger';
    else if (tauxAvancement < 70) progressColor = 'bg-warning';

    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="card border-success mb-3">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations générales
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Code:</strong>
                            <span class="badge bg-primary ms-2">${code}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Libellé:</strong>
                            <p class="mb-0">${libelle}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Description:</strong>
                            <p class="mb-0 text-muted">${description || 'Aucune description disponible'}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Owner:</strong>
                            <span class="badge bg-info ms-2">${owner}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-success mb-3">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-chart-line me-2"></i>
                        Progression
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h4 class="text-success">${tauxAvancement}%</h4>
                            <div class="progress mb-3" style="height: 25px;">
                                <div class="progress-bar ${progressColor}" 
                                     role="progressbar" 
                                     style="width: ${tauxAvancement}%"
                                     aria-valuenow="${tauxAvancement}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    ${tauxAvancement}%
                                </div>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-6">
                                <h5 class="text-muted">${nbObjectifsSpecifiques}</h5>
                                <small class="text-muted">Objectifs Spécifiques</small>
                            </div>
                            <div class="col-6">
                                <h5 class="text-success">0</h5>
                                <small class="text-muted">Terminés</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <i class="fas fa-list-check me-2"></i>
                Objectifs Spécifiques
            </div>
            <div class="card-body">
                ${nbObjectifsSpecifiques > 0 ? 
                    `<p class="text-muted">${nbObjectifsSpecifiques} objectif(s) spécifique(s) associé(s) à cet objectif stratégique.</p>` :
                    `<div class="text-center py-4">
                        <i class="fas fa-list-check fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Aucun objectif spécifique</h6>
                        <p class="text-muted small">Cet objectif stratégique n'a pas encore d'objectifs spécifiques.</p>
                    </div>`
                }
            </div>
        </div>
    `;
    
    modalTitle.textContent = `Détails de l'Objectif ${code}`;
    modal.show();
}

// Gestion du formulaire d'édition
document.getElementById('editObjectifForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const objectifId = document.getElementById('edit_objectif_id').value;
    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch(`/objectifs-strategiques/${objectifId}`, {
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
            alert('Objectif stratégique modifié avec succès !');
            window.location.reload();
        } else {
            alert('Erreur lors de la modification : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la modification.');
    });
});
</script>

<style>
.border-dashed {
    border: 2px dashed #dee2e6 !important;
}

.objectif-card {
    transition: all 0.3s ease;
}

.objectif-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.form-control-sm, .form-select-sm {
    font-size: 0.875rem;
}
</style>
@endpush 