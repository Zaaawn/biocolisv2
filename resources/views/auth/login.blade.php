{{-- resources/views/auth/login.blade.php --}}
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
        <div class="w-full max-w-md">

            {{-- Logo --}}
         <div class="text-center mb-8">
    <a href="{{ route('accueil') }}" class="inline-flex items-center justify-center gap-2 mb-4 group">
        <img src="{{ asset('images/logo-biocolis.png') }}" alt="Biocolis" class="h-10 w-auto">
        <span class="font-bold text-gray-900 text-xl">Biocolis</span>
    </a>

    <h1 class="text-2xl font-bold text-gray-900 mt-2">Créer un compte</h1>
    <p class="text-gray-500 text-sm mt-1">Rejoignez la communauté locale 🌱</p>
</div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

                @if(session('status'))
                    <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Adresse email
                        </label>
                        <input id="email" name="email" type="email"
                            value="{{ old('email') }}"
                            required autofocus autocomplete="email"
                            placeholder="vous@exemple.fr"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition text-sm @error('email') border-red-400 @enderror">
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Mot de passe
                            </label>
                            @if(Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-xs text-green-600 hover:text-green-700 font-medium">
                                    Mot de passe oublié ?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input id="password" name="password" type="password"
                                required autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition text-sm pr-10 @error('password') border-red-400 @enderror">
                            <button type="button" onclick="togglePwd('password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="remember" name="remember" type="checkbox"
                            class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <label for="remember" class="text-sm text-gray-600">
                            Se souvenir de moi
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm shadow-sm">
                        Se connecter
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                    <p class="text-sm text-gray-500">
                        Pas encore de compte ?
                        <a href="{{ route('register') }}" class="text-green-600 font-semibold hover:text-green-700">
                            Créer un compte
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
    </script>
</x-guest-layout>
