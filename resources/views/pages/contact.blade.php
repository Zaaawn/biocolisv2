{{-- resources/views/pages/contact.blade.php --}}
<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-12">

        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-3">Contactez-nous</h1>
            <p class="text-gray-500">Une question ? Un problème ? On est là pour vous aider. 🌱</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            @foreach([
                ['📧', 'Email', 'support@biocolis.fr', 'Réponse sous 24h'],
                ['💬', 'Chat', 'Via la messagerie', 'Disponible après connexion'],
                ['📍', 'Adresse', 'Paris, France', 'Pas de visite sans RDV'],
            ] as [$ico, $titre, $val, $sub])
                <div class="bg-white rounded-2xl border border-gray-100 p-6 text-center">
                    <div class="text-3xl mb-3">{{ $ico }}</div>
                    <div class="font-semibold text-gray-900 mb-1">{{ $titre }}</div>
                    <div class="text-sm text-gray-700 font-medium">{{ $val }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $sub }}</div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- Formulaire --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-7">
                <h2 class="font-bold text-gray-900 text-lg mb-5">Envoyer un message</h2>

                @if(session('contact_sent'))
                    <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm mb-5">
                        ✅ Votre message a bien été envoyé ! Nous vous répondrons sous 24h.
                    </div>
                @endif

                <form method="POST" action="{{ route('pages.contact.send') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Prénom</label>
                            <input type="text" name="prenom" value="{{ old('prenom', auth()->user()?->prenom) }}"
                                required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm"
                                placeholder="Marie">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom</label>
                            <input type="text" name="nom" value="{{ old('nom', auth()->user()?->nom) }}"
                                required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm"
                                placeholder="Dupont">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm"
                            placeholder="vous@exemple.fr">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Sujet</label>
                        <select name="sujet"
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm">
                            <option value="question">Question générale</option>
                            <option value="commande">Problème avec une commande</option>
                            <option value="annonce">Problème avec une annonce</option>
                            <option value="paiement">Problème de paiement</option>
                            <option value="compte">Problème de compte</option>
                            <option value="signalement">Signalement</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Message</label>
                        <textarea name="message" rows="5" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm resize-none"
                            placeholder="Décrivez votre demande...">{{ old('message') }}</textarea>
                        @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                        Envoyer le message
                    </button>
                </form>
            </div>

            {{-- FAQ rapide --}}
            <div class="space-y-4">
                <h2 class="font-bold text-gray-900 text-lg">Questions fréquentes</h2>

                @foreach([
                    ['Comment créer une annonce ?', 'Connectez-vous, cliquez sur "+ Déposer" en haut à droite et remplissez le formulaire. Votre annonce est publiée instantanément.'],
                    ['Comment recevoir mes paiements ?', 'Activez votre compte Stripe depuis votre espace vendeur. Les virements sont effectués automatiquement après chaque vente.'],
                    ['Puis-je annuler une commande ?', 'Oui, tant que le vendeur n\'a pas commencé la préparation. Rendez-vous dans "Mes commandes" pour annuler.'],
                    ['Comment signaler un problème ?', 'Utilisez le bouton de signalement sur l\'annonce ou le profil concerné, ou contactez-nous directement via ce formulaire.'],
                    ['Quels produits puis-je vendre ?', 'Uniquement des produits alimentaires frais : fruits, légumes, herbes aromatiques, champignons et produits de la ferme.'],
                ] as [$q, $r])
                    <div x-data="{ open: false }" class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between px-5 py-4 text-left">
                            <span class="text-sm font-semibold text-gray-900">{{ $q }}</span>
                            <svg :class="open ? 'rotate-180' : ''"
                                 class="h-4 w-4 text-gray-400 transition-transform flex-shrink-0 ml-3"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="px-5 pb-4">
                            <p class="text-sm text-gray-500 leading-relaxed">{{ $r }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
