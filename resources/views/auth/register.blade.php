{{-- resources/views/auth/register.blade.php --}}
<x-guest-layout>
    <div class="min-h-screen bg-gray-50 py-8 px-4">
        <div class="w-full max-w-xl mx-auto">

       <div class="text-center mb-8">
    <a href="{{ route('accueil') }}" class="inline-flex items-center justify-center gap-2 mb-4 group">
        <img src="{{ asset('images/logo-biocolis.png') }}" alt="Biocolis" class="h-10 w-auto">
        <span class="font-bold text-gray-900 text-xl">Biocolis</span>
    </a>

    <h1 class="text-2xl font-bold text-gray-900 mt-2">Créer un compte</h1>
    <p class="text-gray-500 text-sm mt-1">Rejoignez la communauté locale 🌱</p>
</div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

                @if($errors->any())
                    <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                        <ul class="space-y-1 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
                      x-data="{ role: '{{ old('role', 'particulier') }}', strength: 0, strengthLabel: '' }"
                      class="space-y-5">
                    @csrf

                    {{-- Choix du rôle --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Je suis... <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach([
                                ['particulier', '🏡', 'Particulier', 'J\'ai un jardin'],
                                ['professionnel', '🚜', 'Producteur', 'Pro agricole'],
                                ['b2b', '🏨', 'B2B', 'Restaurant, hôtel...'],
                            ] as [$val, $ico, $lbl, $sub])
                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="{{ $val }}" x-model="role" class="sr-only">
                                    <div :class="role === '{{ $val }}'
                                        ? 'border-green-500 bg-green-50 text-green-700'
                                        : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                                        class="border-2 rounded-xl p-3 text-center transition">
                                        <div class="text-2xl mb-1">{{ $ico }}</div>
                                        <div class="text-xs font-semibold">{{ $lbl }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $sub }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Prénom / Nom --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Prénom <span class="text-red-500">*</span></label>
                            <input type="text" name="prenom" value="{{ old('prenom') }}" required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm @error('prenom') border-red-400 @enderror"
                                placeholder="Marie">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom <span class="text-red-500">*</span></label>
                            <input type="text" name="nom" value="{{ old('nom') }}" required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm @error('nom') border-red-400 @enderror"
                                placeholder="Dupont">
                        </div>
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom d'utilisateur <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">@</span>
                            <input type="text" name="username" value="{{ old('username') }}" required
                                class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm @error('username') border-red-400 @enderror"
                                placeholder="marie_dupont">
                        </div>
                        @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm @error('email') border-red-400 @enderror"
                            placeholder="vous@exemple.fr">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Téléphone --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                        <input type="tel" name="telephone" value="{{ old('telephone') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm"
                            placeholder="06 12 34 56 78">
                    </div>

                    {{-- Adresse --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse <span class="text-red-500">*</span></label>
                        <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm"
                            placeholder="4 rue des Lilas, Paris">
                        <input type="hidden" name="latitude" id="lat">
                        <input type="hidden" name="longitude" id="lng">
                    </div>

                    {{-- Infos pro --}}
                    <div x-show="role === 'professionnel' || role === 'b2b'"
                         x-transition class="space-y-4 pt-4 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Informations professionnelles</p>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom société <span class="text-red-500">*</span></label>
                            <input type="text" name="societe_nom" value="{{ old('societe_nom') }}"
                                :required="role === 'professionnel' || role === 'b2b'"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm"
                                placeholder="SARL Les Jardins de Marie">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">SIRET <span class="text-red-500">*</span></label>
                                <input type="text" name="siret" value="{{ old('siret') }}" maxlength="14"
                                    :required="role === 'professionnel' || role === 'b2b'"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm"
                                    placeholder="12345678901234">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Adresse société</label>
                                <input type="text" name="societe_adresse" value="{{ old('societe_adresse') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Mot de passe --}}
                    <div class="pt-4 border-t border-gray-100 space-y-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Sécurité</p>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input id="password" type="password" name="password" required
                                    autocomplete="new-password"
                                    x-on:input="checkPwd($event.target.value)"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm pr-10 @error('password') border-red-400 @enderror"
                                    placeholder="Min. 8 caractères">
                                <button type="button" onclick="togglePwd('password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            {{-- Force mdp --}}
                            <div class="mt-2 space-y-1">
                                <div class="flex gap-1">
                                    <div class="h-1 flex-1 rounded-full transition-colors" :class="strength >= 1 ? 'bg-red-400' : 'bg-gray-200'"></div>
                                    <div class="h-1 flex-1 rounded-full transition-colors" :class="strength >= 2 ? 'bg-orange-400' : 'bg-gray-200'"></div>
                                    <div class="h-1 flex-1 rounded-full transition-colors" :class="strength >= 3 ? 'bg-yellow-400' : 'bg-gray-200'"></div>
                                    <div class="h-1 flex-1 rounded-full transition-colors" :class="strength >= 4 ? 'bg-green-500' : 'bg-gray-200'"></div>
                                </div>
                                <p class="text-xs text-gray-400" x-text="strengthLabel"></p>
                            </div>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                    autocomplete="new-password"
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm pr-10"
                                    placeholder="Répéter le mot de passe">
                                <button type="button" onclick="togglePwd('password_confirmation')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>


                    {{-- Photo de profil --}}
                    <div class="pt-4 border-t border-gray-100">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Photo de profil
                            <span class="text-gray-400 font-normal ml-1">(optionnel)</span>
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="relative flex-shrink-0">
                                <div id="pdp-placeholder"
                                    class="w-20 h-20 rounded-2xl bg-green-100 flex items-center justify-center cursor-pointer hover:bg-green-200 transition border-2 border-dashed border-green-300"
                                    onclick="document.getElementById('photo_profil_input').click()">
                                    <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div id="pdp-preview" class="hidden relative">
                                    <img id="pdp-img" src="" alt="Photo de profil"
                                        class="w-20 h-20 rounded-2xl object-cover border-2 border-green-400 cursor-pointer"
                                        onclick="document.getElementById('photo_profil_input').click()">
                                    <button type="button" onclick="supprimerPdp()"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-sm font-bold shadow transition">
                                        ×
                                    </button>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="file" id="photo_profil_input" name="photo_profil"
                                    accept="image/jpeg,image/png,image/jpg,image/webp"
                                    class="hidden" onchange="previewPdp(this)">
                                <button type="button"
                                    onclick="document.getElementById('photo_profil_input').click()"
                                    class="px-4 py-2 border border-gray-200 hover:border-green-400 rounded-xl text-sm text-gray-600 hover:text-green-600 transition font-medium">
                                    📸 Choisir une photo
                                </button>
                                <p class="text-xs text-gray-400 mt-1.5">JPEG, PNG, WebP — Max 2 Mo</p>
                                <p class="text-xs text-gray-400">Sans photo, un avatar avec vos initiales sera généré.</p>
                            </div>
                        </div>
                    </div>

                    {{-- CGV --}}
                    <div class="flex items-start gap-3 pt-2 border-t border-gray-100">
                        <input type="checkbox" name="cgv" id="cgv" required
                            class="mt-0.5 w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <label for="cgv" class="text-sm text-gray-600 leading-relaxed">
                            J'accepte les
                            <a href="#" class="text-green-600 underline">Conditions d'utilisation</a>
                            et la
                            <a href="#" class="text-green-600 underline">Politique de confidentialité</a>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm shadow-sm">
                        Créer mon compte
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                    <p class="text-sm text-gray-500">
                        Déjà un compte ?
                        <a href="{{ route('login') }}" class="text-green-600 font-semibold hover:text-green-700">
                            Se connecter
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePwd(id) {
            const i = document.getElementById(id);
            i.type = i.type === 'password' ? 'text' : 'password';
        }
        function checkPwd(v) {
            // handled by alpine
        }

        // Géocodage géré par le composant x-adresse-autocomplete

        // ── Photo de profil ──────────────────────────────────────────────────
        function previewPdp(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];

            // Vérif taille max 2Mo
            if (file.size > 2 * 1024 * 1024) {
                alert('La photo ne doit pas dépasser 2 Mo.');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('pdp-img').src = e.target.result;
                document.getElementById('pdp-placeholder').classList.add('hidden');
                document.getElementById('pdp-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        function supprimerPdp() {
            document.getElementById('photo_profil_input').value = '';
            document.getElementById('pdp-img').src = '';
            document.getElementById('pdp-preview').classList.add('hidden');
            document.getElementById('pdp-placeholder').classList.remove('hidden');
        }
    </script>
</x-guest-layout>
