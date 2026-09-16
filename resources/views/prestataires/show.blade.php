<x-app-layout>
    <div class="page-frame">
        <div class="mx-auto max-w-4xl">
            <div class="surface overflow-hidden">
                <div class="relative bg-[#8b1e1e] p-6 text-white sm:p-9">
                    <div class="absolute inset-0 bg-[url('/images/heroes/artisan-workshop.jpg')] bg-cover bg-center opacity-25"></div>
                    <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                            <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full border-4 border-[#C9CAAC] bg-[#efeee2] font-serif text-3xl text-[#8b1e1e]">
                                @if($prestataire->photo)
                                    <img src="{{ asset('storage/' . $prestataire->photo) }}" alt="Photo de {{ $prestataire->name }}" class="h-full w-full object-cover">
                                @else
                                    {{ strtoupper(substr($prestataire->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <p class="eyebrow !text-[#C9CAAC]">Profil prestataire</p>
                                    @if(auth()->id() === $prestataire->id)
                                        <span class="status-pill bg-white/20 text-white">Votre profil public</span>
                                    @endif
                                </div>
                                <h1 class="mt-2 font-serif text-3xl">{{ $prestataire->name }}</h1>
                                @if($prestataire->description)
                                    <p class="mt-3 max-w-2xl text-sm leading-6 text-white/80">{{ $prestataire->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0 sm:self-center">
                            @if(auth()->user()->role === 'client')
                                <form method="POST" action="{{ route('conversations.store', $prestataire) }}">
                                    @csrf
                                    <button type="submit" class="action-primary !bg-[#efeee2] !text-[#8b1e1e] hover:!bg-white">
                                        Envoyer un message <span class="ml-2">✉</span>
                                    </button>
                                </form>
                            @elseif(auth()->id() === $prestataire->id)
                                <a href="{{ route('profile.edit') }}" class="action-quiet !border-white/40 !text-white hover:!bg-white/10">
                                    Modifier mon profil
                                </a>
                            @elseif(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.users.show', $prestataire) }}" class="action-quiet !border-white/40 !text-white hover:!bg-white/10">
                                    Fiche admin →
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid gap-5 p-6 sm:grid-cols-3 sm:p-9">
                    <div>
                        <p class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Compétences</p>
                        <p class="mt-2 text-sm leading-6">{{ $prestataire->competences ?: 'Non renseignées' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Expérience</p>
                        <p class="mt-2 font-serif text-2xl">{{ $prestataire->experience ?: '—' }} <span class="text-sm">{{ $prestataire->experience ? 'ans' : '' }}</span></p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Disponibilité</p>
                        <p class="mt-2 text-sm leading-6">{{ $prestataire->disponibilite ?: 'À confirmer' }}</p>
                    </div>
                </div>
            </div>

            @php
                $moyenne = $prestataire->evaluationsRecues->avg('note');
                $nombreEvaluations = $prestataire->evaluationsRecues->count();
            @endphp
            <div class="surface mt-8 p-6 sm:p-9">
                <div class="flex flex-col gap-4 border-b border-[#e5dfd4] pb-6 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="eyebrow">La confiance se construit</p>
                        <h2 class="mt-2 font-serif text-2xl">Évaluations reçues</h2>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="font-serif text-3xl text-[#8b1e1e]">{{ $moyenne ? number_format($moyenne, 1) : '0.0' }} <span class="text-base text-[#6F6862]">/ 5</span></p>
                        <p class="mt-1 text-sm text-[#6F6862]">{{ $nombreEvaluations }} {{ $nombreEvaluations > 1 ? 'évaluations' : 'évaluation' }}</p>
                    </div>
                </div>

                <div class="mt-6 space-y-5">
                    @forelse($prestataire->evaluationsRecues as $evaluation)
                        <article class="border-b border-[#e5dfd4] pb-5 last:border-0">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold">{{ $evaluation->client->name }}</p>
                                    <p class="mt-1 text-xs text-[#6F6862]">{{ $evaluation->created_at->format('d/m/Y') }}</p>
                                </div>
                                <span class="font-serif text-lg text-[#8b1e1e]">{{ $evaluation->note }}/5 ★</span>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-[#6F6862]">{{ $evaluation->commentaire ?: 'Aucun commentaire.' }}</p>
                        </article>
                    @empty
                        <div class="py-8 text-center text-sm text-[#6F6862]">
                            Aucune évaluation pour le moment.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ url()->previous() }}" class="action-quiet">← Retour</a>
            </div>
        </div>
    </div>
</x-app-layout>
