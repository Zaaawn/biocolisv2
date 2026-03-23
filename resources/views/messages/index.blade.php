{{-- resources/views/messages/index.blade.php --}}
<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Messages
                @if($totalNonLus > 0)
                    <span class="ml-2 text-sm font-normal bg-green-500 text-white px-2 py-0.5 rounded-full">
                        {{ $totalNonLus }} non lu{{ $totalNonLus > 1 ? 's' : '' }}
                    </span>
                @endif
            </h1>
        </div>

        @if($conversations->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
                <div class="text-5xl mb-4">💬</div>
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Aucun message</h2>
                <p class="text-gray-400 text-sm mb-6">Contactez un vendeur depuis une annonce pour démarrer une conversation</p>
                <a href="{{ route('annonces.index') }}"
                   class="inline-block px-6 py-3 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 transition">
                    Parcourir les annonces
                </a>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden divide-y divide-gray-50">
                @foreach($conversations as $conv)
                    @php
                        $autreParticipant = $conv->autre_participant;
                        $nonLus = $conv->non_lus_pour_moi;
                        $dernier = $conv->dernierMessage;
                    @endphp

                    <a href="{{ route('messages.show', ['annonce' => $conv->annonce_id, 'user' => $autreParticipant->id]) }}"
                       class="flex items-center gap-4 p-4 hover:bg-gray-50 transition {{ $nonLus > 0 ? 'bg-green-50/50' : '' }}">

                        {{-- Avatar --}}
                        <div class="relative flex-shrink-0">
                            <img src="{{ $autreParticipant->photo_profil_url }}"
                                 alt="{{ $autreParticipant->nom_complet }}"
                                 class="w-12 h-12 rounded-full object-cover">
                            @if($nonLus > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 text-white text-xs rounded-full flex items-center justify-center font-bold">
                                    {{ $nonLus > 9 ? '9+' : $nonLus }}
                                </span>
                            @endif
                        </div>

                        {{-- Contenu --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline mb-0.5">
                                <span class="font-semibold text-gray-900 text-sm {{ $nonLus > 0 ? 'font-bold' : '' }}">
                                    {{ $autreParticipant->nom_complet }}
                                </span>
                                <span class="text-xs text-gray-400 flex-shrink-0 ml-2">
                                    {{ $conv->dernier_message_at?->diffForHumans(short: true) }}
                                </span>
                            </div>

                            {{-- Annonce --}}
                            <div class="text-xs text-green-600 truncate mb-0.5">
                                🌱 {{ $conv->annonce?->titre }}
                            </div>

                            {{-- Dernier message --}}
                            <p class="text-sm text-gray-500 truncate {{ $nonLus > 0 ? 'text-gray-800 font-medium' : '' }}">
                                @if($dernier)
                                    @if($dernier->sender_id === Auth::id())
                                        <span class="text-gray-400">Vous : </span>
                                    @endif
                                    @if($dernier->type === 'image')
                                        📷 Photo
                                    @elseif($dernier->type === 'systeme')
                                        🔔 {{ $dernier->systeme_type }}
                                    @else
                                        {{ Str::limit($dernier->contenu, 60) }}
                                    @endif
                                @else
                                    Nouvelle conversation
                                @endif
                            </p>
                        </div>

                        {{-- Photo annonce --}}
                        @if($conv->annonce?->photos)
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                <img src="{{ asset('storage/' . $conv->annonce->photos[0]) }}"
                                     class="w-full h-full object-cover">
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="mt-4">{{ $conversations->links() }}</div>
        @endif
    </div>
</x-app-layout>
