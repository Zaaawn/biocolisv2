{{-- resources/views/commandes/success.blade.php --}}
<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-16 text-center">
        <div class="text-6xl mb-6">🎉</div>
        <h1 class="text-3xl font-bold text-gray-900 mb-3">Paiement confirmé !</h1>
        <p class="text-gray-500 mb-8">Merci pour votre commande. Le(s) vendeur(s) ont été notifiés et vont préparer vos produits.</p>

        @if($commandes->isNotEmpty())
            <div class="space-y-4 mb-8 text-left">
                @foreach($commandes as $commande)
                    <div class="bg-white rounded-2xl border border-gray-100 p-5">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $commande->numero }}</div>
                                <div class="text-sm text-gray-400">{{ $commande->vendeur->nom_complet }}</div>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-green-100 text-green-700">
                                ✓ Payée
                            </span>
                        </div>

                        <div class="space-y-2 mb-4">
                            @foreach($commande->lignes as $ligne)
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>{{ $ligne->titre_annonce }} × {{ $ligne->quantite }} {{ $ligne->unite_prix }}</span>
                                    <span class="font-medium">{{ number_format($ligne->sous_total, 2) }}€</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-100 pt-3 flex justify-between font-bold text-gray-900">
                            <span>Total</span>
                            <span class="text-green-600">{{ number_format($commande->total_ttc, 2) }}€</span>
                        </div>

                        <div class="mt-3 text-sm text-gray-500 bg-gray-50 rounded-xl p-3">
                            📦 Livraison :
                            {{ match($commande->livraison?->mode) {
                                'main_propre'   => 'Remise en main propre',
                                'point_relais'  => 'Point relais',
                                'domicile'      => 'À domicile',
                                'locker'        => 'Casier connecté',
                                default         => '—'
                            } }}
                        </div>

                        <a href="{{ route('commandes.show', $commande->id) }}"
                           class="mt-3 w-full block text-center py-2 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:border-green-500 hover:text-green-600 transition">
                            Suivre cette commande
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex gap-3 justify-center">
            <a href="{{ route('commandes.mes-commandes') }}"
               class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition">
                Mes commandes
            </a>
            <a href="{{ route('annonces.index') }}"
               class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:border-gray-300 transition">
                Continuer les achats
            </a>
        </div>
    </div>
</x-app-layout>
