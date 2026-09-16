<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceStoreRequest;
use App\Http\Requests\ServiceUpdateRequest;
use App\Models\Categorie;
use App\Models\Service;
use Illuminate\Support\Facades\Gate;

class ServiceController extends Controller
{
    public function index()
    {
        $services = auth()->user()
            ->services()
            ->with('categorie')
            ->get();

        return view('services.index', compact('services'));
    }

    public function create()
    {
        $categories = Categorie::all();

        return view('services.create', compact('categories'));
    }

    public function store(ServiceStoreRequest $request)
    {
        $validated = $request->validated();

        auth()->user()
            ->services()
            ->create($validated);

        return redirect()
            ->route('services.index')
            ->with('success', 'Service ajouté avec succès.');
    }

    public function edit(Service $service)
    {
        Gate::authorize('update', $service);

        $categories = Categorie::all();

        return view('services.edit', compact('service', 'categories'));
    }

    public function update(
        ServiceUpdateRequest $request,
        Service $service
    ) {
        Gate::authorize('update', $service);

        $service->update($request->validated());

        return redirect()
            ->route('services.index')
            ->with('success', 'Service modifié avec succès.');
    }

    public function destroy(Service $service)
    {
        Gate::authorize('delete', $service);

        $service->delete();

        return redirect()
            ->route('services.index')
            ->with('success', 'Service supprimé avec succès.');
    }
}
