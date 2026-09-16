<x-app-layout>
    <div class="page-frame">
        <div class="mx-auto max-w-4xl">
            <div class="border-b border-[#e5dfd4] pb-8">
                <p class="eyebrow">Administration · profil</p>
                <h1 class="display-title mt-3">{{ $user->name }}</h1>
                <p class="mt-3 text-sm text-[#6F6862]">Fiche membre du réseau DarFix.</p>
            </div>
            <div class="surface mt-8 p-6 sm:p-9">
                <div class="grid gap-8 sm:grid-cols-2">
                    <div>
                        <p class="eyebrow">Identité</p>
                        <dl class="mt-5 space-y-4">
                            <div>
                                <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Nom</dt>
                                <dd class="mt-1 font-semibold">{{ $user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Email</dt>
                                <dd class="mt-1 break-words font-semibold">{{ $user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Rôle</dt>
                                <dd class="mt-1">
                                    <span class="status-pill {{ $user->role === 'prestataire' ? 'status-open' : 'status-progress' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <p class="eyebrow">Contact</p>
                        <dl class="mt-5 space-y-4">
                            <div>
                                <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Téléphone</dt>
                                <dd class="mt-1 font-semibold">{{ $user->telephone ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Adresse</dt>
                                <dd class="mt-1 font-semibold">{{ $user->adresse ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Inscrit le</dt>
                                <dd class="mt-1 font-semibold">{{ $user->created_at?->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @if($user->role === 'prestataire')
                    <div class="mt-8 border-t border-[#e5dfd4] pt-8">
                        <p class="eyebrow">Savoir-faire professionnel</p>
                        <dl class="mt-5 grid gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Description</dt>
                                <dd class="mt-1 leading-6">{{ $user->description ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Compétences</dt>
                                <dd class="mt-1 leading-6">{{ $user->competences ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Expérience</dt>
                                <dd class="mt-1 leading-6">{{ $user->experience ?? '-' }} ans</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-[0.12em] text-[#6F6862]">Disponibilité</dt>
                                <dd class="mt-1 leading-6">{{ $user->disponibilite ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                @endif

                <div class="mt-8 border-t border-[#e5dfd4] pt-6 flex flex-wrap gap-4">
                    <a href="{{ route('admin.users.index') }}" class="action-quiet">← Retour aux utilisateurs</a>
                    @if($user->role === 'prestataire')
                        <a href="{{ route('prestataires.show', $user->id) }}" class="action-quiet">
                            Voir le profil public →
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
