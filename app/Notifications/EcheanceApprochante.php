<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SousAction;
use Carbon\Carbon;

class EcheanceApprochante extends Notification implements ShouldQueue
{
    use Queueable;

    protected $sousAction;
    protected $joursRestants;

    /**
     * Create a new notification instance.
     */
    public function __construct(SousAction $sousAction, int $joursRestants)
    {
        $this->sousAction = $sousAction;
        $this->joursRestants = $joursRestants;
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
        $message = (new MailMessage)
            ->subject('Échéance approchante - ' . $this->sousAction->code_complet)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('L\'échéance de la sous-action suivante approche :')
            ->line('Code : ' . $this->sousAction->code_complet)
            ->line('Libellé : ' . $this->sousAction->libelle)
            ->line('Date d\'échéance : ' . $this->sousAction->date_echeance->format('d/m/Y'))
            ->line('Taux d\'avancement actuel : ' . $this->sousAction->taux_avancement . '%');

        if ($this->joursRestants > 0) {
            $message->line('Il reste ' . $this->joursRestants . ' jour(s) avant l\'échéance.');
        } else {
            $message->line('L\'échéance est dépassée de ' . abs($this->joursRestants) . ' jour(s).');
        }

        return $message
            ->action('Voir la sous-action', url('/sous-actions/' . $this->sousAction->id))
            ->line('Merci de mettre à jour le taux d\'avancement rapidement.')
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
            'date_echeance' => $this->sousAction->date_echeance,
            'taux_avancement' => $this->sousAction->taux_avancement,
            'jours_restants' => $this->joursRestants,
        ];
    }
}
