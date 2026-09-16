<?php

namespace App\Http\Controllers;

use App\Models\Prestation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class FactureController extends Controller
{
    public function download(Prestation $prestation): Response
    {
        abort_if($prestation->statut !== 'terminee', 404);
        abort_unless(
            $prestation->prestataire_id === auth()->id()
                || $prestation->mission->client_id === auth()->id(),
            403
        );

        $prestation->load(['mission.client', 'prestataire', 'offre']);

        return Pdf::loadView('factures.show', compact('prestation'))
            ->download('facture-prestation-'.$prestation->id.'.pdf');
    }
}
