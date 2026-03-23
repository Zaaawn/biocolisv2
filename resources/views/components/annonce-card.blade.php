{{-- resources/views/components/annonce-card.blade.php --}}
@props(['annonce'])

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">

    {{-- Photo --}}
    <a href="{{ route('annonces.show', $annonce->slug) }}" class="block relative aspect-[4/3] overflow-hidden bg-gray-100">
        <img
            src="{{ $annonce->premiere_photo }}"
            alt="{{ $annonce->titre }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            loading="lazy"
        >

        {{-- Badges overlay --}}
        <div class="absolute top-2 left-2 flex flex-wrap gap-1">
            @if($annonce->est_epinglee)
                <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-0.5 rounded-full">📌 Épinglé</span>
            @elseif($annonce->est_mise_en_avant)
                <span class="bg-green-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">⭐ À la une</span>
            @endif

            <span class="bg-white/90 backdrop-blur text-gray-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                {{ match($annonce->label) {
                    'bio' => '🍃 Bio',
                    'local' => '📍 Local',
                    'raisonne' => '♻️ Raisonné',
                    default => '🌾 ' . ucfirst($annonce->label),
                } }}
            </span>
        </div>

        {{-- Like bouton --}}
        @auth
            @if(auth()->id() !== $annonce->user_id)
                <button
                    onclick="toggleLike(event, {{ $annonce->id }}, this)"
                    class="absolute top-2 right-2 w-8 h-8 bg-white/90 backdrop-blur rounded-full flex items-center justify-center hover:bg-white transition shadow-sm"
                >
                    <svg class="h-4 w-4 text-gray-400 like-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            @endif
        @endauth
    </a>

    {{-- Contenu --}}
    <a href="{{ route('annonces.show', $annonce->slug) }}" class="block p-4">
        {{-- Vendeur --}}
        <div class="flex items-center gap-2 mb-2">
            <img src="{{ $annonce->user->photo_profil_url }}"
                 alt="{{ $annonce->user->nom_complet }}"
                 class="w-5 h-5 rounded-full object-cover">
            <span class="text-xs text-gray-500">{{ $annonce->user->prenom }}</span>
            @if($annonce->user->isProfessionnel())
                <span class="text-xs bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded-full">Pro</span>
            @endif
        </div>

        {{-- Titre --}}
        <h3 class="font-semibold text-gray-900 text-sm leading-tight mb-1 line-clamp-2">
            {{ $annonce->titre }}
        </h3>

        {{-- Localisation --}}
        <p class="text-xs text-gray-400 mb-3 flex items-center gap-1">
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ $annonce->ville ?? $annonce->localisation }}
        </p>

        {{-- Prix + modes livraison --}}
        <div class="flex items-center justify-between">
            <div>
                <span class="text-lg font-bold text-green-600">{{ number_format($annonce->prix, 2) }}€</span>
                <span class="text-xs text-gray-400">/ {{ $annonce->unite_prix }}</span>
            </div>
            <div class="flex gap-1">
                @if($annonce->livraison_main_propre)
                    <span title="Main propre" class="text-sm">🤝</span>
                @endif
                @if($annonce->livraison_domicile)
                    <span title="Livraison domicile" class="text-sm">🏠</span>
                @endif
                @if($annonce->livraison_point_relais)
                    <span title="Point relais" class="text-sm">📦</span>
                @endif
                @if($annonce->livraison_locker)
                    <span title="Locker" class="text-sm">🗄️</span>
                @endif
            </div>
        </div>

        {{-- Note --}}
        @if($annonce->note_moyenne > 0)
            <div class="flex items-center gap-1 mt-2">
                <span class="text-yellow-400 text-xs">★</span>
                <span class="text-xs text-gray-600 font-medium">{{ number_format($annonce->note_moyenne, 1) }}</span>
                <span class="text-xs text-gray-400">({{ $annonce->nb_commandes }})</span>
            </div>
        @endif
    </a>
</div>

<script>
async function toggleLike(event, annonceId, btn) {
    event.preventDefault();
    try {
        const res = await fetch(`/annonces/${annonceId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        });
        const data = await res.json();
        const icon = btn.querySelector('.like-icon');
        if (data.liked) {
            icon.setAttribute('fill', '#ef4444');
            icon.setAttribute('stroke', '#ef4444');
        } else {
            icon.setAttribute('fill', 'none');
            icon.setAttribute('stroke', 'currentColor');
        }
    } catch(e) {}
}
</script>
