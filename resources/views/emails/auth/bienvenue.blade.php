{{-- resources/views/emails/auth/bienvenue.blade.php --}}
@extends('emails.layouts.base')

@section('header_title', 'Bienvenue sur Biocolis 🌱')

@section('content')
    <p class="greeting">Bonjour {{ $user->prenom }} ! 👋</p>
    <p class="text">
        Votre compte Biocolis est créé avec succès. Bienvenue dans la communauté du circuit court !
    </p>

    <div style="background: #f0fdf4; border-radius: 12px; padding: 24px; margin: 20px 0; text-align: center;">
        <div style="font-size: 40px; margin-bottom: 12px;">🥕🍓🥦</div>
        <p style="font-size: 15px; color: #166534; font-weight: 600;">
            Des fruits et légumes frais, directement des producteurs locaux.
        </p>
    </div>

    <p style="font-weight: 600; font-size: 15px; margin-bottom: 16px;">Pour commencer :</p>

    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px;">
        @foreach([
            ['🔍', 'Parcourez les annonces', 'Découvrez les producteurs près de chez vous', route('annonces.index')],
            ['📢', 'Déposez une annonce', 'Vendez vos surplus de récolte facilement', route('annonces.create')],
            ['💬', 'Contactez un producteur', 'Échangez directement avec les vendeurs', route('messages.index')],
        ] as [$ico, $titre, $desc, $url])
            <a href="{{ $url }}" style="display: flex; align-items: center; gap: 16px; padding: 14px 16px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; text-decoration: none;">
                <span style="font-size: 24px;">{{ $ico }}</span>
                <div>
                    <div style="font-weight: 600; color: #1a1a1a; font-size: 14px;">{{ $titre }}</div>
                    <div style="color: #6b7280; font-size: 13px;">{{ $desc }}</div>
                </div>
            </a>
        @endforeach
    </div>

    <div style="text-align: center;">
        <a href="{{ route('dashboard') }}" class="btn">Accéder à mon espace →</a>
    </div>

    <hr class="divider">

    <p class="text" style="font-size: 13px; text-align: center; color: #9ca3af;">
        Besoin d'aide ? <a href="{{ route('pages.contact') }}" style="color: #118501;">Contactez-nous</a> ou consultez notre <a href="{{ route('pages.cgu') }}" style="color: #118501;">FAQ</a>.
    </p>
@endsection
