{{-- resources/views/emails/commandes/statut-change.blade.php --}}
@extends('emails.layouts.base')

@section('header_title', 'Mise à jour de votre commande')

@section('content')
    @php
        $infos = [
            'en_preparation' => ['👨‍🍳', 'En cours de préparation', 'Le vendeur prépare vos produits frais.', 'badge-blue'],
            'prete'          => ['📦', 'Prête !', 'Votre commande est prête. Coordonnez la récupération avec le vendeur.', 'badge-green'],
            'en_livraison'   => ['🚴', 'En cours de livraison', 'Votre commande est en route !', 'badge-blue'],
            'livree'         => ['✅', 'Livrée', 'Vous avez reçu votre commande ? Confirmez la réception pour libérer le paiement au vendeur.', 'badge-green'],
        ][$statut] ?? ['📋', ucfirst($statut), '', 'badge-blue'];
    @endphp

    <p class="greeting">Bonjour {{ $commande->acheteur->prenom }} 👋</p>
    <p class="text">Votre commande <strong>{{ $commande->numero }}</strong> vient d'être mise à jour.</p>

    <div style="text-align: center; padding: 24px 0;">
        <div style="font-size: 48px; margin-bottom: 12px;">{{ $infos[0] }}</div>
        <span class="badge {{ $infos[3] }}" style="font-size: 16px; padding: 8px 20px;">{{ $infos[1] }}</span>
        <p class="text" style="margin-top: 16px;">{{ $infos[2] }}</p>
    </div>

    @if($statut === 'livree')
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 16px; margin: 20px 0; text-align: center;">
            <p style="font-size: 14px; color: #15803d; margin-bottom: 12px;">
                Confirmez la réception pour que le vendeur reçoive son paiement.
            </p>
            <a href="{{ route('commandes.show', $commande->id) }}" class="btn">
                Confirmer la réception →
            </a>
        </div>
    @else
        <div style="text-align: center;">
            <a href="{{ route('commandes.show', $commande->id) }}" class="btn">
                Voir ma commande →
            </a>
        </div>
    @endif
@endsection
