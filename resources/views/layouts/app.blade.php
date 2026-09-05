<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Librairie'))</title>
    <meta name="description" content="@yield('meta_description', 'Boutique en ligne — livres et fournitures scolaires, livraison partout en Tunisie, paiement à la livraison.')">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;0,700;1,500&family=Inter:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages.css') }}">
    @stack('styles')
</head>
<body
    data-authenticated="{{ auth()->check() ? '1' : '0' }}"
    @if(request()->routeIs('wishlist.index')) data-wishlist-page @endif
    @if(session('success')) data-flash-success="{{ session('success') }}" @endif
    @if(session('error')) data-flash-error="{{ session('error') }}" @endif
>

    <a href="#main-content" class="skip-link">Aller au contenu principal</a>

    <header class="site-header">
        <div class="container site-header__inner">
            <a href="{{ url('/') }}" class="site-logo">Choumou3<span>.</span></a>

            <nav class="site-nav" aria-label="Navigation principale">
                <a href="{{ url('/') }}" class="site-nav__link {{ request()->is('/') ? 'is-active' : '' }}">Accueil</a>
                <a href="{{ url('/produits') }}" class="site-nav__link {{ request()->is('produits*') ? 'is-active' : '' }}">Catalogue</a>
                <a href="{{ url('/livres') }}" class="site-nav__link {{ request()->is('livres*') ? 'is-active' : '' }}">Livres</a>
                <a href="{{ url('/fournitures-scolaires') }}" class="site-nav__link {{ request()->is('fournitures-scolaires*') ? 'is-active' : '' }}">Fournitures</a>
                <a href="{{ url('/categories') }}" class="site-nav__link {{ request()->is('categories*') ? 'is-active' : '' }}">Catégories</a>
                <a href="{{ url('/suivi-commande') }}" class="site-nav__link {{ request()->is('suivi-commande') ? 'is-active' : '' }}">Suivi de commande</a>
            </nav>

            <div class="site-header__actions">
                <button type="button" class="icon-btn" aria-label="Rechercher" data-search-toggle>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                </button>

                @auth
                    <div style="position:relative">
                        <button type="button" class="icon-btn" data-dropdown-toggle="notif-dropdown" aria-expanded="false" aria-label="Notifications">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                            @if(($unreadNotificationsCount ?? 0) > 0)
                                <span class="icon-btn__badge">{{ $unreadNotificationsCount }}</span>
                            @endif
                        </button>
                        <div id="notif-dropdown" class="dropdown-panel" style="position:absolute;right:0;top:48px;background:var(--color-white);border:1px solid var(--color-border);border-radius:var(--radius-md);box-shadow:var(--shadow-md);min-width:280px;max-width:340px;padding:var(--space-2);z-index:var(--z-dropdown)">
                            @forelse (($latestNotifications ?? collect()) as $notif)
                                <div style="padding:var(--space-2) var(--space-3);border-bottom:1px solid var(--color-border);font-size:var(--text-sm)">
                                    @if ($notif->data['type'] === 'order_status_updated')
                                        Commande {{ $notif->data['order_number'] }} : {{ $notif->data['status_label'] }}
                                    @elseif ($notif->data['type'] === 'review_approved')
                                        Votre avis sur « {{ $notif->data['product_title'] }} » a été publié
                                    @endif
                                    <p class="text-faint" style="font-size:var(--text-xs);margin-top:2px">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            @empty
                                <p class="text-faint" style="padding:var(--space-3);font-size:var(--text-sm)">Aucune notification.</p>
                            @endforelse
                            <a href="{{ route('notifications.index') }}" class="site-footer__link text-primary" style="padding:var(--space-2) var(--space-3);text-align:center;display:block">Tout voir</a>
                        </div>
                    </div>

                    <div style="position:relative">
                        <button type="button" class="icon-btn" data-dropdown-toggle="account-dropdown" aria-expanded="false" aria-label="Mon compte">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-7 8-7s8 3 8 7"/></svg>
                        </button>
                        <div id="account-dropdown" class="dropdown-panel" style="position:absolute;right:0;top:48px;background:var(--color-white);border:1px solid var(--color-border);border-radius:var(--radius-md);box-shadow:var(--shadow-md);min-width:200px;padding:var(--space-2);z-index:var(--z-dropdown)">
                            <a href="{{ route('profile.edit') }}" class="site-footer__link" style="color:var(--color-ink);padding:var(--space-2) var(--space-3);border-radius:var(--radius-sm)">Mon profil</a>
                            <a href="{{ route('orders.index') }}" class="site-footer__link" style="color:var(--color-ink);padding:var(--space-2) var(--space-3);border-radius:var(--radius-sm)">Mes commandes</a>
                            <a href="{{ route('wishlist.index') }}" class="site-footer__link" style="color:var(--color-ink);padding:var(--space-2) var(--space-3);border-radius:var(--radius-sm)">Liste de souhaits</a>
                            <form method="POST" action="{{ url('/deconnexion') }}">
                                @csrf
                                <button type="submit" class="site-footer__link" style="color:var(--color-danger);padding:var(--space-2) var(--space-3);border-radius:var(--radius-sm);width:100%;text-align:left">Déconnexion</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ url('/connexion') }}" class="icon-btn" aria-label="Connexion">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-7 8-7s8 3 8 7"/></svg>
                    </a>
                @endauth

                <a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}" class="icon-btn" aria-label="Liste de souhaits">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-4.5-9.5-9C.8 8.4 2.4 5 6 5c2 0 3.4 1.1 4 2.2C10.6 6.1 12 5 14 5c3.6 0 5.2 3.4 3.5 7-2.5 4.5-9.5 9-9.5 9z"/></svg>
                </a>

                <a href="{{ url('/panier') }}" class="icon-btn" aria-label="Panier">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.5 3h2l2.4 12.4a2 2 0 0 0 2 1.6h8.2a2 2 0 0 0 2-1.6L21 7H6"/></svg>
                    <span class="icon-btn__badge" data-cart-badge @if(($cartCount ?? 0) === 0) hidden @endif>{{ $cartCount ?? 0 }}</span>
                </a>

                <button type="button" class="menu-toggle icon-btn" data-menu-toggle aria-label="Menu" aria-expanded="false">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                </button>
            </div>
        </div>
    </header>

    <div class="mobile-menu" data-mobile-menu>
        <div class="mobile-menu__overlay" data-menu-overlay></div>
        <div class="mobile-menu__panel">
            <div class="flex-between" style="margin-bottom:var(--space-6)">
                <span class="site-logo">Choumou3<span>.</span></span>
                <button type="button" class="icon-btn" data-menu-close aria-label="Fermer">✕</button>
            </div>
            <a href="{{ url('/') }}" class="mobile-menu__link">Accueil</a>
            <a href="{{ url('/produits') }}" class="mobile-menu__link">Catalogue</a>
            <a href="{{ url('/livres') }}" class="mobile-menu__link">Livres</a>
            <a href="{{ url('/fournitures-scolaires') }}" class="mobile-menu__link">Fournitures scolaires</a>
            <a href="{{ url('/categories') }}" class="mobile-menu__link">Catégories</a>
            <a href="{{ url('/suivi-commande') }}" class="mobile-menu__link">Suivi de commande</a>
            @auth
                <a href="{{ route('profile.edit') }}" class="mobile-menu__link">Mon compte</a>
            @else
                <a href="{{ url('/connexion') }}" class="mobile-menu__link">Connexion</a>
                <a href="{{ url('/inscription') }}" class="mobile-menu__link">Créer un compte</a>
            @endauth
        </div>
    </div>

    <main id="main-content" tabindex="-1">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container site-footer__grid">
            <div>
                <p class="site-logo" style="color:var(--color-white)">Choumou3<span>.</span></p>
                <p style="margin-top:var(--space-4);font-size:var(--text-sm);opacity:0.8;max-width:32ch">
                    Votre boutique en ligne : livres et fournitures scolaires, livrés partout en Tunisie, paiement à la réception.
                </p>
            </div>
            <div>
                <p class="site-footer__heading">Boutique</p>
                <a href="{{ url('/produits') }}" class="site-footer__link">Catalogue</a>
                <a href="{{ url('/livres') }}" class="site-footer__link">Livres</a>
                <a href="{{ url('/fournitures-scolaires') }}" class="site-footer__link">Fournitures scolaires</a>
                <a href="{{ url('/categories') }}" class="site-footer__link">Catégories</a>
                <a href="{{ url('/produits?tri=nouveautes') }}" class="site-footer__link">Nouveautés</a>
                <a href="{{ url('/produits?tri=meilleures-ventes') }}" class="site-footer__link">Meilleures ventes</a>
            </div>
            <div>
                <p class="site-footer__heading">Aide</p>
                <a href="{{ url('/suivi-commande') }}" class="site-footer__link">Suivi de commande</a>
                <a href="{{ url('/livraison') }}" class="site-footer__link">Livraison &amp; paiement</a>
                <a href="{{ url('/contact') }}" class="site-footer__link">Contact</a>
            </div>
            <div>
                <p class="site-footer__heading">Mon compte</p>
                <a href="{{ route('profile.edit') }}" class="site-footer__link">Mon profil</a>
                <a href="{{ route('orders.index') }}" class="site-footer__link">Mes commandes</a>
                <a href="{{ url('/liste-de-souhaits') }}" class="site-footer__link">Liste de souhaits</a>
            </div>
        </div>
        <div class="container site-footer__bottom">
            <span>&copy; {{ date('Y') }} Choumou3 — Tous droits réservés.</span>
            <span>Paiement à la livraison &middot; Livraison partout en Tunisie</span>
        </div>
    </footer>

    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/cart.js') }}"></script>
    <script src="{{ asset('assets/js/wishlist.js') }}"></script>
    @stack('scripts')
</body>
</html>
