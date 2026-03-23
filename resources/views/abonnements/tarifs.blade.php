{{-- resources/views/abonnements/tarifs.blade.php --}}
<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-12" x-data="{ periodicite: 'mensuel' }">

        {{-- Header --}}
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-3">Plans B2B Biocolis</h1>
            <p class="text-xl text-gray-500 max-w-2xl mx-auto">
                Commissions réduites, plus de visibilité, outils pro. Développez votre activité.
            </p>

            {{-- Toggle mensuel/annuel --}}
            <div class="flex items-center justify-center gap-4 mt-6">
                <span :class="periodicite === 'mensuel' ? 'text-gray-900 font-semibold' : 'text-gray-400'" class="text-sm">Mensuel</span>
                <button @click="periodicite = periodicite === 'mensuel' ? 'annuel' : 'mensuel'"
                    class="relative w-12 h-6 rounded-full transition-colors"
                    :class="periodicite === 'annuel' ? 'bg-green-600' : 'bg-gray-300'">
                    <span class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform"
                          :class="periodicite === 'annuel' ? 'translate-x-6' : ''"></span>
                </button>
                <span :class="periodicite === 'annuel' ? 'text-gray-900 font-semibold' : 'text-gray-400'" class="text-sm">
                    Annuel <span class="text-green-600 font-bold text-xs ml-1">-17%</span>
                </span>
            </div>
        </div>

        {{-- Plans --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            @foreach($plans as $plan)
                @php
                    $estPopulaire = $plan->slug === 'business';
                    $estActif = $abonnementActif?->plan_id === $plan->id;
                @endphp

                <div class="bg-white rounded-2xl border {{ $estPopulaire ? 'border-green-500 shadow-lg shadow-green-100' : 'border-gray-200' }} p-6 relative flex flex-col">

                    @if($estPopulaire)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="bg-green-600 text-white text-xs font-bold px-4 py-1 rounded-full">⭐ Populaire</span>
                        </div>
                    @endif

                    @if($estActif)
                        <div class="absolute top-4 right-4">
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">✅ Actif</span>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-gray-900">{{ $plan->nom }}</h3>
                        <div class="mt-3">
                            <div x-show="periodicite === 'mensuel'" class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-gray-900">{{ number_format($plan->prix_mensuel, 0) }}€</span>
                                <span class="text-gray-400 text-sm">/mois</span>
                            </div>
                            <div x-show="periodicite === 'annuel'" x-cloak class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-gray-900">{{ number_format($plan->prix_annuel / 12, 0) }}€</span>
                                <span class="text-gray-400 text-sm">/mois</span>
                            </div>
                            <div x-show="periodicite === 'annuel'" x-cloak class="text-xs text-green-600 mt-1">
                                Facturé {{ number_format($plan->prix_annuel, 0) }}€/an
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-green-700 font-semibold bg-green-50 px-3 py-1 rounded-lg inline-block">
                            Commission {{ $plan->commission_pct }}% seulement
                        </div>
                    </div>

                    <ul class="space-y-2 mb-6 flex-1">
                        @foreach($plan->fonctionnalites as $feature)
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <span class="text-green-500 flex-shrink-0 mt-0.5">✓</span>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    @auth
                        @if($estActif)
                            <a href="{{ route('abonnements.mon-abonnement') }}"
                               class="w-full py-3 text-center border-2 border-green-500 text-green-600 font-semibold rounded-xl text-sm transition hover:bg-green-50">
                                Gérer mon abonnement
                            </a>
                        @else
                            <form action="{{ route('abonnements.souscrire', $plan->slug) }}" method="POST">
                                @csrf
                                <input type="hidden" name="periodicite" :value="periodicite">
                                <button type="submit"
                                    class="w-full py-3 {{ $estPopulaire ? 'bg-green-600 hover:bg-green-700 text-white' : 'border-2 border-gray-200 hover:border-green-400 text-gray-700' }} font-semibold rounded-xl text-sm transition">
                                    Commencer avec {{ $plan->nom }}
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('register') }}"
                           class="w-full py-3 text-center {{ $estPopulaire ? 'bg-green-600 hover:bg-green-700 text-white' : 'border-2 border-gray-200 hover:border-green-400 text-gray-700' }} font-semibold rounded-xl text-sm transition block">
                            Commencer avec {{ $plan->nom }}
                        </a>
                    @endauth
                </div>
            @endforeach
        </div>

        {{-- Comparaison commission --}}
        <div class="bg-gradient-to-br from-green-600 to-emerald-500 rounded-2xl p-8 text-white text-center mb-8">
            <h2 class="text-2xl font-bold mb-2">Économisez sur chaque vente</h2>
            <p class="text-green-100 mb-6">vs. la commission standard de 12%</p>
            <div class="grid grid-cols-4 gap-4">
                @foreach([['Standard', '12%', 'Sans abonnement'], ['Starter', '10%', '-2%'], ['Business', '8%', '-4%'], ['Premium', '5%', '-7%']] as [$nom, $pct, $eco])
                    <div class="bg-white/10 rounded-xl p-4">
                        <div class="text-2xl font-bold">{{ $pct }}</div>
                        <div class="text-sm font-semibold mt-1">{{ $nom }}</div>
                        <div class="text-xs text-green-200 mt-0.5">{{ $eco }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- FAQ --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 text-lg mb-4">Questions fréquentes</h2>
            <div class="space-y-4">
                @foreach([
                    ['Puis-je changer de plan ?', 'Oui, vous pouvez upgrader ou downgrader à tout moment. Le nouveau tarif s\'applique immédiatement.'],
                    ['Comment fonctionne la commission réduite ?', 'Dès l\'activation de votre plan, la commission Biocolis est automatiquement réduite sur toutes vos ventes.'],
                    ['Puis-je annuler ?', 'Oui, à tout moment. Votre abonnement reste actif jusqu\'à la fin de la période payée.'],
                    ['Y a-t-il une période d\'essai ?', 'Contactez-nous à support@biocolis.fr pour discuter d\'une démo ou d\'un essai gratuit.'],
                ] as [$q, $r])
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between text-left py-3 border-b border-gray-100">
                            <span class="font-medium text-gray-900 text-sm">{{ $q }}</span>
                            <svg :class="open ? 'rotate-180' : ''" class="h-4 w-4 text-gray-400 transition-transform flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="py-3 text-sm text-gray-500">{{ $r }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
