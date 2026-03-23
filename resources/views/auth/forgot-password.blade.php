{{-- resources/views/auth/forgot-password.blade.php --}}
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <a href="{{ route('accueil') }}">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-white font-bold">B</span>
                    </div>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Mot de passe oublié ?</h1>
                <p class="text-gray-500 text-sm mt-1">Pas de panique, ça arrive !</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <p class="text-sm text-gray-600 mb-6">
                    Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
                </p>

                @if(session('status'))
                    <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
                        ✅ {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Adresse email
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            required autofocus
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition text-sm"
                            placeholder="vous@exemple.fr">
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                        Envoyer le lien de réinitialisation
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">
                        ← Retour à la connexion
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
