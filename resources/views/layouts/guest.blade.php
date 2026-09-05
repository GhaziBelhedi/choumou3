<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Librairie'))</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;0,700;1,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
</head>
<body
    class="auth-layout"
    @if(session('success')) data-flash-success="{{ session('success') }}" @endif
>

    <a href="#auth-main" class="skip-link">Aller au contenu principal</a>

    {{-- Panneau de marque (desktop uniquement) --}}
    <aside class="auth-brand">
        <a href="{{ url('/') }}" class="auth-brand__logo">Choumou3<span>.</span></a>

        <div class="auth-brand__content">
            <h1>Votre librairie, sans détour.</h1>
            <p>Livres et fournitures scolaires, livrés partout en Tunisie. Paiement à la réception, sans surprise.</p>

            <ul class="auth-brand__stats">
                <li><strong>500+</strong><span>Produits</span></li>
                <li><strong>24</strong><span>Gouvernorats livrés</span></li>
                <li><strong>100%</strong><span>Paiement à la livraison</span></li>
            </ul>
        </div>

        <p class="auth-brand__footer">&copy; {{ date('Y') }} Choumou3 — Tous droits réservés.</p>
    </aside>

    {{-- Panneau formulaire --}}
    <main id="auth-main" class="auth-form-panel" tabindex="-1">
        <div class="auth-form-wrap">
            <a href="{{ url('/') }}" class="auth-form-wrap__logo">Choumou3<span>.</span></a>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="padding-inline-start:var(--space-4);list-style:disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="auth-card">
                @yield('content')
            </div>

            <p class="text-center text-muted auth-form-wrap__back">
                <a href="{{ url('/') }}">&larr; Retour à la boutique</a>
            </p>
        </div>
    </main>

    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
