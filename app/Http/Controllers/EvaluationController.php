<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Mission;
use App\Notifications\NouvelleEvaluationNotification;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function store(Request $request, Mission $mission)
    {
        // Vérifier que la mission appartient au client connecté.
        abort_unless(
            $mission->client_id === auth()->id(),
            403
        );

        // Vérifier que la mission est terminée.
        if ($mission->statut !== 'terminee') {
            return back()->with(
                'error',
                'Cette mission n’est pas encore terminée.'
            );
        }

        // Vérifier si le client a déjà évalué cette mission.
        $dejaEvaluee = Evaluation::where('mission_id', $mission->id)
            ->where('client_id', auth()->id())
            ->exists();

        if ($dejaEvaluee) {
            return back()->with(
                'error',
                'Vous avez déjà évalué cette mission.'
            );
        }

        // Valider les données du formulaire.
        $validated = $request->validate([
            'note' => ['required', 'integer', 'min:1', 'max:5'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ]);

        // Récupérer l’offre acceptée.
        $offre = $mission->offres()
            ->where('statut', 'acceptee')
            ->first();

        // Vérifier qu’un prestataire est associé.
        if (! $offre) {
            return back()->with(
                'error',
                'Aucun prestataire associé à cette mission.'
            );
        }

        // Créer l’évaluation.
        $evaluation = Evaluation::create([
            'mission_id' => $mission->id,
            'client_id' => auth()->id(),
            'prestataire_id' => $offre->prestataire_id,
            'note' => $validated['note'],
            'commentaire' => $validated['commentaire'] ?? null,
        ]);

        // Notifier le prestataire.
        $offre->prestataire->notify(
            new NouvelleEvaluationNotification($evaluation)
        );

        return back()->with(
            'success',
            'Votre évaluation a été ajoutée avec succès.'
        );
    }
}
