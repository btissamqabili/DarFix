<x-app-layout>
    <div class="page-frame">
        <div class="mx-auto max-w-4xl">
            <div class="border-b border-[#e5dfd4] pb-8">
                <p class="eyebrow">Mission · détail</p>
                <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <h1 class="display-title">{{ $mission->titre }}</h1>
                    <span class="status-pill {{ $mission->statut === 'ouverte' ? 'status-open' : ($mission->statut === 'terminee' ? 'status-done' : ($mission->statut === 'annulee' ? 'status-cancelled' : 'status-progress')) }}">
                        {{ ucfirst(str_replace('_', ' ', $mission->statut)) }}
                    </span>
                </div>
            </div>

            @if(session('success'))
                <div class="notice-success mt-6">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="notice-error mt-6">{{ session('error') }}</div>
            @endif

            <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_0.85fr]">
                <article class="surface p-6 sm:p-8">
                    <p class="eyebrow">Description du besoin</p>
                    <p class="mt-4 whitespace-pre-line text-sm leading-7 text-[#6F6862]">{{ $mission->description }}</p>

                    <dl class="mt-8 grid gap-5 border-t border-[#e5dfd4] pt-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Budget indicatif</dt>
                            <dd class="mt-1 font-serif text-2xl text-[#8b1e1e]">
                                {{ $mission->budget ? number_format($mission->budget, 2, ',', ' ') . ' DH' : 'À discuter' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Catégorie</dt>
                            <dd class="mt-1 font-semibold">{{ $mission->categorie->nom ?? 'Non précisée' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Lieu d’intervention</dt>
                            <dd class="mt-1 font-semibold">{{ $mission->adresse ?: 'À préciser' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Date souhaitée</dt>
                            <dd class="mt-1 font-semibold">{{ $mission->date_souhaitee?->format('d/m/Y') ?? 'Flexible' }}</dd>
                        </div>
                    </dl>

                    @if(!empty($mission->photos) && is_array($mission->photos) && count($mission->photos) > 0)
                        <div class="mt-8 border-t border-[#e5dfd4] pt-6">
                            <p class="eyebrow">Photos du projet</p>
                            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                @foreach($mission->photos as $photo)
                                    <img src="{{ Storage::disk('public')->url($photo) }}" alt="Photo de la mission" class="aspect-square w-full object-cover">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-8">
                        <a href="{{ route('prestataire.missions.index') }}" class="action-quiet">← Retour aux missions</a>
                    </div>
                </article>

                @php
                    $monOffre = $mission->offres->firstWhere('prestataire_id', auth()->id());
                @endphp

                @if($mission->statut === 'ouverte' && !$aUneOffre)
                    <aside class="surface-soft p-6 sm:p-8">
                        <p class="eyebrow">Votre proposition</p>
                        <h2 class="mt-2 font-serif text-2xl">Proposer mes services</h2>
                        <p class="mt-3 text-sm leading-6 text-[#6F6862]">Présentez un prix clair, un délai estimé et votre approche du travail.</p>

                        <form method="POST" action="{{ route('offres.store', $mission) }}" class="mt-6 grid gap-5">
                            @csrf
                            <div>
                                <label for="prix_propose" class="field-label">Prix proposé (DH)</label>
                                <input type="number" name="prix_propose" id="prix_propose" step="0.01" min="0" value="{{ old('prix_propose') }}" required class="field-control">
                                @error('prix_propose')<p class="mt-1 text-sm text-[#8b1e1e]">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="delai_execution" class="field-label">Délai d’exécution (en jours)</label>
                                <input type="number" name="delai_execution" id="delai_execution" min="1" max="365" value="{{ old('delai_execution') }}" required class="field-control">
                                @error('delai_execution')<p class="mt-1 text-sm text-[#8b1e1e]">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="message" class="field-label">Message au client</label>
                                <textarea name="message" id="message" rows="5" class="field-control" placeholder="Présentez votre proposition, vos disponibilités et votre matériel..."></textarea>
                                @error('message')<p class="mt-1 text-sm text-[#8b1e1e]">{{ $message }}</p>@enderror
                            </div>

                            <button type="submit" class="action-primary w-full">Envoyer ma proposition</button>
                        </form>
                    </aside>
                @elseif($monOffre)
                    <div class="surface-soft h-fit p-6 sm:p-8">
                        <div class="flex items-center justify-between border-b border-[#dcdfc6] pb-4">
                            <p class="eyebrow">Votre suivi d'offre</p>
                            <span class="status-pill {{ $monOffre->statut === 'acceptee' ? 'status-done' : ($monOffre->statut === 'refusee' ? 'status-cancelled' : 'status-progress') }}">
                                {{ ucfirst(str_replace('_', ' ', $monOffre->statut)) }}
                            </span>
                        </div>

                        @if($monOffre->statut === 'acceptee')
                            <div class="mt-5">
                                <h3 class="font-serif text-2xl text-[#386047]">Offre acceptée !</h3>
                                <p class="mt-2 text-sm leading-6 text-[#6F6862]">
                                    Le client a retenu votre proposition pour réaliser cette mission. Vous pouvez dès à présent échanger avec lui par messagerie.
                                </p>

                                <div class="mt-5 rounded border border-[#cbd8c5] bg-[#edf1e8] p-4 text-sm text-[#386047]">
                                    <p><strong>Prix convenu :</strong> {{ number_format($monOffre->prix_propose, 2, ',', ' ') }} DH</p>
                                    <p class="mt-1"><strong>Délai convenu :</strong> {{ $monOffre->delai_execution }} jour(s)</p>
                                    <p class="mt-1"><strong>Client :</strong> {{ $mission->client->name }}</p>
                                </div>

                                <form method="POST" action="{{ route('conversations.store', $mission->client) }}" class="mt-6">
                                    @csrf
                                    <button type="submit" class="action-primary w-full">
                                        Discuter avec le client <span class="ml-1">✉</span>
                                    </button>
                                </form>
                            </div>
                        @elseif($monOffre->statut === 'en_attente')
                            <div class="mt-5">
                                <h3 class="font-serif text-xl">Proposition en attente</h3>
                                <p class="mt-2 text-sm leading-6 text-[#6F6862]">
                                    Le client examine actuellement votre proposition.
                                </p>

                                <div class="mt-4 space-y-2 border-y border-[#dcdfc6] py-4 text-sm text-[#6F6862]">
                                    <p>Votre prix : <strong class="text-[#2F2926]">{{ number_format($monOffre->prix_propose, 2, ',', ' ') }} DH</strong></p>
                                    <p>Délai estimé : <strong class="text-[#2F2926]">{{ $monOffre->delai_execution }} jour(s)</strong></p>
                                    @if($monOffre->message)
                                        <p class="mt-2 italic">"{{ $monOffre->message }}"</p>
                                    @endif
                                </div>

                                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                                    <a href="{{ route('offres.edit', $monOffre) }}" class="action-primary text-center">Modifier mon offre</a>
                                    <form method="POST" action="{{ route('offres.cancel', $monOffre) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir annuler votre proposition ?')" class="action-quiet !text-[#8b1e1e] w-full">
                                            Annuler
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="mt-5">
                                <h3 class="font-serif text-xl text-[#8b1e1e]">Proposition fermée</h3>
                                <p class="mt-2 text-sm leading-6 text-[#6F6862]">
                                    Cette offre a été refusée ou la mission a été clôturée.
                                </p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="surface-soft h-fit p-6 sm:p-8">
                        <p class="eyebrow">Statut</p>
                        <p class="mt-3 font-serif text-xl">Cette mission n’est plus ouverte aux propositions.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
