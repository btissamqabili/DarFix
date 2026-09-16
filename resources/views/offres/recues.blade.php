<x-app-layout>
    <div class="page-frame">
        <div class="border-b border-[#e5dfd4] pb-8">
            <p class="eyebrow">Votre projet · propositions</p>
            <h1 class="display-title mt-3">Les offres reçues</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-[#6F6862]">Comparez les savoir-faire, les délais et les approches avant de choisir la personne qui fera avancer votre mission.</p>
            <div class="mt-5 flex flex-wrap items-center gap-3 text-sm text-[#6F6862]">
                <span class="font-semibold text-[#2F2926]">{{ $mission->titre }}</span>
                <span class="status-pill {{ $mission->statut === 'terminee' ? 'status-done' : ($mission->statut === 'en_cours' ? 'status-progress' : 'status-open') }}">
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

        <div class="mt-8 space-y-5">
            @forelse($offres as $offre)
                @php
                    $statusClass = match($offre->statut) {
                        'acceptee' => 'status-done',
                        'refusee' => 'status-cancelled',
                        default => 'status-progress'
                    };
                @endphp
                <article class="surface p-6 sm:p-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="eyebrow">Proposition de service</p>
                            <div class="mt-2 flex flex-wrap items-center gap-3">
                                <h2 class="font-serif text-2xl">{{ $offre->prestataire->name }}</h2>
                                <span class="status-pill {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $offre->statut)) }}
                                </span>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center gap-4">
                                <a href="{{ route('prestataires.show', $offre->prestataire->id) }}" class="text-sm font-bold text-[#8b1e1e] hover:underline">
                                    Voir le profil du prestataire →
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 border-y border-[#e5dfd4] py-4 lg:border-y-0 lg:border-l lg:pl-8">
                            <div>
                                <p class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Prix proposé</p>
                                <p class="mt-1 font-serif text-2xl text-[#8b1e1e]">{{ number_format($offre->prix_propose, 2, ',', ' ') }} DH</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Délai</p>
                                <p class="mt-1 font-serif text-2xl">{{ $offre->delai_execution ?? '-' }} <span class="text-sm">{{ $offre->delai_execution ? 'jours' : '' }}</span></p>
                            </div>
                        </div>
                    </div>

                    @if($offre->message)
                        <div class="mt-6 border-l-2 border-[#869B7E] pl-4">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#869B7E]">Son approche</p>
                            <p class="mt-2 text-sm leading-6 text-[#6F6862]">{{ $offre->message }}</p>
                        </div>
                    @endif

                    <div class="mt-6 flex flex-col gap-3 border-t border-[#e5dfd4] pt-5 sm:flex-row sm:items-center">
                        <form method="POST" action="{{ route('conversations.store', $offre->prestataire) }}">
                            @csrf
                            <button type="submit" class="action-quiet w-full sm:w-auto">
                                Discuter avec ce prestataire <span class="ml-1">✉</span>
                            </button>
                        </form>

                        @if($offre->statut === 'en_attente' && $mission->statut === 'ouverte')
                            <form method="POST" action="{{ route('offres.accept', $offre) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" onclick="return confirm('Voulez-vous accepter cette offre ? Cela créera une prestation et fermera les autres propositions.')" class="action-primary w-full sm:w-auto">
                                    Accepter l’offre
                                </button>
                            </form>

                            <form method="POST" action="{{ route('offres.refuse', $offre) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" onclick="return confirm('Voulez-vous refuser cette offre ?')" class="action-quiet !text-[#8b1e1e] w-full sm:w-auto">
                                    Refuser
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="surface p-10 text-center">
                    <p class="font-serif text-2xl">Aucune offre reçue pour le moment.</p>
                    <p class="mt-2 text-sm text-[#6F6862]">Les propositions des artisans et bricoleurs apparaîtront ici.</p>
                </div>
            @endforelse
        </div>

        @if($mission->statut === 'terminee')
            @php
                $offreAcceptee = $offres->firstWhere('statut', 'acceptee');
                $evaluationExiste = \App\Models\Evaluation::where('mission_id', $mission->id)->where('client_id', auth()->id())->exists();
                $prestation = \App\Models\Prestation::where('mission_id', $mission->id)->where('statut', 'terminee')->first();
            @endphp
            <div class="mt-8 flex flex-wrap gap-4">
                @if($prestation)
                    <a href="{{ route('prestations.facture', $prestation) }}" class="action-primary">
                        Télécharger la facture (PDF) ↓
                    </a>
                @endif
            </div>

            @if($offreAcceptee)
                <section class="surface-soft mt-8 p-6 sm:p-8">
                    <p class="eyebrow">Après le chantier</p>
                    <h2 class="mt-2 font-serif text-2xl">Évaluer {{ $offreAcceptee->prestataire->name }}</h2>
                    @if($evaluationExiste)
                        <div class="notice-success mt-5">Vous avez déjà évalué ce prestataire pour cette mission.</div>
                    @else
                        <form method="POST" action="{{ route('evaluations.store', $mission) }}" class="mt-6 grid gap-5 sm:grid-cols-2">
                            @csrf
                            <div>
                                <label for="note" class="field-label">Votre note (sur 5)</label>
                                <select id="note" name="note" required class="field-control">
                                    <option value="">Choisir une note</option>
                                    @for($note = 5; $note >= 1; $note--)
                                        <option value="{{ $note }}">{{ $note }} ★ ({{ $note }}/5)</option>
                                    @endfor
                                </select>
                                @error('note')<p class="mt-1 text-sm text-[#8b1e1e]">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="commentaire" class="field-label">Votre commentaire</label>
                                <textarea id="commentaire" name="commentaire" rows="4" maxlength="1000" class="field-control" placeholder="Parlez de la qualité du travail, de la ponctualité et du professionnalisme..."></textarea>
                                @error('commentaire')<p class="mt-1 text-sm text-[#8b1e1e]">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit" class="action-primary w-full sm:w-fit">Publier l’évaluation</button>
                        </form>
                    @endif
                </section>
            @endif
        @endif

        <div class="mt-8">
            <a href="{{ route('missions.index') }}" class="action-quiet">← Retour à mes missions</a>
        </div>
    </div>
</x-app-layout>
