{{-- resources/views/panier/index.blade.php --}}
<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-8">

        <h1 class="text-2xl font-bold text-gray-900 mb-6">
            🛒 Mon panier
            @if($lignes->isNotEmpty())
                <span class="text-base font-normal text-gray-400 ml-2">{{ $lignes->count() }} article(s)</span>
            @endif
        </h1>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
        @endif

        @if($lignes->isEmpty())
            {{-- Panier vide --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
                <div class="text-6xl mb-4">🛒</div>
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Votre panier est vide</h2>
                <p class="text-gray-400 text-sm mb-6">Découvrez les produits frais de nos producteurs locaux</p>
                <a href="{{ route('annonces.index') }}"
                   class="inline-block px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition">
                    Parcourir les annonces
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ── LISTE DES ARTICLES ────────────────────────────────── --}}
                <div class="lg:col-span-2 space-y-4" id="panier-lignes">

                    @foreach($lignes as $ligne)
                        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex gap-4 {{ !$ligne->disponible ? 'opacity-60' : '' }}"
                             id="ligne-{{ $ligne->id }}">

                            {{-- Photo --}}
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                <img src="{{ $ligne->annonce->premiere_photo }}"
                                     alt="{{ $ligne->annonce->titre }}"
                                     class="w-full h-full object-cover">
                            </div>

                            {{-- Infos --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start mb-1">
                                    <a href="{{ route('annonces.show', $ligne->annonce->slug) }}"
                                       class="font-semibold text-gray-900 text-sm hover:text-green-600 truncate">
                                        {{ $ligne->annonce->titre }}
                                    </a>
                                    <button onclick="supprimerLigne({{ $ligne->id }})"
                                        class="ml-2 text-gray-300 hover:text-red-400 transition flex-shrink-0">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <p class="text-xs text-gray-400 mb-2">
                                    par {{ $ligne->annonce->user->prenom }} ·
                                    {{ number_format($ligne->annonce->prix, 2) }}€ / {{ $ligne->annonce->unite_prix }}
                                </p>

                                @if(!$ligne->disponible)
                                    <p class="text-xs text-red-500 mb-2">⚠️ Plus disponible — retirez cet article</p>
                                @endif

                                <div class="flex items-center gap-3 flex-wrap">
                                    {{-- Quantité --}}
                                    <div class="flex items-center gap-1">
                                        <button onclick="changerQte({{ $ligne->id }}, -0.5)"
                                            class="w-7 h-7 rounded-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:border-gray-300 text-sm">−</button>
                                        <input type="number" id="qte-{{ $ligne->id }}"
                                            value="{{ $ligne->quantite }}"
                                            min="{{ $ligne->annonce->quantite_min_commande ?? 0.5 }}"
                                            max="{{ $ligne->annonce->quantite_disponible }}"
                                            step="0.01"
                                            onchange="mettreAJourLigne({{ $ligne->id }})"
                                            class="w-16 text-center py-1 text-sm border border-gray-200 rounded-lg focus:border-green-500 outline-none">
                                        <button onclick="changerQte({{ $ligne->id }}, 0.5)"
                                            class="w-7 h-7 rounded-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:border-gray-300 text-sm">+</button>
                                    </div>

                                    {{-- Mode livraison --}}
                                    <select id="livraison-{{ $ligne->id }}"
                                        onchange="mettreAJourLigne({{ $ligne->id }})"
                                        class="text-xs px-2 py-1.5 border border-gray-200 rounded-lg focus:border-green-500 outline-none">
                                        @if($ligne->annonce->livraison_main_propre)
                                            <option value="main_propre" {{ $ligne->mode_livraison === 'main_propre' ? 'selected' : '' }}>🤝 Main propre — 0€</option>
                                        @endif
                                        @if($ligne->annonce->livraison_point_relais)
                                            <option value="point_relais" {{ $ligne->mode_livraison === 'point_relais' ? 'selected' : '' }}>📦 Relais — 3€</option>
                                        @endif
                                        @if($ligne->annonce->livraison_domicile)
                                            <option value="domicile" {{ $ligne->mode_livraison === 'domicile' ? 'selected' : '' }}>🏠 Domicile — 6€</option>
                                        @endif
                                        @if($ligne->annonce->livraison_locker)
                                            <option value="locker" {{ $ligne->mode_livraison === 'locker' ? 'selected' : '' }}>🗄️ Locker — 2,50€</option>
                                        @endif
                                    </select>

                                    {{-- Sous-total ligne --}}
                                    <span class="ml-auto font-semibold text-green-600 text-sm" id="st-{{ $ligne->id }}">
                                        {{ number_format($ligne->sous_total, 2, ',', ' ') }} €
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Vider le panier --}}
                    <div class="text-right">
                        <form action="{{ route('panier.vider') }}" method="POST"
                              onsubmit="return confirm('Vider tout le panier ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-gray-400 hover:text-red-400 transition">
                                Vider le panier
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ── RÉCAP COMMANDE ────────────────────────────────────── --}}
                <div class="space-y-4">
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-4">
                        <h2 class="font-semibold text-gray-900 mb-4">Récapitulatif</h2>

                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Sous-total produits</span>
                                <span id="recap-sous-total">{{ number_format($sousTotal, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Frais de livraison</span>
                                <span id="recap-livraison">{{ number_format($fraisLivraison, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Frais de service</span>
                                <span id="recap-service">{{ number_format($fraisService, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="pt-3 border-t border-gray-100 flex justify-between font-bold text-gray-900 text-base">
                                <span>Total TTC</span>
                                <span id="recap-total" class="text-green-600">{{ number_format($totalTTC, 2, ',', ' ') }} €</span>
                            </div>
                        </div>

                        <a href="{{ route('panier.checkout') }}"
                           class="mt-5 w-full block text-center py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                            Passer la commande →
                        </a>

                        <div class="mt-3 flex items-center justify-center gap-2 text-xs text-gray-400">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Paiement 100% sécurisé par Stripe
                        </div>
                    </div>

                    {{-- Continuer les achats --}}
                    <a href="{{ route('annonces.index') }}"
                       class="block text-center text-sm text-gray-500 hover:text-green-600 transition">
                        ← Continuer les achats
                    </a>
                </div>
            </div>
        @endif
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        function changerQte(ligneId, delta) {
            const input = document.getElementById(`qte-${ligneId}`);
            const step  = parseFloat(input.step) || 0.1;
            const min   = parseFloat(input.min) || 0.5;
            const max   = parseFloat(input.max);
            const newVal = Math.max(min, Math.min(max, parseFloat(input.value) + delta));
            input.value = newVal.toFixed(2);
            mettreAJourLigne(ligneId);
        }

        async function mettreAJourLigne(ligneId) {
            const quantite     = document.getElementById(`qte-${ligneId}`).value;
            const modeLivraison = document.getElementById(`livraison-${ligneId}`)?.value;

            try {
                const res = await fetch(`/panier/${ligneId}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ quantite, mode_livraison: modeLivraison }),
                });
                const data = await res.json();
                if (data.success) {
                    if (data.sous_total_ligne)
                        document.getElementById(`st-${ligneId}`).textContent = data.sous_total_ligne + ' €';
                    if (data.sous_total)
                        document.getElementById('recap-sous-total').textContent = data.sous_total;
                    if (data.frais_livraison)
                        document.getElementById('recap-livraison').textContent = data.frais_livraison;
                    if (data.total_ttc)
                        document.getElementById('recap-total').textContent = data.total_ttc;
                } else {
                    alert(data.message || 'Erreur');
                }
            } catch(e) { console.error(e); }
        }

        async function supprimerLigne(ligneId) {
            try {
                const res = await fetch(`/panier/${ligneId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                });
                const data = await res.json();
                if (data.success) {
                    document.getElementById(`ligne-${ligneId}`).remove();
                    if (data.sous_total)    document.getElementById('recap-sous-total').textContent = data.sous_total;
                    if (data.frais_livraison) document.getElementById('recap-livraison').textContent = data.frais_livraison;
                    if (data.total_ttc)     document.getElementById('recap-total').textContent = data.total_ttc;
                    if (data.nb_articles === 0) location.reload();
                }
            } catch(e) { console.error(e); }
        }
    </script>
</x-app-layout>
