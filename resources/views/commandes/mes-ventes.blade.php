{{-- resources/views/commandes/mes-ventes.blade.php --}}
<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mes ventes</h1>
                <p class="text-gray-500 text-sm mt-1">{{ $commandes->total() }} vente(s) au total</p>
            </div>
            <a href="{{ route('stripe.dashboard') }}"
               class="flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition">
                💳 Mes revenus
            </a>
        </div>

        @if($commandes->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
                <div class="text-5xl mb-4">💰</div>
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Aucune vente</h2>
                <p class="text-gray-400 text-sm mb-6">Vos futures ventes apparaîtront ici</p>
                <a href="{{ route('annonces.create') }}"
                   class="inline-block px-6 py-3 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 transition">
                    Déposer une annonce
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($commandes as $commande)
                    @php
                        $colors = [
                            'payee'          => 'bg-blue-100 text-blue-700',
                            'en_preparation' => 'bg-orange-100 text-orange-700',
                            'prete'          => 'bg-purple-100 text-purple-700',
                            'en_livraison'   => 'bg-indigo-100 text-indigo-700',
                            'livree'         => 'bg-teal-100 text-teal-700',
                            'terminee'       => 'bg-green-100 text-green-700',
                            'annulee'        => 'bg-red-100 text-red-600',
                        ][$commande->statut] ?? 'bg-gray-100 text-gray-600';
                    @endphp

                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $commande->acheteur->photo_profil_url }}"
                                     class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $commande->acheteur->nom_complet }}</div>
                                    <div class="text-xs text-gray-400">
                                        {{ $commande->numero }} · {{ $commande->created_at->format('d/m/Y à H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $colors }}">
                                    {{ ucfirst(str_replace('_',' ',$commande->statut)) }}
                                </span>
                                <div class="text-lg font-bold text-green-600 mt-1">
                                    +{{ number_format($commande->montant_vendeur, 2) }}€
                                </div>
                                <div class="text-xs text-gray-400">après commission</div>
                            </div>
                        </div>

                        {{-- Articles --}}
                        <div class="flex gap-2 mb-4">
                            @foreach($commande->lignes->take(4) as $ligne)
                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                    @if($ligne->photo_annonce)
                                        <img src="{{ asset('storage/'.$ligne->photo_annonce) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-lg">🥕</div>
                                    @endif
                                </div>
                            @endforeach
                            <div class="flex-1 flex flex-col justify-center">
                                <div class="text-sm text-gray-600">
                                    {{ $commande->lignes->pluck('titre_annonce')->join(', ') }}
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $commande->livraison?->label_mode ?? '—' }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <div class="flex gap-2">
                                @if(in_array($commande->statut, ['payee','en_preparation','prete','en_livraison']))
                                    <form action="{{ route('commandes.statut', $commande->id) }}" method="POST" class="flex gap-2">
                                        @csrf @method('PATCH')
                                        <select name="statut"
                                            onchange="this.form.submit()"
                                            class="text-xs px-3 py-1.5 border border-gray-200 rounded-lg bg-white focus:border-green-500 outline-none">
                                            @if($commande->statut === 'payee')
                                                <option value="payee" disabled selected>Statut actuel</option>
                                                <option value="en_preparation">→ En préparation</option>
                                            @elseif($commande->statut === 'en_preparation')
                                                <option value="en_preparation" disabled selected>En préparation</option>
                                                <option value="prete">→ Prête</option>
                                            @elseif($commande->statut === 'prete')
                                                <option value="prete" disabled selected>Prête</option>
                                                <option value="en_livraison">→ En livraison</option>
                                            @elseif($commande->statut === 'en_livraison')
                                                <option value="en_livraison" disabled selected>En livraison</option>
                                                <option value="livree">→ Livrée ✅</option>
                                            @endif
                                        </select>
                                    </form>
                                @endif
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('messages.show', ['annonce' => $commande->lignes->first()?->annonce_id, 'user' => $commande->acheteur_id]) }}"
                                   class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs text-gray-600 hover:border-green-400 hover:text-green-600 transition">
                                    💬 Message
                                </a>
                                <a href="{{ route('commandes.show', $commande->id) }}"
                                   class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs text-gray-600 hover:border-gray-300 transition">
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
