{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Biocolis') }} — @yield('title', 'Fruits & Légumes locaux')</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
  

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        :root { --biocolis: #118501; --biocolis-dark: #0d6901; --biocolis-light: #e6f4e3; }
        .bg-green-600, .bg-green-600:hover { background-color: var(--biocolis) !important; }
        .hover\:bg-green-700:hover { background-color: var(--biocolis-dark) !important; }
        .bg-green-700 { background-color: var(--biocolis-dark) !important; }
        .text-green-600 { color: var(--biocolis) !important; }
        .text-green-700 { color: var(--biocolis-dark) !important; }
        .border-green-500, .focus\:border-green-500:focus { border-color: var(--biocolis) !important; }
        .border-green-400, .hover\:border-green-400:hover { border-color: var(--biocolis) !important; }
        .focus\:ring-green-100:focus { --tw-ring-color: rgba(122,169,92,0.15) !important; }
        .bg-green-50 { background-color: #f2f7ee !important; }
        .bg-green-100 { background-color: var(--biocolis-light) !important; }
        .text-green-500 { color: var(--biocolis) !important; }
        .bg-green-500 { background-color: var(--biocolis) !important; }
        .ring-green-400 { --tw-ring-color: var(--biocolis) !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    {{-- ── NAVIGATION ──────────────────────────────────────────────────────── --}}
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm"
         x-data="{ menuOpen: false, searchOpen: false }">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('accueil') }}" class="flex items-center gap-1 flex-shrink-0">
                    <img src="{{ asset('images/logo-biocolis.png') }}" alt="Biocolis" class="h-10 w-auto">
                </a>

                {{-- Recherche desktop --}}
                <div class="hidden md:flex flex-1 max-w-md mx-6">
                    <form action="{{ route('annonces.index') }}" method="GET" class="w-full">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Tomates, fraises, carottes..."
                                class="w-full pl-9 pr-4 py-2 text-sm bg-gray-100 rounded-xl border border-transparent focus:bg-white focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none transition">
                        </div>
                    </form>
                </div>

                {{-- Actions droite --}}
                <div class="flex items-center gap-2">

                    {{-- Recherche mobile --}}
                    <button @click="searchOpen = !searchOpen"
                        class="md:hidden p-2 text-gray-500 hover:text-green-600 transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>

                    @auth
                        {{-- Notifications --}}
                        <x-notification-bell />

                        {{-- Panier --}}
                        <a href="{{ route('panier.index') }}"
                           class="relative p-2 text-gray-500 hover:text-green-600 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span id="panier-badge"
                                  class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-green-500 text-white text-xs rounded-full flex items-center justify-center font-bold hidden">
                                0
                            </span>
                        </a>

                        {{-- Messages --}}
                        <a href="{{ route('messages.index') }}"
                           class="relative p-2 text-gray-500 hover:text-green-600 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span id="messages-badge"
                                  class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold hidden">
                                0
                            </span>
                        </a>

                        {{-- Avatar menu --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false"
                                class="flex items-center gap-2 pl-1">
                                <img src="{{ Auth::user()->photo_profil_url }}"
                                     alt="{{ Auth::user()->prenom }}"
                                     class="w-8 h-8 rounded-full object-cover border-2 border-gray-100 hover:border-green-400 transition">
                                <svg class="h-3.5 w-3.5 text-gray-400 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Dropdown --}}
                            <div x-show="open" x-transition x-cloak
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-lg border border-gray-100 py-2 z-50">

                                {{-- Infos user --}}
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <div class="font-semibold text-gray-900 text-sm">{{ Auth::user()->nom_complet }}</div>
                                    <div class="text-xs text-gray-400">{{ Auth::user()->email }}</div>
                                    <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full
                                        {{ match(Auth::user()->role) {
                                            'admin' => 'bg-red-100 text-red-600',
                                            'b2b' => 'bg-purple-100 text-purple-600',
                                            'professionnel' => 'bg-blue-100 text-blue-600',
                                            default => 'bg-green-100 text-green-600'
                                        } }}">
                                        {{ ucfirst(Auth::user()->role) }}
                                    </span>
                                </div>

                                {{-- Menu items --}}
                                <div class="py-1">
                                    <a href="{{ route('dashboard') }}"
                                       class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <span>🏠</span> Mon espace
                                    </a>
                                    <a href="{{ route('annonces.mes-annonces') }}"
                                       class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <span>📢</span> Mes annonces
                                    </a>
                                    <a href="{{ route('commandes.mes-commandes') }}"
                                       class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <span>📦</span> Mes commandes
                                    </a>
                                    @if(Auth::user()->isProfessionnel())
                                        <a href="{{ route('commandes.mes-ventes') }}"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                            <span>💰</span> Mes ventes
                                        </a>
                                        <a href="{{ route('stripe.dashboard') }}"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                            <span>💳</span> Mes revenus
                                        </a>
                                    @endif
                                    @if(Auth::user()->isAdmin())
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <a href="{{ route('dashboard') }}"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                            <span>⚡</span> Admin
                                        </a>
                                    @endif
                                </div>

                                <div class="border-t border-gray-100 py-1">
                                    <a href="{{ route('profile.edit') }}"
                                       class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <span>⚙️</span> Paramètres
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition">
                                            <span>🚪</span> Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Déposer annonce --}}
                        <a href="{{ route('annonces.create') }}"
                           class="hidden sm:flex items-center gap-1.5 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition ml-1">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Déposer
                        </a>

                    @else
                        {{-- Non connecté --}}
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-gray-700 hover:text-green-600 transition px-3 py-2">
                            Connexion
                        </a>
                        <a href="{{ route('register') }}"
                           class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition">
                            S'inscrire
                        </a>
                    @endauth

                    {{-- Menu mobile --}}
                    <button @click="menuOpen = !menuOpen" class="md:hidden p-2 text-gray-500">
                        <svg x-show="!menuOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="menuOpen" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Recherche mobile --}}
            <div x-show="searchOpen" x-cloak class="pb-3 md:hidden">
                <form action="{{ route('annonces.index') }}" method="GET">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Rechercher des produits..."
                        class="w-full px-4 py-2.5 text-sm bg-gray-100 rounded-xl border border-transparent focus:bg-white focus:border-green-400 outline-none">
                </form>
            </div>

            {{-- Menu mobile --}}
            <div x-show="menuOpen" x-cloak class="pb-4 md:hidden border-t border-gray-100 pt-3 space-y-1">
                <a href="{{ route('annonces.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl">
                    🌱 Annonces
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl">
                        🏠 Mon espace
                    </a>
                    <a href="{{ route('panier.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl">
                        🛒 Panier
                    </a>
                    <a href="{{ route('messages.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl">
                        💬 Messages
                    </a>
                    <a href="{{ route('annonces.create') }}" class="block px-3 py-2 text-sm font-semibold text-green-600 hover:bg-green-50 rounded-xl">
                        + Déposer une annonce
                    </a>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl">Connexion</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-sm font-semibold text-green-600 hover:bg-green-50 rounded-xl">S'inscrire</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ── FLASH MESSAGES ──────────────────────────────────────────────────── --}}
    @if(session('success') || session('error') || session('warning'))
        <div class="max-w-6xl mx-auto px-4 pt-4" id="flash-container">
            @if(session('success'))
                <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm mb-2">
                    <span>✅</span> {{ session('success') }}
                    <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-600">×</button>
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm mb-2">
                    <span>❌</span> {{ session('error') }}
                    <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600">×</button>
                </div>
            @endif
            @if(session('warning'))
                <div class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-700 text-sm mb-2">
                    <span>⚠️</span> {{ session('warning') }}
                    <button onclick="this.parentElement.remove()" class="ml-auto text-amber-400 hover:text-amber-600">×</button>
                </div>
            @endif
        </div>
    @endif

    {{-- ── CONTENU PRINCIPAL ───────────────────────────────────────────────── --}}
    <main>
        {{ $slot }}
    </main>

    {{-- ── FOOTER ──────────────────────────────────────────────────────────── --}}
    <footer class="bg-white border-t border-gray-100 mt-16 py-10">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-1 mb-3">
                        <img src="{{ asset('images/logo-biocolis.png') }}" alt="Biocolis" class="h-8 w-auto">
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        La marketplace des fruits et légumes locaux. Circuit court, fraîcheur garantie.
                    </p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm mb-3">Acheter</h3>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="{{ route('annonces.index') }}" class="hover:text-green-600 transition">Toutes les annonces</a></li>
                        <li><a href="{{ route('annonces.index', ['type' => 'legume']) }}" class="hover:text-green-600 transition">Légumes</a></li>
                        <li><a href="{{ route('annonces.index', ['type' => 'fruit']) }}" class="hover:text-green-600 transition">Fruits</a></li>
                        <li><a href="{{ route('annonces.index', ['label' => 'bio']) }}" class="hover:text-green-600 transition">Bio</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm mb-3">Vendre</h3>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="{{ route('annonces.create') }}" class="hover:text-green-600 transition">Déposer une annonce</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-green-600 transition">Créer un compte</a></li>
                        @auth
                            <li><a href="{{ route('stripe.dashboard') }}" class="hover:text-green-600 transition">Mes revenus</a></li>
                        @endauth
                         <li><a href="{{ route('abonnements.tarifs') }}">Tarifs B2B</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm mb-3">Biocolis</h3>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-green-600 transition">Qui sommes-nous</a></li>
                        <li><a href="#" class="hover:text-green-600 transition">CGU</a></li>
                        <li><a href="#" class="hover:text-green-600 transition">Confidentialité</a></li>
                        <li><a href="#" class="hover:text-green-600 transition">Contact</a></li>
                       
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-6 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-gray-400">
                <span>© {{ date('Y') }} Biocolis. Tous droits réservés.</span>
                <span>Paiement sécurisé par 💳 Stripe</span>
            </div>
        </div>
    </footer>

    {{-- ── SCRIPTS GLOBAUX ─────────────────────────────────────────────────── --}}
    <script>
        // Badges panier et messages (polling toutes les 30 sec)
        async function updateBadges() {
            try {
                @auth
                // Panier
                const panierRes = await fetch('{{ route("panier.compteur") }}');
                const panierData = await panierRes.json();
                const panierBadge = document.getElementById('panier-badge');
                if (panierBadge) {
                    if (panierData.nb > 0) {
                        panierBadge.textContent = panierData.nb > 9 ? '9+' : panierData.nb;
                        panierBadge.classList.remove('hidden');
                    } else {
                        panierBadge.classList.add('hidden');
                    }
                }

                // Messages
                const msgRes = await fetch('{{ route("messages.non-lus") }}');
                const msgData = await msgRes.json();
                const msgBadge = document.getElementById('messages-badge');
                if (msgBadge) {
                    if (msgData.total > 0) {
                        msgBadge.textContent = msgData.total > 9 ? '9+' : msgData.total;
                        msgBadge.classList.remove('hidden');
                    } else {
                        msgBadge.classList.add('hidden');
                    }
                }
                @endauth
            } catch(e) {}
        }

        // Premier appel immédiat, puis toutes les 30 secondes
        updateBadges();
        setInterval(updateBadges, 30000);

        // Auto-hide flash messages après 5 secondes
        setTimeout(() => {
            document.getElementById('flash-container')?.remove();
        }, 5000);
    </script>
    {{-- Autocomplétion adresse universelle --}}
    <x-adresse-autocomplete />
    <x-signalement-modal />
</body>
</html>
