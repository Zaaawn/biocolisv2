{{-- resources/views/auth/reset-password.blade.php --}}
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <a href="{{ route('accueil') }}">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-white font-bold">B</span>
                    </div>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Nouveau mot de passe</h1>
                <p class="text-gray-500 text-sm mt-1">Choisissez un mot de passe sécurisé</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                @if($errors->any())
                    <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input id="email" type="email" name="email"
                            value="{{ old('email', $request->email) }}"
                            required autofocus
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition text-sm">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nouveau mot de passe
                        </label>
                        <input id="password" type="password" name="password"
                            required autocomplete="new-password"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition text-sm"
                            placeholder="Min. 8 caractères">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Confirmer le mot de passe
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            required autocomplete="new-password"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition text-sm"
                            placeholder="Répéter le mot de passe">
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                        Réinitialiser le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
