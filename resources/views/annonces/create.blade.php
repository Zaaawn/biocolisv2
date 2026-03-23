{{-- resources/views/annonces/create.blade.php --}}
<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-8">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Déposer une annonce</h1>
            <p class="text-gray-500 text-sm mt-1">Partagez vos fruits et légumes avec votre communauté 🌱</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('annonces.store') }}" enctype="multipart/form-data"
              x-data="annonceForm()" class="space-y-6">
            @csrf

            {{-- ── SECTION 1 : Produit ──────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
                <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-6 h-6 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                    Votre produit
                </h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Titre de l'annonce <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="titre" value="{{ old('titre') }}" required
                        maxlength="255"
                        placeholder="Ex: Tomates cerises bio du jardin"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm @error('titre') border-red-400 @enderror">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type_produit" required
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                            <option value="">Choisir...</option>
                            <option value="legume" {{ old('type_produit') === 'legume' ? 'selected' : '' }}>🥕 Légume</option>
                            <option value="fruit" {{ old('type_produit') === 'fruit' ? 'selected' : '' }}>🍓 Fruit</option>
                            <option value="herbe" {{ old('type_produit') === 'herbe' ? 'selected' : '' }}>🌿 Herbe aromatique</option>
                            <option value="champignon" {{ old('type_produit') === 'champignon' ? 'selected' : '' }}>🍄 Champignon</option>
                            <option value="autre" {{ old('type_produit') === 'autre' ? 'selected' : '' }}>📦 Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="categorie" value="{{ old('categorie') }}" required
                            placeholder="Ex: tomates, pommes, basilic..."
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Label <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach(['bio'=>['🍃','Bio'],'local'=>['📍','Local'],'raisonne'=>['♻️','Raisonné'],'conventionnel'=>['🌾','Conventionnel']] as $val => [$ico, $lbl])
                            <label class="cursor-pointer">
                                <input type="radio" name="label" value="{{ $val }}"
                                    {{ old('label', 'local') === $val ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="border-2 rounded-xl p-2 text-center text-xs transition peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 border-gray-200 text-gray-600 hover:border-gray-300">
                                    <div class="text-lg">{{ $ico }}</div>
                                    <div class="font-medium mt-0.5">{{ $lbl }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" rows="4" required minlength="20"
                        placeholder="Décrivez votre produit : variété, mode de culture, particularités..."
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Minimum 20 caractères</p>
                </div>
            </div>

            {{-- ── SECTION 2 : Prix & Quantité ─────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
                <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-6 h-6 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                    Prix & Quantité
                </h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Prix <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="prix" value="{{ old('prix') }}"
                                required min="0.01" step="0.01"
                                placeholder="2.50"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm pr-16">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">€ /</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Unité <span class="text-red-500">*</span>
                        </label>
                        <select name="unite_prix" required
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                            <option value="kg" {{ old('unite_prix', 'kg') === 'kg' ? 'selected' : '' }}>kg</option>
                            <option value="unite" {{ old('unite_prix') === 'unite' ? 'selected' : '' }}>unité</option>
                            <option value="lot" {{ old('unite_prix') === 'lot' ? 'selected' : '' }}>lot</option>
                            <option value="caisse" {{ old('unite_prix') === 'caisse' ? 'selected' : '' }}>caisse</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Quantité disponible <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="quantite_disponible" value="{{ old('quantite_disponible') }}"
                            required min="0.1" step="0.01"
                            placeholder="10"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Commande minimum
                        </label>
                        <input type="number" name="quantite_min_commande" value="{{ old('quantite_min_commande', 0.5) }}"
                            min="0.1" step="0.01"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de récolte <span class="text-red-500">*</span></label>
                        <input type="date" name="date_recolte" value="{{ old('date_recolte') }}"
                            required max="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Disponible jusqu'au</label>
                        <input type="date" name="disponible_jusqu_a" value="{{ old('disponible_jusqu_a') }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                    </div>
                </div>
            </div>

            {{-- ── SECTION 3 : Localisation ─────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-5">
                <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-6 h-6 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                    Localisation
                </h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Adresse <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="localisation" id="localisation" value="{{ old('localisation') }}"
                        required placeholder="4 rue des Lilas, Lille"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                    <input type="hidden" name="ville" id="ville" value="{{ old('ville') }}">
                    <input type="hidden" name="code_postal" id="code_postal" value="{{ old('code_postal') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Rayon de livraison (km)
                    </label>
                    <input type="number" name="rayon_livraison_km" value="{{ old('rayon_livraison_km', 30) }}"
                        min="1" max="200"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                </div>
            </div>

            {{-- ── SECTION 4 : Livraison ────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-6 h-6 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs font-bold">4</span>
                    Modes de livraison <span class="text-red-500">*</span>
                </h2>

                @error('livraison')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror

                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        'main_propre'   => ['🤝', 'Main propre', 'Gratuit'],
                        'point_relais'  => ['📦', 'Point relais', '3,00€'],
                        'domicile'      => ['🏠', 'À domicile', '6,00€'],
                        'locker'        => ['🗄️', 'Locker', '2,50€'],
                    ] as $mode => [$ico, $lbl, $prix])
                        <label class="cursor-pointer flex items-center gap-3 p-3 rounded-xl border-2 transition hover:border-gray-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50 border-gray-200">
                            <input type="checkbox" name="livraison_{{ $mode }}" value="1"
                                {{ old('livraison_'.$mode) ? 'checked' : ($mode === 'main_propre' ? 'checked' : '') }}
                                class="w-4 h-4 rounded text-green-600">
                            <div>
                                <div class="text-sm font-medium text-gray-700">{{ $ico }} {{ $lbl }}</div>
                                <div class="text-xs text-gray-400">{{ $prix }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- ── SECTION 5 : Photos ───────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-6 h-6 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs font-bold">5</span>
                    Photos <span class="text-gray-400 text-xs font-normal">(jusqu'à 8 photos)</span>
                </h2>

                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-green-400 transition cursor-pointer"
                     onclick="document.getElementById('photos-input').click()">
                    <input type="file" id="photos-input" name="photos[]"
                        multiple accept="image/*" class="hidden"
                        onchange="previewPhotos(this)">
                    <div class="text-3xl mb-2">📸</div>
                    <p class="text-sm text-gray-600 font-medium">Cliquez pour ajouter des photos</p>
                    <p class="text-xs text-gray-400 mt-1">JPEG, PNG, WebP — Max 5 Mo par photo</p>
                </div>

                <div id="photos-preview" class="grid grid-cols-4 gap-2 hidden"></div>
            </div>

            {{-- ── BOUTONS ──────────────────────────────────────────────────── --}}
            <div class="flex gap-3">
                <a href="{{ route('annonces.index') }}"
                   class="flex-1 py-3 text-center border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:border-gray-300 transition">
                    Annuler
                </a>
                <button type="submit"
                    class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                    Publier l'annonce
                </button>
            </div>
        </form>
    </div>

    <script>
        function annonceForm() {
            return {};
        }

        function previewPhotos(input) {
            const preview = document.getElementById('photos-preview');
            preview.innerHTML = '';
            preview.classList.remove('hidden');

            Array.from(input.files).slice(0, 8).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'aspect-square rounded-xl overflow-hidden bg-gray-100';
                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        // Géocodage auto de l'adresse
        let geoTimeout;
        document.getElementById('localisation')?.addEventListener('input', function() {
            clearTimeout(geoTimeout);
            geoTimeout = setTimeout(() => {
                const adresse = this.value;
                if (adresse.length < 5) return;
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(adresse)}&limit=1&countrycodes=fr`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.length > 0) {
                            document.getElementById('latitude').value  = data[0].lat;
                            document.getElementById('longitude').value = data[0].lon;
                            // Extraire ville et code postal si disponible
                            const addr = data[0].display_name.split(',');
                            if (addr.length > 1) {
                                document.getElementById('ville').value = addr[1]?.trim() ?? '';
                            }
                        }
                    })
                    .catch(() => {});
            }, 700);
        });
    </script>
</x-app-layout>
