<x-app-layout>
    <div class="page-frame">
        <div class="flex flex-col gap-4 border-b border-[#e5dfd4] pb-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="eyebrow">Votre activité</p>
                <h1 class="display-title mt-3">Notifications</h1>
                <p class="mt-3 text-sm leading-6 text-[#6F6862]">Les événements importants liés à vos missions, offres et messages.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm text-[#6F6862]">{{ $notifications->count() }} notification(s)</span>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <a href="{{ route('notifications.readAll') }}" class="action-primary">Tout marquer comme lu</a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="notice-success mt-6">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="notice-error mt-6">{{ session('error') }}</div>
        @endif

        @if(auth()->user()->unreadNotifications->count() > 0)
            <div class="surface-soft mt-6 p-5">
                <p class="font-semibold text-[#8b1e1e]">{{ auth()->user()->unreadNotifications->count() }} notification(s) non lue(s)</p>
                <p class="mt-1 text-sm text-[#6F6862]">Cliquez sur une notification pour accéder directement à la page associée.</p>
            </div>
        @endif

        <div class="mt-8 space-y-4">
            @forelse($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $type = $notification->type;
                    $title = match($type) {
                        'App\\Notifications\\NewMessageNotification' => 'Nouveau message',
                        'App\\Notifications\\NouvelleOffreNotification' => 'Nouvelle offre reçue',
                        'App\\Notifications\\OffreAcceptedNotification' => 'Offre acceptée',
                        'App\\Notifications\\NouvelleMissionNotification' => 'Nouvelle mission disponible',
                        'App\\Notifications\\NouvelleEvaluationNotification' => 'Nouvelle évaluation reçue',
                        default => 'Notification'
                    };
                @endphp
                <div class="surface p-5 sm:p-7 {{ $isUnread ? 'border-l-4 border-l-[#8b1e1e]' : '' }}">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <p class="eyebrow">{{ $title }}</p>
                                @if($isUnread)
                                    <span class="status-pill status-cancelled">Non lue</span>
                                @else
                                    <span class="status-pill bg-[#f1eee8] text-[#6F6862]">Lue</span>
                                @endif
                            </div>

                            @if($type === 'App\\Notifications\\NewMessageNotification')
                                <p class="mt-3 text-sm text-[#6F6862]">De : <strong class="text-[#2F2926]">{{ $notification->data['sender_name'] ?? 'Utilisateur' }}</strong></p>
                            @endif

                            <p class="mt-2 text-sm leading-6 text-[#2F2926]">{{ $notification->data['message'] ?? 'Vous avez une notification.' }}</p>

                            @if($type === 'App\\Notifications\\NouvelleMissionNotification' && isset($notification->data['titre']))
                                <p class="mt-2 text-sm text-[#6F6862]">Mission : <strong class="text-[#2F2926]">{{ $notification->data['titre'] }}</strong></p>
                            @endif

                            @if(isset($notification->data['budget']))
                                <p class="mt-1 text-sm text-[#6F6862]">Budget : <strong class="text-[#2F2926]">{{ $notification->data['budget'] }} DH</strong></p>
                            @endif

                            @if($type === 'App\\Notifications\\NouvelleEvaluationNotification' && isset($notification->data['note']))
                                <p class="mt-1 text-sm text-[#6F6862]">Note reçue : <strong class="text-[#8b1e1e]">{{ $notification->data['note'] }}/5</strong></p>
                            @endif

                            <p class="mt-4 text-xs text-[#6F6862]">{{ $notification->created_at->format('d/m/Y à H:i') }}</p>
                        </div>

                        <div class="flex items-center gap-3 shrink-0 sm:self-center">
                            <a href="{{ route('notifications.read', $notification->id) }}" class="action-quiet w-full sm:w-auto">
                                Consulter <span class="ml-2">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="surface p-10 text-center">
                    <p class="font-serif text-2xl">Aucune notification</p>
                    <p class="mt-2 text-sm text-[#6F6862]">Votre fil d’activité est calme pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
