<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\Offre;
use App\Models\Prestation;
use App\Notifications\NouvelleOffreNotification;
use App\Notifications\OffreAcceptedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OffreController extends Controller
{
    /**
     * Afficher les offres reçues pour les missions du client.
     */
    public function recues(Mission $mission)
    {
        abort_unless($mission->client_id === auth()->id(), 403);

        $offres = $mission->offres()
            ->with('prestataire')
            ->latest()
            ->get();

        return view('offres.recues', compact('mission', 'offres'));
    }

    /**
     * Créer une offre pour une mission.
     */
    public function store(Request $request, Mission $mission): RedirectResponse
    {
        abort_unless(auth()->user()->role === 'prestataire', 403);
        abort_if($mission->statut !== 'ouverte', 404);

        $validated = $request->validate([
            'prix_propose' => ['required', 'numeric', 'min:0'],
            'message' => ['required', 'string', 'max:1000'],
            'delai_execution' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $dejaPropose = $mission->offres()
            ->where('prestataire_id', auth()->id())
            ->exists();

        if ($dejaPropose) {
            return back()->withErrors([
                'message' => 'Vous avez déjà proposé une offre pour cette mission.',
            ]);
        }

        $offre = Offre::create([
            'mission_id' => $mission->id,
            'prestataire_id' => auth()->id(),
            'prix_propose' => $validated['prix_propose'],
            'message' => $validated['message'],
            'delai_execution' => $validated['delai_execution'],
            'statut' => 'en_attente',
        ]);

        $mission->client->notify(
            new NouvelleOffreNotification(
                $offre->mission_id,
                $offre->prestataire->name,
                (float) $offre->prix_propose
            )
        );

        return back()->with(
            'success',
            'Votre offre a été envoyée avec succès.'
        );
    }

    /**
     * Accepter une offre.
     */
    public function accept(Offre $offre): RedirectResponse
    {
        $mission = $offre->mission;

        abort_unless($mission->client_id === auth()->id(), 403);

        DB::transaction(function () use ($offre, $mission) {
            $mission->lockForUpdate()->first();

            abort_if($mission->statut !== 'ouverte', 409);
            abort_if($offre->statut !== 'en_attente', 409);

            $offre->update([
                'statut' => 'acceptee',
            ]);

            Prestation::create([
                'offre_id' => $offre->id,
                'mission_id' => $mission->id,
                'prestataire_id' => $offre->prestataire_id,
                'date_debut' => now(),
                'statut' => 'en_cours',
                'montant' => $offre->prix_propose,
            ]);

            Offre::where('mission_id', $mission->id)
                ->where('id', '!=', $offre->id)
                ->where('statut', 'en_attente')
                ->update(['statut' => 'refusee']);

            $mission->update(['statut' => 'en_cours']);
        });

        $offre->prestataire->notify(
            new OffreAcceptedNotification($offre)
        );

        return back()->with(
            'success',
            'Offre acceptée avec succès. La mission est maintenant en cours.'
        );
    }

    /**
     * Refuser une offre.
     */
    public function refuse(Offre $offre): RedirectResponse
    {
        $mission = $offre->mission;

        abort_unless($mission->client_id === auth()->id(), 403);
        abort_if($offre->statut !== 'en_attente', 409);

        $offre->update([
            'statut' => 'refusee',
        ]);

        return back()->with(
            'success',
            'Offre refusée avec succès.'
        );
    }

    public function edit(Offre $offre)
    {
        abort_unless($offre->prestataire_id === auth()->id(), 403);
        abort_if($offre->statut !== 'en_attente', 409);

        return view('offres.edit', compact('offre'));
    }

    public function update(Request $request, Offre $offre): RedirectResponse
    {
        abort_unless($offre->prestataire_id === auth()->id(), 403);
        abort_if($offre->statut !== 'en_attente', 409);

        $validated = $request->validate([
            'prix_propose' => ['required', 'numeric', 'min:0'],
            'message' => ['required', 'string', 'max:1000'],
            'delai_execution' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $offre->update($validated);

        return redirect()
            ->route('prestataire.missions.show', $offre->mission)
            ->with('success', 'Votre offre a été modifiée avec succès.');
    }

    public function cancel(Offre $offre): RedirectResponse
    {
        abort_unless($offre->prestataire_id === auth()->id(), 403);
        abort_if($offre->statut !== 'en_attente', 409);

        $offre->update(['statut' => 'refusee']);

        return redirect()
            ->route('prestataire.missions.show', $offre->mission)
            ->with('success', 'Votre offre a été annulée.');
    }
}
