{{-- resources/views/emails/auth/reset-password.blade.php --}}
@extends('emails.layouts.base')

@section('header_title', 'Réinitialisation de mot de passe 🔑')

@section('content')
    <p class="greeting">Bonjour {{ $user->prenom }} 👋</p>
    <p class="text">
        Vous avez demandé la réinitialisation de votre mot de passe Biocolis.
        Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
    </p>

    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ $url }}" class="btn">
            🔑 Réinitialiser mon mot de passe
        </a>
    </div>

    <div style="background: #fef9c3; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 16px; margin: 20px 0;">
        <p style="font-size: 13px; color: #92400e;">
            ⚠️ Ce lien expire dans <strong>60 minutes</strong>.
            Si vous n'avez pas demandé cette réinitialisation, ignorez cet email — votre mot de passe ne sera pas modifié.
        </p>
    </div>

    <p class="text" style="font-size: 13px; color: #9ca3af;">
        Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
        <a href="{{ $url }}" style="color: #118501; word-break: break-all; font-size: 12px;">{{ $url }}</a>
    </p>
@endsection
