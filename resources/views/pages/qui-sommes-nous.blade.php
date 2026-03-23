{{-- resources/views/pages/qui-sommes-nous.blade.php --}}
<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-12">

        {{-- Hero --}}
        <div class="text-center mb-16">
            <div class="w-16 h-16 bg-green-600 rounded-2xl flex items-center justify-center text-white font-bold text-3xl mx-auto mb-6">B</div>
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Notre mission</h1>
            <p class="text-xl text-gray-500 max-w-2xl mx-auto leading-relaxed">
                Connecter les producteurs locaux et les consommateurs pour une alimentation plus saine, plus fraîche et plus responsable.
            </p>
        </div>

        {{-- Histoire --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">🌱 Notre histoire</h2>
            <div class="text-gray-600 leading-relaxed space-y-4">
                <p>
                    Biocolis est né d'un constat simple : entre le supermarché et le jardin du voisin, il manquait un pont numérique simple et efficace. Des producteurs locaux avec des surplus de récolte, des consommateurs cherchant des produits frais et authentiques — mais aucun moyen facile de se connecter.
                </p>
                <p>
                    Nous avons créé Biocolis pour combler ce manque. Une marketplace pensée pour le circuit court, où chaque transaction profite directement au producteur, sans intermédiaire superflu.
                </p>
            </div>
        </div>

        {{-- Valeurs --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @foreach([
                ['🥕', 'Fraîcheur', 'Des produits récoltés dans les 24-48h, jamais stockés en entrepôt.'],
                ['📍', 'Localité', 'Priorité aux producteurs de votre région pour réduire l\'empreinte carbone.'],
                ['🤝', 'Confiance', 'Avis vérifiés, profils authentiques, paiements sécurisés par Stripe.'],
                ['♻️', 'Responsabilité', 'Emballages recyclables encouragés, zéro gaspillage alimentaire.'],
                ['💰', 'Équité', '88% du prix revient au producteur. Une commission juste et transparente.'],
                ['🌍', 'Communauté', 'Un réseau de producteurs et consommateurs engagés pour une meilleure alimentation.'],
            ] as [$ico, $titre, $desc])
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="text-3xl mb-3">{{ $ico }}</div>
                    <h3 class="font-bold text-gray-900 mb-2">{{ $titre }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>

        {{-- Chiffres --}}
        <div class="bg-gradient-to-br from-green-600 to-emerald-500 rounded-2xl p-8 text-white mb-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Biocolis en chiffres</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                @foreach([
                    ['100%', 'Circuit court'],
                    ['12%', 'Commission seulement'],
                    ['24h', 'Fraîcheur garantie'],
                    ['0', 'Publicité'],
                ] as [$val, $lbl])
                    <div>
                        <div class="text-3xl font-extrabold mb-1">{{ $val }}</div>
                        <div class="text-green-100 text-sm">{{ $lbl }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- CTA --}}
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Rejoignez la communauté</h2>
            <p class="text-gray-500 mb-6">Que vous soyez producteur ou consommateur, votre place est ici.</p>
            <div class="flex gap-3 justify-center">
                <a href="{{ route('register') }}"
                   class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition text-sm">
                    Créer un compte gratuit
                </a>
                <a href="{{ route('annonces.index') }}"
                   class="px-6 py-3 border-2 border-gray-200 hover:border-green-400 text-gray-700 font-semibold rounded-xl transition text-sm">
                    Voir les annonces
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
