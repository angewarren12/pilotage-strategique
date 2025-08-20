<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Pilier;
use App\Models\ObjectifStrategique;
use App\Models\ObjectifSpecifique;
use App\Models\Action;
use App\Models\SousAction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Créer une notification de changement d'avancement
     */
    public function notifyAvancementChange($elementType, $elementId, $oldValue, $newValue, $element = null)
    {
        try {
            // Récupérer les utilisateurs concernés selon le type d'élément
            $users = $this->getUsersForElement($elementType, $elementId, $element);
            
            foreach ($users as $user) {
                Notification::createAvancementChange(
                    $user->id,
                    $elementType,
                    $elementId,
                    $oldValue,
                    $newValue
                );
            }
            
            Log::info("Notification d'avancement créée pour {$elementType} #{$elementId}");
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de notification d'avancement: " . $e->getMessage());
        }
    }

    /**
     * Créer une notification d'échéance approchante
     */
    public function notifyEcheanceApproche($elementType, $elementId, $daysLeft, $element = null)
    {
        try {
            $users = $this->getUsersForElement($elementType, $elementId, $element);
            
            foreach ($users as $user) {
                Notification::createEcheanceApproche(
                    $user->id,
                    $elementType,
                    $elementId,
                    $daysLeft
                );
            }
            
            Log::info("Notification d'échéance approchante créée pour {$elementType} #{$elementId}");
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de notification d'échéance: " . $e->getMessage());
        }
    }

    /**
     * Créer une notification de délai dépassé
     */
    public function notifyDelaiDepasse($elementType, $elementId, $daysLate, $element = null)
    {
        try {
            $users = $this->getUsersForElement($elementType, $elementId, $element);
            
            foreach ($users as $user) {
                Notification::createDelaiDepasse(
                    $user->id,
                    $elementType,
                    $elementId,
                    $daysLate
                );
            }
            
            Log::info("Notification de délai dépassé créée pour {$elementType} #{$elementId}");
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de notification de délai: " . $e->getMessage());
        }
    }

    /**
     * Créer une notification de nouveau commentaire
     */
    public function notifyCommentNew($elementType, $elementId, $commentAuthor, $element = null)
    {
        try {
            $users = $this->getUsersForElement($elementType, $elementId, $element);
            
            foreach ($users as $user) {
                // Ne pas notifier l'auteur du commentaire
                if ($user->id !== $commentAuthor) {
                    Notification::createCommentNew(
                        $user->id,
                        $elementType,
                        $elementId,
                        $commentAuthor
                    );
                }
            }
            
            Log::info("Notification de commentaire créée pour {$elementType} #{$elementId}");
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de notification de commentaire: " . $e->getMessage());
        }
    }

    /**
     * Créer une notification de validation requise
     */
    public function notifyValidationRequired($elementType, $elementId, $requestedBy, $element = null)
    {
        try {
            $users = $this->getUsersForValidation($elementType, $elementId, $element);
            
            foreach ($users as $user) {
                Notification::createValidationRequired(
                    $user->id,
                    $elementType,
                    $elementId,
                    $requestedBy
                );
            }
            
            Log::info("Notification de validation requise créée pour {$elementType} #{$elementId}");
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de notification de validation: " . $e->getMessage());
        }
    }

    /**
     * Récupérer les utilisateurs concernés par un élément
     */
    private function getUsersForElement($elementType, $elementId, $element = null)
    {
        $users = collect();
        
        if (!$element) {
            $element = $this->getElement($elementType, $elementId);
        }
        
        if (!$element) {
            return $users;
        }

        switch ($elementType) {
            case 'pilier':
                // Owner du pilier + admin général
                if ($element->owner) {
                    $users->push($element->owner);
                }
                $users = $users->merge(User::where('role', 'admin_general')->get());
                break;
                
            case 'objectif_strategique':
                // Owner de l'OS + owner du pilier + admin général
                if ($element->owner) {
                    $users->push($element->owner);
                }
                if ($element->pilier && $element->pilier->owner) {
                    $users->push($element->pilier->owner);
                }
                $users = $users->merge(User::where('role', 'admin_general')->get());
                break;
                
            case 'objectif_specifique':
                // Owner de l'OSpec + owner de l'OS + owner du pilier + admin général
                if ($element->owner) {
                    $users->push($element->owner);
                }
                if ($element->objectifStrategique && $element->objectifStrategique->owner) {
                    $users->push($element->objectifStrategique->owner);
                }
                if ($element->objectifStrategique && $element->objectifStrategique->pilier && $element->objectifStrategique->pilier->owner) {
                    $users->push($element->objectifStrategique->pilier->owner);
                }
                $users = $users->merge(User::where('role', 'admin_general')->get());
                break;
                
            case 'action':
                // Owner de l'action + owner de l'OSpec + owner de l'OS + owner du pilier + admin général
                if ($element->owner) {
                    $users->push($element->owner);
                }
                if ($element->objectifSpecifique && $element->objectifSpecifique->owner) {
                    $users->push($element->objectifSpecifique->owner);
                }
                if ($element->objectifSpecifique && $element->objectifSpecifique->objectifStrategique && $element->objectifSpecifique->objectifStrategique->owner) {
                    $users->push($element->objectifSpecifique->objectifStrategique->owner);
                }
                if ($element->objectifSpecifique && $element->objectifSpecifique->objectifStrategique && $element->objectifSpecifique->objectifStrategique->pilier && $element->objectifSpecifique->objectifStrategique->pilier->owner) {
                    $users->push($element->objectifSpecifique->objectifStrategique->pilier->owner);
                }
                $users = $users->merge(User::where('role', 'admin_general')->get());
                break;
                
            case 'sous_action':
                // Owner de la sous-action + owner de l'action + owner de l'OSpec + owner de l'OS + owner du pilier + admin général
                if ($element->owner) {
                    $users->push($element->owner);
                }
                if ($element->action && $element->action->owner) {
                    $users->push($element->action->owner);
                }
                if ($element->action && $element->action->objectifSpecifique && $element->action->objectifSpecifique->owner) {
                    $users->push($element->action->objectifSpecifique->owner);
                }
                if ($element->action && $element->action->objectifSpecifique && $element->action->objectifSpecifique->objectifStrategique && $element->action->objectifSpecifique->objectifStrategique->owner) {
                    $users->push($element->action->objectifSpecifique->objectifStrategique->owner);
                }
                if ($element->action && $element->action->objectifSpecifique && $element->action->objectifSpecifique->objectifStrategique && $element->action->objectifSpecifique->objectifStrategique->pilier && $element->action->objectifSpecifique->objectifStrategique->pilier->owner) {
                    $users->push($element->action->objectifSpecifique->objectifStrategique->pilier->owner);
                }
                $users = $users->merge(User::where('role', 'admin_general')->get());
                break;
        }
        
        return $users->unique('id');
    }

    /**
     * Récupérer les utilisateurs pour validation
     */
    private function getUsersForValidation($elementType, $elementId, $element = null)
    {
        // Pour la validation, on notifie les niveaux supérieurs
        return $this->getUsersForElement($elementType, $elementId, $element);
    }

    /**
     * Récupérer un élément par type et ID
     */
    private function getElement($elementType, $elementId)
    {
        return match($elementType) {
            'pilier' => Pilier::find($elementId),
            'objectif_strategique' => ObjectifStrategique::with(['pilier.owner'])->find($elementId),
            'objectif_specifique' => ObjectifSpecifique::with(['objectifStrategique.pilier.owner', 'objectifStrategique.owner'])->find($elementId),
            'action' => Action::with(['objectifSpecifique.objectifStrategique.pilier.owner', 'objectifSpecifique.owner'])->find($elementId),
            'sous_action' => SousAction::with(['action.objectifSpecifique.objectifStrategique.pilier.owner', 'action.objectifSpecifique.owner', 'action.owner'])->find($elementId),
            default => null
        };
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($notificationId, $userId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();
            
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        
        return false;
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);
    }

    /**
     * Supprimer une notification
     */
    public function deleteNotification($notificationId, $userId)
    {
        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * Récupérer les notifications d'un utilisateur
     */
    public function getUserNotifications($userId, $limit = 50, $unreadOnly = false)
    {
        $query = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc');
            
        if ($unreadOnly) {
            $query->unread();
        }
        
        return $query->limit($limit)->get();
    }

    /**
     * Compter les notifications non lues d'un utilisateur
     */
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Vérifier les échéances approchantes (à exécuter via une tâche cron)
     */
    public function checkEcheancesApprochantes()
    {
        // Vérifier les sous-actions avec échéance dans les 7 prochains jours
        $sousActions = SousAction::whereNotNull('date_echeance')
            ->where('date_echeance', '>=', Carbon::now())
            ->where('date_echeance', '<=', Carbon::now()->addDays(7))
            ->whereNull('date_realisation')
            ->get();

        foreach ($sousActions as $sousAction) {
            $daysLeft = Carbon::now()->diffInDays($sousAction->date_echeance, false);
            
            if ($daysLeft >= 0 && $daysLeft <= 7) {
                $this->notifyEcheanceApproche('sous_action', $sousAction->id, $daysLeft, $sousAction);
            }
        }
    }

    /**
     * Vérifier les délais dépassés (à exécuter via une tâche cron)
     */
    public function checkDelaisDepasses()
    {
        // Vérifier les sous-actions avec échéance dépassée
        $sousActions = SousAction::whereNotNull('date_echeance')
            ->where('date_echeance', '<', Carbon::now())
            ->whereNull('date_realisation')
            ->get();

        foreach ($sousActions as $sousAction) {
            $daysLate = Carbon::now()->diffInDays($sousAction->date_echeance, false);
            
            if ($daysLate > 0) {
                $this->notifyDelaiDepasse('sous_action', $sousAction->id, abs($daysLate), $sousAction);
            }
        }
    }
}
