<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NouvelleOffreNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $missionId,
        public string $prestataireNom,
        public float $prixPropose
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Le prestataire {$this->prestataireNom} a proposé une offre de {$this->prixPropose} DH pour votre mission.",
            'mission_id' => $this->missionId,
            'prestataire_nom' => $this->prestataireNom,
            'prix_propose' => $this->prixPropose,
        ];
    }
}
