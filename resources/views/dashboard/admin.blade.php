<x-app-layout>
    <div class="page-frame">
        <section class="flex flex-col justify-between gap-8 border-b border-[#e5dfd4] pb-10 lg:flex-row lg:items-end">
            <div>
                <p class="eyebrow">DarFix · Poste de pilotage</p>
                <h1 class="display-title mt-4">Faire grandir la confiance, une mission à la fois.</h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-[#6F6862]">
                    Supervision globale de la plateforme : utilisateurs enregistrés, projets en cours, offres modérées et retours de la communauté.
                </p>
            </div>
            <div class="text-left lg:text-right">
                <p class="text-sm text-[#6F6862]">Aujourd’hui</p>
                <p class="mt-1 font-serif text-2xl">{{ now()->format('d F Y') }}</p>
            </div>
        </section>

        <section class="grid gap-8 py-10 lg:grid-cols-[1fr_0.62fr]">
            <div class="surface p-7 sm:p-9">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="eyebrow">La plateforme en mouvement</p>
                        <h2 class="mt-2 font-serif text-2xl">Indicateurs clés</h2>
                    </div>
                    <span class="text-sm text-[#869B7E]">Vue générale</span>
                </div>
                <div class="mt-9 grid gap-8 sm:grid-cols-2">
                    <a href="{{ route('admin.users.index') }}" class="group block border-l-4 border-[#8b1e1e] pl-5 transition hover:bg-[#fcfbf8]">
                        <p class="text-sm text-[#6F6862] group-hover:text-[#8b1e1e]">Clients inscrits →</p>
                        <p class="mt-2 font-serif text-4xl text-[#2F2926]">{{ $nombreClients }}</p>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="group block border-l-4 border-[#869B7E] pl-5 transition hover:bg-[#fcfbf8]">
                        <p class="text-sm text-[#6F6862] group-hover:text-[#869B7E]">Prestataires actifs →</p>
                        <p class="mt-2 font-serif text-4xl text-[#2F2926]">{{ $nombrePrestataires }}</p>
                    </a>
                    <a href="{{ route('admin.missions.index') }}" class="group block border-l-4 border-[#C9CAAC] pl-5 transition hover:bg-[#fcfbf8]">
                        <p class="text-sm text-[#6F6862] group-hover:text-[#2F2926]">Missions publiées →</p>
                        <p class="mt-2 font-serif text-4xl text-[#2F2926]">{{ $nombreMissions }}</p>
                    </a>
                    <a href="{{ route('admin.offres.index') }}" class="group block border-l-4 border-[#2F2926] pl-5 transition hover:bg-[#fcfbf8]">
                        <p class="text-sm text-[#6F6862] group-hover:text-[#2F2926]">Offres échangées →</p>
                        <p class="mt-2 font-serif text-4xl text-[#2F2926]">{{ $nombreOffres }}</p>
                    </a>
                </div>
            </div>

            <div class="relative min-h-[300px] overflow-hidden bg-[#2F2926] p-7 text-white sm:p-9">
                <img src="{{ asset('images/heroes/admin-team.jpg') }}" alt="Équipe qui coordonne des projets" class="absolute inset-0 h-full w-full object-cover opacity-30">
                <div class="absolute inset-0 bg-[#2F2926]/85"></div>
                <div class="relative">
                    <p class="eyebrow !text-[#C9CAAC]">Qualité & Avis</p>
                    <p class="mt-6 font-serif text-3xl leading-tight">{{ $nombreEvaluations }} évaluations déposées.</p>
                    <p class="mt-5 text-sm leading-6 text-white/70">
                        Chaque retour renforce la qualité des rencontres et aide les meilleurs artisans à se démarquer.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('admin.evaluations.index') }}" class="inline-block border-b border-[#C9CAAC] pb-1 text-sm font-bold text-[#C9CAAC]">
                            Consulter les avis →
                        </a>
                        <a href="{{ route('admin.prestations.index') }}" class="inline-block border-b border-white/50 pb-1 text-sm font-bold text-white/90">
                            Suivre les prestations →
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="surface overflow-hidden">
            <div class="flex flex-col gap-3 border-b border-[#e5dfd4] p-6 sm:flex-row sm:items-end sm:justify-between sm:p-8">
                <div>
                    <p class="eyebrow">Le signal humain</p>
                    <h2 class="mt-2 font-serif text-2xl">Dernières évaluations de la communauté</h2>
                </div>
                <a href="{{ route('admin.evaluations.index') }}" class="text-sm font-bold text-[#8b1e1e]">
                    Ouvrir le registre complet →
                </a>
            </div>
            @forelse($evaluations as $evaluation)
                <div class="grid gap-4 border-b border-[#e5dfd4] p-6 last:border-0 sm:grid-cols-[0.8fr_0.8fr_1.5fr_0.35fr_0.5fr] sm:items-center sm:p-8">
                    <div>
                        <p class="text-xs uppercase tracking-[0.14em] text-[#869B7E]">Client</p>
                        <p class="mt-1 font-semibold">{{ $evaluation->client->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.14em] text-[#869B7E]">Prestataire</p>
                        <p class="mt-1 font-semibold">{{ $evaluation->prestataire->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.14em] text-[#869B7E]">Mission</p>
                        <p class="mt-1 text-sm text-[#6F6862]">{{ $evaluation->mission->titre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.14em] text-[#869B7E]">Note</p>
                        <p class="mt-1 font-serif text-xl text-[#8b1e1e]">{{ $evaluation->note }}/5 ★</p>
                    </div>
                    <div class="text-sm text-[#6F6862]">{{ $evaluation->created_at?->format('d/m/Y') }}</div>
                </div>
            @empty
                <div class="p-10 text-center font-serif text-xl">Aucune évaluation pour le moment.</div>
            @endforelse
        </section>
    </div>
</x-app-layout>
