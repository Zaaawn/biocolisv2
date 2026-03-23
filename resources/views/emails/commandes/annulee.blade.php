{{-- resources/views/emails/commandes/annulee.blade.php --}}
@extends('emails.layouts.base')

@section('header_title', 'Commande annulée')

@section('content')
    <p class="greeting">Bonjour {{ $commande->acheteur->prenom }},</p>
    <p class="text">
        Votre commande <strong>{{ $commande->numero }}</strong> a été annulée.
        Si vous avez été débité, un remboursement sera effectué sous 5 à 10 jours ouvrés.
    </p>

    <div class="box">
        <div class="box-row">
            <span class="box-label">Commande</span>
            <span class="box-value">{{ $commande->numero }}</span>
        </div>
        <div class="box-row">
            <span class="box-label">Montant</span>
            <span class="box-value">{{ number_format($commande->total_ttc, 2) }}€</span>
        </div>
        <div class="box-row">
            <span class="box-label">Statut remboursement</span>
            <span class="badge badge-orange">En cours</span>
        </div>
    </div>

    <p class="text">
        Des questions ? Contactez-nous à <a href="mailto:support@biocolis.fr" style="color: #118501;">support@biocolis.fr</a>
    </p>

    <div style="text-align: center;">
        <a href="{{ route('annonces.index') }}" class="btn">Voir les annonces →</a>
    </div>
@endsection
