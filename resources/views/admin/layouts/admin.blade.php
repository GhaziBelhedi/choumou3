<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') — Choumou3</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;0,700;1,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    @stack('styles')
</head>
<body
    @if(session('success')) data-flash-success="{{ session('success') }}" @endif
    @if(session('error')) data-flash-error="{{ session('error') }}" @endif
>

    <a href="#admin-main-content" class="skip-link">Aller au contenu principal</a>

    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-sidebar__logo">Choumou3<span>.</span> <span style="font-size:var(--text-sm);color:var(--color-ink-faint)">admin</span></div>

            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav__link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                    📊 Tableau de bord
                </a>

                <p class="admin-nav__group-label">Catalogue</p>
                <a href="{{ route('admin.produits.index') }}" class="admin-nav__link {{ request()->routeIs('admin.produits.*') ? 'is-active' : '' }}">📚 Produits</a>
                <a href="{{ route('admin.categories.index') }}" class="admin-nav__link {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}">🏷️ Catégories</a>

                <p class="admin-nav__group-label">Ventes</p>
                <a href="{{ route('admin.orders.index') }}" class="admin-nav__link {{ request()->routeIs('admin.orders.*') ? 'is-active' : '' }}">
                    📦 Commandes
                    @if (($pendingOrdersCount ?? 0) > 0)
                        <span class="admin-nav__badge">{{ $pendingOrdersCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.coupons.index') }}" class="admin-nav__link {{ request()->routeIs('admin.coupons.*') ? 'is-active' : '' }}">🎟️ Coupons</a>
                <a href="{{ route('admin.reviews.index') }}" class="admin-nav__link {{ request()->routeIs('admin.reviews.*') ? 'is-active' : '' }}">
                    ⭐ Avis
                    @if (($pendingReviewsCount ?? 0) > 0)
                        <span class="admin-nav__badge">{{ $pendingReviewsCount }}</span>
                    @endif
                </a>

                <p class="admin-nav__group-label">Configuration</p>
                <a href="{{ route('admin.users.index') }}" class="admin-nav__link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">👤 Utilisateurs</a>
                <a href="{{ route('admin.governorates.index') }}" class="admin-nav__link {{ request()->routeIs('admin.governorates.*') ? 'is-active' : '' }}">🚚 Livraison</a>
                <a href="{{ route('admin.settings.edit') }}" class="admin-nav__link {{ request()->routeIs('admin.settings.*') ? 'is-active' : '' }}">⚙️ Paramètres</a>
            </nav>

            <div class="admin-sidebar__footer">
                <a href="{{ route('home') }}">← Retour au site</a>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="flex" style="gap:var(--space-3)">
                    <button type="button" class="icon-btn admin-topbar__mobile-toggle" data-menu-toggle aria-label="Menu">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                    </button>
                    <span class="text-muted" style="font-size:var(--text-sm)">Connecté en tant que <strong>{{ auth()->user()->name }}</strong></span>
                </div>
                <form method="POST" action="{{ url('/deconnexion') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm">Déconnexion</button>
                </form>
            </header>

            <div class="admin-content" id="admin-main-content" tabindex="-1">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul style="padding-inline-start:var(--space-4);list-style:disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    {{-- Menu mobile admin : réutilise le drawer du site client --}}
    <div class="mobile-menu" data-mobile-menu>
        <div class="mobile-menu__overlay" data-menu-overlay></div>
        <div class="mobile-menu__panel">
            <div class="flex-between" style="margin-bottom:var(--space-6)">
                <span class="site-logo">Admin</span>
                <button type="button" class="icon-btn" data-menu-close aria-label="Fermer">✕</button>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="mobile-menu__link">Tableau de bord</a>
            <a href="{{ route('admin.produits.index') }}" class="mobile-menu__link">Produits</a>
            <a href="{{ route('admin.categories.index') }}" class="mobile-menu__link">Catégories</a>
            <a href="{{ route('admin.orders.index') }}" class="mobile-menu__link">Commandes</a>
            <a href="{{ route('admin.coupons.index') }}" class="mobile-menu__link">Coupons</a>
            <a href="{{ route('admin.reviews.index') }}" class="mobile-menu__link">Avis</a>
            <a href="{{ route('admin.users.index') }}" class="mobile-menu__link">Utilisateurs</a>
            <a href="{{ route('admin.governorates.index') }}" class="mobile-menu__link">Livraison</a>
            <a href="{{ route('admin.settings.edit') }}" class="mobile-menu__link">Paramètres</a>
        </div>
    </div>

    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/admin.js') }}"></script>
    @stack('scripts')
</body>
</html>
