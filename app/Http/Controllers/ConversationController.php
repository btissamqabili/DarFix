<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;

class ConversationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'client') {
            $conversations = $user->conversationsClient()
                ->with('prestataire')
                ->latest()
                ->get();
        } else {
            $conversations = $user->conversationsPrestataire()
                ->with('client')
                ->latest()
                ->get();
        }

        return view(
            'conversations.index',
            compact('conversations')
        );
    }

    public function show(Conversation $conversation)
    {
        $userId = auth()->id();

        abort_unless(
            $conversation->client_id === $userId ||
            $conversation->prestataire_id === $userId,
            403
        );

        // Marquer comme lus les messages reçus.
        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('lu_at')
            ->update([
                'lu_at' => now(),
            ]);

        $conversation->load([
            'client',
            'prestataire',
            'messages.sender',
        ]);

        return view(
            'conversations.show',
            compact('conversation')
        );
    }

    public function store(User $user)
    {
        abort_unless(
            in_array(
                auth()->user()->role,
                ['client', 'prestataire']
            ),
            403
        );

        abort_if($user->id === auth()->id(), 403);

        $currentUser = auth()->user();

        if ($currentUser->role === 'client') {
            abort_unless($user->role === 'prestataire', 403);

            $clientId = $currentUser->id;
            $prestataireId = $user->id;
        } else {
            abort_unless($user->role === 'client', 403);

            $clientId = $user->id;
            $prestataireId = $currentUser->id;
        }

        $conversation = Conversation::firstOrCreate([
            'client_id' => $clientId,
            'prestataire_id' => $prestataireId,
        ]);

        return redirect()->route(
            'conversations.show',
            $conversation
        );
    }
}
