{{-- resources/views/emails/commandes/payee-vendeur.blade.php --}}
@extends('emails.layouts.base')

@section('header_title', 'Nouvelle vente 🛒')

@section('content')
    <p class="greeting">Bonne nouvelle {{ $commande->vendeur->prenom }} ! 🎉</p>
    <p class="text">
        Vous avez reçu une nouvelle commande. Préparez vos produits et coordonnez la livraison avec l'acheteur.
    </p>

    <div class="box">
        <div class="box-row">
            <span class="box-label">Numéro</span>
            <span class="box-value">{{ $commande->numero }}</span>
        </div>
        <div class="box-row">
            <span class="box-label">Acheteur</span>
            <span class="box-value">{{ $commande->acheteur->nom_complet }}</span>
        </div>
        <div class="box-row">
            <span class="box-label">Mode de livraison</span>
            <span class="box-value">
                {{ match($commande->livraison?->mode) {
                    'main_propre'  => '🤝 Remise en main propre',
                    'point_relais' => '📦 Point relais',
                    'domicile'     => '🏠 Livraison à domicile',
                    default        => '—'
                } }}
            </span>
        </div>
        <div class="box-row">
            <span class="box-label">Votre gain</span>
            <span class="box-value" style="color: #118501; font-size: 16px;">+{{ number_format($commande->montant_vendeur, 2) }}€</span>
        </div>
    </div>

    {{-- Articles --}}
    <p style="font-weight: 600; margin-bottom: 12px; color: #1a1a1a;">Articles à préparer</p>
    @foreach($commande->lignes as $ligne)
        <div class="product-row">
            <div class="product-info">
                <div class="product-name">{{ $ligne->titre_annonce }}</div>
                <div class="product-detail">{{ $ligne->quantite }} {{ $ligne->unite_prix }}</div>
            </div>
            <div class="product-price">{{ number_format($ligne->sous_total, 2) }}€</div>
        </div>
    @endforeach

    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 16px; margin: 20px 0;">
        <p style="font-size: 14px; color: #15803d; font-weight: 600; margin-bottom: 4px;">💡 Prochaine étape</p>
        <p style="font-size: 13px; color: #166534;">
            Mettez à jour le statut de la commande au fur et à mesure de la préparation.
            L'acheteur sera notifié automatiquement.
        </p>
    </div>

    <div style="text-align: center;">
        <a href="{{ route('commandes.show', $commande->id) }}" class="btn">
            Gérer cette commande →
        </a>
    </div>
@endsection
