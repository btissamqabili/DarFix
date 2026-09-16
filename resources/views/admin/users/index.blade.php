<x-app-layout>
    <div class="page-frame">
        <div class="border-b border-[#e5dfd4] pb-8">
            <p class="eyebrow">Administration · réseau</p>
            <h1 class="display-title mt-3">Utilisateurs</h1>
            <p class="mt-3 text-sm text-[#6F6862]">Les personnes qui donnent vie à DarFix.</p>
        </div>

        @if(session('success'))
            <div class="notice-success mt-6">{{ session('success') }}</div>
        @endif

        <div class="surface mt-8 overflow-hidden">
            <div class="hidden overflow-x-auto md:block">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Téléphone</th>
                            <th>Inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="font-semibold">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="status-pill {{ $user->role === 'prestataire' ? 'status-open' : 'status-progress' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->telephone ?? '-' }}</td>
                                <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                                <td>
                                    <div class="flex gap-3">
                                        <a href="{{ route('admin.users.show', $user) }}" class="font-semibold text-[#8b1e1e]">Voir</a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')" class="font-semibold text-[#8b1e1e]">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-[#6F6862]">Aucun utilisateur trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="md:hidden">
                @forelse($users as $user)
                    <article class="list-row">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="font-serif text-xl">{{ $user->name }}</h2>
                                <p class="mt-1 text-sm text-[#6F6862]">{{ $user->email }}</p>
                            </div>
                            <span class="status-pill {{ $user->role === 'prestataire' ? 'status-open' : 'status-progress' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                        <div class="mt-5 flex flex-wrap gap-4 text-sm">
                            <a href="{{ route('admin.users.show', $user) }}" class="font-semibold text-[#8b1e1e]">Voir le profil →</a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')" class="font-semibold text-[#8b1e1e]">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="p-10 text-center text-[#6F6862]">Aucun utilisateur trouvé.</div>
                @endforelse
            </div>
        </div>

        <div class="mt-6">{{ $users->links() }}</div>
    </div>
</x-app-layout>
