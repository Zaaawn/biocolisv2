{{-- resources/views/emails/commandes/payee-acheteur.blade.php --}}
@extends('emails.layouts.base')

@section('header_title', 'Commande confirmée ✅')

@section('content')
    <p class="greeting">Bonjour {{ $commande->acheteur->prenom }} 👋</p>
    <p class="text">
        Votre paiement a bien été reçu ! Votre commande est confirmée et le vendeur va préparer vos produits frais.
    </p>

    {{-- Récap commande --}}
    <div class="box">
        <div class="box-row">
            <span class="box-label">Numéro de commande</span>
            <span class="box-value">{{ $commande->numero }}</span>
        </div>
        <div class="box-row">
            <span class="box-label">Vendeur</span>
            <span class="box-value">{{ $commande->vendeur->nom_complet }}</span>
        </div>
        <div class="box-row">
            <span class="box-label">Mode de livraison</span>
            <span class="box-value">
                {{ match($commande->livraison?->mode) {
                    'main_propre'  => '🤝 Remise en main propre',
                    'point_relais' => '📦 Point relais',
                    'domicile'     => '🏠 Livraison à domicile',
                    'locker'       => '🗄️ Casier connecté',
                    default        => '—'
                } }}
            </span>
        </div>
        <div class="box-row">
            <span class="box-label">Statut</span>
            <span class="badge badge-green">✅ Payée</span>
        </div>
    </div>

    {{-- Articles --}}
    <p style="font-weight: 600; margin-bottom: 12px; color: #1a1a1a;">Articles commandés</p>
    @foreach($commande->lignes as $ligne)
        <div class="product-row">
            <div class="product-info">
                <div class="product-name">{{ $ligne->titre_annonce }}</div>
                <div class="product-detail">{{ $ligne->quantite }} {{ $ligne->unite_prix }} × {{ number_format($ligne->prix_unitaire, 2) }}€</div>
            </div>
            <div class="product-price">{{ number_format($ligne->sous_total, 2) }}€</div>
        </div>
    @endforeach

    <hr class="divider">

    {{-- Total --}}
    <div class="box">
        <div class="box-row">
            <span class="box-label">Sous-total</span>
            <span class="box-value">{{ number_format($commande->sous_total, 2) }}€</span>
        </div>
        <div class="box-row">
            <span class="box-label">Livraison</span>
            <span class="box-value">{{ number_format($commande->frais_livraison, 2) }}€</span>
        </div>
        <div class="box-row">
            <span class="box-label">Frais de service</span>
            <span class="box-value">{{ number_format($commande->frais_service, 2) }}€</span>
        </div>
        <div class="box-row">
            <span class="box-label">Total payé</span>
            <span class="box-value" style="color: #118501; font-size: 16px;">{{ number_format($commande->total_ttc, 2) }}€</span>
        </div>
    </div>

    <div style="text-align: center;">
        <a href="{{ route('commandes.show', $commande->id) }}" class="btn">
            Suivre ma commande →
        </a>
    </div>

    <p class="text" style="margin-top: 20px; font-size: 13px; color: #9ca3af;">
        Une question ? Contactez le vendeur directement via la messagerie Biocolis.
    </p>
@endsection
