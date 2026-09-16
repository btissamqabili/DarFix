@php
    $role = auth()->user()->role;
    $unreadNotificationsCount = auth()->user()->unreadNotifications->count();
    $links = match ($role) {
        'client' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
            ['label' => 'Mes missions', 'route' => 'missions.index', 'active' => 'missions.*'],
            ['label' => 'Messages', 'route' => 'conversations.index', 'active' => 'conversations.*'],
            ['label' => 'Notifications', 'route' => 'notifications.index', 'active' => 'notifications.*'],
        ],
        'prestataire' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
            ['label' => 'Mes services', 'route' => 'services.index', 'active' => 'services.*'],
            ['label' => 'Missions disponibles', 'route' => 'prestataire.missions.index', 'active' => 'prestataire.missions.*'],
            ['label' => 'Messages', 'route' => 'conversations.index', 'active' => 'conversations.*'],
            ['label' => 'Notifications', 'route' => 'notifications.index', 'active' => 'notifications.*'],
        ],
        'admin' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
            ['label' => 'Missions', 'route' => 'admin.missions.index', 'active' => 'admin.missions.*'],
            ['label' => 'Offres', 'route' => 'admin.offres.index', 'active' => 'admin.offres.*'],
            ['label' => 'Prestations', 'route' => 'admin.prestations.index', 'active' => 'admin.prestations.*'],
            ['label' => 'Catégories', 'route' => 'categories.index', 'active' => 'categories.*'],
            ['label' => 'Utilisateurs', 'route' => 'admin.users.index', 'active' => 'admin.users.*'],
            ['label' => 'Évaluations', 'route' => 'admin.evaluations.index', 'active' => 'admin.evaluations.*'],
            ['label' => 'Notifications', 'route' => 'notifications.index', 'active' => 'notifications.*'],
        ],
        default => [],
    };
@endphp

<nav x-data="{ open: false }" class="border-b border-[#e5dfd4] bg-[#f6f3eb]/95">
    <div class="mx-auto max-w-[1440px] px-5 sm:px-8 lg:px-12">
        <div class="flex min-h-[76px] items-center justify-between gap-8">
            <a href="{{ route('dashboard') }}" class="shrink-0 font-serif text-2xl font-bold tracking-[-0.04em] text-[#8b1e1e]">
                <span aria-label="DarFix" class="flex items-center gap-3">
                    <span aria-hidden="true" class="flex h-9 w-9 items-center justify-center rounded-sm bg-[#8b1e1e] text-lg text-[#F6F3EB]">⚒</span>
                    DarFix
                </span>
            </a>

            <div class="hidden items-center gap-1 sm:flex">
                @foreach($links as $link)
                    <a href="{{ route($link['route']) }}" class="border-b-2 px-3 py-3 text-sm font-semibold transition {{ request()->routeIs($link['active']) ? 'border-[#8b1e1e] text-[#8b1e1e]' : 'border-transparent text-[#6F6862] hover:text-[#8b1e1e]' }}">
                        {{ $link['label'] }}
                        @if($link['label'] === 'Notifications' && $unreadNotificationsCount > 0)
                            <span class="ml-1 inline-flex items-center justify-center rounded-full bg-[#8b1e1e] px-1.5 py-0.5 text-[10px] font-bold text-white leading-none">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="hidden items-center sm:flex">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 border-l border-[#e5dfd4] pl-5 text-sm font-semibold text-[#2F2926] transition hover:text-[#8b1e1e]">
                            {{ Auth::user()->name }}
                            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Se déconnecter
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <button @click="open = ! open" class="min-h-11 min-w-11 rounded-sm border border-[#e5dfd4] p-2 text-[#8b1e1e] sm:hidden" aria-label="Ouvrir le menu">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path :class="{'hidden': open, 'inline-flex': ! open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /><path :class="{'hidden': ! open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-[#e5dfd4] py-3 sm:hidden">
            @foreach($links as $link)
                <a href="{{ route($link['route']) }}" class="flex items-center justify-between min-h-11 px-2 py-3 text-sm font-semibold {{ request()->routeIs($link['active']) ? 'text-[#8b1e1e]' : 'text-[#6F6862]' }}">
                    <span>{{ $link['label'] }}</span>
                    @if($link['label'] === 'Notifications' && $unreadNotificationsCount > 0)
                        <span class="rounded-full bg-[#8b1e1e] px-2 py-0.5 text-xs text-white">
                            {{ $unreadNotificationsCount }}
                        </span>
                    @endif
                </a>
            @endforeach
            <a href="{{ route('profile.edit') }}" class="block px-2 py-3 text-sm font-semibold text-[#6F6862]">Profil</a>
            <form method="POST" action="{{ route('logout') }}" class="border-t border-[#e5dfd4] mt-2 pt-2">
                @csrf
                <button type="submit" class="block w-full text-left min-h-11 px-2 py-3 text-sm font-semibold text-[#8b1e1e]">
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>
</nav>
