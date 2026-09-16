<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(
        Request $request,
        Conversation $conversation
    ) {
        $userId = auth()->id();

        // Vérifier que l'utilisateur appartient à la conversation.
        abort_unless(
            $conversation->client_id === $userId ||
            $conversation->prestataire_id === $userId,
            403
        );

        $validated = $request->validate([
            'contenu' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'contenu' => $validated['contenu'],
        ]);

        // Déterminer le destinataire.
        $destinataireId = $conversation->client_id === $userId
            ? $conversation->prestataire_id
            : $conversation->client_id;

        $destinataire = User::find($destinataireId);

        // Envoyer la notification.
        $destinataire->notify(
            new NewMessageNotification(
                $message->id,
                auth()->user()->name,
                $message->contenu,
                $conversation->id
            )
        );

        return redirect()->route(
            'conversations.show',
            $conversation
        );
    }
}
