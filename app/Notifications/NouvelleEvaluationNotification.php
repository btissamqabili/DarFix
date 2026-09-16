<?php

namespace App\Notifications;

use App\Models\Evaluation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NouvelleEvaluationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Evaluation $evaluation
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'evaluation_id' => $this->evaluation->id,
            'mission_id' => $this->evaluation->mission_id,
            'client_name' => $this->evaluation->client->name,
            'note' => $this->evaluation->note,
            'message' => 'Vous avez reçu une nouvelle évaluation.',
        ];
    }
}
