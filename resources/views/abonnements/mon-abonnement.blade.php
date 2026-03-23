{{-- resources/views/abonnements/mon-abonnement.blade.php --}}
<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-8">

        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-xl border border-gray-200 hover:border-gray-300 text-gray-500">←</a>
            <h1 class="text-2xl font-bold text-gray-900">Mon abonnement</h1>
        </div>

        @if($abonnement && $abonnement->statut === 'actif')
            {{-- Abonnement actif --}}
            <div class="bg-white rounded-2xl border border-green-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-xs text-green-600 font-semibold uppercase tracking-wide mb-1">Plan actuel</div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $abonnement->plan->nom }}</h2>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">✅ Actif</span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <div class="text-xs text-gray-400 mb-1">Commission</div>
                        <div class="font-bold text-gray-900">{{ $abonnement->plan->commission_pct }}%</div>
                        <div class="text-xs text-green-600">vs 12% standard</div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <div class="text-xs text-gray-400 mb-1">Périodicité</div>
                        <div class="font-bold text-gray-900">{{ ucfirst($abonnement->periodicite) }}</div>
                        <div class="text-xs text-gray-400">{{ number_format($abonnement->montant, 2) }}€</div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <div class="text-xs text-gray-400 mb-1">Début</div>
                        <div class="font-bold text-gray-900">{{ $abonnement->debut_at->format('d/m/Y') }}</div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <div class="text-xs text-gray-400 mb-1">Prochain paiement</div>
                        <div class="font-bold text-gray-900">{{ $abonnement->fin_at?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                </div>

                {{-- Features --}}
                <div class="mb-5">
                    <div class="text-sm font-semibold text-gray-700 mb-2">Inclus dans votre plan :</div>
                    <ul class="space-y-1">
                        @foreach($abonnement->plan->fonctionnalites as $feature)
                            <li class="text-sm text-gray-600 flex items-center gap-2">
                                <span class="text-green-500">✓</span> {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Annuler --}}
                <div x-data="{ confirm: false }">
                    <button @click="confirm = !confirm" class="text-sm text-red-400 hover:text-red-600 transition">
                        Annuler mon abonnement
                    </button>
                    <div x-show="confirm" x-transition class="mt-3 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <p class="text-sm text-red-700 mb-3">
                            Votre abonnement restera actif jusqu'au {{ $abonnement->fin_at?->format('d/m/Y') }}.
                            Êtes-vous sûr de vouloir annuler ?
                        </p>
                        <div class="flex gap-3">
                            <form action="{{ route('abonnements.annuler', $abonnement->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition">
                                    Confirmer l'annulation
                                </button>
                            </form>
                            <button @click="confirm = false" class="px-4 py-2 border border-gray-200 text-sm text-gray-600 rounded-xl hover:border-gray-300 transition">
                                Garder mon abonnement
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Upgrade --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h2 class="font-semibold text-gray-900 mb-3">Changer de plan</h2>
                <div class="space-y-3">
                    @foreach($plans as $plan)
                        @if($plan->id !== $abonnement->plan_id)
                            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:border-green-400 transition">
                                <div>
                                    <div class="font-semibold text-gray-900 text-sm">{{ $plan->nom }}</div>
                                    <div class="text-xs text-gray-400">Commission {{ $plan->commission_pct }}% · {{ number_format($plan->prix_mensuel, 0) }}€/mois</div>
                                </div>
                                <form action="{{ route('abonnements.souscrire', $plan->slug) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="periodicite" value="mensuel">
                                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-xl transition">
                                        Passer à {{ $plan->nom }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

        @else
            {{-- Pas d'abonnement --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                <div class="text-5xl mb-4">📋</div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Aucun abonnement actif</h2>
                <p class="text-gray-500 text-sm mb-6">Découvrez nos plans B2B pour réduire vos commissions et booster votre activité.</p>
                <a href="{{ route('abonnements.tarifs') }}"
                   class="inline-block px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition">
                    Voir les plans →
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
