@extends('layouts.app')

@section('title', 'Choumou3 — Livres & fournitures scolaires')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/home.js') }}"></script>
@endpush

@section('content')

    {{-- ============ 1. Hero slider + 2. Sidebar catégories ============ --}}
    <section class="container" style="padding-top:var(--space-6)">
        <div class="home-hero-layout">
            <aside class="category-sidebar" data-reveal>
                <p class="category-sidebar__title">Catégories</p>
                <ul>
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('categories.show', $category->slug) }}">
                                <span>{{ $category->name }}</span>
                                <span class="text-faint">{{ $category->products_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('products.index') }}" class="category-sidebar__all">Voir tout le catalogue →</a>
            </aside>

            <div class="hero-slider" data-hero-slider>
                <div class="hero-slider__track">
                    <div class="hero-slide is-active">
                        <div class="hero-slide__content">
                            <span class="badge badge-gold">Nouveauté</span>
                            <h1>Votre prochaine lecture vous attend.</h1>
                            <p>Des milliers de livres, livrés partout en Tunisie. Paiement à la réception, sans surprise.</p>
                            <a href="{{ route('products.books') }}" class="btn btn-primary btn-lg">Découvrir les livres</a>
                        </div>
                    </div>
                    <div class="hero-slide">
                        <div class="hero-slide__content">
                            <span class="badge badge-gold">Rentrée scolaire</span>
                            <h1>Toutes les fournitures en un clic.</h1>
                            <p>Cahiers, stylos, trousses... tout pour la rentrée, à petit prix.</p>
                            <a href="{{ route('products.supplies') }}" class="btn btn-primary btn-lg">Voir les fournitures</a>
                        </div>
                    </div>
                    <div class="hero-slide">
                        <div class="hero-slide__content">
                            <span class="badge badge-gold">Offre</span>
                            <h1>Livraison gratuite dès {{ number_format((float) \App\Models\Setting::get('free_shipping_threshold', 0), 0) }} DT.</h1>
                            <p>Paiement à la livraison, partout en Tunisie, sans engagement.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">Commencer mes achats</a>
                        </div>
                    </div>
                </div>
                <div class="hero-slider__dots" data-hero-dots></div>
            </div>
        </div>
    </section>

    {{-- ============ 3. Tuiles catégories ============ --}}
    @if ($categoryTiles->isNotEmpty())
        <section class="container" style="padding-block:var(--space-10)" data-reveal>
            <div class="promo-tiles">
                @foreach ($categoryTiles as $tile)
                    <a href="{{ route('categories.show', $tile->slug) }}" class="promo-tile">
                        <span class="promo-tile__label">Explorez</span>
                        <span class="promo-tile__name">{{ $tile->name }}</span>
                        <span class="promo-tile__cta">Voir les produits →</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ 4. Deal du jour + onglets ============ --}}
    <section class="container" style="padding-bottom:var(--space-12)" data-reveal>
        <div class="deal-layout">
            @if ($dealProduct)
                <div class="deal-panel">
                    <span class="badge badge-primary" style="margin-bottom:var(--space-3)">Deal du jour</span>
                    <img src="{{ $dealProduct->coverUrl() }}" alt="Couverture de {{ $dealProduct->title }}" class="deal-panel__cover">
                    <h3 class="deal-panel__title"><a href="{{ route('products.show', $dealProduct->slug) }}">{{ $dealProduct->title }}</a></h3>
                    <x-product.price-tag :product="$dealProduct" />

                    <div class="deal-countdown" data-deal-countdown data-deadline="{{ $dealProduct->deal_ends_at->toIso8601String() }}">
                        <div><span data-countdown-hours>00</span><small>Heures</small></div>
                        <div><span data-countdown-minutes>00</span><small>Min</small></div>
                        <div><span data-countdown-seconds>00</span><small>Sec</small></div>
                    </div>

                    <div class="deal-progress">
                        <div class="deal-progress__bar" style="width:{{ $dealProduct->dealProgressPercent() }}%"></div>
                    </div>
                    <p class="text-faint" style="font-size:var(--text-xs);margin-top:var(--space-2)">
                        Disponible : {{ $dealProduct->stock_quantity }} · Déjà vendus : {{ $dealProduct->sales_count }}
                    </p>

                    <a href="{{ route('products.show', $dealProduct->slug) }}" class="btn btn-primary btn-block" style="margin-top:var(--space-4)">Profiter de l'offre</a>
                </div>
            @endif

            <div class="{{ $dealProduct ? '' : 'deal-layout__full' }}">
                <div class="tabs__list">
                    <button type="button" class="tabs__tab is-active" data-tab="nouveautes">Nouveautés</button>
                    <button type="button" class="tabs__tab" data-tab="promotions">Promotions</button>
                    <button type="button" class="tabs__tab" data-tab="mieux-notes">Mieux notés</button>
                </div>

                <div class="tabs__panel is-active" data-tab-panel="nouveautes">
                    <div class="product-grid product-grid--tight">
                        @forelse ($newProducts as $product)
                            <x-product.product-card :product="$product" />
                        @empty
                            <p class="text-faint">Aucun produit pour l'instant.</p>
                        @endforelse
                    </div>
                </div>
                <div class="tabs__panel" data-tab-panel="promotions">
                    <div class="product-grid product-grid--tight">
                        @forelse ($onSaleProducts as $product)
                            <x-product.product-card :product="$product" />
                        @empty
                            <p class="text-faint">Aucune promotion pour l'instant.</p>
                        @endforelse
                    </div>
                </div>
                <div class="tabs__panel" data-tab-panel="mieux-notes">
                    <div class="product-grid product-grid--tight">
                        @forelse ($topRatedProducts as $product)
                            <x-product.product-card :product="$product" />
                        @empty
                            <p class="text-faint">Pas encore assez d'avis pour ce classement.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ Bloc réassurance ============ --}}
    <section class="section" style="background:var(--color-paper-soft);padding-block:var(--space-12)" data-reveal>
        <div class="container">
            <div class="grid grid-3">
                <div class="card text-center">
                    <h3 style="font-family:var(--font-serif);margin-bottom:var(--space-2)">🚚 Livraison rapide</h3>
                    <p class="text-muted" style="font-size:var(--text-sm)">Partout en Tunisie, frais calculés automatiquement selon votre gouvernorat.</p>
                </div>
                <div class="card text-center">
                    <h3 style="font-family:var(--font-serif);margin-bottom:var(--space-2)">💵 Paiement à la livraison</h3>
                    <p class="text-muted" style="font-size:var(--text-sm)">Payez en espèces à la réception, aucune carte requise.</p>
                </div>
                <div class="card text-center">
                    <h3 style="font-family:var(--font-serif);margin-bottom:var(--space-2)">📚 Large sélection</h3>
                    <p class="text-muted" style="font-size:var(--text-sm)">Livres et fournitures scolaires, en français, arabe et anglais.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ 5. Meilleures ventes par catégorie ============ --}}
    @if ($bestsellersByCategory->isNotEmpty())
        <section class="section" data-reveal>
            <div class="container">
                <h2 class="section-title">Meilleures ventes</h2>

                <div class="tab-pills" data-bestseller-tabs>
                    @foreach ($bestsellersByCategory as $i => $entry)
                        <button type="button" class="tab-pill {{ $i === 0 ? 'is-active' : '' }}" data-bestseller-tab="cat-{{ $entry['category']->id }}">
                            {{ $entry['category']->name }}
                        </button>
                    @endforeach
                </div>

                @foreach ($bestsellersByCategory as $i => $entry)
                    <div class="bestseller-panel {{ $i === 0 ? 'is-active' : '' }}" data-bestseller-panel="cat-{{ $entry['category']->id }}">
                        <div class="scroll-row">
                            @foreach ($entry['products'] as $product)
                                <x-product.product-card :product="$product" />
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ 6. Bannière promo ============ --}}
    <section class="promo-banner" data-reveal>
        <div class="container promo-banner__inner">
            <div>
                <p class="promo-banner__eyebrow">Offre permanente</p>
                <h2>Livraison gratuite dès {{ number_format((float) \App\Models\Setting::get('free_shipping_threshold', 0), 0) }} DT d'achat</h2>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-lg">Voir le catalogue</a>
        </div>
    </section>

    {{-- ============ 7. Consultés récemment (AJAX) ============ --}}
    <section class="section" data-reveal data-recently-viewed-section style="display:none">
        <div class="container">
            <h2 class="section-title">Consultés récemment</h2>
            <div class="product-grid" data-recently-viewed-grid></div>
        </div>
    </section>

    {{-- ============ 8. Colonnes compactes ============ --}}
    <section class="section" data-reveal>
        <div class="container">
            <div class="grid grid-3">
                <div>
                    <h3 class="compact-list__title">Nouveautés</h3>
                    @foreach ($newProducts->take(5) as $product)
                        <a href="{{ route('products.show', $product->slug) }}" class="compact-list__row">
                            <img src="{{ $product->coverUrl() }}" alt="" loading="lazy">
                            <span>
                                <span class="compact-list__row-title">{{ $product->title }}</span>
                                <span class="compact-list__row-price">{{ number_format((float) $product->price, 2) }} DT</span>
                            </span>
                        </a>
                    @endforeach
                </div>
                <div>
                    <h3 class="compact-list__title">Promotions</h3>
                    @forelse ($onSaleProducts->take(5) as $product)
                        <a href="{{ route('products.show', $product->slug) }}" class="compact-list__row">
                            <img src="{{ $product->coverUrl() }}" alt="" loading="lazy">
                            <span>
                                <span class="compact-list__row-title">{{ $product->title }}</span>
                                <span class="compact-list__row-price">{{ number_format((float) $product->price, 2) }} DT</span>
                            </span>
                        </a>
                    @empty
                        <p class="text-faint" style="font-size:var(--text-sm)">Aucune promotion pour l'instant.</p>
                    @endforelse
                </div>
                <div>
                    <h3 class="compact-list__title">Mieux notés</h3>
                    @forelse ($topRatedProducts->take(5) as $product)
                        <a href="{{ route('products.show', $product->slug) }}" class="compact-list__row">
                            <img src="{{ $product->coverUrl() }}" alt="" loading="lazy">
                            <span>
                                <span class="compact-list__row-title">{{ $product->title }}</span>
                                <span class="compact-list__row-price">{{ number_format((float) $product->price, 2) }} DT</span>
                            </span>
                        </a>
                    @empty
                        <p class="text-faint" style="font-size:var(--text-sm)">Pas encore assez d'avis.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- ============ 9. Newsletter ============ --}}
    <section class="newsletter-section" data-reveal>
        <div class="container newsletter-section__inner">
            <div>
                <h2>Restez informé de nos nouveautés</h2>
                <p>Recevez nos meilleures offres et nouveautés directement par e-mail.</p>
            </div>
            <form data-newsletter-form action="{{ route('newsletter.store') }}" method="POST" class="newsletter-form">
                @csrf
                <input type="email" name="email" class="input" placeholder="Votre adresse e-mail" required>
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </form>
        </div>
    </section>

@endsection
