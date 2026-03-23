{{-- resources/views/auth/confirm-password.blade.php --}}
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <a href="{{ route('accueil') }}" class="inline-flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">B</span>
                    </div>
                </a>
                <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-3">🔐</div>
                <h1 class="text-2xl font-bold text-gray-900">Zone sécurisée</h1>
                <p class="text-gray-500 text-sm mt-1">Confirmez votre mot de passe pour continuer</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                @if($errors->any())
                    <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe</label>
                        <input type="password" name="password" required autocomplete="current-password"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm"
                            placeholder="••••••••">
                    </div>
                    <button type="submit"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                        Confirmer
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
