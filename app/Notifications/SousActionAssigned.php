<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SousAction;

class SousActionAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $sousAction;

    /**
     * Create a new notification instance.
     */
    public function __construct(SousAction $sousAction)
    {
        $this->sousAction = $sousAction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle sous-action assignée')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Une nouvelle sous-action vous a été assignée :')
            ->line('Code : ' . $this->sousAction->code_complet)
            ->line('Libellé : ' . $this->sousAction->libelle)
            ->line('Action parente : ' . $this->sousAction->action->libelle)
            ->line('Date d\'échéance : ' . ($this->sousAction->date_echeance ? $this->sousAction->date_echeance->format('d/m/Y') : 'Non définie'))
            ->action('Voir la sous-action', url('/sous-actions/' . $this->sousAction->id))
            ->line('Merci de mettre à jour le taux d\'avancement régulièrement.')
            ->salutation('Cordialement,');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sous_action_id' => $this->sousAction->id,
            'code' => $this->sousAction->code_complet,
            'libelle' => $this->sousAction->libelle,
            'action_parente' => $this->sousAction->action->libelle,
            'date_echeance' => $this->sousAction->date_echeance,
        ];
    }
}
