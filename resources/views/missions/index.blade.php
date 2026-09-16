<x-app-layout>
    <div class="page-frame">
        <div class="flex flex-col justify-between gap-5 border-b border-[#e5dfd4] pb-8 sm:flex-row sm:items-end">
            <div>
                <p class="eyebrow">Espace client</p>
                <h1 class="display-title mt-3">Mes missions</h1>
                <p class="mt-3 text-sm leading-6 text-[#6F6862]">Chaque demande est une conversation avec le bon savoir-faire.</p>
            </div>
            <a href="{{ route('missions.create') }}" class="action-primary">Ajouter une mission <span class="ml-3">+</span></a>
        </div>

        @if(session('success'))
            <div class="notice-success mt-6">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="notice-error mt-6">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="notice-error mt-6">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-8 space-y-5">
            @forelse($missions as $mission)
                @php
                    $statusClass = match($mission->statut) {
                        'ouverte' => 'status-open',
                        'en_cours' => 'status-progress',
                        'terminee' => 'status-done',
                        'annulee' => 'status-cancelled',
                        default => 'bg-[#f1eee8] text-[#6F6862]'
                    };
                    $prestation = $mission->prestations->firstWhere('statut', 'terminee') ?? $mission->prestations->first();
                    $offreAcceptee = $mission->offres->firstWhere('statut', 'acceptee') ?? $mission->offres->first();
                @endphp
                <article class="surface overflow-hidden">
                    <div class="list-row">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="max-w-2xl">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-[#869B7E]">{{ $mission->created_at?->format('d/m/Y') }}</span>
                                    <span class="status-pill {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $mission->statut)) }}</span>
                                    @if($offreAcceptee && $offreAcceptee->prestataire)
                                        <span class="text-xs text-[#6F6862]">Prestataire retenu : <strong class="text-[#2F2926]">{{ $offreAcceptee->prestataire->name }}</strong></span>
                                    @endif
                                </div>
                                <h2 class="mt-3 font-serif text-2xl">{{ $mission->titre }}</h2>
                                <p class="mt-2 text-sm leading-6 text-[#6F6862]">{{ Str::limit($mission->description, 180) }}</p>
                            </div>
                            <div class="text-left lg:text-right">
                                <p class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Budget</p>
                                <p class="mt-1 font-serif text-2xl text-[#8b1e1e]">{{ $mission->budget ? number_format($mission->budget, 2, ',', ' ') . ' DH' : 'À définir' }}</p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col gap-3 border-t border-[#e5dfd4] pt-5 sm:flex-row sm:flex-wrap sm:items-center">
                            @if($mission->statut === 'ouverte')
                                <a href="{{ route('missions.edit', $mission) }}" class="action-quiet">Modifier</a>
                            @endif

                            <a href="{{ route('missions.offres', $mission) }}" class="action-primary">Voir les offres</a>

                            @if($offreAcceptee && $offreAcceptee->prestataire)
                                <form method="POST" action="{{ route('conversations.store', $offreAcceptee->prestataire) }}">
                                    @csrf
                                    <button type="submit" class="action-quiet">Discuter ✉</button>
                                </form>
                            @endif

                            @if($mission->statut === 'en_cours')
                                <form method="POST" action="{{ route('missions.complete', $mission) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" onclick="return confirm('Confirmez-vous que les travaux sont terminés ?')" class="action-quiet !text-[#386047] w-full sm:w-auto">
                                        Valider la fin des travaux ✓
                                    </button>
                                </form>
                            @endif

                            @if($mission->statut === 'terminee' && $prestation)
                                <a href="{{ route('prestations.facture', $prestation) }}" class="action-quiet w-full sm:w-auto">
                                    Facture (PDF) ↓
                                </a>
                            @endif

                            @if($mission->statut === 'ouverte')
                                <form method="POST" action="{{ route('missions.destroy', $mission) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Voulez-vous vraiment supprimer cette mission ?')" class="min-h-11 w-full px-4 py-2 text-sm font-semibold text-[#8b1e1e] sm:w-auto">
                                        Supprimer
                                    </button>
                                </form>
                            @endif
                        </div>

                        @if($mission->statut === 'terminee')
                            @php
                                $aDejaEvalue = $mission->evaluations->where('client_id', auth()->id())->isNotEmpty();
                            @endphp
                            <div class="mt-6 border-t border-[#e5dfd4] pt-6">
                                @if($aDejaEvalue)
                                    <p class="text-sm font-semibold text-[#386047]">✓ Vous avez déjà évalué cette mission.</p>
                                @elseif(!$offreAcceptee)
                                    <p class="text-sm text-[#6F6862]">Cette mission ne peut pas encore être évaluée car aucun prestataire n'a été associé.</p>
                                @else
                                    <div class="surface-soft p-5 sm:p-6">
                                        <p class="eyebrow">Après le chantier</p>
                                        <h3 class="mt-2 font-serif text-xl">Évaluer le prestataire ({{ $offreAcceptee->prestataire->name }})</h3>
                                        <form method="POST" action="{{ route('evaluations.store', $mission) }}" class="mt-5 grid gap-4 sm:grid-cols-2">
                                            @csrf
                                            <div>
                                                <label for="note-{{ $mission->id }}" class="field-label">Note (sur 5)</label>
                                                <select id="note-{{ $mission->id }}" name="note" required class="field-control">
                                                    <option value="">Choisir une note</option>
                                                    @for($note = 5; $note >= 1; $note--)
                                                        <option value="{{ $note }}">{{ $note }} ★ ({{ $note }}/5)</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label for="commentaire-{{ $mission->id }}" class="field-label">Commentaire</label>
                                                <textarea id="commentaire-{{ $mission->id }}" name="commentaire" rows="3" maxlength="1000" class="field-control" placeholder="Donnez votre avis sur le travail réalisé..."></textarea>
                                            </div>
                                            <button type="submit" class="action-primary w-full sm:w-fit">Publier l’évaluation</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="surface p-10 text-center">
                    <p class="font-serif text-2xl">Aucune mission publiée.</p>
                    <p class="mt-2 text-sm text-[#6F6862]">Votre prochain projet mérite quelques lignes pour commencer.</p>
                    <a href="{{ route('missions.create') }}" class="action-primary mt-6">Décrire une mission</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
