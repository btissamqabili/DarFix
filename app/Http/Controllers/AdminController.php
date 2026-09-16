<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Mission;
use App\Models\Offre;
use App\Models\Prestation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function dashboard()
    {
        $nombreClients = User::where('role', 'client')->count();

        $nombrePrestataires = User::where('role', 'prestataire')->count();

        $nombreMissions = Mission::count();

        $nombreOffres = Offre::count();

        $nombreEvaluations = Evaluation::count();

        $evaluations = Evaluation::with([
            'client',
            'prestataire',
            'mission',
        ])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.admin', compact(
            'nombreClients',
            'nombrePrestataires',
            'nombreMissions',
            'nombreOffres',
            'nombreEvaluations',
            'evaluations'
        ));
    }

    public function evaluations()
    {
        $evaluations = Evaluation::with([
            'client',
            'prestataire',
            'mission',
        ])
            ->latest()
            ->paginate(15);

        return view('admin.evaluations.index', compact('evaluations'));
    }

    public function users()
    {
        $users = User::latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function missions()
    {
        $missions = Mission::with(['client', 'categorie'])
            ->latest()
            ->paginate(15);

        return view('admin.missions.index', compact('missions'));
    }

    public function offres()
    {
        $offres = Offre::with(['mission', 'prestataire'])
            ->latest()
            ->paginate(15);

        return view('admin.offres.index', compact('offres'));
    }

    public function prestations()
    {
        $prestations = Prestation::with(['mission', 'prestataire', 'offre'])
            ->latest()
            ->paginate(15);

        return view('admin.prestations.index', compact('prestations'));
    }

    public function cancelMission(Mission $mission)
    {
        DB::transaction(function () use ($mission) {
            $mission->update(['statut' => 'annulee']);
            $mission->offres()
                ->where('statut', 'en_attente')
                ->update(['statut' => 'refusee']);
            $mission->prestations()
                ->where('statut', 'en_cours')
                ->update(['statut' => 'annulee', 'date_fin' => now()]);
        });

        return back()->with('success', 'Mission désactivée avec succès.');
    }

    public function refuseOffre(Offre $offre)
    {
        abort_if($offre->statut !== 'en_attente', 409);

        $offre->update(['statut' => 'refusee']);

        return back()->with('success', 'Offre modérée avec succès.');
    }

    public function destroyEvaluation(Evaluation $evaluation)
    {
        $evaluation->delete();

        return back()->with('success', 'Évaluation supprimée avec succès.');
    }

    public function showUser(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function destroyUser(User $user)
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
