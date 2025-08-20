<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationCenter extends Component
{
    public $showNotifications = false;
    public $notifications = [];
    public $unreadCount = 0;
    public $selectedNotification = null;
    public $showNotificationDetails = false;

    protected $listeners = [
        'refreshNotifications' => 'loadNotifications',
        'notificationReceived' => 'handleNewNotification'
    ];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        if (Auth::check()) {
            try {
                // Utiliser le système de notifications personnalisé
                $this->notifications = DB::table('notifications')
                    ->where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get();
                
                $this->unreadCount = DB::table('notifications')
                    ->where('user_id', Auth::id())
                    ->whereNull('read_at')
                    ->count();
                
                $this->dispatch('console.log', '✅ [DEBUG] Notifications chargées:', count($this->notifications));
                $this->dispatch('console.log', '✅ [DEBUG] Nombre de notifications non lues:', $this->unreadCount);
                
            } catch (\Exception $e) {
                $this->dispatch('console.log', '❌ [DEBUG] Erreur lors du chargement des notifications:', $e->getMessage());
                $this->notifications = collect();
                $this->unreadCount = 0;
            }
        }
    }

    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;
        if ($this->showNotifications) {
            $this->loadNotifications();
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = DB::table('notifications')
            ->where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->first();

        if ($notification) {
            DB::table('notifications')
                ->where('id', $notificationId)
                ->update(['read_at' => now()]);
            
            $this->loadNotifications();
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Notification marquée comme lue']);
        }
    }

    public function markAllAsRead()
    {
        DB::table('notifications')
            ->where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        $this->loadNotifications();
        $this->dispatch('showToast', ['type' => 'success', 'message' => 'Toutes les notifications ont été marquées comme lues']);
    }

    public function deleteNotification($notificationId)
    {
        $notification = DB::table('notifications')
            ->where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->first();

        if ($notification) {
            DB::table('notifications')
                ->where('id', $notificationId)
                ->delete();
            
            $this->loadNotifications();
            $this->dispatch('showToast', ['type' => 'success', 'message' => 'Notification supprimée']);
        }
    }

    public function showNotificationDetails($notificationId)
    {
        $this->selectedNotification = DB::table('notifications')
            ->where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->first();
        
        if ($this->selectedNotification) {
            $this->showNotificationDetails = true;
            if ($this->isUnread($this->selectedNotification)) {
                $this->markAsRead($notificationId);
            }
        }
    }
    
    private function isUnread($notification)
    {
        return $notification->read_at === null;
    }

    public function closeNotificationDetails()
    {
        $this->showNotificationDetails = false;
        $this->selectedNotification = null;
    }

    public function handleNewNotification($data = null)
    {
        $this->loadNotifications();
        $this->dispatch('showToast', ['type' => 'info', 'message' => 'Nouvelle notification reçue']);
    }

    public function getNotificationIcon($type)
    {
        return match($type) {
            'avancement_change' => 'fas fa-chart-line text-info',
            'echeance_approche' => 'fas fa-clock text-warning',
            'delai_depasse' => 'fas fa-exclamation-triangle text-danger',
            'comment_new' => 'fas fa-comments text-primary',
            'validation_required' => 'fas fa-check-circle text-success',
            'objectif_strategique_assigned' => 'fas fa-bullseye text-primary',
            'objectif_specifique_assigned' => 'fas fa-tasks text-success',
            'action_assigned' => 'fas fa-play-circle text-warning',
            default => 'fas fa-bell text-secondary'
        };
    }

    public function getNotificationColor($priority)
    {
        return match($priority) {
            'urgent' => 'border-danger',
            'high' => 'border-warning',
            'normal' => 'border-info',
            'low' => 'border-secondary',
            default => 'border-secondary'
        };
    }

    public function render()
    {
        return view('livewire.notification-center');
    }
}
