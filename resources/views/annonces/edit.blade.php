{{-- resources/views/annonces/edit.blade.php --}}
<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-8">

        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('annonces.show', $annonce->slug) }}"
               class="text-gray-400 hover:text-gray-600 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Modifier l'annonce</h1>
                <p class="text-gray-500 text-sm">{{ $annonce->titre }}</p>
            </div>
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

        <form method="POST" action="{{ route('annonces.update', $annonce->slug) }}"
              enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Statut --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut de l'annonce</label>
                <select name="statut"
                    class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                    @foreach(['disponible'=>'✅ Disponible','brouillon'=>'📝 Brouillon','desactivee'=>'🚫 Désactivée'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $annonce->statut === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Section 1 : Produit --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h2 class="font-semibold text-gray-900">Votre produit</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Titre</label>
                    <input type="text" name="titre" value="{{ old('titre', $annonce->titre) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Type</label>
                        <select name="type_produit" required
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                            @foreach(['legume'=>'🥕 Légume','fruit'=>'🍓 Fruit','herbe'=>'🌿 Herbe','champignon'=>'🍄 Champignon','autre'=>'📦 Autre'] as $val => $lbl)
                                <option value="{{ $val }}" {{ $annonce->type_produit === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie</label>
                        <input type="text" name="categorie" value="{{ old('categorie', $annonce->categorie) }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Label</label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach(['bio'=>['🍃','Bio'],'local'=>['📍','Local'],'raisonne'=>['♻️','Raisonné'],'conventionnel'=>['🌾','Conv.']] as $val => [$ico,$lbl])
                            <label class="cursor-pointer">
                                <input type="radio" name="label" value="{{ $val }}"
                                    {{ old('label', $annonce->label) === $val ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="border-2 rounded-xl p-2 text-center text-xs transition peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 border-gray-200 text-gray-600">
                                    <div class="text-lg">{{ $ico }}</div>
                                    <div class="font-medium">{{ $lbl }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                    <textarea name="description" rows="4" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm resize-none">{{ old('description', $annonce->description) }}</textarea>
                </div>
            </div>

            {{-- Section 2 : Prix & Quantité --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h2 class="font-semibold text-gray-900">Prix & Quantité</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Prix (€)</label>
                        <input type="number" name="prix" value="{{ old('prix', $annonce->prix) }}"
                            step="0.1" min="0.1" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Unité</label>
                        <select name="unite_prix"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                            @foreach(['kg'=>'kg','unite'=>'unité','lot'=>'lot','caisse'=>'caisse'] as $val => $lbl)
                                <option value="{{ $val }}" {{ $annonce->unite_prix === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Quantité disponible</label>
                        <input type="number" name="quantite_disponible"
                            value="{{ old('quantite_disponible', $annonce->quantite_disponible) }}"
                            step="0.1" min="0.01" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de récolte</label>
                        <input type="date" name="date_recolte"
                            value="{{ old('date_recolte', $annonce->date_recolte?->format('Y-m-d')) }}"
                            max="{{ date('Y-m-d') }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                    </div>
                </div>
            </div>

            {{-- Section 3 : Photos --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
                <h2 class="font-semibold text-gray-900">Photos</h2>

                {{-- Photos existantes --}}
                @if($annonce->photos && count($annonce->photos) > 0)
                    <div>
                        <p class="text-xs text-gray-500 mb-2">Photos actuelles (cochez pour supprimer)</p>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($annonce->photos as $photo)
                                <label class="relative cursor-pointer group">
                                    <input type="checkbox" name="photos_supprimer[]" value="{{ $photo }}" class="sr-only peer">
                                    <div class="aspect-square rounded-xl overflow-hidden border-2 border-transparent peer-checked:border-red-500 peer-checked:opacity-50 transition">
                                        <img src="{{ asset('storage/'.$photo) }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center opacity-0 peer-checked:opacity-100 transition">×</div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Nouvelles photos --}}
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-green-400 transition cursor-pointer"
                     onclick="document.getElementById('new-photos').click()">
                    <input type="file" id="new-photos" name="photos_nouvelles[]" multiple accept="image/*" class="hidden"
                           onchange="previewNewPhotos(this)">
                    <div class="text-2xl mb-1">📸</div>
                    <p class="text-sm text-gray-500">Ajouter des photos</p>
                </div>
                <div id="new-photos-preview" class="grid grid-cols-4 gap-2 hidden"></div>
            </div>

            {{-- Boutons --}}
            <div class="flex gap-3">
                <a href="{{ route('annonces.show', $annonce->slug) }}"
                   class="flex-1 py-3 text-center border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:border-gray-300 transition">
                    Annuler
                </a>
                <button type="submit"
                    class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    <script>
        function previewNewPhotos(input) {
            const preview = document.getElementById('new-photos-preview');
            preview.innerHTML = '';
            preview.classList.remove('hidden');
            Array.from(input.files).forEach(f => {
                const r = new FileReader();
                r.onload = e => {
                    const div = document.createElement('div');
                    div.className = 'aspect-square rounded-xl overflow-hidden bg-gray-100';
                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    preview.appendChild(div);
                };
                r.readAsDataURL(f);
            });
        }
    </script>
</x-app-layout>
