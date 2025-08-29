<div>
    <!-- Modal principale -->
    <div class="modal fade" id="pilier-hierarchique-v2-modal" tabindex="-1" aria-labelledby="pilier-hierarchique-v2-modal-label" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <!-- Header de la modal -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="pilier-hierarchique-v2-modal-label">
                        <i class="fas fa-sitemap me-2"></i>
                        Vue Hiérarchique du Pilier
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <!-- Body de la modal -->
                <div class="modal-body p-0">
                    <!-- Breadcrumb de navigation -->
                    @include('livewire.pilier-hierarchique-v2.partials.breadcrumb')

                    <!-- Contenu principal selon la vue actuelle -->
                    @if($currentView === 'pilier' && $pilier)
                        @include('livewire.pilier-hierarchique-v2.components.pilier-detail', [
                            'pilier' => $pilier,
                            'canCreateObjectifStrategique' => $canCreateOS(),
                            'canEditObjectifStrategique' => $canEditOS,
                            'canDeleteObjectifStrategique' => $canDeleteOS,
                            'canCreateObjectifSpecifique' => $canCreateOSP(),
                            'canEditObjectifSpecifique' => $canEditOSP,
                            'canDeleteObjectifSpecifique' => $canDeleteOSP
                        ])
                    @elseif($currentView === 'objectifStrategique')
                        @include('livewire.pilier-hierarchique-v2.components.objectif-strategique-list', [
                            'pilier' => $pilier,
                            'selectedObjectifStrategique' => $selectedObjectifStrategique,
                            'canCreateObjectifSpecifique' => $canCreateOSP(),
                            'canEditObjectifSpecifique' => $canEditOSP,
                            'canDeleteObjectifSpecifique' => $canDeleteOSP,
                            'canCreateAction' => $canCreateAction(),
                            'canEditAction' => $canEditAction,
                            'canDeleteAction' => $canDeleteAction
                        ])
                    @elseif($currentView === 'objectifSpecifique')
                        @include('livewire.pilier-hierarchique-v2.components.objectif-specifique-list', [
                            'pilier' => $pilier,
                            'selectedObjectifStrategique' => $selectedObjectifStrategique,
                            'selectedObjectifSpecifique' => $selectedObjectifSpecifique,
                            'canCreateAction' => $canCreateAction(),
                            'canEditAction' => $canEditAction,
                            'canDeleteAction' => $canDeleteAction,
                            'canCreateSousAction' => $canCreateSousAction(),
                            'canEditSousAction' => $canEditSousAction,
                            'canDeleteSousAction' => $canDeleteSousAction
                        ])
                    @elseif($currentView === 'action')
                        @include('livewire.pilier-hierarchique-v2.components.action-list', [
                            'pilier' => $pilier,
                            'selectedObjectifStrategique' => $selectedObjectifStrategique,
                            'selectedObjectifSpecifique' => $selectedObjectifSpecifique,
                            'selectedAction' => $selectedAction,
                            'canCreateSousAction' => $canCreateSousAction(),
                            'canEditSousAction' => $canEditSousAction,
                            'canDeleteSousAction' => $canDeleteSousAction
                        ])
                    @elseif($currentView === 'sousAction')
                        @include('livewire.pilier-hierarchique-v2.components.sous-action-list')
                    @endif
                </div>

                <!-- Footer de la modal -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tous les modals sont maintenant centralisés dans modals.blade.php -->

    <!-- CSS pour le slider de progression -->
    <link rel="stylesheet" href="{{ asset('css/progress-slider.css') }}">
    
    <!-- CSS pour la progression circulaire améliorée -->
    <link rel="stylesheet" href="{{ asset('css/progress-circle.css') }}?v={{ time() }}">
    
    <!-- CSS pour le modal des activités -->
    <link rel="stylesheet" href="{{ asset('css/activities-modal.css') }}">

    <!-- Scripts JavaScript -->
    <script>
        // Écouter les événements Livewire pour ouvrir/fermer la modal
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-modal', (modalId) => {
                const modal = new bootstrap.Modal(document.getElementById(modalId));
                modal.show();
            });

            Livewire.on('close-modal', (modalId) => {
                const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                if (modal) {
                    modal.hide();
                }
            });

            // Gestion des sliders de progression
            Livewire.on('progress-updated', (data) => {
                console.log('🎯 Événement progress-updated reçu:', data);
                try {
                    // Mettre à jour visuellement les cercles de progression
                    updateProgressCircles(data);
                    console.log('✅ Cercles de progression mis à jour');
                } catch (error) {
                    console.error('❌ Erreur lors de la mise à jour des cercles:', error);
                }
            });

            // Log des événements wire:change
            document.addEventListener('change', function(event) {
                if (event.target.classList.contains('progress-slider')) {
                    console.log('🎯 Slider changé:', event.target.value);
                }
            });

            // Log des événements wire:change Livewire
            Livewire.on('change', (data) => {
                console.log('🔄 Événement change Livewire:', data);
            });

            // Gestion des erreurs Livewire
            Livewire.on('error', (error) => {
                console.error('🚨 Erreur Livewire détectée:', error);
            });

            // Gestion des événements de chargement
            Livewire.on('loading', () => {
                console.log('⏳ Chargement en cours...');
            });

            // Gestion des événements de fin de chargement
            Livewire.on('loaded', () => {
                console.log('✅ Chargement terminé');
            });
        });

        // Fonction pour mettre à jour les cercles de progression
        function updateProgressCircles(data) {
            console.log('🔄 Début de updateProgressCircles avec données:', data);
            
            try {
                // Mettre à jour le cercle de l'action
                if (data.action_progress !== undefined) {
                    console.log('🔄 Mise à jour cercle Action:', data.action_progress);
                    const actionCircle = document.querySelector('.action-info .progress-ring circle:last-child');
                    if (actionCircle) {
                        const circumference = 2 * Math.PI * 35;
                        const offset = circumference - (circumference * data.action_progress / 100);
                        actionCircle.style.strokeDashoffset = offset;
                        console.log('✅ Cercle Action mis à jour');
                    } else {
                        console.warn('⚠️ Cercle Action non trouvé');
                    }
                }

                // Mettre à jour les cercles des cartes parentes
                if (data.osp_progress !== undefined) {
                    console.log('🔄 Mise à jour cercle OSP:', data.osp_progress);
                    const ospCircle = document.querySelector('.statistics-container .col-xl-4:nth-child(3) .progress-ring circle:last-child');
                    if (ospCircle) {
                        const circumference = 2 * Math.PI * 15;
                        const offset = circumference - (circumference * data.osp_progress / 100);
                        ospCircle.style.strokeDashoffset = offset;
                        console.log('✅ Cercle OSP mis à jour');
                    } else {
                        console.warn('⚠️ Cercle OSP non trouvé');
                    }
                }

                if (data.os_progress !== undefined) {
                    console.log('🔄 Mise à jour cercle OS:', data.os_progress);
                    const osCircle = document.querySelector('.statistics-container .col-xl-4:nth-child(2) .progress-ring circle:last-child');
                    if (osCircle) {
                        const circumference = 2 * Math.PI * 15;
                        const offset = circumference - (circumference * data.os_progress / 100);
                        osCircle.style.strokeDashoffset = offset;
                        console.log('✅ Cercle OS mis à jour');
                    } else {
                        console.warn('⚠️ Cercle OS non trouvé');
                    }
                }

                if (data.pilier_progress !== undefined) {
                    console.log('🔄 Mise à jour cercle Pilier:', data.pilier_progress);
                    const pilierCircle = document.querySelector('.statistics-container .col-xl-4:nth-child(1) .progress-ring circle:last-child');
                    if (pilierCircle) {
                        const circumference = 2 * Math.PI * 15;
                        const offset = circumference - (circumference * data.pilier_progress / 100);
                        pilierCircle.style.strokeDashoffset = offset;
                        console.log('✅ Cercle Pilier mis à jour');
                    } else {
                        console.warn('⚠️ Cercle Pilier non trouvé');
                    }
                }
                
                console.log('✅ Tous les cercles mis à jour avec succès');
            } catch (error) {
                console.error('❌ Erreur dans updateProgressCircles:', error);
                console.error('Stack trace:', error.stack);
            }
        }
    </script>
</div>
