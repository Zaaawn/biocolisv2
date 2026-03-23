{{-- resources/views/panier/checkout.blade.php --}}
<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-8">

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Finaliser la commande</h1>
        <p class="text-sm text-gray-400 mb-8">Vérifiez vos articles et procédez au paiement sécurisé</p>

        {{-- Articles par vendeur --}}
        @foreach($groupes as $vendeurId => $lignesVendeur)
            @php $vendeur = $lignesVendeur->first()->annonce->user; @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-4">
                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100">
                    <img src="{{ $vendeur->photo_profil_url }}" class="w-8 h-8 rounded-full object-cover">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ $vendeur->nom_complet }}</div>
                        <div class="text-xs text-gray-400">{{ $lignesVendeur->count() }} article(s)</div>
                    </div>
                </div>

                <div class="space-y-3 mb-4">
                    @foreach($lignesVendeur as $ligne)
                        <div class="flex items-center gap-3">
                            <img src="{{ $ligne->annonce->premiere_photo }}"
                                 class="w-12 h-12 rounded-xl object-cover">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $ligne->annonce->titre }}</div>
                                <div class="text-xs text-gray-400">
                                    {{ $ligne->quantite }} {{ $ligne->annonce->unite_prix }} ×
                                    {{ number_format($ligne->annonce->prix, 2) }}€
                                </div>
                            </div>
                            <div class="text-sm font-semibold text-gray-900">
                                {{ number_format($ligne->sous_total, 2) }}€
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-gray-50 rounded-xl p-3 text-xs text-gray-500 flex items-center gap-2">
                    @php $modeLiv = $lignesVendeur->first()->mode_livraison; @endphp
                    <span>{{ match($modeLiv) { 'main_propre'=>'🤝', 'point_relais'=>'📦', 'domicile'=>'🏠', 'locker'=>'🗄️', default=>'' } }}</span>
                    <span>
                        Livraison :
                        {{ match($modeLiv) { 'main_propre'=>'Main propre (gratuit)', 'point_relais'=>'Point relais (+3,00€)', 'domicile'=>'À domicile (+6,00€)', 'locker'=>'Casier connecté (+2,50€)', default=>$modeLiv } }}
                    </span>
                </div>
            </div>
        @endforeach

        {{-- Récapitulatif total --}}
        <div class="bg-white rounded-2xl border border-green-100 p-5 mb-6">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Sous-total produits</span>
                    <span>{{ number_format($sousTotal, 2) }}€</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Frais de livraison</span>
                    <span>{{ number_format($fraisLivraison, 2) }}€</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Frais de service Biocolis</span>
                    <span>{{ number_format($fraisService, 2) }}€</span>
                </div>
                <div class="flex justify-between font-bold text-gray-900 text-base pt-3 border-t border-gray-100">
                    <span>Total à payer</span>
                    <span class="text-green-600">{{ number_format($totalTTC, 2) }}€</span>
                </div>
            </div>
        </div>

        {{-- Bouton paiement --}}
        <form action="{{ route('commandes.payer') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-2xl transition flex items-center justify-center gap-3 text-base">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Payer {{ number_format($totalTTC, 2) }}€ — Sécurisé par Stripe
            </button>
        </form>

        <div class="mt-4 flex items-center justify-center gap-4 text-xs text-gray-400">
            <span>💳 Visa, Mastercard, CB</span>
            <span>·</span>
            <span>🔒 Paiement chiffré SSL</span>
            <span>·</span>
            <span>↩️ Remboursement garanti</span>
        </div>
    </div>
</x-app-layout>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- resources/views/commandes/success.blade.php                               --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
