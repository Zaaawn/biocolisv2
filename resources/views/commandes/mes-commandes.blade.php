{{-- resources/views/commandes/mes-commandes.blade.php --}}
<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">

        <h1 class="text-2xl font-bold text-gray-900 mb-6">Mes commandes</h1>

        @if($commandes->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
                <div class="text-5xl mb-4">📦</div>
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Aucune commande</h2>
                <p class="text-gray-400 text-sm mb-6">Vous n'avez pas encore passé de commande</p>
                <a href="{{ route('annonces.index') }}"
                   class="inline-block px-6 py-3 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 transition">
                    Découvrir les annonces
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($commandes as $commande)
                    @php
                        $statutConfig = [
                            'en_attente'       => ['bg-gray-100', 'text-gray-600', '⏳'],
                            'paiement_en_cours'=> ['bg-yellow-100', 'text-yellow-700', '💳'],
                            'payee'            => ['bg-blue-100', 'text-blue-700', '✓'],
                            'en_preparation'   => ['bg-orange-100', 'text-orange-700', '👨‍🍳'],
                            'prete'            => ['bg-purple-100', 'text-purple-700', '📦'],
                            'en_livraison'     => ['bg-indigo-100', 'text-indigo-700', '🚴'],
                            'livree'           => ['bg-teal-100', 'text-teal-700', '✅'],
                            'terminee'         => ['bg-green-100', 'text-green-700', '🌟'],
                            'annulee'          => ['bg-red-100', 'text-red-600', '✗'],
                            'remboursee'       => ['bg-red-50', 'text-red-500', '↩️'],
                        ][$commande->statut] ?? ['bg-gray-100', 'text-gray-600', '?'];
                    @endphp

                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $commande->numero }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $commande->created_at->format('d/m/Y à H:i') }} ·
                                    Vendeur : {{ $commande->vendeur->nom_complet }}
                                </div>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $statutConfig[0] }} {{ $statutConfig[1] }}">
                                {{ $statutConfig[2] }}
                                {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                            </span>
                        </div>

                        {{-- Articles --}}
                        <div class="flex gap-2 mb-4 overflow-x-auto">
                            @foreach($commande->lignes->take(4) as $ligne)
                                <div class="flex-shrink-0 w-12 h-12 rounded-xl overflow-hidden bg-gray-100">
                                    @if($ligne->photo_annonce)
                                        <img src="{{ asset('storage/' . $ligne->photo_annonce) }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300 text-lg">🥕</div>
                                    @endif
                                </div>
                            @endforeach
                            @if($commande->lignes->count() > 4)
                                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-xs text-gray-500 font-medium">
                                    +{{ $commande->lignes->count() - 4 }}
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                {{ $commande->lignes->count() }} article(s) ·
                                <span class="font-bold text-gray-900">{{ number_format($commande->total_ttc, 2) }}€</span>
                            </div>
                            <div class="flex gap-2">
                                @if($commande->estAnnulable() && $commande->statut === 'payee')
                                    <form action="{{ route('commandes.annuler', $commande->id) }}" method="POST"
                                          onsubmit="return confirm('Annuler cette commande ?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-3 py-1.5 border border-red-200 text-red-500 rounded-lg text-xs hover:bg-red-50 transition">
                                            Annuler
                                        </button>
                                    </form>
                                @endif

                                @if($commande->statut === 'livree')
                                    <form action="{{ route('commandes.confirmer-reception', $commande->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs hover:bg-green-700 transition">
                                            Confirmer réception
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('commandes.show', $commande->id) }}"
                                   class="px-3 py-1.5 border border-gray-200 text-gray-700 rounded-lg text-xs hover:border-gray-300 transition">
                                    Détails →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $commandes->links() }}</div>
        @endif
    </div>
</x-app-layout>
