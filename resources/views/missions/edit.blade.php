<x-app-layout>
    <div class="page-frame">
        <div class="mx-auto max-w-3xl">
            <div class="border-b border-[#e5dfd4] pb-8">
                <p class="eyebrow">Votre projet</p>
                <h1 class="display-title mt-3">Affiner la mission</h1>
                <p class="mt-3 text-sm leading-6 text-[#6F6862]">Mettez à jour les informations utiles aux prestataires qui vous répondent.</p>
            </div>

            <div class="surface mt-8 p-6 sm:p-9">
                @if($errors->any())
                    <div class="notice-error mb-7">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('missions.update', $mission) }}" class="grid gap-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="titre" class="field-label">Titre de la mission</label>
                        <input type="text" id="titre" name="titre" value="{{ old('titre', $mission->titre) }}" required class="field-control">
                    </div>

                    <div>
                        <label for="description" class="field-label">Description</label>
                        <textarea id="description" name="description" rows="5" required class="field-control">{{ old('description', $mission->description) }}</textarea>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="categorie_id" class="field-label">Catégorie</label>
                            <select id="categorie_id" name="categorie_id" required class="field-control">
                                <option value="">Sélectionner une catégorie</option>
                                @foreach($categories as $categorie)
                                    <option value="{{ $categorie->id }}" @selected(old('categorie_id', $mission->categorie_id) == $categorie->id)>{{ $categorie->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="budget" class="field-label">Budget indicatif (DH)</label>
                            <input type="number" id="budget" name="budget" value="{{ old('budget', $mission->budget) }}" min="0" step="0.01" class="field-control">
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="adresse" class="field-label">Lieu de l’intervention</label>
                            <input type="text" id="adresse" name="adresse" value="{{ old('adresse', $mission->adresse) }}" class="field-control">
                        </div>

                        <div>
                            <label for="date_souhaitee" class="field-label">Date souhaitée</label>
                            <input type="date" id="date_souhaitee" name="date_souhaitee" value="{{ old('date_souhaitee', optional($mission->date_souhaitee)->format('Y-m-d')) }}" min="{{ now()->toDateString() }}" class="field-control">
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-[#e5dfd4] pt-6 sm:flex-row">
                        <button type="submit" class="action-primary">Enregistrer les changements</button>
                        <a href="{{ route('missions.index') }}" class="action-quiet">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
