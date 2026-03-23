{{-- resources/views/notifications/index.blade.php --}}
<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $notifications->total() }} notification(s)</p>
            </div>
            @if(auth()->user()->unreadNotifications()->count() > 0)
                <form action="{{ route('notifications.tout-lire') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="text-sm text-green-600 hover:underline font-medium">
                        Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>

        @if($notifications->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
                <div class="text-5xl mb-4">🔔</div>
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Aucune notification</h2>
                <p class="text-gray-400 text-sm">Vos notifications apparaîtront ici</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach($notifications as $notif)
                    @php
                        $icones = [
                            'nouvelle_vente'   => ['🛒', 'bg-green-50 border-green-200'],
                            'commande_payee'   => ['✅', 'bg-blue-50 border-blue-200'],
                            'commande_livree'  => ['📦', 'bg-teal-50 border-teal-200'],
                            'statut_commande'  => ['📋', 'bg-orange-50 border-orange-200'],
                            'nouveau_message'  => ['💬', 'bg-indigo-50 border-indigo-200'],
                            'nouvel_avis'      => ['⭐', 'bg-yellow-50 border-yellow-200'],
                            'annonce_likee'    => ['❤️', 'bg-red-50 border-red-200'],
                        ][$notif->data['type'] ?? ''] ?? ['🔔', 'bg-gray-50 border-gray-200'];
                    @endphp

                    <div class="bg-white rounded-2xl border {{ $notif->read_at ? 'border-gray-100' : 'border-green-200 bg-green-50/30' }} p-4 flex items-start gap-4">

                        {{-- Icône --}}
                        <div class="w-10 h-10 rounded-xl {{ $icones[1] }} border flex items-center justify-center text-xl flex-shrink-0">
                            {{ $icones[0] }}
                        </div>

                        {{-- Contenu --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="font-semibold text-gray-900 text-sm">
                                        {{ $notif->data['titre'] ?? '' }}
                                        @if(!$notif->read_at)
                                            <span class="ml-2 inline-block w-2 h-2 bg-green-500 rounded-full"></span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mt-0.5">{{ $notif->data['message'] ?? '' }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                                <form action="{{ route('notifications.supprimer', $notif->id) }}" method="POST" class="flex-shrink-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-300 hover:text-red-400 transition">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            @if(isset($notif->data['url']))
                                <a href="{{ route('notifications.lire', $notif->id) }}"
                                   class="inline-block mt-2 text-xs text-green-600 hover:underline font-medium">
                                    Voir les détails →
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $notifications->links() }}</div>
        @endif
    </div>
</x-app-layout>
