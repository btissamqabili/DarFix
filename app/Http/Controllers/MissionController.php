<?php

namespace App\Http\Controllers;

use App\Http\Requests\MissionStoreRequest;
use App\Http\Requests\MissionUpdateRequest;
use App\Models\Categorie;
use App\Models\Mission;
use App\Models\User;
use App\Notifications\NouvelleMissionNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class MissionController extends Controller
{
    public function index()
    {
        $missions = auth()->user()
            ->missions()
            ->with([
                'evaluations',
                'prestations',
                'offres' => function ($query) {
                    $query->where('statut', 'acceptee')->with('prestataire');
                },
            ])
            ->latest()
            ->get();

        return view('missions.index', compact('missions'));
    }

    public function create()
    {
        return view('missions.create', [
            'categories' => Categorie::orderBy('nom')->get(),
        ]);
    }

    public function edit(Mission $mission)
    {
        Gate::authorize('update', $mission);

        return view('missions.edit', [
            'mission' => $mission,
            'categories' => Categorie::orderBy('nom')->get(),
        ]);
    }

    public function store(MissionStoreRequest $request)
    {
        $validated = $request->validated();

        $duplicate = $this->findRecentDuplicate($validated);

        if ($duplicate) {
            return redirect()
                ->route('missions.index')
                ->with('success', 'Votre mission a déjà été publiée à l’instant, aucune doublure n’a été créée.');
        }

        $mission = auth()->user()
            ->missions()
            ->create($validated);

        $prestataires = User::where('role', 'prestataire')->get();

        foreach ($prestataires as $prestataire) {
            $prestataire->notify(
                new NouvelleMissionNotification($mission)
            );
        }

        return redirect()
            ->route('missions.index')
            ->with('success', 'Mission créée avec succès.');
    }

    /**
     * Détecte une mission identique créée par le même client
     * dans les dernières secondes (double soumission accidentelle).
     */
    private function findRecentDuplicate(array $validated): ?Mission
    {
        $query = Mission::query()
            ->where('client_id', auth()->id())
            ->where('created_at', '>=', now()->subSeconds(30));

        foreach ([
            'titre',
            'description',
            'categorie_id',
            'budget',
            'adresse',
            'date_souhaitee',
        ] as $field) {
            if (! array_key_exists($field, $validated)) {
                continue;
            }

            $value = $validated[$field];

            if ($field === 'date_souhaitee' && $value !== null) {
                $value = Carbon::parse($value);
            }

            $query->where($field, $value);
        }

        return $query->latest('id')->first();
    }

    public function update(
        MissionUpdateRequest $request,
        Mission $mission
    ) {
        Gate::authorize('update', $mission);

        $validated = $request->validated();

        $mission->update($validated);

        return redirect()
            ->route('missions.index')
            ->with('success', 'Mission modifiée avec succès.');
    }

    public function destroy(Mission $mission)
    {
        Gate::authorize('delete', $mission);

        Storage::disk('public')->delete($mission->photos ?? []);
        $mission->delete();

        return redirect()
            ->route('missions.index')
            ->with('success', 'Mission supprimée avec succès.');
    }

    public function offres(Mission $mission)
    {
        abort_unless(
            $mission->client_id === auth()->id(),
            403
        );

        $offres = $mission->offres()
            ->with('prestataire')
            ->latest()
            ->get();

        return view(
            'missions.offres',
            compact('mission', 'offres')
        );
    }

    public function complete(Mission $mission)
    {
        abort_unless(
            $mission->client_id === auth()->id(),
            403
        );

        abort_if(
            $mission->statut !== 'en_cours',
            404
        );

        $mission->update([
            'statut' => 'terminee',
        ]);

        $mission->prestations()
            ->where('statut', 'en_cours')
            ->update([
                'statut' => 'terminee',
                'date_fin' => now(),
            ]);

        return back()->with(
            'success',
            'La mission a été terminée avec succès.'
        );
    }
}
