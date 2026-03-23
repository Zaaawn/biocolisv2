{{-- resources/views/dashboard/vendeur.blade.php --}}
<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <img src="{{ $user->photo_profil_url }}" class="w-14 h-14 rounded-full object-cover border-2 border-green-100">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Espace vendeur</h1>
                    <p class="text-gray-400 text-sm">{{ $user->nom_complet }} · Producteur</p>
                </div>
            </div>
            @if(!$stripe_actif)
                <a href="{{ route('stripe.dashboard') }}"
                   class="flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold transition">
                    ⚠️ Activer Stripe
                </a>
            @else
                <a href="{{ route('stripe.dashboard') }}"
                   class="flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition">
                    💳 Mes revenus
                </a>
            @endif
        </div>

        {{-- ⚠️ Bannière Stripe si pas encore activé --}}
        @if(!$stripe_actif)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">💳</div>
                    <div>
                        <div class="font-semibold text-amber-900">Activez votre compte pour recevoir vos paiements</div>
                        <div class="text-sm text-amber-700 mt-0.5">
                            Sans compte Stripe actif, vos acheteurs peuvent commander mais vous ne recevrez pas l'argent.
                            L'activation prend 2 minutes.
                        </div>
                    </div>
                </div>
                <a href="{{ route('stripe.dashboard') }}"
                   class="flex-shrink-0 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-sm transition whitespace-nowrap">
                    Activer maintenant →
                </a>
            </div>
        @endif

        {{-- Stats financières --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="text-xs text-gray-400 mb-1">Total gagné</div>
                <div class="text-2xl font-bold text-green-600">{{ number_format($stats['total_gagne'], 2) }}€</div>
                <div class="text-xs text-gray-400 mt-1">après commission 12%</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="text-xs text-gray-400 mb-1">Ce mois</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['ce_mois'], 2) }}€</div>
                <div class="text-xs text-gray-400 mt-1">{{ now()->translatedFormat('F Y') }}</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="text-xs text-gray-400 mb-1">Ventes</div>
                <div class="text-2xl font-bold text-gray-900">{{ $stats['nb_ventes'] }}</div>
                <div class="text-xs text-gray-400 mt-1">commandes traitées</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="text-xs text-gray-400 mb-1">Note</div>
                <div class="text-2xl font-bold text-gray-900 flex items-center gap-1">
                    <span class="text-yellow-400">★</span>
                    {{ $stats['note_moyenne'] > 0 ? number_format($stats['note_moyenne'], 1) : '—' }}
                </div>
                <div class="text-xs text-gray-400 mt-1">{{ $stats['nb_avis'] }} avis</div>
            </div>
        </div>

        {{-- Graphique revenus --}}
        @if($revenus_mensuels->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6">
                <h2 class="font-semibold text-gray-900 mb-4">Revenus sur 6 mois</h2>
                <div class="flex items-end gap-2 h-24">
                    @php $max = $revenus_mensuels->max() ?: 1; @endphp
                    @foreach($revenus_mensuels as $mois => $montant)
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="text-xs font-medium text-green-600">{{ number_format($montant, 0) }}€</div>
                            <div class="w-full bg-green-500 rounded-t-lg transition-all"
                                 style="height: {{ max(4, ($montant / $max) * 80) }}px"></div>
                            <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($mois)->format('M') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">

                {{-- Commandes à traiter --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                            Commandes à traiter
                            @if($commandes_a_traiter->count() > 0)
                                <span class="bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                                    {{ $commandes_a_traiter->count() }}
                                </span>
                            @endif
                        </h2>
                        <a href="{{ route('commandes.mes-ventes') }}" class="text-xs text-green-600 hover:underline">Tout voir →</a>
                    </div>

                    @if($commandes_a_traiter->isEmpty())
                        <div class="text-center py-8 text-gray-400">
                            <div class="text-3xl mb-2">✅</div>
                            <p class="text-sm">Tout est traité !</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($commandes_a_traiter as $commande)
                                @php
                                    $colors = [
                                        'payee'          => ['bg-blue-50 border-blue-200', 'text-blue-700 bg-blue-100'],
                                        'en_preparation' => ['bg-orange-50 border-orange-200', 'text-orange-700 bg-orange-100'],
                                        'prete'          => ['bg-purple-50 border-purple-200', 'text-purple-700 bg-purple-100'],
                                        'en_livraison'   => ['bg-indigo-50 border-indigo-200', 'text-indigo-700 bg-indigo-100'],
                                    ][$commande->statut] ?? ['bg-gray-50 border-gray-200', 'text-gray-600 bg-gray-100'];
                                @endphp
                                <div class="border rounded-xl p-4 {{ $colors[0] }}">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <span class="font-semibold text-gray-900 text-sm">{{ $commande->numero }}</span>
                                            <span class="ml-2 text-xs px-2 py-0.5 rounded-full font-medium {{ $colors[1] }}">
                                                {{ ucfirst(str_replace('_',' ',$commande->statut)) }}
                                            </span>
                                        </div>
                                        <span class="font-bold text-green-600">{{ number_format($commande->montant_vendeur, 2) }}€</span>
                                    </div>

                                    <div class="text-xs text-gray-500 mb-3">
                                        👤 {{ $commande->acheteur->nom_complet }} ·
                                        📦 {{ $commande->livraison?->label_mode ?? '—' }} ·
                                        {{ $commande->created_at->format('d/m H:i') }}
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="text-xs text-gray-500">
                                            {{ $commande->lignes->count() }} article(s) :
                                            {{ $commande->lignes->pluck('titre_annonce')->join(', ') }}
                                        </div>

                                        {{-- Changer statut --}}
                                        <form action="{{ route('commandes.statut', $commande->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <select name="statut" onchange="this.form.submit()"
                                                class="text-xs px-2 py-1.5 border border-gray-300 rounded-lg bg-white focus:border-green-500 outline-none">
                                                @if($commande->statut === 'payee')
                                                    <option value="payee" selected>Statut actuel</option>
                                                    <option value="en_preparation">→ En préparation</option>
                                                @elseif($commande->statut === 'en_preparation')
                                                    <option value="en_preparation" selected>Statut actuel</option>
                                                    <option value="prete">→ Prête</option>
                                                @elseif($commande->statut === 'prete')
                                                    <option value="prete" selected>Statut actuel</option>
                                                    <option value="en_livraison">→ En livraison</option>
                                                @elseif($commande->statut === 'en_livraison')
                                                    <option value="en_livraison" selected>Statut actuel</option>
                                                    <option value="livree">→ Livrée ✅</option>
                                                @endif
                                            </select>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Mes annonces --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-semibold text-gray-900">Mes annonces</h2>
                        <a href="{{ route('annonces.create') }}"
                           class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-semibold hover:bg-green-700 transition">
                            + Nouvelle
                        </a>
                    </div>
                    @if($mes_annonces->isEmpty())
                        <div class="text-center py-8 text-gray-400">
                            <p class="text-sm">Aucune annonce active</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($mes_annonces as $annonce)
                                <a href="{{ route('annonces.show', $annonce->slug) }}"
                                   class="group relative rounded-xl overflow-hidden aspect-video bg-gray-100">
                                    <img src="{{ $annonce->premiere_photo }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex flex-col justify-end p-2">
                                        <div class="text-white text-xs font-medium truncate">{{ $annonce->titre }}</div>
                                        <div class="text-white/70 text-xs">
                                            👁 {{ $annonce->nb_vues }} · 🛒 {{ $annonce->panier_lignes_count }}
                                        </div>
                                    </div>
                                    @if($annonce->statut !== 'disponible')
                                        <div class="absolute top-2 right-2 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded">
                                            {{ $annonce->statut }}
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ route('annonces.mes-annonces') }}" class="block text-center text-xs text-green-600 hover:underline mt-3">
                            Gérer toutes mes annonces →
                        </a>
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
                            <span>📢</span> Nouvelle annonce
                        </a>
                        <a href="{{ route('messages.index') }}"
                           class="flex items-center gap-3 px-4 py-3 border border-gray-200 hover:border-green-400 rounded-xl text-sm font-medium text-gray-700 transition">
                            <span>💬</span> Messages
                            @if($nb_messages > 0)
                                <span class="ml-auto bg-green-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">{{ $nb_messages }}</span>
                            @endif
                        </a>
                        <a href="{{ route('stripe.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 border border-gray-200 hover:border-indigo-400 rounded-xl text-sm font-medium text-gray-700 transition">
                            <span>💰</span> Mes revenus
                        </a>
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-3 px-4 py-3 border border-gray-200 hover:border-gray-300 rounded-xl text-sm font-medium text-gray-700 transition">
                            <span>⚙️</span> Mon profil
                        </a>
                    </div>
                </div>

                {{-- Derniers avis --}}
                @if($avis_recents->isNotEmpty())
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <h2 class="font-semibold text-gray-900 mb-3">Derniers avis</h2>
                        <div class="space-y-3">
                            @foreach($avis_recents as $avis)
                                <div class="border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <img src="{{ $avis->auteur->photo_profil_url }}" class="w-6 h-6 rounded-full object-cover">
                                        <span class="text-sm font-medium text-gray-900">{{ $avis->auteur->prenom }}</span>
                                        <div class="text-yellow-400 text-xs ml-auto">
                                            {{ str_repeat('★', $avis->note) }}{{ str_repeat('☆', 5 - $avis->note) }}
                                        </div>
                                    </div>
                                    @if($avis->commentaire)
                                        <p class="text-xs text-gray-500 line-clamp-2">{{ $avis->commentaire }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
