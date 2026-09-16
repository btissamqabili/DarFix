<?php

namespace App\Http\Controllers;

use App\Models\User;

class PrestataireController extends Controller
{
    public function show($id)
    {
        $prestataire = User::where('role', 'prestataire')
            ->with([
                'evaluationsRecues.client',
            ])
            ->findOrFail($id);

        return view('prestataires.show', compact('prestataire'));
    }
}
