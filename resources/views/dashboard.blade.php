{{-- resources/views/stripe/dashboard.blade.php --}}
<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mes revenus</h1>
                <p class="text-gray-500 text-sm mt-1">Tableau de bord financier Biocolis</p>
            </div>

            @if($stripe_actif)
                <a href="{{ route('stripe.login') }}"
                   class="flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Tableau de bord Stripe
                </a>
            @endif
        </div>

        {{-- Statut du compte Stripe --}}
        @if(!$stripe_actif)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-8">
                <div class="flex items-start gap-4">
                    <div class="text-3xl">💳</div>
                    <div class="flex-1">
                        <h2 class="font-semibold text-amber-800 mb-1">
                            @if($statut === 'non_cree')
                                Activez votre compte pour recevoir vos paiements
                            @else
                                Finalise ton activation — quelques infos manquantes
                            @endif
                        </h2>
                        <p class="text-amber-700 text-sm mb-2">
                            @if($statut === 'non_cree')
                                Pour recevoir l'argent de vos ventes directement sur votre compte bancaire,
                                il suffit de renseigner vos informations personnelles et votre IBAN.
                                <strong>Pas besoin de société ou de SIRET</strong> — un simple particulier peut vendre sur Biocolis.
                            @else
                                Stripe a besoin de quelques informations supplémentaires pour valider votre compte.
                                Cliquez ci-dessous pour compléter.
                            @endif
                        </p>
                        <ul class="text-amber-700 text-xs mb-4 space-y-0.5 list-none">
                            @if($statut === 'non_cree')
                                <li>✅ Vos nom et prénom</li>
                                <li>✅ Votre date de naissance</li>
                                <li>✅ Votre adresse</li>
                                <li>✅ Votre IBAN (pour recevoir les virements)</li>
                                <li>✅ Une pièce d'identité (carte d'identité ou passeport)</li>
                            @endif
                        </ul>
                        <a href="{{ route('stripe.onboarding') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-semibold transition">
                            {{ $statut === 'non_cree' ? '🚀 Activer mon compte de paiement' : '▶️ Compléter mon activation' }}
                        </a>
                        <p class="text-xs text-amber-600 mt-2">🔒 Sécurisé par Stripe — vos données bancaires ne transitent jamais par Biocolis</p>
                    </div>
                </div>
            </div>
        @else
            {{-- Compte actif — stats financières --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <div class="text-sm text-gray-500 mb-1">Solde disponible</div>
                    <div class="text-2xl font-bold text-green-600">
                        {{ number_format($solde_disponible ?? 0, 2) }}€
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Prêt à être versé</div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <div class="text-sm text-gray-500 mb-1">En attente</div>
                    <div class="text-2xl font-bold text-orange-500">
                        {{ number_format($solde_en_attente ?? 0, 2) }}€
                    </div>
                    <div class="text-xs text-gray-400 mt-1">En cours de traitement</div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <div class="text-sm text-gray-500 mb-1">Total gagné</div>
                    <div class="text-2xl font-bold text-gray-900">
                       {{ number_format($total_gagne, 2) }}€
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Depuis le début</div>
                </div>
            </div>
        @endif

        {{-- Ventes récentes --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-900 mb-5">Ventes récentes</h2>

            @if($ventes_recentes->isEmpty())
                <div class="text-center py-10 text-gray-400">
                    <div class="text-4xl mb-3">💰</div>
                    <p class="text-sm">Aucune vente pour l'instant</p>
                    <a href="{{ route('annonces.create') }}"
                       class="inline-block mt-4 px-4 py-2 bg-green-600 text-white rounded-xl text-sm font-medium hover:bg-green-700 transition">
                        Déposer une annonce
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($ventes_recentes as $commande)
                        @php
                            $statutColors = [
                                'payee'          => 'bg-blue-100 text-blue-700',
                                'en_preparation' => 'bg-orange-100 text-orange-700',
                                'prete'          => 'bg-purple-100 text-purple-700',
                                'en_livraison'   => 'bg-indigo-100 text-indigo-700',
                                'livree'         => 'bg-teal-100 text-teal-700',
                                'terminee'       => 'bg-green-100 text-green-700',
                            ][$commande->statut] ?? 'bg-gray-100 text-gray-600';
                        @endphp

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                            <div class="flex items-center gap-4">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $commande->numero }}</div>
                                    <div class="text-xs text-gray-400">
                                        {{ $commande->acheteur->prenom }} ·
                                        {{ $commande->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $statutColors }}">
                                    {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                                </span>

                                {{-- Changer statut --}}
                                @if(in_array($commande->statut, ['payee', 'en_preparation', 'prete', 'en_livraison']))
                                    <form action="{{ route('commandes.statut', $commande->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <select name="statut" onchange="this.form.submit()"
                                            class="text-xs px-2 py-1.5 border border-gray-200 rounded-lg focus:border-green-500 outline-none bg-white">
                                            @if($commande->statut === 'payee')
                                                <option value="en_preparation">→ En préparation</option>
                                            @endif
                                            @if($commande->statut === 'en_preparation')
                                                <option value="prete">→ Prête</option>
                                            @endif
                                            @if($commande->statut === 'prete')
                                                <option value="en_livraison">→ En livraison</option>
                                            @endif
                                            @if($commande->statut === 'en_livraison')
                                                <option value="livree">→ Livrée</option>
                                            @endif
                                        </select>
                                    </form>
                                @endif

                                <div class="text-right">
                                    <div class="text-sm font-bold text-green-600">
                                        +{{ number_format($commande->montant_vendeur, 2) }}€
                                    </div>
                                    <div class="text-xs text-gray-400">après commission</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <a href="{{ route('commandes.mes-ventes') }}"
                   class="mt-4 block text-center text-sm text-green-600 hover:text-green-700 font-medium">
                    Voir toutes mes ventes →
                </a>
            @endif
        </div>

        {{-- Info commission --}}
        <div class="mt-4 bg-green-50 rounded-2xl p-4 text-sm text-green-700">
            <strong>Commission Biocolis :</strong> 12% sur chaque vente.
            Vous recevez 88% du montant directement sur votre compte bancaire via Stripe.
        </div>
    </div>
</x-app-layout>
