{{-- resources/views/annonces/show.blade.php --}}
<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-8">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-400 mb-6 flex items-center gap-2">
            <a href="{{ route('annonces.index') }}" class="hover:text-green-600">Annonces</a>
            <span>/</span>
            <span class="text-gray-700">{{ $annonce->titre }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ── COLONNE GAUCHE : Photos + Détails ──────────────────────── --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Galerie photos --}}
                <div x-data="{ photo: '{{ $annonce->premiere_photo }}' }"
                     class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

                    {{-- Photo principale --}}
                    <div class="aspect-[16/10] bg-gray-100 overflow-hidden">
                        <img :src="photo" alt="{{ $annonce->titre }}"
                             class="w-full h-full object-cover">
                    </div>

                    {{-- Miniatures --}}
                    @if(count($annonce->photos ?? []) > 1)
                        <div class="flex gap-2 p-3 overflow-x-auto">
                            @foreach($annonce->photos as $p)
                                <button @click="photo='{{ asset('storage/' . $p) }}'"
                                    class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition"
                                    :class="photo === '{{ asset('storage/' . $p) }}' ? 'border-green-500' : 'border-transparent'">
                                    <img src="{{ asset('storage/' . $p) }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Infos produit --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                    <div class="flex items-start justify-between">
                        <div>
                            {{-- Badges --}}
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium
                                    {{ $annonce->label === 'bio' ? 'bg-green-100 text-green-700' : 'bg-blue-50 text-blue-600' }}">
                                    {{ match($annonce->label) { 'bio'=>'🍃 Bio', 'local'=>'📍 Local', 'raisonne'=>'♻️ Raisonné', default=>'🌾 '.ucfirst($annonce->label) } }}
                                </span>
                                <span class="text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 font-medium">
                                    {{ match($annonce->type_produit) { 'fruit'=>'🍓 Fruit', 'legume'=>'🥕 Légume', 'herbe'=>'🌿 Herbe', 'champignon'=>'🍄 Champignon', default=>'📦 Autre' } }}
                                </span>
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $annonce->titre }}</h1>
                        </div>

                        {{-- Like --}}
                        @auth
                            @if(auth()->id() !== $annonce->user_id)
                                <button id="like-btn" onclick="toggleLikeShow({{ $annonce->id }})"
                                    class="flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 hover:border-red-300 transition text-sm">
                                    <svg id="like-icon" class="h-5 w-5 {{ $estLike ? 'text-red-500' : 'text-gray-400' }}"
                                         fill="{{ $estLike ? '#ef4444' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor"
                                         stroke-width="{{ $estLike ? '0' : '2' }}">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    <span id="nb-likes">{{ $annonce->nb_likes }}</span>
                                </button>
                            @endif
                        @endauth
                    </div>

                    {{-- Stats rapides --}}
                    <div class="grid grid-cols-3 gap-4 py-4 border-y border-gray-100">
                        <div class="text-center">
                            <div class="text-xl font-bold text-green-600">{{ number_format($annonce->prix, 2) }}€</div>
                            <div class="text-xs text-gray-400">/ {{ $annonce->unite_prix }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-gray-900">{{ $annonce->quantite_disponible }}</div>
                            <div class="text-xs text-gray-400">{{ $annonce->unite_prix }} disponible(s)</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-gray-900 flex items-center justify-center gap-1">
                                <span class="text-yellow-400">★</span>
                                {{ $annonce->note_moyenne > 0 ? number_format($annonce->note_moyenne, 1) : '—' }}
                            </div>
                            <div class="text-xs text-gray-400">{{ $annonce->nb_commandes }} commande(s)</div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <h2 class="font-semibold text-gray-900 mb-2">Description</h2>
                        <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">{{ $annonce->description }}</p>
                    </div>

                    {{-- Infos complémentaires --}}
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="flex items-center gap-2 text-gray-600">
                            <span>📅</span>
                            <span>Récolté le {{ $annonce->date_recolte->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <span>📍</span>
                            <span>{{ $annonce->ville ?? $annonce->localisation }}</span>
                        </div>
                        @if($annonce->disponible_a_partir_de)
                            <div class="flex items-center gap-2 text-gray-600">
                                <span>⏰</span>
                                <span>Dispo à partir du {{ $annonce->disponible_a_partir_de->format('d/m/Y') }}</span>
                            </div>
                        @endif
                        @if($annonce->quantite_min_commande)
                            <div class="flex items-center gap-2 text-gray-600">
                                <span>📦</span>
                                <span>Minimum {{ $annonce->quantite_min_commande }} {{ $annonce->unite_prix }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Modes de livraison --}}
                    <div>
                        <h2 class="font-semibold text-gray-900 mb-2">Modes de livraison</h2>
                        <div class="flex flex-wrap gap-2">
                            @if($annonce->livraison_main_propre)
                                <span class="flex items-center gap-1.5 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                                    🤝 <span>Main propre</span> <span class="text-green-600 font-semibold">Gratuit</span>
                                </span>
                            @endif
                            @if($annonce->livraison_point_relais)
                                <span class="flex items-center gap-1.5 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                                    📦 <span>Point relais</span> <span class="text-gray-500 font-semibold">3,00€</span>
                                </span>
                            @endif
                            @if($annonce->livraison_domicile)
                                <span class="flex items-center gap-1.5 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                                    🏠 <span>Domicile</span> <span class="text-gray-500 font-semibold">6,00€</span>
                                </span>
                            @endif
                            @if($annonce->livraison_locker)
                                <span class="flex items-center gap-1.5 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                                    🗄️ <span>Locker</span> <span class="text-gray-500 font-semibold">2,50€</span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Avis --}}
                @if($annonce->ratings->count() > 0)
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <h2 class="font-semibold text-gray-900 mb-4">
                            Avis ({{ $annonce->ratings->count() }})
                        </h2>
                        <div class="space-y-4">
                            @foreach($annonce->ratings->where('is_visible', true)->take(5) as $rating)
                                <div class="flex gap-3">
                                    <img src="{{ $rating->auteur->photo_profil_url }}"
                                         class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-medium text-gray-900">{{ $rating->auteur->prenom }}</span>
                                            <div class="flex text-yellow-400 text-xs">
                                                @for($i = 1; $i <= 5; $i++)
                                                    {{ $i <= $rating->note ? '★' : '☆' }}
                                                @endfor
                                            </div>
                                            <span class="text-xs text-gray-400">{{ $rating->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if($rating->commentaire)
                                            <p class="text-sm text-gray-600">{{ $rating->commentaire }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ── COLONNE DROITE : Vendeur + Achat ───────────────────────── --}}
            <div class="space-y-4">

                {{-- Card vendeur --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ $annonce->user->photo_profil_url }}"
                             alt="{{ $annonce->user->nom_complet }}"
                             class="w-12 h-12 rounded-full object-cover">
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-900">{{ $annonce->user->nom_complet }}</div>
                            <div class="text-xs text-gray-400 flex items-center gap-1">
                                @if($annonce->user->note_moyenne > 0)
                                    <span class="text-yellow-400">★</span>
                                    {{ number_format($annonce->user->note_moyenne, 1) }}
                                    · {{ $annonce->user->nb_avis }} avis ·
                                @endif
                                {{ $annonce->user->nb_ventes }} vente(s)
                            </div>
                        </div>
                        {{-- Signaler discret en haut à droite --}}
                        @auth
                            @if(auth()->id() !== $annonce->user_id)
                                <x-signaler-btn type="annonce" :id="$annonce->id" />
                            @endif
                        @endauth
                    </div>

                    @if($annonce->user->isProfessionnel())
                        <div class="text-xs bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg mb-3">
                            🏢 {{ $annonce->user->societe_nom }}
                        </div>
                    @endif

                    {{-- Contacter le vendeur --}}
                    @auth
                        @if(auth()->id() !== $annonce->user_id)
                            <a href="{{ route('messages.show', ['annonce' => $annonce->id, 'user' => $annonce->user_id]) }}"
                               class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:border-green-500 hover:text-green-600 transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Contacter le vendeur
                            </a>
                        @endif
                    @endauth
                </div>

                {{-- Card achat --}}
                @auth
                    @if(auth()->id() !== $annonce->user_id && $annonce->isDisponible())
                        <div class="bg-white rounded-2xl border border-green-100 p-5 sticky top-4">
                            <h3 class="font-semibold text-gray-900 mb-4">Commander</h3>

                            <form action="{{ route('panier.ajouter', $annonce->id) }}" method="POST">
                                @csrf
                                {{-- Quantité --}}
                                <div class="mb-4">
                                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">
                                        Quantité ({{ $annonce->unite_prix }})
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="changeQty(-1)"
                                            class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center hover:border-gray-300 transition text-gray-600">−</button>
                                        <input type="number" id="quantite" name="quantite"
                                            value="{{ $annonce->quantite_min_commande ?? 1 }}"
                                            min="{{ $annonce->quantite_min_commande ?? 0.5 }}"
                                            max="{{ $annonce->quantite_disponible }}"
                                            step="0.01"
                                            class="flex-1 text-center py-2 rounded-lg border border-gray-200 focus:border-green-500 outline-none text-sm font-medium"
                                            oninput="updateTotal()">
                                        <button type="button" onclick="changeQty(1)"
                                            class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center hover:border-gray-300 transition text-gray-600">+</button>
                                    </div>
                                </div>

                                {{-- Mode livraison --}}
                                <div class="mb-4">
                                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Livraison</label>
                                    <select name="mode_livraison" id="mode_livraison" onchange="updateTotal()"
                                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                                        @if($annonce->livraison_main_propre)
                                            <option value="main_propre" data-frais="0">🤝 Main propre — Gratuit</option>
                                        @endif
                                        @if($annonce->livraison_point_relais)
                                            <option value="point_relais" data-frais="3">📦 Point relais — 3,00€</option>
                                        @endif
                                        @if($annonce->livraison_domicile)
                                            <option value="domicile" data-frais="6">🏠 Domicile — 6,00€</option>
                                        @endif
                                        @if($annonce->livraison_locker)
                                            <option value="locker" data-frais="2.5">🗄️ Locker — 2,50€</option>
                                        @endif
                                    </select>
                                </div>

                                {{-- Total estimé --}}
                                <div class="bg-gray-50 rounded-xl p-3 mb-4 space-y-1.5 text-sm">
                                    <div class="flex justify-between text-gray-600">
                                        <span>Produits</span>
                                        <span id="total-produits">{{ number_format($annonce->prix * ($annonce->quantite_min_commande ?? 1), 2) }}€</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Livraison</span>
                                        <span id="total-livraison">0,00€</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Frais de service</span>
                                        <span>0,99€</span>
                                    </div>
                                    <div class="flex justify-between font-bold text-gray-900 pt-1.5 border-t border-gray-200">
                                        <span>Total</span>
                                        <span id="total-final" class="text-green-600">{{ number_format($annonce->prix * ($annonce->quantite_min_commande ?? 1) + 0.99, 2) }}€</span>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                                    {{ $dansPanier ? '✓ Dans le panier' : 'Ajouter au panier' }}
                                </button>
                            </form>
                        </div>
                    @elseif(auth()->id() === $annonce->user_id)
                        <div class="bg-white rounded-2xl border border-gray-100 p-5">
                            <p class="text-sm text-gray-500 text-center mb-3">C'est votre annonce</p>
                            <a href="{{ route('annonces.edit', $annonce->slug) }}"
                               class="w-full block text-center py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:border-gray-300 transition">
                                ✏️ Modifier l'annonce
                            </a>
                        </div>
                    @endif
                @else
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">
                        <p class="text-sm text-gray-500 mb-3">Connectez-vous pour commander</p>
                        <a href="{{ route('login') }}"
                           class="w-full block py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition">
                            Se connecter
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        {{-- Annonces similaires --}}
        @if($similaires->count() > 0)
            <div class="mt-12">
                <h2 class="text-lg font-bold text-gray-900 mb-5">Vous aimerez aussi</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($similaires as $s)
                        <x-annonce-card :annonce="$s" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <script>
        const prixUnitaire = {{ $annonce->prix }};

        function changeQty(delta) {
            const input = document.getElementById('quantite');
            const step = parseFloat(input.step) || 0.1;
            const newVal = Math.max(parseFloat(input.min), parseFloat(input.value) + delta * step);
            input.value = Math.min(parseFloat(input.max), newVal).toFixed(2);
            updateTotal();
        }

        function updateTotal() {
            const qty   = parseFloat(document.getElementById('quantite').value) || 0;
            const sel   = document.getElementById('mode_livraison');
            const frais = sel ? parseFloat(sel.options[sel.selectedIndex].dataset.frais || 0) : 0;
            const produits = qty * prixUnitaire;
            const total  = produits + frais + 0.99;

            document.getElementById('total-produits').textContent  = produits.toFixed(2).replace('.', ',') + '€';
            document.getElementById('total-livraison').textContent = frais.toFixed(2).replace('.', ',') + '€';
            document.getElementById('total-final').textContent     = total.toFixed(2).replace('.', ',') + '€';
        }

        async function toggleLikeShow(annonceId) {
            try {
                const res = await fetch(`/annonces/${annonceId}/like`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await res.json();
                const icon = document.getElementById('like-icon');
                const nb   = document.getElementById('nb-likes');
                if (data.liked) {
                    icon.setAttribute('fill', '#ef4444');
                    icon.setAttribute('stroke-width', '0');
                } else {
                    icon.setAttribute('fill', 'none');
                    icon.setAttribute('stroke-width', '2');
                }
                nb.textContent = data.nb_likes;
            } catch(e) {}
        }
    </script>
</x-app-layout>