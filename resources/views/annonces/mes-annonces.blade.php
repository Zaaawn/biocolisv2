{{-- resources/views/annonces/mes-annonces.blade.php --}}
<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mes annonces</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $annonces->total() }} annonce(s) au total</p>
            </div>
            <a href="{{ route('annonces.create') }}"
               class="flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle annonce
            </a>
            
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        @if($annonces->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
                <div class="text-5xl mb-4">📢</div>
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Aucune annonce</h2>
                <p class="text-gray-400 text-sm mb-6">Partagez vos fruits et légumes avec votre communauté</p>
                <a href="{{ route('annonces.create') }}"
                   class="inline-block px-6 py-3 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 transition">
                    Déposer une annonce
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($annonces as $annonce)
                    @php
                        $statutConfig = [
                            'disponible'  => ['bg-green-100', 'text-green-700', '✅'],
                            'brouillon'   => ['bg-gray-100',  'text-gray-600',  '📝'],
                            'reservee'    => ['bg-blue-100',  'text-blue-700',  '🔒'],
                            'vendue'      => ['bg-purple-100','text-purple-700','✓'],
                            'expiree'     => ['bg-red-100',   'text-red-600',   '⏰'],
                            'desactivee'  => ['bg-gray-100',  'text-gray-500',  '🚫'],
                        ][$annonce->statut] ?? ['bg-gray-100', 'text-gray-600', '?'];
                    @endphp

                    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex gap-4 items-center
                                {{ $annonce->trashed() ? 'opacity-50' : '' }}">

                        {{-- Photo --}}
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                            <img src="{{ $annonce->premiere_photo }}"
                                 class="w-full h-full object-cover">
                        </div>

                        {{-- Infos --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-900 text-sm truncate">{{ $annonce->titre }}</span>
                                <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-full font-medium {{ $statutConfig[0] }} {{ $statutConfig[1] }}">
                                    {{ $statutConfig[2] }} {{ ucfirst($annonce->statut) }}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-3 text-xs text-gray-400">
                                <span>💶 {{ number_format($annonce->prix, 2) }}€ / {{ $annonce->unite_prix }}</span>
                                <span>📦 {{ $annonce->quantite_disponible }} {{ $annonce->unite_prix }}</span>
                                <span>👁 {{ $annonce->nb_vues }} vues</span>
                                <span>❤️ {{ $annonce->nb_likes }} likes</span>
                                <span>📅 {{ $annonce->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if(!$annonce->trashed())
                                <a href="{{ route('annonces.show', $annonce->slug) }}"
                                   class="p-2 text-gray-400 hover:text-green-600 transition" title="Voir">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('annonces.edit', $annonce->slug) }}"
                                   class="p-2 text-gray-400 hover:text-blue-600 transition" title="Modifier">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('annonces.destroy', $annonce->slug) }}" method="POST"
                                      onsubmit="return confirm('Supprimer cette annonce ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition" title="Supprimer">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 italic">Supprimée</span>
                            @endif
                            <a href="{{ route('options.boost', $annonce->slug) }}"
   class="text-xs text-green-600 hover:underline font-medium">
    🚀 Booster
</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $annonces->links() }}</div>
        @endif
    </div>
</x-app-layout>
