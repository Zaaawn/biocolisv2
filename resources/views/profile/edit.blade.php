{{-- resources/views/profile/edit.blade.php --}}
<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-8">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Mon profil</h1>
            <p class="text-gray-500 text-sm mt-1">Gérez vos informations personnelles et votre sécurité</p>
        </div>

        {{-- ── PHOTO + INFOS RAPIDES ─────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 flex items-center gap-5">
            <div class="relative">
                <img src="{{ auth()->user()->photo_profil_url }}"
                     class="w-20 h-20 rounded-2xl object-cover border-2 border-gray-100"
                     id="photo-preview-main">
                <label class="absolute -bottom-1 -right-1 w-7 h-7 bg-green-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-green-700 transition shadow">
                    <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    <input type="file" class="hidden" accept="image/*"
                           onchange="previewProfilePhoto(this)">
                </label>
            </div>
            <div>
                <div class="font-bold text-gray-900 text-lg">{{ auth()->user()->nom_complet }}</div>
                <div class="text-sm text-gray-400">{{ auth()->user()->email }}</div>
                <span class="inline-block mt-1 text-xs px-2.5 py-1 rounded-full font-medium
                    {{ match(auth()->user()->role) {
                        'admin' => 'bg-red-100 text-red-600',
                        'b2b' => 'bg-purple-100 text-purple-600',
                        'professionnel' => 'bg-blue-100 text-blue-600',
                        default => 'bg-green-100 text-green-600'
                    } }}">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>
            <div class="ml-auto text-right hidden sm:block">
                <div class="text-2xl font-bold text-gray-900">{{ auth()->user()->nb_annonces }}</div>
                <div class="text-xs text-gray-400">annonces</div>
            </div>
            <div class="text-right hidden sm:block">
                <div class="text-2xl font-bold text-gray-900 flex items-center gap-1">
                    <span class="text-yellow-400">★</span>
                    {{ auth()->user()->note_moyenne > 0 ? number_format(auth()->user()->note_moyenne, 1) : '—' }}
                </div>
                <div class="text-xs text-gray-400">note moyenne</div>
            </div>
        </div>

        {{-- ── INFOS PERSONNELLES ────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
            <h2 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">
                <span class="w-6 h-6 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                Informations personnelles
            </h2>

            @if(session('profile-updated'))
                <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
                    ✅ Profil mis à jour avec succès.
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')

                {{-- Photo cachée pour upload --}}
                <input type="file" name="photo_profil" id="photo-input-hidden" class="hidden"
                       onchange="previewProfilePhoto(this)">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom', auth()->user()->prenom) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                        @error('prenom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom</label>
                        <input type="text" name="nom" value="{{ old('nom', auth()->user()->nom) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                        @error('nom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom d'utilisateur</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">@</span>
                        <input type="text" name="username" value="{{ old('username', auth()->user()->username) }}"
                            class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                    </div>
                    @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email
                        @if(!auth()->user()->hasVerifiedEmail())
                            <span class="text-amber-500 text-xs ml-1">(non vérifié)</span>
                        @endif
                    </label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                    <input type="tel" name="telephone" value="{{ old('telephone', auth()->user()->telephone) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm"
                        placeholder="06 12 34 56 78">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse</label>
                    <input type="text" name="adresse" value="{{ old('adresse', auth()->user()->adresse) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                </div>

                {{-- Infos pro --}}
                @if(auth()->user()->isProfessionnel())
                    <div class="pt-4 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Informations professionnelles</p>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom de la société</label>
                                <input type="text" name="societe_nom" value="{{ old('societe_nom', auth()->user()->societe_nom) }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">SIRET</label>
                                    <input type="text" name="siret" value="{{ old('siret', auth()->user()->siret) }}"
                                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">N° TVA</label>
                                    <input type="text" name="tva_intracommunautaire" value="{{ old('tva_intracommunautaire', auth()->user()->tva_intracommunautaire) }}"
                                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="pt-2">
                    <button type="submit"
                        class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>

        {{-- ── CHANGER MOT DE PASSE ──────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
            <h2 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">
                <span class="w-6 h-6 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                Sécurité
            </h2>

            @if(session('password-updated'))
                <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
                    ✅ Mot de passe mis à jour.
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe actuel</label>
                    <input type="password" name="current_password"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm"
                        placeholder="••••••••">
                    @error('current_password', 'updatePassword')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nouveau mot de passe</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm"
                        placeholder="Min. 8 caractères">
                    @error('password', 'updatePassword')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmer le nouveau mot de passe</label>
                    <input type="password" name="password_confirmation"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm"
                        placeholder="Répéter le mot de passe">
                </div>

                <button type="submit"
                    class="px-6 py-2.5 bg-gray-800 hover:bg-gray-900 text-white font-semibold rounded-xl transition text-sm">
                    Changer le mot de passe
                </button>
            </form>
        </div>
  {{-- ── IBAN — Recevoir les paiements ───────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6" id="iban">
            <h2 class="font-semibold text-gray-900 mb-1 flex items-center gap-2">
                <span class="w-6 h-6 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                Coordonnées bancaires
                <span class="text-xs font-normal text-gray-400 ml-1">— Pour recevoir vos paiements de vente</span>
            </h2>
            <p class="text-sm text-gray-500 mb-5">
                Biocolis vire vos gains directement sur votre compte bancaire après chaque vente confirmée.
                <strong>Pas besoin de société</strong> — votre IBAN personnel suffit.
            </p>

            @if(session('iban-updated'))
                <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
                    ✅ Coordonnées bancaires mises à jour.
                </div>
            @endif

            <form method="POST" action="{{ route('profile.iban') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Titulaire du compte
                    </label>
                    <input type="text" name="titulaire_compte"
                        value="{{ old('titulaire_compte', auth()->user()->titulaire_compte) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm"
                        placeholder="Marie Dupont">
                    @error('titulaire_compte') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        IBAN
                    </label>
                    <input type="text" name="iban"
                        value="{{ old('iban', auth()->user()->iban ? '••••' . substr(auth()->user()->iban, -4) : '') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm font-mono tracking-wider uppercase"
                        placeholder="FR76 3000 6000 0112 3456 7890 189"
                        maxlength="34"
                        x-on:input="this.value = this.value.replace(/[^A-Z0-9]/gi, '').toUpperCase().replace(/(.{4})/g, '$1 ').trim()">
                    <p class="text-xs text-gray-400 mt-1">Format : FR76 XXXX XXXX XXXX XXXX XXXX XXX</p>
                    @error('iban') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        BIC / SWIFT <span class="text-gray-400 font-normal">(optionnel)</span>
                    </label>
                    <input type="text" name="bic"
                        value="{{ old('bic', auth()->user()->bic) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm font-mono uppercase"
                        placeholder="BNPAFRPP"
                        maxlength="11">
                    @error('bic') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-start gap-3 pt-2">
                    <div class="text-green-600 mt-0.5">🔒</div>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Vos coordonnées bancaires sont chiffrées et sécurisées.
                        Elles sont uniquement utilisées pour vous virer vos gains de ventes.
                        Biocolis ne prélève jamais d'argent sur votre compte.
                    </p>
                </div>

                <button type="submit"
                    class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                    Enregistrer mes coordonnées bancaires
                </button>
            </form>
        </div>

        {{-- ── SUPPRIMER LE COMPTE ───────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-red-100 p-6"
             x-data="{ confirm: false }">
            <h2 class="font-semibold text-red-600 mb-2 flex items-center gap-2">
                <span>⚠️</span> Zone de danger
            </h2>
            <p class="text-sm text-gray-500 mb-4">
                La suppression de votre compte est définitive. Toutes vos annonces, messages et données seront supprimés.
            </p>

            <button @click="confirm = true"
                class="px-4 py-2 border border-red-300 text-red-500 hover:bg-red-50 rounded-xl text-sm font-medium transition">
                Supprimer mon compte
            </button>

            {{-- Modal confirmation --}}
            <div x-show="confirm" x-cloak
                 class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-xl">
                    <h3 class="font-bold text-gray-900 mb-2">Êtes-vous sûr ?</h3>
                    <p class="text-sm text-gray-500 mb-5">Cette action est irréversible. Toutes vos données seront définitivement supprimées.</p>
                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Confirmez avec votre mot de passe
                            </label>
                            <input type="password" name="password"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-red-400 outline-none text-sm"
                                placeholder="••••••••">
                            @error('password', 'userDeletion')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="confirm = false"
                                class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:border-gray-300 transition">
                                Annuler
                            </button>
                            <button type="submit"
                                class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition">
                                Supprimer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewProfilePhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('photo-preview-main').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
                // Copie le fichier dans le champ caché du form
                const dt = new DataTransfer();
                dt.items.add(input.files[0]);
                document.getElementById('photo-input-hidden').files = dt.files;
            }
        }
    </script>
</x-app-layout>
