{{-- resources/views/annonces/index.blade.php --}}
<x-app-layout>
    <div class="min-h-screen bg-gray-50">

        {{-- ── HERO RECHERCHE ──────────────────────────────────────────────── --}}
        <div class="bg-white border-b border-gray-100 py-6 px-4">
            <div class="max-w-6xl mx-auto">
                <form method="GET" action="{{ route('annonces.index') }}" x-data="{ showFiltres: false }">

                    {{-- Barre de recherche principale --}}
                    <div class="flex gap-3">
                        <div class="relative flex-1">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Tomates, fraises, carottes..."
                                class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                        </div>
                        <button type="submit"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                            Rechercher
                        </button>
                        {{-- Bouton géoloc --}}
                        <button type="button"
                            id="btn-geoloc"
                            onclick="geolocMe()"
                            title="Rechercher près de moi"
                            class="px-4 py-3 border border-gray-200 rounded-xl hover:border-green-400 hover:text-green-600 transition text-sm text-gray-600 flex items-center gap-2 {{ request('lat') ? 'border-green-400 text-green-600 bg-green-50' : '' }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span id="geoloc-label">{{ request('lat') ? 'Près de moi ✓' : 'Près de moi' }}</span>
                        </button>
                        {{-- Inputs cachés coordonnées --}}
                        <input type="hidden" name="lat" id="input-lat" value="{{ request('lat') }}">
                        <input type="hidden" name="lng" id="input-lng" value="{{ request('lng') }}">
                        <input type="hidden" name="rayon" id="input-rayon" value="{{ request('rayon', 30) }}">
                        <button type="button" @click="showFiltres = !showFiltres"
                            class="px-4 py-3 border border-gray-200 rounded-xl hover:border-gray-300 transition text-sm text-gray-600 flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                            </svg>
                            Filtres
                            @if(request()->hasAny(['type','label','prix_min','prix_max','livraison']))
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            @endif
                        </button>
                    </div>

                    {{-- Filtres avancés --}}
                    <div x-show="showFiltres" x-transition
                         class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-4">

                        {{-- Type produit --}}
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Type</label>
                            <select name="type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-green-500 outline-none">
                                <option value="">Tous</option>
                                <option value="legume" {{ request('type') === 'legume' ? 'selected' : '' }}>🥕 Légumes</option>
                                <option value="fruit" {{ request('type') === 'fruit' ? 'selected' : '' }}>🍓 Fruits</option>
                                <option value="herbe" {{ request('type') === 'herbe' ? 'selected' : '' }}>🌿 Herbes</option>
                                <option value="champignon" {{ request('type') === 'champignon' ? 'selected' : '' }}>🍄 Champignons</option>
                                <option value="autre" {{ request('type') === 'autre' ? 'selected' : '' }}>📦 Autre</option>
                            </select>
                        </div>

                        {{-- Label --}}
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Label</label>
                            <select name="label" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-green-500 outline-none">
                                <option value="">Tous</option>
                                <option value="bio" {{ request('label') === 'bio' ? 'selected' : '' }}>🍃 Bio</option>
                                <option value="local" {{ request('label') === 'local' ? 'selected' : '' }}>📍 Local</option>
                                <option value="raisonne" {{ request('label') === 'raisonne' ? 'selected' : '' }}>♻️ Raisonné</option>
                            </select>
                        </div>

                        {{-- Prix --}}
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Prix (€/kg)</label>
                            <div class="flex gap-2">
                                <input type="number" name="prix_min" value="{{ request('prix_min') }}"
                                    placeholder="Min" step="0.1" min="0"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-green-500 outline-none">
                                <input type="number" name="prix_max" value="{{ request('prix_max') }}"
                                    placeholder="Max" step="0.1" min="0"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-green-500 outline-none">
                            </div>
                        </div>

                        {{-- Livraison --}}
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Livraison</label>
                            <select name="livraison" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-green-500 outline-none">
                                <option value="">Tous modes</option>
                                <option value="main_propre" {{ request('livraison') === 'main_propre' ? 'selected' : '' }}>🤝 Main propre</option>
                                <option value="point_relais" {{ request('livraison') === 'point_relais' ? 'selected' : '' }}>📦 Point relais</option>
                                <option value="domicile" {{ request('livraison') === 'domicile' ? 'selected' : '' }}>🏠 Domicile</option>
                                <option value="locker" {{ request('livraison') === 'locker' ? 'selected' : '' }}>🗄️ Locker</option>
                            </select>
                        </div>

                        {{-- Rayon géoloc si actif --}}
                        @if(request('lat'))
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">📍 Rayon (km)</label>
                                <select name="rayon" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-green-500 outline-none">
                                    @foreach([5, 10, 20, 30, 50, 100] as $r)
                                        <option value="{{ $r }}" {{ request('rayon', 30) == $r ? 'selected' : '' }}>{{ $r }} km</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="lat" value="{{ request('lat') }}">
                                <input type="hidden" name="lng" value="{{ request('lng') }}">
                            </div>
                        @endif

                        <div class="col-span-2 md:col-span-4 flex gap-2">
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                                Appliquer
                            </button>
                            <a href="{{ route('annonces.index') }}"
                               class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:border-gray-300 transition">
                                Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── CONTENU PRINCIPAL ────────────────────────────────────────────── --}}
        <div class="max-w-6xl mx-auto px-4 py-8">

            {{-- En-tête résultats --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">
                        {{ $annonces->total() }} annonce{{ $annonces->total() > 1 ? 's' : '' }}
                        @if(request('search'))
                            pour « {{ request('search') }} »
                        @endif
                    </h1>
                    <p class="text-sm text-gray-500 mt-0.5">Produits frais et locaux près de chez vous</p>
                </div>

                @auth
                    <a href="{{ route('annonces.create') }}"
                       class="flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Déposer une annonce
                    </a>
                @endauth
            </div>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Grille annonces --}}
            @if($annonces->isEmpty())
                <div class="text-center py-20">
                    <div class="text-6xl mb-4">🥕</div>
                    <h2 class="text-lg font-semibold text-gray-700">Aucune annonce trouvée</h2>
                    <p class="text-gray-500 text-sm mt-2">Essayez d'autres critères de recherche</p>
                    @auth
                        <a href="{{ route('annonces.create') }}"
                           class="inline-block mt-6 px-6 py-3 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 transition">
                            Déposer la première annonce
                        </a>
                    @endauth
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($annonces as $annonce)
                        <x-annonce-card :annonce="$annonce" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-10">
                    {{ $annonces->links() }}
                </div>
            @endif
        </div>
    </div>

