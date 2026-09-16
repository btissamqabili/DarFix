<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Mission;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->get();

        return view(
            'notifications.index',
            compact('notifications')
        );
    }

    public function read(string $id)
    {
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($id);

        // Marquer la notification comme lue si ce n'est pas déjà fait.
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $type = $notification->type;
        $data = $notification->data ?? [];

        // 1. Nouvelle offre reçue (destinée au client).
        if ($type === 'App\\Notifications\\NouvelleOffreNotification') {
            $missionId = $data['mission_id'] ?? null;
            $mission = $missionId ? Mission::find($missionId) : null;

            if ($user->role === 'client') {
                if ($mission && $mission->client_id === $user->id) {
                    return redirect()->route('missions.offres', $mission);
                }

                return redirect()->route('missions.index')
                    ->with('error', 'Cette mission n’est plus disponible.');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.offres.index');
            }

            return redirect()->route('prestataire.missions.index');
        }

        // 2. Offre acceptée (destinée au prestataire).
        if ($type === 'App\\Notifications\\OffreAcceptedNotification') {
            $missionId = $data['mission_id'] ?? null;
            $mission = $missionId ? Mission::find($missionId) : null;

            if ($user->role === 'prestataire') {
                if ($mission) {
                    return redirect()->route('prestataire.missions.show', $mission);
                }

                return redirect()->route('dashboard')
                    ->with('error', 'La mission associée n’est plus disponible.');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.prestations.index');
            }

            if ($mission && $mission->client_id === $user->id) {
                return redirect()->route('missions.offres', $mission);
            }

            return redirect()->route('missions.index');
        }

        // 3. Nouveau message dans une conversation.
        if ($type === 'App\\Notifications\\NewMessageNotification') {
            $conversationId = $data['conversation_id'] ?? null;

            if (! $conversationId && ! empty($data['message_id'])) {
                $message = Message::find($data['message_id']);
                $conversationId = $message?->conversation_id;
            }

            if ($conversationId) {
                $conversation = Conversation::find($conversationId);
                if (
                    $conversation &&
                    ($conversation->client_id === $user->id || $conversation->prestataire_id === $user->id)
                ) {
                    return redirect()->route('conversations.show', $conversation);
                }
            }

            return redirect()->route('conversations.index')
                ->with('error', 'La conversation demandée n’est plus accessible.');
        }

        // 4. Nouvelle mission disponible (destinée au prestataire).
        if ($type === 'App\\Notifications\\NouvelleMissionNotification') {
            $missionId = $data['mission_id'] ?? null;
            $mission = $missionId ? Mission::find($missionId) : null;

            if ($user->role === 'prestataire') {
                if ($mission && ($mission->statut === 'ouverte' || $mission->offres()->where('prestataire_id', $user->id)->exists())) {
                    return redirect()->route('prestataire.missions.show', $mission);
                }

                return redirect()->route('prestataire.missions.index')
                    ->with('error', 'Cette mission n’est plus disponible.');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.missions.index');
            }

            return redirect()->route('missions.index');
        }

        // 5. Nouvelle évaluation reçue (destinée au prestataire).
        if ($type === 'App\\Notifications\\NouvelleEvaluationNotification') {
            if ($user->role === 'prestataire') {
                return redirect()->route('prestataires.show', $user->id);
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.evaluations.index');
            }

            return redirect()->route('missions.index');
        }

        // Sécurité : type de notification inconnu.
        return redirect()->route('notifications.index');
    }

    public function readAll()
    {
        auth()->user()
            ->unreadNotifications
            ->markAsRead();

        return back()->with(
            'success',
            'Toutes les notifications ont été marquées comme lues.'
        );
    }
}
