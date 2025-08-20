<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Action;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActionCommentsModal extends Component
{
    public $showModal = false;
    public $action = null;
    public $newComment = '';
    public $comments = [];
    public $hierarchyOwners = [];
    public $isLoading = false;

    protected $listeners = [
        'openActionCommentsModal' => 'openModal',
        'refreshComments' => 'loadComments'
    ];

    public function mount()
    {
        $this->resetState();
    }

    public function openModal($actionId)
    {
        try {
            $this->isLoading = true;
            
            // Charger l'action avec toutes les relations nécessaires
            $this->action = Action::with([
                'objectifSpecifique.objectifStrategique.pilier.owner',
                'objectifSpecifique.owner',
                'objectifSpecifique.objectifStrategique.owner',
                'owner',
                'sousActions.owner',
                'comments.user'
            ])->findOrFail($actionId);

            // Vérification de la hiérarchie
            if (
                !$this->action->objectifSpecifique ||
                !$this->action->objectifSpecifique->objectifStrategique ||
                !$this->action->objectifSpecifique->objectifStrategique->pilier
            ) {
                throw new \Exception('Hiérarchie incomplète pour cette action. Veuillez vérifier que chaque action est bien liée à un objectif spécifique, un objectif stratégique et un pilier.');
            }

            // Construire la hiérarchie des owners
            $this->buildHierarchyOwners();
            
            // Charger les commentaires
            $this->loadComments();
            
            $this->showModal = true;
            
            Log::info('Modal de commentaires ouvert pour l\'action', [
                'action_id' => $actionId,
                'action_code' => $this->action->code
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ouverture du modal de commentaires', [
                'action_id' => $actionId,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('showToast', (object)[
                'type' => 'error',
                'message' => $e->getMessage() ?: 'Erreur lors du chargement des commentaires'
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetState();
    }

    public function resetState()
    {
        $this->action = null;
        $this->newComment = '';
        $this->comments = [];
        $this->hierarchyOwners = [];
        $this->isLoading = false;
    }

    public function buildHierarchyOwners()
    {
        if (!$this->action) return;

        $this->hierarchyOwners = [];

        // Pilier (Great-grandparent)
        if (
            $this->action->objectifSpecifique &&
            $this->action->objectifSpecifique->objectifStrategique &&
            $this->action->objectifSpecifique->objectifStrategique->pilier &&
            $this->action->objectifSpecifique->objectifStrategique->pilier->owner
        ) {
            $this->hierarchyOwners[] = [
                'level' => 'Pilier',
                'code' => $this->action->objectifSpecifique->objectifStrategique->pilier->code,
                'name' => $this->action->objectifSpecifique->objectifStrategique->pilier->libelle,
                'user' => $this->action->objectifSpecifique->objectifStrategique->pilier->owner,
                'color' => 'primary'
            ];
        }

        // Objectif Stratégique (Grandparent)
        if (
            $this->action->objectifSpecifique &&
            $this->action->objectifSpecifique->objectifStrategique &&
            $this->action->objectifSpecifique->objectifStrategique->owner
        ) {
            $this->hierarchyOwners[] = [
                'level' => 'Objectif Stratégique',
                'code' => $this->action->objectifSpecifique->objectifStrategique->code,
                'name' => $this->action->objectifSpecifique->objectifStrategique->libelle,
                'user' => $this->action->objectifSpecifique->objectifStrategique->owner,
                'color' => 'info'
            ];
        }

        // Objectif Spécifique (Parent)
        if (
            $this->action->objectifSpecifique &&
            $this->action->objectifSpecifique->owner
        ) {
            $this->hierarchyOwners[] = [
                'level' => 'Objectif Spécifique',
                'code' => $this->action->objectifSpecifique->code,
                'name' => $this->action->objectifSpecifique->libelle,
                'user' => $this->action->objectifSpecifique->owner,
                'color' => 'warning'
            ];
        }

        // Action (Current)
        if ($this->action->owner) {
            $this->hierarchyOwners[] = [
                'level' => 'Action',
                'code' => $this->action->code,
                'name' => $this->action->libelle,
                'user' => $this->action->owner,
                'color' => 'success'
            ];
        }

        // Sous-actions (Children) - uniquement les owners uniques
        $sousActionOwners = [];
        foreach ($this->action->sousActions as $sousAction) {
            if ($sousAction->owner && !in_array($sousAction->owner->id, array_column($sousActionOwners, 'user_id'))) {
                $sousActionOwners[] = [
                    'level' => 'Sous-Action',
                    'code' => $sousAction->code,
                    'name' => $sousAction->libelle,
                    'user' => $sousAction->owner,
                    'color' => 'secondary',
                    'user_id' => $sousAction->owner->id
                ];
            }
        }

        $this->hierarchyOwners = array_merge($this->hierarchyOwners, $sousActionOwners);

        // Ajouter l'admin général automatiquement (s'il n'est pas déjà présent)
        $adminGeneral = $this->getAdminGeneral();
        if ($adminGeneral && !$this->isUserInHierarchy($adminGeneral->id)) {
            $this->hierarchyOwners[] = [
                'level' => 'Administrateur Général',
                'code' => 'ADMIN',
                'name' => 'Supervision globale',
                'user' => $adminGeneral,
                'color' => 'danger',
                'is_admin' => true
            ];
        }
    }

    /**
     * Récupère l'admin général
     */
    private function getAdminGeneral()
    {
        return User::whereHas('role', function($query) {
            $query->where('nom', 'admin_general');
        })->first();
    }

    /**
     * Vérifie si un utilisateur est déjà dans la hiérarchie
     */
    private function isUserInHierarchy($userId)
    {
        foreach ($this->hierarchyOwners as $owner) {
            if ($owner['user'] && $owner['user']->id === $userId) {
                return true;
            }
        }
        return false;
    }

    public function loadComments()
    {
        if (!$this->action) return;

        $this->comments = $this->action->commentsLatest()
            ->with('user')
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user_name' => $comment->user->name,
                    'user_id' => $comment->user->id,
                    'created_at' => $comment->formatted_created_at,
                    'is_own_comment' => $comment->user_id === Auth::id()
                ];
            });
    }

    public function addComment()
    {
        $currentUser = Auth::user();
        $minLength = ($currentUser && $currentUser->role && $currentUser->role->nom === 'admin_general') ? 1 : 3;
        
        $this->validate([
            'newComment' => "required|string|min:$minLength|max:1000"
        ]);

        try {
            $comment = Comment::create([
                'content' => trim($this->newComment),
                'user_id' => Auth::id(),
                'action_id' => $this->action->id
            ]);

            // Recharger les commentaires
            $this->loadComments();
            
            // Vider le champ de commentaire
            $this->newComment = '';

            Log::info('Nouveau commentaire ajouté', [
                'comment_id' => $comment->id,
                'action_id' => $this->action->id,
                'user_id' => Auth::id()
            ]);

            $this->dispatch('showToast', (object)[
                'type' => 'success',
                'message' => 'Commentaire ajouté avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout du commentaire', [
                'error' => $e->getMessage(),
                'action_id' => $this->action->id,
                'user_id' => Auth::id()
            ]);

            $this->dispatch('showToast', (object)[
                'type' => 'error',
                'message' => 'Erreur lors de l\'ajout du commentaire'
            ]);
        }
    }

    public function deleteComment($commentId)
    {
        try {
            $comment = Comment::where('id', $commentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $comment->delete();

            // Recharger les commentaires
            $this->loadComments();

            Log::info('Commentaire supprimé', [
                'comment_id' => $commentId,
                'user_id' => Auth::id()
            ]);

            $this->dispatch('showToast', (object)[
                'type' => 'success',
                'message' => 'Commentaire supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du commentaire', [
                'error' => $e->getMessage(),
                'comment_id' => $commentId,
                'user_id' => Auth::id()
            ]);

            $this->dispatch('showToast', (object)[
                'type' => 'error',
                'message' => 'Erreur lors de la suppression du commentaire'
            ]);
        }
    }

    public function getCurrentUserProperty()
    {
        return Auth::user();
    }

    public function canComment()
    {
        if (!$this->action || !Auth::check()) return false;

        $currentUser = Auth::user();

        // L'admin général peut toujours commenter
        if ($currentUser && $currentUser->role && $currentUser->role->nom === 'admin_general') {
            return true;
        }

        // Vérifier si l'utilisateur actuel est un owner dans la hiérarchie
        $currentUserId = $currentUser->id;
        
        // Vérifier directement les relations owner
        if ($this->action->owner && $this->action->owner->id === $currentUserId) return true;
        if ($this->action->objectifSpecifique && $this->action->objectifSpecifique->owner && $this->action->objectifSpecifique->owner->id === $currentUserId) return true;
        if ($this->action->objectifSpecifique && $this->action->objectifSpecifique->objectifStrategique && $this->action->objectifSpecifique->objectifStrategique->owner && $this->action->objectifSpecifique->objectifStrategique->owner->id === $currentUserId) return true;
        if ($this->action->objectifSpecifique && $this->action->objectifSpecifique->objectifStrategique && $this->action->objectifSpecifique->objectifStrategique->pilier && $this->action->objectifSpecifique->objectifStrategique->pilier->owner && $this->action->objectifSpecifique->objectifStrategique->pilier->owner->id === $currentUserId) return true;
        
        // Vérifier les sous-actions
        foreach ($this->action->sousActions as $sousAction) {
            if ($sousAction->owner && $sousAction->owner->id === $currentUserId) return true;
        }

        return false;
    }

    public function render()
    {
        return view('livewire.action-comments-modal');
    }
}
