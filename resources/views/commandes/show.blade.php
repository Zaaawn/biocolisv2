{{-- resources/views/commandes/show.blade.php --}}
<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-8">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('commandes.mes-commandes') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Commande {{ $commande->numero }}</h1>
                <p class="text-sm text-gray-400">{{ $commande->created_at->format('d/m/Y à H:i') }}</p>
            </div>
        </div>

        @php
            $statutConfig = [
                'en_attente'       => ['bg-gray-100',    'text-gray-600',   '⏳', 'En attente'],
                'paiement_en_cours'=> ['bg-yellow-100',  'text-yellow-700', '💳', 'Paiement en cours'],
                'payee'            => ['bg-blue-100',    'text-blue-700',   '✓',  'Payée'],
                'en_preparation'   => ['bg-orange-100',  'text-orange-700', '👨‍🍳', 'En préparation'],
                'prete'            => ['bg-purple-100',  'text-purple-700', '📦', 'Prête'],
                'en_livraison'     => ['bg-indigo-100',  'text-indigo-700', '🚴', 'En livraison'],
                'livree'           => ['bg-teal-100',    'text-teal-700',   '✅', 'Livrée'],
                'terminee'         => ['bg-green-100',   'text-green-700',  '🌟', 'Terminée'],
                'annulee'          => ['bg-red-100',     'text-red-600',    '✗',  'Annulée'],
                'remboursee'       => ['bg-red-50',      'text-red-500',    '↩️', 'Remboursée'],
            ][$commande->statut] ?? ['bg-gray-100', 'text-gray-600', '?', $commande->statut];
        @endphp

        {{-- Statut --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">Statut de la commande</span>
                <span class="text-sm px-3 py-1.5 rounded-full font-semibold {{ $statutConfig[0] }} {{ $statutConfig[1] }}">
                    {{ $statutConfig[2] }} {{ $statutConfig[3] }}
                </span>
            </div>

            {{-- Barre de progression --}}
            @php
                $etapes = ['payee', 'en_preparation', 'prete', 'en_livraison', 'livree', 'terminee'];
                $etapeActuelle = array_search($commande->statut, $etapes);
            @endphp
            @if($etapeActuelle !== false)
                <div class="mt-4 flex items-center gap-1">
                    @foreach($etapes as $i => $etape)
                        <div class="flex-1 h-1.5 rounded-full {{ $i <= $etapeActuelle ? 'bg-green-500' : 'bg-gray-200' }} transition-all"></div>
                    @endforeach
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-xs text-gray-400">Payée</span>
                    <span class="text-xs text-gray-400">Terminée</span>
                </div>
            @endif
        </div>

        {{-- Participants --}}
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <p class="text-xs text-gray-400 mb-2 uppercase font-semibold">Acheteur</p>
                <div class="flex items-center gap-3">
                    <img src="{{ $commande->acheteur->photo_profil_url }}" class="w-8 h-8 rounded-full object-cover">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $commande->acheteur->nom_complet }}</div>
                        <div class="text-xs text-gray-400">{{ $commande->acheteur->email }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <p class="text-xs text-gray-400 mb-2 uppercase font-semibold">Vendeur</p>
                <div class="flex items-center gap-3">
                    <img src="{{ $commande->vendeur->photo_profil_url }}" class="w-8 h-8 rounded-full object-cover">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $commande->vendeur->nom_complet }}</div>
                        <div class="text-xs text-gray-400">{{ $commande->vendeur->email }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Articles --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-4">
            <h2 class="font-semibold text-gray-900 mb-4">Articles commandés</h2>
            <div class="space-y-3">
                @foreach($commande->lignes as $ligne)
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                            @if($ligne->photo_annonce)
                                <img src="{{ asset('storage/'.$ligne->photo_annonce) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-xl">🥕</div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $ligne->titre_annonce }}</div>
                            <div class="text-xs text-gray-400">
                                {{ $ligne->quantite }} {{ $ligne->unite_prix }} × {{ number_format($ligne->prix_unitaire, 2) }}€
                            </div>
                        </div>
                        <div class="text-sm font-semibold text-gray-900">{{ number_format($ligne->sous_total, 2) }}€</div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-gray-100 mt-4 pt-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-500">
                    <span>Sous-total</span>
                    <span>{{ number_format($commande->sous_total, 2) }}€</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Livraison</span>
                    <span>{{ number_format($commande->frais_livraison, 2) }}€</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span>Frais de service</span>
                    <span>{{ number_format($commande->frais_service, 2) }}€</span>
                </div>
                <div class="flex justify-between font-bold text-gray-900 text-base pt-1 border-t border-gray-100">
                    <span>Total payé</span>
                    <span class="text-green-600">{{ number_format($commande->total_ttc, 2) }}€</span>
                </div>
            </div>
        </div>

        {{-- Livraison --}}
        @if($commande->livraison)
            <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-4">
                <h2 class="font-semibold text-gray-900 mb-3">Livraison</h2>
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-2xl">
                        {{ match($commande->livraison->mode) {
                            'main_propre' => '🤝',
                            'point_relais' => '📦',
                            'domicile' => '🏠',
                            'locker' => '🗄️',
                            default => '📦'
                        } }}
                    </span>
                    <div>
                        <div class="font-medium text-gray-900">{{ $commande->livraison->label_mode }}</div>
                        @if($commande->livraison->adresse_rdv)
                            <div class="text-gray-400">{{ $commande->livraison->adresse_rdv }}</div>
                        @endif
                        @if($commande->livraison->creneau_debut)
                            <div class="text-gray-400">
                                {{ $commande->livraison->creneau_debut->format('d/m/Y à H:i') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex flex-wrap gap-3">
            @if($commande->acheteur_id === Auth::id())
                @if($commande->estAnnulable())
                    <form action="{{ route('commandes.annuler', $commande->id) }}" method="POST"
                          onsubmit="return confirm('Annuler cette commande ?')">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="px-4 py-2.5 border border-red-200 text-red-500 rounded-xl text-sm font-medium hover:bg-red-50 transition">
                            Annuler la commande
                        </button>
                    </form>
                @endif
                @if($commande->statut === 'livree')
                    <form action="{{ route('commandes.confirmer-reception', $commande->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition">
                            ✅ Confirmer la réception
                        </button>
                    </form>
                @endif
                <a href="{{ route('messages.show', ['annonce' => $commande->lignes->first()?->annonce_id, 'user' => $commande->vendeur_id]) }}"
                   class="px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:border-green-400 hover:text-green-600 transition">
                    💬 Contacter le vendeur
                </a>
            @endif

            @if($commande->vendeur_id === Auth::id() && in_array($commande->statut, ['payee', 'en_preparation', 'prete', 'en_livraison']))
                <form action="{{ route('commandes.statut', $commande->id) }}" method="POST" class="flex items-center gap-2">
                    @csrf @method('PATCH')
                    <select name="statut" class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:border-green-500 outline-none">
                        @if($commande->statut === 'payee')
                            <option value="en_preparation">→ En préparation</option>
                        @elseif($commande->statut === 'en_preparation')
                            <option value="prete">→ Prête</option>
                        @elseif($commande->statut === 'prete')
                            <option value="en_livraison">→ En livraison</option>
                        @elseif($commande->statut === 'en_livraison')
                            <option value="livree">→ Livrée</option>
                        @endif
                    </select>
                    <button type="submit" class="px-4 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 transition">
                        Mettre à jour
                    </button>
                </form>
            @endif
        </div>
    </div>
{{--
    AJOUTE CE BLOC dans resources/views/commandes/show.blade.php
    JUSTE AVANT </x-app-layout> (tout en bas du fichier)
--}}

    {{-- ── SECTION AVIS ──────────────────────────────────────────────────── --}}
    <div id="avis" class="max-w-2xl mx-auto px-4 pb-12">

        @php
            $monAvis = $commande->ratings->where('auteur_id', Auth::id())->first();
            $peutNoter = $commande->acheteur_id === Auth::id()
                && $commande->statut === 'terminee'
                && !$monAvis;
        @endphp

        {{-- Formulaire laisser un avis --}}
        @if($peutNoter)
            <div class="bg-white rounded-2xl border border-green-200 p-6 mb-6">
                <h2 class="font-bold text-gray-900 text-lg mb-1">Laisser un avis ⭐</h2>
                <p class="text-sm text-gray-500 mb-5">Partagez votre expérience avec ce vendeur.</p>

                <form action="{{ route('ratings.store', $commande->id) }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Note globale --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Note globale *</label>
                        <div class="flex gap-2" x-data="{ note: 0, hover: 0 }">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button"
                                    @click="note = {{ $i }}"
                                    @mouseenter="hover = {{ $i }}"
                                    @mouseleave="hover = 0"
                                    :class="(hover >= {{ $i }} || note >= {{ $i }}) ? 'text-yellow-400' : 'text-gray-300'"
                                    class="text-4xl transition-colors focus:outline-none">
                                    ★
                                </button>
                                <input type="radio" name="note" value="{{ $i }}"
                                    x-bind:checked="note === {{ $i }}"
                                    class="hidden">
                            @endfor
                        </div>
                        @error('note') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Critères détaillés --}}
                    <div class="grid grid-cols-3 gap-4">
                        @foreach([
                            ['fraicheur', '🥕 Fraîcheur'],
                            ['emballage', '📦 Emballage'],
                            ['communication', '💬 Communication'],
                        ] as [$field, $label])
                            <div x-data="{ val: 0 }">
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ $label }}</label>
                                <div class="flex gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                            @click="val = {{ $i }}"
                                            :class="val >= {{ $i }} ? 'text-yellow-400' : 'text-gray-200'"
                                            class="text-xl transition-colors">★</button>
                                        <input type="radio" name="{{ $field }}" value="{{ $i }}"
                                            x-bind:checked="val === {{ $i }}" class="hidden">
                                    @endfor
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Commentaire --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Commentaire <span class="text-gray-400 font-normal">(optionnel)</span>
                        </label>
                        <textarea name="commentaire" rows="3"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm resize-none"
                            placeholder="Décrivez votre expérience...">{{ old('commentaire') }}</textarea>
                        @error('commentaire') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                        Publier mon avis
                    </button>
                </form>
            </div>
        @endif

        {{-- Avis existants --}}
        @if($commande->ratings->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-bold text-gray-900 text-lg mb-5">
                    Avis
                    <span class="text-sm font-normal text-gray-400 ml-1">({{ $commande->ratings->count() }})</span>
                </h2>

                <div class="space-y-5">
                    @foreach($commande->ratings as $rating)
                        <div class="border-b border-gray-100 pb-5 last:border-0 last:pb-0">

                            {{-- Auteur + note --}}
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $rating->auteur->photo_profil_url }}"
                                         class="w-9 h-9 rounded-full object-cover">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $rating->auteur->prenom }}</div>
                                        <div class="text-xs text-gray-400">{{ $rating->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="flex text-yellow-400 text-lg">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $rating->note ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                                    @endfor
                                </div>
                            </div>

                            {{-- Critères --}}
                            @if($rating->criteres)
                                <div class="flex gap-4 mb-2">
                                    @foreach(['fraicheur' => '🥕', 'emballage' => '📦', 'communication' => '💬'] as $key => $ico)
                                        @if(isset($rating->criteres[$key]))
                                            <div class="text-xs text-gray-500">
                                                {{ $ico }}
                                                <span class="text-yellow-500 font-semibold">{{ $rating->criteres[$key] }}/5</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            {{-- Commentaire --}}
                            @if($rating->commentaire)
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $rating->commentaire }}</p>
                            @endif

                            {{-- Réponse vendeur --}}
                            @if($rating->reponse_vendeur)
                                <div class="mt-3 ml-4 pl-4 border-l-2 border-green-200 bg-green-50 rounded-r-xl p-3">
                                    <p class="text-xs font-semibold text-green-700 mb-1">
                                        🌱 Réponse du vendeur · {{ $rating->repondu_at->diffForHumans() }}
                                    </p>
                                    <p class="text-sm text-gray-700">{{ $rating->reponse_vendeur }}</p>
                                </div>
                            @elseif($commande->vendeur_id === Auth::id())
                                {{-- Formulaire réponse pour le vendeur --}}
                                <div class="mt-3 ml-4" x-data="{ open: false }">
                                    <button @click="open = !open"
                                        class="text-xs text-green-600 hover:underline font-medium">
                                        Répondre à cet avis
                                    </button>
                                    <div x-show="open" x-transition class="mt-2">
                                        <form action="{{ route('ratings.repondre', $rating->id) }}" method="POST" class="flex gap-2">
                                            @csrf @method('PATCH')
                                            <input type="text" name="reponse"
                                                placeholder="Votre réponse..."
                                                class="flex-1 px-3 py-2 text-sm border border-gray-200 rounded-xl focus:border-green-500 outline-none">
                                            <button type="submit"
                                                class="px-4 py-2 bg-green-600 text-white text-xs font-semibold rounded-xl hover:bg-green-700 transition">
                                                Publier
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            {{-- Bouton mon avis (si c'est le sien) --}}
                            @if($rating->auteur_id === Auth::id())
                                <div class="mt-2">
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
                                        ✓ Votre avis
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</x-app-layout>
