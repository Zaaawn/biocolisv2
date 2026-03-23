{{-- resources/views/emails/messages/nouveau-message.blade.php --}}
@extends('emails.layouts.base')

@section('header_title', 'Nouveau message 💬')

@section('content')
    <p class="greeting">Bonjour {{ $destinataire->prenom }} 👋</p>
    <p class="text">
        <strong>{{ $expediteur->prenom }} {{ $expediteur->nom }}</strong> vous a envoyé un message sur Biocolis
        @if($conversation->annonce)
            concernant l'annonce <strong>{{ $conversation->annonce->titre }}</strong>
        @endif
        .
    </p>

    <div style="background: #f9fafb; border-left: 4px solid #118501; border-radius: 0 10px 10px 0; padding: 16px 20px; margin: 20px 0;">
        <p style="font-size: 13px; color: #6b7280; margin-bottom: 6px;">{{ $expediteur->prenom }} écrit :</p>
        <p style="font-size: 15px; color: #1a1a1a; font-style: italic;">
            "{{ $conversation->dernierMessage?->contenu ?? '(image ou fichier joint)' }}"
        </p>
    </div>

    <div style="text-align: center;">
        <a href="{{ route('messages.show', ['annonce' => $conversation->annonce_id, 'user' => $expediteur->id]) }}" class="btn">
            Répondre au message →
        </a>
    </div>

    <p class="text" style="font-size: 13px; color: #9ca3af; margin-top: 20px;">
        Pour ne plus recevoir ces notifications, gérez vos préférences dans votre profil.
    </p>
@endsection