<script>
function geolocMe() {
    const btn   = document.getElementById('btn-geoloc');
    const label = document.getElementById('geoloc-label');

    // Si déjà actif → désactiver
    if (document.getElementById('input-lat').value) {
        document.getElementById('input-lat').value = '';
        document.getElementById('input-lng').value = '';
        document.getElementById('input-rayon').value = '';
        btn.classList.remove('border-green-400', 'text-green-600', 'bg-green-50');
        label.textContent = 'Près de moi';
        btn.closest('form').submit();
        return;
    }

    if (!navigator.geolocation) {
        alert('La géolocalisation n\'est pas supportée par votre navigateur.');
        return;
    }

    label.textContent = 'Localisation...';
    btn.disabled = true;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            document.getElementById('input-lat').value   = position.coords.latitude;
            document.getElementById('input-lng').value   = position.coords.longitude;
            document.getElementById('input-rayon').value = 30;
            label.textContent = 'Près de moi ✓';
            btn.classList.add('border-green-400', 'text-green-600', 'bg-green-50');
            btn.disabled = false;
            // Soumettre automatiquement le formulaire
            btn.closest('form').submit();
        },
        (error) => {
            btn.disabled = false;
            label.textContent = 'Près de moi';
            const messages = {
                1: 'Accès refusé. Autorisez la localisation dans votre navigateur.',
                2: 'Position introuvable. Réessayez.',
                3: 'La demande a expiré. Réessayez.',
            };
            alert(messages[error.code] ?? 'Erreur de géolocalisation.');
        },
        { enableHighAccuracy: false, timeout: 8000, maximumAge: 300000 }
    );
}
</script>

</x-app-layout>