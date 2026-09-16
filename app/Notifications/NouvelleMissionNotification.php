<?php

namespace App\Notifications;

use App\Models\Mission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NouvelleMissionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Mission $mission
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'mission_id' => $this->mission->id,
            'titre' => $this->mission->titre,
            'budget' => $this->mission->budget,
            'client_name' => $this->mission->client->name,
            'message' => 'Une nouvelle mission est disponible.',
        ];
    }
}
