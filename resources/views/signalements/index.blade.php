{{-- resources/views/signalements/index.blade.php --}}
<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">⚠️ Signalements</h1>
                <p class="text-gray-500 text-sm mt-1">Modération du contenu Biocolis</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-green-600">← Dashboard</a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            @foreach([
                ['en_attente', '⏳', 'En attente', 'bg-amber-50 border-amber-200 text-amber-700'],
                ['traite',     '✅', 'Traités',    'bg-green-50 border-green-200 text-green-700'],
                ['rejete',     '❌', 'Rejetés',    'bg-gray-50 border-gray-200 text-gray-600'],
            ] as [$key, $ico, $lbl, $cls])
                <a href="?statut={{ $key }}"
                   class="border rounded-2xl p-4 text-center transition hover:shadow-sm {{ $statut === $key ? $cls : 'bg-white border-gray-100' }}">
                    <div class="text-2xl font-bold">{{ $stats[$key] }}</div>
                    <div class="text-sm mt-0.5">{{ $ico }} {{ $lbl }}</div>
                </a>
            @endforeach
        </div>

        {{-- Filtres statut --}}
        <div class="flex gap-2 mb-5">
            @foreach(['en_attente' => '⏳ En attente', 'traite' => '✅ Traités', 'rejete' => '❌ Rejetés', 'tous' => '📋 Tous'] as $key => $lbl)
                <a href="?statut={{ $key }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $statut === $key ? 'bg-green-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-green-400' }}">
                    {{ $lbl }}
                </a>
            @endforeach
        </div>

        {{-- Liste signalements --}}
        @if($signalements->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
                <div class="text-5xl mb-3">🎉</div>
                <p class="font-semibold text-gray-700">Aucun signalement
                    {{ $statut === 'en_attente' ? 'en attente' : '' }}
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($signalements as $sig)
                    @php
                        $cible = $sig->cible_resolved;
                        $estAnnonce = $sig->cible_type === \App\Models\Annonce::class;
                        $statutColors = [
                            'en_attente' => 'bg-amber-100 text-amber-700',
                            'traite'     => 'bg-green-100 text-green-700',
                            'rejete'     => 'bg-gray-100 text-gray-600',
                        ][$sig->statut] ?? 'bg-gray-100 text-gray-600';
                        $motifLabels = [
                            'contenu_inapproprie' => '🚫 Contenu inapproprié',
                            'arnaque'             => '💸 Arnaque',
                            'faux_produit'        => '🥦 Faux produit',
                            'spam'                => '📨 Spam',
                            'autre'               => '❓ Autre',
                        ];
                    @endphp

                    <div class="bg-white rounded-2xl border {{ $sig->statut === 'en_attente' ? 'border-amber-200' : 'border-gray-100' }} p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                {{-- Header --}}
                                <div class="flex items-center gap-3 mb-3">
                                    <img src="{{ $sig->auteur->photo_profil_url }}" class="w-8 h-8 rounded-full object-cover">
                                    <div>
                                        <span class="text-sm font-semibold text-gray-900">{{ $sig->auteur->nom_complet }}</span>
                                        <span class="text-xs text-gray-400 ml-2">signale un {{ $estAnnonce ? 'annonce' : 'utilisateur' }}</span>
                                        <span class="text-xs text-gray-400 ml-1">· {{ $sig->created_at->diffForHumans() }}</span>
                                    </div>
                                    <span class="ml-auto px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statutColors }}">
                                        {{ ucfirst(str_replace('_', ' ', $sig->statut)) }}
                                    </span>
                                </div>

                                {{-- Cible --}}
                                <div class="bg-gray-50 rounded-xl p-3 mb-3">
                                    <div class="text-xs text-gray-400 mb-1">{{ $estAnnonce ? '📢 Annonce signalée' : '👤 Utilisateur signalé' }}</div>
                                    @if($cible)
                                        @if($estAnnonce)
                                            <a href="{{ route('annonces.show', $cible->slug) }}" target="_blank"
                                               class="text-sm font-semibold text-green-600 hover:underline">
                                                {{ $cible->titre }}
                                            </a>
                                            <span class="text-xs text-gray-400 ml-2">par {{ $cible->user?->nom_complet }}</span>
                                        @else
                                            <span class="text-sm font-semibold text-gray-900">{{ $cible->nom_complet }}</span>
                                            <span class="text-xs text-gray-400 ml-2">{{ $cible->email }}</span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-400 italic">Élément supprimé</span>
                                    @endif
                                </div>

                                {{-- Motif + description --}}
                                <div class="flex items-start gap-3">
                                    <span class="text-sm font-semibold text-gray-700">
                                        {{ $motifLabels[$sig->motif] ?? $sig->motif }}
                                    </span>
                                </div>
                                @if($sig->description)
                                    <p class="text-sm text-gray-500 mt-1 italic">"{{ $sig->description }}"</p>
                                @endif

                                {{-- Note admin si traité --}}
                                @if($sig->note_admin)
                                    <div class="mt-2 text-xs text-gray-400 bg-gray-50 rounded-lg px-3 py-2">
                                        📝 Note admin : {{ $sig->note_admin }}
                                        @if($sig->traitePar) · par {{ $sig->traitePar->prenom }} @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Actions (seulement si en attente) --}}
                            @if($sig->statut === 'en_attente')
                                <div x-data="{ openForm: false }" class="flex-shrink-0">
                                    <button @click="openForm = !openForm"
                                        class="px-4 py-2 bg-gray-900 hover:bg-gray-700 text-white text-xs font-semibold rounded-xl transition">
                                        Traiter →
                                    </button>

                                    <div x-show="openForm" x-transition class="mt-3 w-72">
                                        <form action="{{ route('signalements.traiter', $sig->id) }}" method="POST" class="space-y-3 bg-gray-50 rounded-xl p-4 border border-gray-200">
                                            @csrf @method('PATCH')

                                            <textarea name="note_admin" rows="2" placeholder="Note interne (optionnel)..."
                                                class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:border-green-500 outline-none resize-none"></textarea>

                                            @if($estAnnonce && $cible)
                                                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                                                    <input type="checkbox" name="action_supplementaire" value="supprimer_annonce" class="rounded">
                                                    🗑️ Supprimer l'annonce
                                                </label>
                                            @elseif(!$estAnnonce && $cible)
                                                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                                                    <input type="checkbox" name="action_supplementaire" value="bannir_user" class="rounded">
                                                    🔨 Bannir l'utilisateur
                                                </label>
                                            @endif

                                            <div class="flex gap-2">
                                                <button type="submit" name="action" value="traite"
                                                    class="flex-1 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition">
                                                    ✅ Valider
                                                </button>
                                                <button type="submit" name="action" value="rejete"
                                                    class="flex-1 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-xs font-semibold rounded-lg transition">
                                                    ❌ Rejeter
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $signalements->links() }}</div>
        @endif
    </div>
</x-app-layout>
