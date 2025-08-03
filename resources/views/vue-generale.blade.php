@extends('layouts.app')

@section('title', 'SUIVI DES DILIGENCES - Pilotage Stratégique')

@section('content')
<div class="container-fluid mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-success">
                    <i class="fas fa-chart-line me-2"></i>
                    SUIVI DES DILIGENCES
                </h1>
                <div>
                    <a href="{{ route('piliers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Retour aux Piliers
                    </a>
                </div>
            </div>

            <!-- Tableau de vue générale -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="vueGeneraleTable">
                            <thead class="table-success">
                                <tr>
                                    <th colspan="2" class="text-center">COMITE STRATEGIQUE</th>
                                    <th colspan="2" class="text-center">COMITE DE PILOTAGE</th>
                                    <th colspan="2" class="text-center">ACTIONS</th>
                                    <th colspan="2" class="text-center">COMITE EXECUTIF</th>
                                    <th colspan="5" class="text-center">EXECUTIONS</th>
                                </tr>
                                <tr>
                                    <th>%</th>
                                    <th>OWNER</th>
                                    <th>%</th>
                                    <th>OWNER</th>
                                    <th>%</th>
                                    <th>OWNER</th>
                                    <th>%</th>
                                    <th>OWNER</th>
                                    <th>ECHEANCE</th>
                                    <th>DATE REALISAT</th>
                                    <th>ECART</th>
                                    <th>PROGRESSION</th>
                                </tr>
                            </thead>
                            <tbody id="vueGeneraleBody">
                                <!-- Les données seront chargées dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Indicateur de chargement -->
            <div id="loadingIndicator" class="text-center py-5">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-3 text-muted">Chargement du suivi des diligences...</p>
            </div>
        </div>
    </div>
</div>

<!-- Styles pour le tableau -->
<style>
    #vueGeneraleTable {
        font-size: 0.8rem;
        border-collapse: collapse;
    }
    
    #vueGeneraleTable th {
        background-color: #28a745;
        color: white;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        padding: 8px;
        border: 1px solid #1e7e34;
    }
    
    #vueGeneraleTable td {
        padding: 6px 8px;
        vertical-align: middle;
        border: 1px solid #dee2e6;
        position: relative;
    }
    
    .pilier-header {
        background-color: #e8f5e8;
        font-weight: bold;
        color: #155724;
        text-align: center;
    }
    
    .objectif-strategique {
        background-color: #f8f9fa;
        font-weight: 500;
    }
    
    .objectif-specifique {
        background-color: #ffffff;
    }
    
    .action-row {
        background-color: #f8f9fa;
    }
    
    .sous-action-row {
        background-color: #ffffff;
    }
    
    .progress-cell {
        width: 60px;
        text-align: center;
        font-weight: bold;
    }
    
    .owner-cell {
        width: 100px;
        text-align: center;
    }
    
    .date-cell {
        width: 90px;
        text-align: center;
    }
    
    .ecart-cell {
        width: 70px;
        text-align: center;
    }
    
    .progression-cell {
        width: 120px;
        text-align: center;
    }
    
    .status-done {
        background-color: #28a745;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.7rem;
        font-weight: bold;
    }
    
    .status-delay {
        background-color: #dc3545;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.7rem;
        font-weight: bold;
    }
    
    .status-open {
        background-color: #ffc107;
        color: #212529;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.7rem;
        font-weight: bold;
    }
    
    .status-error {
        background-color: #6c757d;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.7rem;
        font-weight: bold;
    }
    
    .owner-badge {
        font-size: 0.7rem;
        padding: 2px 6px;
    }
    
    .progress-slider {
        width: 100px;
        height: 20px;
    }
    
    .progress-display {
        font-size: 0.7rem;
        font-weight: bold;
        margin-top: 2px;
    }
    
    .merged-cell {
        background-color: #f8f9fa;
        font-weight: 500;
    }
    
    .hierarchy-indent {
        padding-left: 20px;
    }
    
    .hierarchy-indent-2 {
        padding-left: 40px;
    }
    
    .hierarchy-indent-3 {
        padding-left: 60px;
    }
</style>

@endsection

@push('scripts')
<script>
// Variables globales pour stocker les données
let vueGeneraleData = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 [DEBUG] Chargement du suivi des diligences');
    chargerVueGenerale();
});

