<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ObjectifStrategique;

class ObjectifStrategiqueAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $objectifStrategique;

    /**
     * Create a new notification instance.
     */
    public function __construct(ObjectifStrategique $objectifStrategique)
    {
        $this->objectifStrategique = $objectifStrategique;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvel objectif stratégique assigné')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Un nouvel objectif stratégique vous a été assigné :')
            ->line('Code : ' . $this->objectifStrategique->code)
            ->line('Libellé : ' . $this->objectifStrategique->libelle)
            ->line('Pilier : ' . $this->objectifStrategique->pilier->libelle)
            ->line('Description : ' . ($this->objectifStrategique->description ?: 'Aucune description'))
            ->action('Voir l\'objectif stratégique', url('/objectifs-strategiques/' . $this->objectifStrategique->id))
            ->line('Vous êtes maintenant responsable de la réalisation de cet objectif stratégique.')
            ->line('Merci de mettre à jour régulièrement le taux d\'avancement.')
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
            'objectif_strategique_id' => $this->objectifStrategique->id,
            'objectif_strategique_code' => $this->objectifStrategique->code,
            'objectif_strategique_libelle' => $this->objectifStrategique->libelle,
            'pilier_libelle' => $this->objectifStrategique->pilier->libelle,
            'message' => 'Un nouvel objectif stratégique vous a été assigné : ' . $this->objectifStrategique->libelle,
            'type' => 'objectif_strategique_assigned'
        ];
    }
}




