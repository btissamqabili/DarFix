<?php

namespace App\Notifications;

use App\Models\Offre;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OffreAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Offre $offre
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Votre offre pour la mission "'
                .$this->offre->mission->titre
                .'" a été acceptée.',

            'offre_id' => $this->offre->id,

            'mission_id' => $this->offre->mission_id,
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
