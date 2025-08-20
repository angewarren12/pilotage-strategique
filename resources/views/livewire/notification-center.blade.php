<div class="notification-center">
    <!-- Bouton de notification avec badge -->
    <div class="position-relative">
        <button class="btn btn-outline-primary position-relative" wire:click="toggleNotifications" title="Notifications">
            <i class="fas fa-bell"></i>
            @if($unreadCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </button>
    </div>

    <!-- Panneau des notifications -->
    @if($showNotifications)
        <div class="notification-panel position-absolute top-100 end-0 mt-2 bg-white border-0 rounded-3 shadow-lg" style="width: 420px; max-height: 600px; z-index: 1050;">
            <!-- Header du panneau -->
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-gradient-primary text-white rounded-top-3">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-bell me-2"></i>
                    Centre de Notifications
                </h6>
                <div class="d-flex gap-2">
                    @if($unreadCount > 0)
                        <button class="btn btn-sm btn-outline-light" wire:click="markAllAsRead" title="Marquer tout comme lu">
                            <i class="fas fa-check-double me-1"></i>
                            Tout marquer
                        </button>
                    @endif
                    <button class="btn btn-sm btn-outline-light" wire:click="toggleNotifications" title="Fermer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Liste des notifications -->
            <div class="notification-list" style="max-height: 400px; overflow-y: auto;">
                @if(count($notifications) > 0)
                    @foreach($notifications as $notification)
                        <div class="notification-item p-3 border-0 {{ $this->isUnread($notification) ? 'bg-light-blue' : 'bg-white' }} {{ $this->getNotificationColor($notification->priority) }} border-start border-3" 
                             wire:click="showNotificationDetails({{ $notification->id }})"
                             style="cursor: pointer; transition: all 0.2s ease;">
                            <div class="d-flex align-items-start gap-3">
                                <!-- Icône de notification -->
                                <div class="flex-shrink-0">
                                    <div class="notification-icon-wrapper {{ $this->isUnread($notification) ? 'unread' : '' }}">
                                        <i class="{{ $this->getNotificationIcon($notification->type) }} fs-5"></i>
                                    </div>
                                </div>
                                
                                <!-- Contenu de la notification -->
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-1 text-truncate {{ $this->isUnread($notification) ? 'fw-bold text-primary' : 'text-dark' }}">
                                            {{ $notification->title }}
                                        </h6>
                                        <div class="d-flex gap-1">
                                            @if($this->isUnread($notification))
                                                <span class="badge bg-primary rounded-pill px-2 py-1">
                                                    <i class="fas fa-star me-1"></i>Nouveau
                                                </span>
                                            @endif
                                            <button class="btn btn-sm btn-outline-danger rounded-circle" 
                                                    wire:click.stop="deleteNotification({{ $notification->id }})"
                                                    title="Supprimer"
                                                    style="width: 32px; height: 32px;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="mb-2 text-muted small lh-sm">{{ Str::limit($notification->message, 120) }}</p>
                                    <div class="d-flex align-items-center gap-2">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                        </small>
                                        @if($notification->priority !== 'normal')
                                            <span class="badge bg-{{ $notification->priority === 'urgent' ? 'danger' : ($notification->priority === 'high' ? 'warning' : 'info') }} rounded-pill px-2 py-1">
                                                {{ ucfirst($notification->priority) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <div class="notification-empty-state">
                            <i class="fas fa-bell-slash text-muted fs-1 mb-3"></i>
                            <h6 class="text-muted mb-2">Aucune notification</h6>
                            <p class="text-muted small mb-0">Vous êtes à jour !</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer du panneau -->
            @if(count($notifications) > 0)
                <div class="p-3 border-top bg-light rounded-bottom-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted fw-medium">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ $unreadCount }} notification(s) non lue(s)
                        </small>
                        <a href="#" class="text-decoration-none small text-primary fw-medium">
                            <i class="fas fa-external-link-alt me-1"></i>
                            Voir toutes
                        </a>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Modal de détails de notification -->
    @if($showNotificationDetails && $selectedNotification)
        <div class="modal fade show" style="display: block; z-index: 1060;" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="d-flex align-items-center gap-3">
                            <i class="{{ $this->getNotificationIcon($selectedNotification->type) }} fs-4"></i>
                                                            <div>
                                    <h5 class="modal-title mb-0">{{ $selectedNotification->title }}</h5>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($selectedNotification->created_at)->diffForHumans() }}</small>
                                </div>
                        </div>
                        <button type="button" class="btn-close" wire:click="closeNotificationDetails"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="notification-details">
                            <p class="mb-3">{{ $selectedNotification->message }}</p>
                            
                            @if($selectedNotification->data)
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Détails supplémentaires</h6>
                                    </div>
                                    <div class="card-body">
                                        <pre class="mb-0"><code>{{ json_encode($selectedNotification->data, JSON_PRETTY_PRINT) }}</code></pre>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeNotificationDetails">
                            Fermer
                        </button>
                        @if($this->isUnread($selectedNotification))
                            <button type="button" class="btn btn-primary" wire:click="markAsRead({{ $selectedNotification->id }})">
                                Marquer comme lu
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show" style="z-index: 1055;"></div>
    @endif

    <style>
    .notification-center {
        position: relative;
    }

    .notification-panel {
        animation: slideDown 0.3s ease-out;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }

    .bg-light-blue {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .notification-item {
        transition: all 0.3s ease;
        border-radius: 8px;
        margin: 4px 8px;
        border: 1px solid transparent;
    }

    .notification-item:hover {
        background-color: rgba(0, 123, 255, 0.08) !important;
        transform: translateX(4px);
        border-color: rgba(0, 123, 255, 0.2);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.1);
    }

    .notification-icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(0, 123, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .notification-icon-wrapper.unread {
        background: rgba(0, 123, 255, 0.2);
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .notification-item:hover .notification-icon-wrapper {
        background: rgba(0, 123, 255, 0.2);
        transform: scale(1.1);
    }

    .notification-empty-state {
        padding: 2rem;
    }

    .notification-empty-state i {
        opacity: 0.6;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .notification-list::-webkit-scrollbar {
        width: 8px;
    }

    .notification-list::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 4px;
    }

    .notification-list::-webkit-scrollbar-thumb {
        background: #dee2e6;
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .notification-list::-webkit-scrollbar-thumb:hover {
        background: #adb5bd;
    }

    .badge.rounded-pill {
        font-size: 0.75rem;
        font-weight: 500;
    }

    .btn.rounded-circle {
        transition: all 0.2s ease;
    }

    .btn.rounded-circle:hover {
        transform: scale(1.1);
    }
    </style>

    <script>
    // Fermer le panneau de notifications en cliquant à l'extérieur
    document.addEventListener('click', function(event) {
        const notificationCenter = document.querySelector('.notification-center');
        const notificationPanel = document.querySelector('.notification-panel');
        
        if (notificationPanel && !notificationCenter.contains(event.target)) {
            @this.toggleNotifications();
        }
    });

    // Actualiser les notifications toutes les 30 secondes
    setInterval(function() {
        @this.loadNotifications();
    }, 30000);
    </script>
</div>
