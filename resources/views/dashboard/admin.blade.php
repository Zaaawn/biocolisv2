{{-- resources/views/dashboard/admin.blade.php --}}
<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 py-8">

        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-xl">⚡</div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
                <p class="text-sm text-gray-400">Vue globale de la plateforme Biocolis</p>
            </div>
        </div>

        {{-- Stats globales --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            @foreach([
                ['👥', 'Utilisateurs', number_format($stats['nb_users']), 'inscrits'],
                ['📢', 'Annonces', number_format($stats['nb_annonces']), 'publiées'],
                ['🛒', 'Commandes', number_format($stats['nb_commandes']), 'total'],
                ['💶', 'GMV Total', number_format($stats['gmv_total'], 2).'€', 'volume brut'],
                ['💰', 'Revenus Biocolis', number_format($stats['revenus_biocolis'], 2).'€', 'commissions 12%'],
                ['📅', 'GMV ce mois', number_format($stats['ce_mois_gmv'], 2).'€', now()->translatedFormat('F')],
                ['⚠️', 'Signalements', $stats['nb_signalements'], 'en attente'],
                ['✅', 'Comptes Stripe', $stats['nb_stripe_actif'], 'vendeurs actifs'],
            ] as [$ico, $lbl, $val, $sub])
                <div class="bg-white rounded-2xl border border-gray-100 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-lg">{{ $ico }}</span>
                        <span class="text-xs text-gray-400">{{ $lbl }}</span>
                    </div>
                    <div class="text-xl font-bold text-gray-900">{{ $val }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $sub }}</div>
                </div>
            @endforeach
        </div>

        {{-- Graphique GMV --}}
        @if($gmv_mensuel->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6">
                <h2 class="font-semibold text-gray-900 mb-4">Volume brut mensuel (GMV)</h2>
                <div class="flex items-end gap-2 h-28">
                    @php $max = $gmv_mensuel->max() ?: 1; @endphp
                    @foreach($gmv_mensuel as $mois => $montant)
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="text-xs font-medium text-gray-600">{{ number_format($montant, 0) }}€</div>
                            <div class="w-full bg-green-500 rounded-t-lg"
                                 style="height: {{ max(4, ($montant / $max) * 96) }}px"></div>
                            <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($mois)->format('M') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Dernières commandes --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h2 class="font-semibold text-gray-900 mb-4">Dernières commandes</h2>
                <div class="space-y-2">
                    @foreach($dernieres_commandes as $commande)
                        @php
                            $color = match($commande->statut) {
                                'payee','en_preparation','prete','en_livraison' => 'text-blue-600',
                                'livree','terminee' => 'text-green-600',
                                'annulee','remboursee' => 'text-red-500',
                                default => 'text-gray-500',
                            };
                        @endphp
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div>
                                <span class="text-sm font-medium text-gray-900">{{ $commande->numero }}</span>
                                <div class="text-xs text-gray-400">
                                    {{ $commande->acheteur->prenom }} → {{ $commande->vendeur->prenom }} ·
                                    {{ $commande->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-gray-900">{{ number_format($commande->total_ttc, 2) }}€</div>
                                <div class="text-xs {{ $color }}">{{ ucfirst(str_replace('_',' ',$commande->statut)) }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                {{-- Nouveaux utilisateurs --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h2 class="font-semibold text-gray-900 mb-4">Nouveaux inscrits</h2>
                    <div class="space-y-3">
                        @foreach($nouveaux_users as $u)
                            <div class="flex items-center gap-3">
                                <img src="{{ $u->photo_profil_url }}" class="w-8 h-8 rounded-full object-cover">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ $u->nom_complet }}</div>
                                    <div class="text-xs text-gray-400">{{ $u->role }} · {{ $u->created_at->diffForHumans() }}</div>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ match($u->role) {
                                        'admin'=>'bg-red-100 text-red-600',
                                        'b2b'=>'bg-purple-100 text-purple-600',
                                        'professionnel'=>'bg-blue-100 text-blue-600',
                                        default=>'bg-gray-100 text-gray-600'
                                    } }}">
                                    {{ $u->role }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Signalements --}}
                @if($signalements->isNotEmpty())
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
                        <h2 class="font-semibold text-red-800 mb-4 flex items-center gap-2">
                            ⚠️ Signalements à traiter
                            <span class="bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                                {{ $signalements->count() }}
                            </span>
                        </h2>
                        <div class="space-y-2">
                            @foreach($signalements as $sig)
                                <div class="bg-white rounded-xl p-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">{{ $sig->auteur->prenom }}</span>
                                            <span class="text-xs text-gray-400 ml-2">signale</span>
                                            <span class="text-xs font-medium text-gray-700 ml-1">{{ class_basename($sig->cible_type) }}</span>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $sig->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ ucfirst(str_replace('_', ' ', $sig->motif)) }}
                                        @if($sig->description) — {{ Str::limit($sig->description, 60) }} @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
