{{-- resources/views/dashboard/particulier.blade.php --}}
<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-8">

        {{-- Bienvenue --}}
        <div class="flex items-center gap-4 mb-8">
            <img src="{{ $user->photo_profil_url }}" class="w-14 h-14 rounded-full object-cover border-2 border-green-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bonjour {{ $user->prenom }} 👋</h1>
                <p class="text-gray-400 text-sm">Bienvenue sur votre espace Biocolis</p>
            </div>
        </div>

        {{-- Bannière Stripe IBAN --}}
        @if($mes_annonces->isNotEmpty() && !auth()->user()->hasStripeAccount() && empty($user->iban))
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">💳</span>
                    <div>
                        <div class="font-semibold text-amber-900 text-sm">Renseignez votre IBAN pour recevoir vos paiements</div>
                        <div class="text-xs text-amber-700 mt-0.5">Vous avez des annonces actives mais pas encore d'IBAN enregistré.</div>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}#iban"
                   class="flex-shrink-0 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-xs transition whitespace-nowrap">
                    Ajouter mon IBAN →
                </a>
            </div>
        @endif

        {{-- Avis à laisser --}}
        @if($avis_a_laisser->isNotEmpty())
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6">
                <h2 class="font-semibold text-amber-800 mb-3 flex items-center gap-2">
                    ⭐ Laissez un avis
                    <span class="text-xs bg-amber-200 text-amber-800 px-2 py-0.5 rounded-full">{{ $avis_a_laisser->count() }}</span>
                </h2>
                <div class="space-y-2">
                    @foreach($avis_a_laisser as $commande)
                        <div class="bg-white rounded-xl p-3 flex items-center justify-between gap-3">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $commande->vendeur->nom_complet }}</div>
                                <div class="text-xs text-gray-400">{{ $commande->lignes->first()?->titre_annonce }}</div>
                            </div>
                            <a href="{{ route('commandes.show', $commande->id) }}#avis"
                               class="flex-shrink-0 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold transition">
                                Noter →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
                <div class="text-2xl mb-1">💰</div>
                <div class="text-xl font-bold text-green-600">{{ number_format($stats['total_gagne'], 2) }}€</div>
                <div class="text-xs text-gray-400">total gagné</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
                <div class="text-2xl mb-1">🛒</div>
                <div class="text-xl font-bold text-gray-900">{{ $stats['nb_ventes'] }}</div>
                <div class="text-xs text-gray-400">ventes</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
                <div class="text-2xl mb-1">📢</div>
                <div class="text-xl font-bold text-gray-900">{{ $stats['nb_annonces'] }}</div>
                <div class="text-xs text-gray-400">annonces actives</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center">
                <div class="text-2xl mb-1">⏳</div>
                <div class="text-xl font-bold text-orange-500">{{ number_format($stats['solde_en_attente'], 2) }}€</div>
                <div class="text-xs text-gray-400">en attente</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">

                {{-- Ventes récentes --}}
                @if($ventes_recentes->isNotEmpty())
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="font-semibold text-gray-900">Mes ventes récentes</h2>
                            <a href="{{ route('stripe.dashboard') }}" class="text-xs text-green-600 hover:underline">Tout voir →</a>
                        </div>
                        <div class="space-y-3">
                            @foreach($ventes_recentes as $commande)
                                <a href="{{ route('commandes.show', $commande->id) }}"
                                   class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $commande->numero }}</div>
                                        <div class="text-xs text-gray-400">{{ $commande->acheteur->prenom }} · {{ $commande->created_at->format('d/m/Y') }}</div>
                                    </div>
                                    <div class="text-sm font-bold text-green-600">
                                        +{{ number_format($commande->montant_vendeur, 2) }}€
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Commandes récentes (achats) --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-semibold text-gray-900">Mes achats récents</h2>
                        <a href="{{ route('commandes.mes-commandes') }}" class="text-xs text-green-600 hover:underline">Tout voir →</a>
                    </div>
                    @if($commandes_recentes->isEmpty())
                        <div class="text-center py-8 text-gray-400">
                            <div class="text-3xl mb-2">📦</div>
                            <p class="text-sm">Aucun achat</p>
                            <a href="{{ route('annonces.index') }}"
                               class="inline-block mt-3 px-4 py-2 bg-green-600 text-white rounded-xl text-xs font-semibold hover:bg-green-700 transition">
                                Parcourir les annonces
                            </a>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($commandes_recentes as $commande)
                                @php
                                    $colors = ['payee'=>'text-blue-600','en_preparation'=>'text-orange-600','prete'=>'text-purple-600','en_livraison'=>'text-indigo-600','livree'=>'text-teal-600','terminee'=>'text-green-600','annulee'=>'text-red-500'][$commande->statut] ?? 'text-gray-500';
                                @endphp
                                <a href="{{ route('commandes.show', $commande->id) }}"
                                   class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition">
                                    <div class="flex gap-1">
                                        @foreach($commande->lignes->take(2) as $l)
                                            <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100">
                                                @if($l->photo_annonce)
                                                    <img src="{{ asset('storage/'.$l->photo_annonce) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-lg">🥕</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ $commande->numero }}</div>
                                        <div class="text-xs {{ $colors }}">{{ ucfirst(str_replace('_',' ',$commande->statut)) }}</div>
                                    </div>
                                    <div class="text-sm font-bold text-gray-900">{{ number_format($commande->total_ttc, 2) }}€</div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Colonne droite --}}
            <div class="space-y-4">
                {{-- Actions rapides --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h2 class="font-semibold text-gray-900 mb-3">Actions rapides</h2>
                    <div class="space-y-2">
                        <a href="{{ route('annonces.create') }}"
                           class="flex items-center gap-3 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition">
                            <span>📢</span> Déposer une annonce
                        </a>
                        <a href="{{ route('stripe.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 border border-gray-200 hover:border-green-400 rounded-xl text-sm font-medium text-gray-700 transition">
                            <span>💰</span> Mes revenus
                        </a>
                        <a href="{{ route('panier.index') }}"
                           class="flex items-center gap-3 px-4 py-3 border border-gray-200 hover:border-green-400 rounded-xl text-sm font-medium text-gray-700 transition">
                            <span>🛒</span> Mon panier
                            @if($nb_panier > 0)
                                <span class="ml-auto bg-green-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">{{ $nb_panier }}</span>
                            @endif
                        </a>
                        <a href="{{ route('messages.index') }}"
                           class="flex items-center gap-3 px-4 py-3 border border-gray-200 hover:border-green-400 rounded-xl text-sm font-medium text-gray-700 transition">
                            <span>💬</span> Messages
                            @if($nb_messages > 0)
                                <span class="ml-auto bg-green-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">{{ $nb_messages }}</span>
                            @endif
                        </a>
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-3 px-4 py-3 border border-gray-200 hover:border-gray-300 rounded-xl text-sm font-medium text-gray-700 transition">
                            <span>⚙️</span> Mon profil
                        </a>
                    </div>
                </div>

                {{-- Mes annonces --}}
                @if($mes_annonces->isNotEmpty())
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <div class="flex justify-between mb-3">
                            <h2 class="font-semibold text-gray-900">Mes annonces</h2>
                            <a href="{{ route('annonces.mes-annonces') }}" class="text-xs text-green-600 hover:underline">Tout voir</a>
                        </div>
                        <div class="space-y-2">
                            @foreach($mes_annonces as $annonce)
                                <a href="{{ route('annonces.show', $annonce->slug) }}"
                                   class="flex items-center gap-3 hover:bg-gray-50 p-2 rounded-xl transition">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                        <img src="{{ $annonce->premiere_photo }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $annonce->titre }}</div>
                                        <div class="text-xs text-gray-400">{{ number_format($annonce->prix, 2) }}€ / {{ $annonce->unite_prix }}</div>
                                    </div>
                                    <div class="text-xs text-gray-400">{{ $annonce->nb_vues }} 👁</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
