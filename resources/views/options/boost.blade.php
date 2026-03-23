{{-- resources/views/options/boost.blade.php --}}
<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 py-8">

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('annonces.show', $annonce->slug) }}"
               class="p-2 rounded-xl border border-gray-200 hover:border-gray-300 transition text-gray-500">
                ←
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">🚀 Booster mon annonce</h1>
                <p class="text-gray-500 text-sm mt-0.5">{{ $annonce->titre }}</p>
            </div>
        </div>

        {{-- Annonce résumé --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 flex items-center gap-4">
            <img src="{{ $annonce->premiere_photo }}" class="w-16 h-16 rounded-xl object-cover">
            <div>
                <div class="font-semibold text-gray-900">{{ $annonce->titre }}</div>
                <div class="text-sm text-gray-400">{{ number_format($annonce->prix, 2) }}€ / {{ $annonce->unite_prix }} · {{ $annonce->nb_vues }} vues</div>
            </div>
            @if($annonce->est_mise_en_avant || $annonce->est_epinglee)
                <div class="ml-auto flex gap-2">
                    @if($annonce->est_mise_en_avant)
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full font-medium">⭐ En avant</span>
                    @endif
                    @if($annonce->est_epinglee)
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium">📌 Épinglée</span>
                    @endif
                </div>
            @endif
        </div>

        {{-- Options --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            @foreach($options as $option)
                <div class="bg-white rounded-2xl border {{ $option['actif'] ? 'border-green-300 bg-green-50/30' : 'border-gray-100' }} p-5 relative overflow-hidden">

                    @if($option['actif'])
                        <div class="absolute top-3 right-3">
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">✅ Actif</span>
                        </div>
                    @endif

                    <div class="text-3xl mb-3">{{ $option['ico'] }}</div>
                    <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $option['titre'] }}</h3>
                    <p class="text-sm text-gray-500 mb-4 leading-relaxed">{{ $option['description'] }}</p>

                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ number_format($option['tarif'], 2) }}€</div>
                            @if($option['duree'])
                                <div class="text-xs text-gray-400">pendant {{ $option['duree'] }} jours</div>
                            @endif
                        </div>

                        @if(!$option['actif'])
                            <form action="{{ route('options.payer', $annonce->slug) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="{{ $option['type'] }}">
                                <button type="submit"
                                    class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl text-sm transition">
                                    Activer →
                                </button>
                            </form>
                        @else
                            <div class="text-sm text-green-600 font-medium">Déjà actif</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Historique options --}}
        @if($optionsActives->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h2 class="font-semibold text-gray-900 mb-4">Options actives</h2>
                <div class="space-y-3">
                    @foreach($optionsActives as $opt)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ ['mise_en_avant'=>'⭐ Mise en avant','epinglage'=>'📌 Épinglage','remontee'=>'🚀 Remontée','urgent'=>'🔥 Urgent'][$opt->type] ?? $opt->type }}
                                </div>
                                @if($opt->fin_at)
                                    <div class="text-xs text-gray-400">
                                        Expire {{ $opt->fin_at->diffForHumans() }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-sm font-semibold text-gray-900">
                                {{ number_format($opt->prix_paye, 2) }}€
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Info --}}
        <div class="mt-4 bg-blue-50 rounded-2xl p-4 text-sm text-blue-700 flex items-start gap-2">
            <span>ℹ️</span>
            <div>
                Le paiement est sécurisé par Stripe. Les options sont activées immédiatement après paiement.
                Vous pouvez cumuler plusieurs options sur la même annonce.
            </div>
        </div>
    </div>
</x-app-layout>
