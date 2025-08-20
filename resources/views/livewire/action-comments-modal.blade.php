<div>
    @if($showModal)
    <div class="modal fade show" style="display: block; z-index: 10000;" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <!-- Header du Modal -->
                <div class="modal-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-comments fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0">
                                <strong>Discussion sur l'Action</strong>
                            </h5>
                            @if($action)
                                <small class="text-white-75">
                                    {{ $action->code }} - {{ $action->libelle }}
                                </small>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>

                <!-- Contenu du Modal -->
                <div class="modal-body p-0">
                    @if($isLoading)
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            <p class="mt-2">Chargement des commentaires...</p>
                        </div>
                    @else
                        <div class="row h-100">
                            <!-- Colonne de gauche : Hiérarchie des Owners -->
                            <div class="col-md-4 border-end">
                                <div class="p-3">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-users me-2"></i>
                                        Participants à la Discussion
                                    </h6>
                                    
                                    <div class="hierarchy-owners">
                                        @foreach($hierarchyOwners as $owner)
                                            <div class="owner-card mb-3 p-3 border rounded" 
                                                 style="border-left: 4px solid var(--bs-{{ $owner['color'] }}) !important;">
                                                                                                 <div class="d-flex align-items-center mb-2">
                                                     <div class="owner-avatar me-2">
                                                         <div class="avatar-circle bg-{{ $owner['color'] }}">
                                                             @if(isset($owner['is_admin']) && $owner['is_admin'])
                                                                 <i class="fas fa-crown"></i>
                                                             @else
                                                                 {{ strtoupper(substr($owner['user']->name ?? 'U', 0, 1)) }}
                                                             @endif
                                                         </div>
                                                     </div>
                                                     <div class="flex-grow-1">
                                                         <div class="fw-bold text-{{ $owner['color'] }}">
                                                             {{ $owner['user']->name ?? 'Non assigné' }}
                                                             @if(isset($owner['is_admin']) && $owner['is_admin'])
                                                                 <i class="fas fa-crown text-warning ms-1" title="Administrateur Général"></i>
                                                             @endif
                                                         </div>
                                                         <small class="text-muted">{{ $owner['level'] }}</small>
                                                     </div>
                                                     @if($owner['user'] && $owner['user']->id === auth()->id())
                                                         <span class="badge bg-success">
                                                             <i class="fas fa-user-check"></i> Vous
                                                         </span>
                                                     @endif
                                                 </div>
                                                <div class="owner-details">
                                                    <div class="small text-muted mb-1">
                                                        <strong>{{ $owner['code'] }}</strong>
                                                    </div>
                                                    <div class="small">
                                                        {{ Str::limit($owner['name'], 50) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Statistiques -->
                                    <div class="mt-4 p-3 bg-light rounded">
                                        <h6 class="text-muted mb-2">
                                            <i class="fas fa-chart-bar me-2"></i>
                                            Statistiques
                                        </h6>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="h5 mb-0 text-primary">{{ count($comments) }}</div>
                                                <small class="text-muted">Commentaires</small>
                                            </div>
                                            <div class="col-6">
                                                <div class="h5 mb-0 text-success">{{ count($hierarchyOwners) }}</div>
                                                <small class="text-muted">Participants</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Colonne de droite : Zone de Discussion -->
                            <div class="col-md-8">
                                <div class="d-flex flex-column h-100">
                                    <!-- Zone des commentaires -->
                                    <div class="flex-grow-1 p-3" style="max-height: 60vh; overflow-y: auto;">
                                        <div class="comments-container">
                                            @if(count($comments) > 0)
                                                @foreach($comments as $comment)
                                                    <div class="comment-item mb-3 {{ $comment['is_own_comment'] ? 'own-comment' : '' }}">
                                                        <div class="comment-header d-flex align-items-center mb-2">
                                                            <div class="comment-avatar me-2">
                                                                <div class="avatar-circle bg-{{ $comment['is_own_comment'] ? 'success' : 'secondary' }}">
                                                                    {{ strtoupper(substr($comment['user_name'], 0, 1)) }}
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="fw-bold">
                                                                    {{ $comment['user_name'] }}
                                                                    @if($comment['is_own_comment'])
                                                                        <span class="badge bg-success ms-2">Vous</span>
                                                                    @endif
                                                                </div>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-clock me-1"></i>
                                                                    {{ $comment['created_at'] }}
                                                                </small>
                                                            </div>
                                                            @if($comment['is_own_comment'])
                                                                <div class="comment-actions">
                                                                    <button type="button" 
                                                                            class="btn btn-sm btn-outline-danger"
                                                                            wire:click="deleteComment({{ $comment['id'] }})"
                                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="comment-content p-3 bg-light rounded">
                                                            {{ $comment['content'] }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="fas fa-comments fa-3x mb-3"></i>
                                                        <h6>Aucun commentaire pour le moment</h6>
                                                        <p>Soyez le premier à commenter cette action !</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Zone de saisie du commentaire -->
                                    @if($this->canComment())
                                        <div class="p-3 border-top bg-light">
                                            <div class="comment-form">
                                                <div class="d-flex align-items-start">
                                                    <div class="comment-avatar me-3">
                                                        <div class="avatar-circle bg-success">
                                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="form-group">
                                                            <textarea 
                                                                wire:model="newComment"
                                                                class="form-control"
                                                                rows="3"
                                                                placeholder="Ajouter un commentaire..."
                                                                maxlength="1000"
                                                                wire:keydown.enter.prevent="addComment"></textarea>
                                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                                <small class="text-muted">
                                                                    {{ strlen($newComment) }}/1000 caractères
                                                                </small>
                                                                                                                                 <button type="button" 
                                                                         class="btn btn-primary btn-sm"
                                                                         wire:click="addComment"
                                                                         wire:loading.attr="disabled"
                                                                         {{ (strlen($newComment) < 3 && !auth()->user()->role || auth()->user()->role->nom !== 'admin_general') ? 'disabled' : '' }}>
                                                                    <span wire:loading.remove>
                                                                        <i class="fas fa-paper-plane me-1"></i>
                                                                        Envoyer
                                                                    </span>
                                                                    <span wire:loading>
                                                                        <i class="fas fa-spinner fa-spin me-1"></i>
                                                                        Envoi...
                                                                    </span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-3 border-top bg-light text-center">
                                            <div class="text-muted">
                                                <i class="fas fa-lock me-2"></i>
                                                Seuls les participants à cette action peuvent commenter
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer du Modal -->
                <div class="modal-footer">
                    <div class="d-flex justify-content-between w-100">
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ count($comments) }} commentaire(s) | 
                                {{ count($hierarchyOwners) }} participant(s)
                            </small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">
                                <i class="fas fa-times me-1"></i>
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif

    <style>
        /* Styles pour les avatars */
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        /* Styles pour les cartes d'owners */
        .owner-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .owner-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Styles pour les commentaires */
        .comment-item {
            transition: all 0.3s ease;
        }

        .comment-item:hover {
            transform: translateX(2px);
        }

        .own-comment {
            margin-left: 20px;
        }

        .own-comment .comment-content {
            background-color: #e8f5e8 !important;
            border-left: 4px solid #28a745;
        }

        .comment-content {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Animation pour les nouveaux commentaires */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .comment-item {
            animation: slideInRight 0.3s ease;
        }

        /* Styles pour la hiérarchie */
        .hierarchy-owners {
            max-height: 50vh;
            overflow-y: auto;
        }

        /* Styles pour la zone de commentaires */
        .comments-container {
            min-height: 200px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 0;
                max-width: 100%;
                height: 100%;
            }
            
            .modal-content {
                height: 100%;
                border-radius: 0;
            }
        }
    </style>

    <script>
        document.addEventListener('livewire:init', () => {
            // Auto-scroll vers le bas quand un nouveau commentaire est ajouté
            Livewire.on('commentAdded', () => {
                const commentsContainer = document.querySelector('.comments-container');
                if (commentsContainer) {
                    commentsContainer.scrollTop = commentsContainer.scrollHeight;
                }
            });
        });

        // Auto-resize du textarea
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.querySelector('textarea[wire\\:model="newComment"]');
            if (textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 150) + 'px';
                });
            }
        });
    </script>
</div>
