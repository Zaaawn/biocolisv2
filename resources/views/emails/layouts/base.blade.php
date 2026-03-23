{{-- resources/views/emails/layouts/base.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biocolis</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; color: #1a1a1a; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .card { background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #118501; padding: 32px; text-align: center; }
        .header img { height: 40px; width: auto; }
        .header-text { color: white; font-size: 22px; font-weight: 700; margin-top: 12px; }
        .body { padding: 32px; }
        .greeting { font-size: 18px; font-weight: 600; color: #1a1a1a; margin-bottom: 12px; }
        .text { font-size: 15px; color: #4b5563; line-height: 1.6; margin-bottom: 16px; }
        .btn { display: inline-block; padding: 14px 28px; background: #118501; color: #ffffff !important; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 15px; margin: 16px 0; }
        .box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 20px; margin: 20px 0; }
        .box-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .box-row:last-child { border-bottom: none; font-weight: 600; }
        .box-label { color: #6b7280; }
        .box-value { color: #1a1a1a; font-weight: 500; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-blue { background: #dbeafe; color: #2563eb; }
        .badge-orange { background: #ffedd5; color: #ea580c; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
        .footer { padding: 24px 32px; background: #f9fafb; border-top: 1px solid #e5e7eb; text-align: center; }
        .footer p { font-size: 12px; color: #9ca3af; line-height: 1.6; }
        .footer a { color: #118501; text-decoration: none; }
        .product-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
        .product-row:last-child { border-bottom: none; }
        .product-info { flex: 1; }
        .product-name { font-size: 14px; font-weight: 600; color: #1a1a1a; }
        .product-detail { font-size: 13px; color: #6b7280; margin-top: 2px; }
        .product-price { font-size: 14px; font-weight: 700; color: #118501; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            {{-- Header --}}
            <div class="header">
                <div style="font-size: 28px; font-weight: 800; color: white; letter-spacing: -0.5px;">🌱 Biocolis</div>
                <div class="header-text">@yield('header_title')</div>
            </div>

            {{-- Body --}}
            <div class="body">
                @yield('content')
            </div>

            {{-- Footer --}}
            <div class="footer">
                <p>
                    Vous recevez cet email car vous avez un compte sur <a href="{{ config('app.url') }}">Biocolis</a>.<br>
                    La marketplace des fruits et légumes locaux — Circuit court, fraîcheur garantie.<br><br>
                    <a href="{{ config('app.url') }}/profil">Gérer mes préférences</a> ·
                    <a href="{{ config('app.url') }}/cgu">CGU</a> ·
                    <a href="{{ config('app.url') }}/confidentialite">Confidentialité</a>
                </p>
            </div>
        </div>

        <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 20px;">
            © {{ date('Y') }} Biocolis · Paiement sécurisé par Stripe
        </p>
    </div>
</body>
</html>
