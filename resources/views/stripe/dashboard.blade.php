{{-- resources/views/stripe/dashboard.blade.php --}}
<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mes revenus</h1>
                <p class="text-gray-500 text-sm mt-1">Tableau de bord financier Biocolis</p>
            </div>
            <a href="{{ route('profile.edit') }}#iban"
               class="flex items-center gap-2 px-4 py-2.5 border border-gray-200 hover:border-green-400 text-gray-700 rounded-xl text-sm font-semibold transition">
                🏦 Mon IBAN
            </a>
        </div>

        {{-- ⚠️ Pas d'IBAN renseigné --}}
        @if(!$a_iban)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6 flex items-start gap-4">
                <div class="text-2xl flex-shrink-0">💳</div>
                <div class="flex-1">
                    <div class="font-semibold text-amber-800 mb-1">Renseignez votre IBAN pour recevoir vos virements</div>
                    <p class="text-sm text-amber-700 mb-3">
                        Biocolis vire directement vos gains sur votre compte bancaire.
                        Pas besoin de société — il suffit de votre IBAN personnel.
                    </p>
                    <a href="{{ route('profile.edit') }}#iban"
                       class="inline-block px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold transition">
                        Ajouter mon IBAN →
                    </a>
                </div>
            </div>
        @else
            <div class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
                <span class="text-green-600 text-xl">✅</span>
                <div>
                    <span class="text-sm font-semibold text-green-800">IBAN enregistré</span>
                    <span class="text-sm text-green-600 ml-2">— Vos gains seront virés sous 3-5 jours ouvrés après chaque vente confirmée.</span>
                </div>
            </div>
        @endif

        {{-- Stats financières --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="text-xs text-gray-400 mb-1">Solde disponible</div>
                <div class="text-2xl font-bold text-green-600">{{ number_format($solde_disponible, 2) }}€</div>
                <div class="text-xs text-gray-400 mt-1">Prêt à être viré</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="text-xs text-gray-400 mb-1">En attente</div>
                <div class="text-2xl font-bold text-orange-500">{{ number_format($en_attente, 2) }}€</div>
                <div class="text-xs text-gray-400 mt-1">Commandes en cours</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="text-xs text-gray-400 mb-1">Ce mois</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($ce_mois, 2) }}€</div>
                <div class="text-xs text-gray-400 mt-1">{{ now()->translatedFormat('F Y') }}</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="text-xs text-gray-400 mb-1">Total gagné</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($total_gagne, 2) }}€</div>
                <div class="text-xs text-gray-400 mt-1">Depuis le début</div>
            </div>
        </div>

        {{-- Comment ça marche --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6">
            <h2 class="font-semibold text-gray-900 mb-4">Comment fonctionne le paiement ?</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center text-sm">
                @foreach([
                    ['🛒', 'Acheteur commande', 'L\'acheteur paye en sécurité par carte'],
                    ['⏳', 'En attente', 'Votre gain est mis en attente pendant la livraison'],
                    ['✅', 'Réception confirmée', 'L\'acheteur confirme avoir reçu sa commande'],
                    ['💸', 'Virement', 'Biocolis vous vire sous 3-5 jours ouvrés sur votre IBAN'],
                ] as [$ico, $titre, $desc])
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-xl">{{ $ico }}</div>
                        <div class="font-semibold text-gray-900">{{ $titre }}</div>
                        <div class="text-xs text-gray-400">{{ $desc }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Ventes récentes --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="font-semibold text-gray-900">Ventes récentes</h2>
                <a href="{{ route('commandes.mes-ventes') }}" class="text-xs text-green-600 hover:underline">
                    Tout voir →
                </a>
            </div>

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
                                'payee'          => ['bg-blue-100',   'text-blue-700',   '✓ Payée'],
                                'en_preparation' => ['bg-orange-100', 'text-orange-700', '👨‍🍳 En prépa'],
                                'prete'          => ['bg-purple-100', 'text-purple-700', '📦 Prête'],
                                'en_livraison'   => ['bg-indigo-100', 'text-indigo-700', '🚴 Livraison'],
                                'livree'         => ['bg-teal-100',   'text-teal-700',   '✅ Livrée'],
                                'terminee'       => ['bg-green-100',  'text-green-700',  '🌟 Terminée'],
                            ][$commande->statut] ?? ['bg-gray-100', 'text-gray-600', $commande->statut];
                        @endphp

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $commande->numero }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $commande->acheteur->prenom }} ·
                                    {{ $commande->created_at->format('d/m/Y') }}
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $statutColors[0] }} {{ $statutColors[1] }}">
                                    {{ $statutColors[2] }}
                                </span>

                                {{-- Changer statut --}}
                                @if(in_array($commande->statut, ['payee', 'en_preparation', 'prete', 'en_livraison']))
                                    <form action="{{ route('commandes.statut', $commande->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <select name="statut" onchange="this.form.submit()"
                                            class="text-xs px-2 py-1.5 border border-gray-200 rounded-lg bg-white focus:border-green-500 outline-none">
                                            @if($commande->statut === 'payee')
                                                <option value="en_preparation">→ En préparation</option>
                                            @elseif($commande->statut === 'en_preparation')
                                                <option value="prete">→ Prête</option>
                                            @elseif($commande->statut === 'prete')
                                                <option value="en_livraison">→ En livraison</option>
                                            @elseif($commande->statut === 'en_livraison')
                                                <option value="livree">→ Livrée ✅</option>
                                            @endif
                                        </select>
                                    </form>
                                @endif

                                <div class="text-right">
                                    <div class="text-sm font-bold {{ $commande->statut === 'terminee' ? 'text-green-600' : 'text-orange-500' }}">
                                        {{ $commande->statut === 'terminee' ? '✓' : '⏳' }}
                                        {{ number_format($commande->montant_vendeur, 2) }}€
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $commande->statut === 'terminee' ? 'Virement en cours' : 'En attente' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Info commission --}}
        <div class="mt-4 bg-gray-50 rounded-2xl p-4 text-sm text-gray-500 flex items-start gap-2">
            <span>ℹ️</span>
            <div>
                <strong class="text-gray-700">Commission Biocolis : 12%</strong> prélevée sur chaque vente.
                Vous recevez <strong class="text-gray-700">88% du montant</strong> directement sur votre IBAN sous 3 à 5 jours ouvrés après confirmation de réception par l'acheteur.
            </div>
        </div>
    </div>
</x-app-layout>
