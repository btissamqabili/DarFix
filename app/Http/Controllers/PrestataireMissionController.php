<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Mission;
use Illuminate\Http\Request;

class PrestataireMissionController extends Controller
{
    /**
     * Afficher les missions ouvertes.
     */
    public function index(Request $request)
    {
        $missions = Mission::query()
            ->with('categorie')
            ->where('statut', 'ouverte')
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->string('q')->trim();

                $query->where(function ($query) use ($search) {
                    $query->where('titre', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('adresse', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('categorie_id'), function ($query) use ($request) {
                $query->where('categorie_id', $request->integer('categorie_id'));
            })
            ->when($request->filled('budget_max'), function ($query) use ($request) {
                $query->where('budget', '<=', $request->input('budget_max'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Categorie::orderBy('nom')->get();

        return view('missions.disponibles', compact('missions', 'categories'));
    }

    /**
     * Afficher le détail d'une mission.
     *
     * Un prestataire peut accéder à une mission ouverte
     * ou à une mission pour laquelle il a déjà envoyé une offre.
     */
    public function show(Mission $mission)
    {
        $prestataire = auth()->user();

        $aUneOffre = $mission->offres()
            ->where('prestataire_id', $prestataire->id)
            ->exists();

        abort_unless(
            $mission->statut === 'ouverte' || $aUneOffre,
            404
        );

        $mission->load('offres.prestataire');

        return view(
            'missions.show',
            compact('mission', 'aUneOffre')
        );
    }
}