function chargerVueGenerale() {
    console.log('📊 [DEBUG] Chargement des données du suivi des diligences');
    
    fetch('/api/vue-generale')
        .then(response => {
            console.log('📡 [DEBUG] Réponse reçue:', { status: response.status, statusText: response.statusText });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📊 [DEBUG] Données reçues:', data);
            vueGeneraleData = data.piliers;
            afficherVueGenerale(vueGeneraleData);
        })
        .catch(error => {
            console.error('💥 [DEBUG] Erreur lors du chargement:', error);
            document.getElementById('loadingIndicator').innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Erreur lors du chargement</h5>
                    <p class="mb-0">Détails: ${error.message}</p>
                    <button type="button" class="btn btn-primary mt-3" onclick="chargerVueGenerale()">
                        <i class="fas fa-redo me-2"></i>Réessayer
                    </button>
                </div>
            `;
        });
}

function afficherVueGenerale(piliers) {
    const tbody = document.getElementById('vueGeneraleBody');
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    if (!piliers || piliers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="13" class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun pilier trouvé</h5>
                    <p class="text-muted">Commencez par créer des piliers pour voir le suivi des diligences.</p>
                </td>
            </tr>
        `;
        loadingIndicator.style.display = 'none';
        return;
    }
    
    let html = '';
    
    piliers.forEach(pilier => {
        // En-tête du pilier
        html += `
            <tr class="pilier-header">
                <td colspan="13">
                    <strong>PILIER ${pilier.code} - ${pilier.libelle.toUpperCase()} - ${pilier.taux_avancement}%</strong>
                </td>
            </tr>
        `;
        
        if (!pilier.objectifs_strategiques || pilier.objectifs_strategiques.length === 0) {
            html += `
                <tr>
                    <td colspan="13" class="text-center py-3 text-muted">
                        <i class="fas fa-bullseye me-2"></i>
                        Aucun objectif stratégique pour ce pilier
                    </td>
                </tr>
            `;
            return;
        }
        
        pilier.objectifs_strategiques.forEach(objectifStrategique => {
            // Calculer le rowspan pour l'objectif stratégique
            const osRowSpan = calculerRowSpanObjectifStrategique(objectifStrategique);
            
            // Objectif stratégique - COMITE STRATEGIQUE
            html += `
                <tr class="objectif-strategique" data-os-id="${objectifStrategique.id}">
                    <td class="progress-cell" rowspan="${osRowSpan}">
                        <strong id="os-${objectifStrategique.id}-taux">${objectifStrategique.taux_avancement}%</strong>
                    </td>
                    <td class="owner-cell" rowspan="${osRowSpan}">
                        <span class="badge bg-primary owner-badge">${objectifStrategique.owner ? objectifStrategique.owner.name : 'Non assigné'}</span>
                    </td>
                    <td colspan="11" class="hierarchy-indent">
                        <strong>${pilier.code}.${objectifStrategique.code}. ${objectifStrategique.libelle}</strong>
                    </td>
                </tr>
            `;
            
            if (!objectifStrategique.objectifs_specifiques || objectifStrategique.objectifs_specifiques.length === 0) {
                html += `
                    <tr>
                        <td colspan="11" class="text-center py-2 text-muted">
                            <i class="fas fa-tasks me-2"></i>
                            Aucun objectif spécifique pour cet objectif stratégique
                        </td>
                    </tr>
                `;
                return;
            }
            
            objectifStrategique.objectifs_specifiques.forEach(objectifSpecifique => {
                // Calculer le rowspan pour l'objectif spécifique
                const oSpecRowSpan = calculerRowSpanObjectifSpecifique(objectifSpecifique);
                
                // Objectif spécifique - COMITE DE PILOTAGE
                html += `
                    <tr class="objectif-specifique" data-ospec-id="${objectifSpecifique.id}">
                        <td class="progress-cell" rowspan="${oSpecRowSpan}">
                            <strong id="ospec-${objectifSpecifique.id}-taux">${objectifSpecifique.taux_avancement}%</strong>
                        </td>
                        <td class="owner-cell" rowspan="${oSpecRowSpan}">
                            <span class="badge bg-info owner-badge">${objectifSpecifique.owner ? objectifSpecifique.owner.name : 'Non assigné'}</span>
                        </td>
                        <td colspan="9" class="hierarchy-indent-2">
                            <strong>${pilier.code}.${objectifStrategique.code}.${objectifSpecifique.code}. ${objectifSpecifique.libelle}</strong>
                        </td>
                    </tr>
                `;
                
                if (!objectifSpecifique.actions || objectifSpecifique.actions.length === 0) {
                    html += `
                        <tr>
                            <td colspan="9" class="text-center py-2 text-muted">
                                <i class="fas fa-cogs me-2"></i>
                                Aucune action pour cet objectif spécifique
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                objectifSpecifique.actions.forEach(action => {
                    // Calculer le rowspan pour l'action
                    const actionRowSpan = calculerRowSpanAction(action);
                    
                    // Action - ACTIONS
                    html += `
                        <tr class="action-row" data-action-id="${action.id}">
                            <td class="progress-cell" rowspan="${actionRowSpan}">
                                <strong id="action-${action.id}-taux">${action.taux_avancement}%</strong>
                            </td>
                            <td class="owner-cell" rowspan="${actionRowSpan}">
                                <span class="badge bg-warning owner-badge">${action.owner ? action.owner.name : 'Non assigné'}</span>
                            </td>
                            <td colspan="7" class="hierarchy-indent-3">
                                <strong>${pilier.code}.${objectifStrategique.code}.${objectifSpecifique.code}.${action.code}. ${action.libelle}</strong>
                            </td>
                        </tr>
                    `;
                    
                    if (!action.sous_actions || action.sous_actions.length === 0) {
                        html += `
                            <tr>
                                <td colspan="7" class="text-center py-2 text-muted">
                                    <i class="fas fa-list me-2"></i>
                                    Aucune sous-action pour cette action
                                </td>
                            </tr>
                        `;
                        return;
                    }
                    
                    action.sous_actions.forEach(sousAction => {
                        const ecart = calculerEcart(sousAction.date_echeance, sousAction.date_realisation, sousAction.taux_avancement);
                        
                        // Sous-action - COMITE EXECUTIF + EXECUTIONS
                        html += `
                            <tr class="sous-action-row" data-sous-action-id="${sousAction.id}">
                                <td class="progress-cell">
                                    <strong id="sous-action-${sousAction.id}-taux">${sousAction.taux_avancement}%</strong>
                                </td>
                                <td class="owner-cell">
                                    <span class="badge bg-success owner-badge">${sousAction.owner ? sousAction.owner.name : 'Non assigné'}</span>
                                </td>
                                <td class="date-cell">${formaterDate(sousAction.date_echeance)}</td>
                                <td class="date-cell">${formaterDate(sousAction.date_realisation)}</td>
                                <td class="ecart-cell">
                                    <span class="${ecart.classe}" id="sous-action-${sousAction.id}-ecart">${ecart.texte}</span>
                                </td>
                                <td class="progression-cell">
                                    <input type="range" 
                                           class="form-range progress-slider" 
                                           id="slider-${sousAction.id}"
                                           min="0" 
                                           max="100" 
                                           value="${sousAction.taux_avancement}"
                                           oninput="updateSousActionProgress(${sousAction.id}, this.value)"
                                           onchange="saveSousActionProgress(${sousAction.id}, this.value)">
                                    <div class="progress-display" id="display-${sousAction.id}">${sousAction.taux_avancement}%</div>
                                </td>
                            </tr>
                        `;
                    });
                });
            });
        });
    });
    
    tbody.innerHTML = html;
    loadingIndicator.style.display = 'none';
    
    console.log('✅ [DEBUG] Suivi des diligences affiché avec succès');
}

function calculerRowSpanObjectifStrategique(objectifStrategique) {
    let rowSpan = 1;
    
    if (objectifStrategique.objectifs_specifiques) {
        objectifStrategique.objectifs_specifiques.forEach(ospec => {
            if (ospec.actions) {
                ospec.actions.forEach(action => {
                    if (action.sous_actions && action.sous_actions.length > 0) {
                        rowSpan += action.sous_actions.length;
                    } else {
                        rowSpan += 1;
                    }
                });
            } else {
                rowSpan += 1;
            }
        });
    }
    
    return Math.max(rowSpan, 1);
}

function calculerRowSpanObjectifSpecifique(objectifSpecifique) {
    let rowSpan = 1;
    
    if (objectifSpecifique.actions) {
        objectifSpecifique.actions.forEach(action => {
            if (action.sous_actions && action.sous_actions.length > 0) {
                rowSpan += action.sous_actions.length;
            } else {
                rowSpan += 1;
            }
        });
    }
    
    return Math.max(rowSpan, 1);
}

function calculerRowSpanAction(action) {
    let rowSpan = 1;
    
    if (action.sous_actions && action.sous_actions.length > 0) {
        rowSpan += action.sous_actions.length;
    }
    
    return Math.max(rowSpan, 1);
}

function calculerEcart(dateEcheance, dateRealisation, tauxAvancement) {
    if (!dateEcheance) {
        return { texte: 'Open', classe: 'status-open' };
    }
    
    if (tauxAvancement === 100) {
        return { texte: 'Done', classe: 'status-done' };
    }
    
    try {
        const echeance = new Date(dateEcheance);
        const maintenant = new Date();
        
        if (dateRealisation) {
            const realisation = new Date(dateRealisation);
            const diffJours = Math.ceil((realisation - echeance) / (1000 * 60 * 60 * 24));
            
            if (diffJours <= 0) {
                return { texte: 'Done', classe: 'status-done' };
            } else {
                return { texte: `${Math.abs(diffJours)}J`, classe: 'status-delay' };
            }
        } else {
            const diffJours = Math.ceil((echeance - maintenant) / (1000 * 60 * 60 * 24));
            
            if (diffJours < 0) {
                return { texte: `${Math.abs(diffJours)}J`, classe: 'status-delay' };
            } else if (diffJours === 0) {
                return { texte: 'Aujourd\'hui', classe: 'status-open' };
            } else {
                return { texte: `${diffJours}J`, classe: 'status-open' };
            }
        }
    } catch (error) {
        return { texte: '#REF!', classe: 'status-error' };
    }
}

// Fonction pour mettre à jour l'affichage de la progression en temps réel
function updateSousActionProgress(sousActionId, value) {
    console.log('🔄 [DEBUG] Mise à jour progression sous-action:', { sousActionId, value });
    
    // Mettre à jour l'affichage
    document.getElementById(`display-${sousActionId}`).textContent = `${value}%`;
    
    // Mettre à jour les données en mémoire
    const sousAction = trouverSousAction(sousActionId);
    if (sousAction) {
        sousAction.taux_avancement = parseInt(value);
        
        // Mettre à jour l'écart en temps réel
        const ecart = calculerEcart(sousAction.date_echeance, sousAction.date_realisation, sousAction.taux_avancement);
        document.getElementById(`sous-action-${sousActionId}-ecart`).textContent = ecart.texte;
        document.getElementById(`sous-action-${sousActionId}-ecart`).className = ecart.classe;
        
        // Mettre à jour les pourcentages parents
        updateActionPercentage(sousAction.action_id);
    }
}

// Fonction pour sauvegarder la progression dans la base de données
function saveSousActionProgress(sousActionId, value) {
    console.log('💾 [DEBUG] Sauvegarde progression sous-action:', { sousActionId, value });
    
    fetch(`/api/sous-actions/${sousActionId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            taux_avancement: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ [DEBUG] Progression sauvegardée avec succès');
        } else {
            console.error('❌ [DEBUG] Erreur lors de la sauvegarde:', data.message);
        }
    })
    .catch(error => {
        console.error('💥 [DEBUG] Erreur lors de la sauvegarde:', error);
    });
}

function updateActionPercentage(actionId) {
    const action = trouverAction(actionId);
    if (!action) return;
    
    const sousActions = action.sous_actions || [];
    const newTaux = Math.round(sousActions.reduce((sum, sa) => sum + (sa.taux_avancement || 0), 0) / sousActions.length);
    
    // Mettre à jour l'affichage de l'action
    document.getElementById(`action-${action.id}-taux`).textContent = `${newTaux}%`;
    action.taux_avancement = newTaux;
    
    // Mettre à jour l'objectif spécifique parent
    updateObjectifSpecifiquePercentage(action.objectif_specifique_id);
}

function updateObjectifSpecifiquePercentage(objectifSpecifiqueId) {
    const objectifSpecifique = trouverObjectifSpecifique(objectifSpecifiqueId);
    if (!objectifSpecifique) return;
    
    const actions = objectifSpecifique.actions || [];
    const newTaux = Math.round(actions.reduce((sum, action) => sum + (action.taux_avancement || 0), 0) / actions.length);
    
    // Mettre à jour l'affichage
    document.getElementById(`ospec-${objectifSpecifique.id}-taux`).textContent = `${newTaux}%`;
    objectifSpecifique.taux_avancement = newTaux;
    
    // Mettre à jour l'objectif stratégique parent
    updateObjectifStrategiquePercentage(objectifSpecifique.objectif_strategique_id);
}

function updateObjectifStrategiquePercentage(objectifStrategiqueId) {
    const objectifStrategique = trouverObjectifStrategique(objectifStrategiqueId);
    if (!objectifStrategique) return;
    
    const objectifsSpecifiques = objectifStrategique.objectifs_specifiques || [];
    const newTaux = Math.round(objectifsSpecifiques.reduce((sum, ospec) => sum + (ospec.taux_avancement || 0), 0) / objectifsSpecifiques.length);
    
    // Mettre à jour l'affichage
    document.getElementById(`os-${objectifStrategique.id}-taux`).textContent = `${newTaux}%`;
    objectifStrategique.taux_avancement = newTaux;
    
    // Mettre à jour le pilier parent
    updatePilierPercentage(objectifStrategique.pilier_id);
}

function updatePilierPercentage(pilierId) {
    const pilier = trouverPilier(pilierId);
    if (!pilier) return;
    
    const objectifsStrategiques = pilier.objectifs_strategiques || [];
    const newTaux = Math.round(objectifsStrategiques.reduce((sum, os) => sum + (os.taux_avancement || 0), 0) / objectifsStrategiques.length);
    
    // Mettre à jour l'en-tête du pilier
    const pilierHeader = document.querySelector(`tr.pilier-header td[colspan="13"]`);
    if (pilierHeader) {
        pilierHeader.innerHTML = `<strong>PILIER ${pilier.code} - ${pilier.libelle.toUpperCase()} - ${newTaux}%</strong>`;
    }
    
    pilier.taux_avancement = newTaux;
}

// Fonctions utilitaires pour trouver les éléments dans les données
function trouverSousAction(sousActionId) {
    for (const pilier of vueGeneraleData) {
        for (const os of pilier.objectifs_strategiques || []) {
            for (const ospec of os.objectifs_specifiques || []) {
                for (const action of ospec.actions || []) {
                    for (const sousAction of action.sous_actions || []) {
                        if (sousAction.id == sousActionId) {
                            return sousAction;
                        }
                    }
                }
            }
        }
    }
    return null;
}

function trouverAction(actionId) {
    for (const pilier of vueGeneraleData) {
        for (const os of pilier.objectifs_strategiques || []) {
            for (const ospec of os.objectifs_specifiques || []) {
                for (const action of ospec.actions || []) {
                    if (action.id == actionId) {
                        return action;
                    }
                }
            }
        }
    }
    return null;
}

function trouverObjectifSpecifique(objectifSpecifiqueId) {
    for (const pilier of vueGeneraleData) {
        for (const os of pilier.objectifs_strategiques || []) {
            for (const ospec of os.objectifs_specifiques || []) {
                if (ospec.id == objectifSpecifiqueId) {
                    return ospec;
                }
            }
        }
    }
    return null;
}

function trouverObjectifStrategique(objectifStrategiqueId) {
    for (const pilier of vueGeneraleData) {
        for (const os of pilier.objectifs_strategiques || []) {
            if (os.id == objectifStrategiqueId) {
                return os;
            }
        }
    }
    return null;
}

function trouverPilier(pilierId) {
    for (const pilier of vueGeneraleData) {
        if (pilier.id == pilierId) {
            return pilier;
        }
    }
    return null;
}

function formaterDate(dateString) {
    if (!dateString) return '';
    
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    } catch (error) {
        return '';
    }
}
</script>
@endpush 