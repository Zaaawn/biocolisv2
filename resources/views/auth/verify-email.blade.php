{{-- resources/views/auth/verify-email.blade.php --}}
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">📧</div>
                <h1 class="text-2xl font-bold text-gray-900">Vérifiez votre email</h1>
                <p class="text-gray-500 text-sm mt-2">Bienvenue sur Biocolis !</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <p class="text-sm text-gray-600 leading-relaxed mb-6">
                    Un lien de vérification a été envoyé à votre adresse email.
                    Cliquez dessus pour activer votre compte et commencer à acheter ou vendre des produits frais. 🌱
                </p>

                @if(session('status') == 'verification-link-sent')
                    <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
                        ✅ Un nouveau lien de vérification a été envoyé à votre adresse email.
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                        Renvoyer le lien de vérification
                    </button>
                </form>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-sm text-gray-400 hover:text-red-500 transition">
                            Se déconnecter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
