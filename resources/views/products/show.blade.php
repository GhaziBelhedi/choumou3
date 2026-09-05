@extends('layouts.app')

@section('title', $product->title.($product->isBook() ? ' — '.$product->author : '').' — Choumou3')
@section('meta_description', Str::limit(strip_tags($product->description), 155))

@push('scripts')
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.trackRecentlyViewed) window.trackRecentlyViewed({{ $product->id }});
        });
    </script>
@endpush

@php
    $allImages = collect([$product->cover_path ? $product->coverUrl() : null])
        ->merge($product->images->map->url())
        ->filter()
        ->values();
    if ($allImages->isEmpty()) {
        $allImages = collect([$product->coverUrl()]);
    }
@endphp

@section('content')

    <div class="container">
        <nav class="breadcrumb" style="padding-top:var(--space-6)">
            <a href="{{ route('home') }}">Accueil</a>
            <span>/</span>
            <a href="{{ $product->isBook() ? route('products.books') : route('products.supplies') }}">
                {{ $product->isBook() ? 'Livres' : 'Fournitures scolaires' }}
            </a>
            @if ($product->categories->isNotEmpty())
                <span>/</span>
                <a href="{{ route('categories.show', $product->categories->first()->slug) }}">{{ $product->categories->first()->name }}</a>
            @endif
            <span>/</span>
            <span>{{ $product->title }}</span>
        </nav>
    </div>

    <div class="container product-page">

        {{-- Galerie --}}
        <div>
            <div class="product-gallery__main" data-gallery-main>
                <img src="{{ $allImages->first() }}" alt="Couverture de {{ $product->title }}" id="gallery-main-img">
            </div>

            @if ($allImages->count() > 1)
                <div class="product-gallery__thumbs">
                    @foreach ($allImages as $i => $src)
                        <button
                            type="button"
                            class="product-gallery__thumb {{ $i === 0 ? 'is-active' : '' }}"
                            data-gallery-thumb
                            data-full-src="{{ $src }}"
                        >
                            <img src="{{ $src }}" alt="Image {{ $i + 1 }} de {{ $product->title }}" loading="lazy">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Infos & achat --}}
        <div>
            <div class="flex" style="gap:var(--space-2);margin-bottom:var(--space-3);flex-wrap:wrap">
                <span class="badge {{ $product->isBook() ? 'badge-primary' : 'badge-info' }}">{{ $product->typeLabel() }}</span>
                @foreach ($product->categories as $category)
                    <a href="{{ route('categories.show', $category->slug) }}" class="badge badge-neutral">{{ $category->name }}</a>
                @endforeach
            </div>

            <h1 class="product-info__title">{{ $product->title }}</h1>
            @if ($product->isBook())
                <p class="product-info__author">de <strong>{{ $product->author }}</strong></p>
            @endif

            <div class="flex" style="gap:var(--space-3)">
                <x-product.rating-stars :rating="$product->average_rating" :count="$product->reviews_count" :size="18" />
                <x-product.stock-badge :quantity="$product->stock_quantity" />
            </div>

            <div style="margin-top:var(--space-5)">
                <x-product.price-tag :product="$product" size="lg" />
            </div>

            <div class="product-info__meta">
                @if ($product->isBook())
                    <div><span>Auteur</span>{{ $product->author }}</div>
                    <div><span>ISBN</span>{{ $product->isbn ?? '—' }}</div>
                    <div><span>Pages</span>{{ $product->pages ?? '—' }}</div>
                    <div><span>Langue</span>{{ ['fr' => 'Français', 'ar' => 'Arabe', 'en' => 'Anglais'][$product->language] ?? '—' }}</div>
                    <div><span>Parution</span>{{ $product->publication_date?->format('d/m/Y') ?? '—' }}</div>
                @else
                    <div><span>Référence</span>{{ $product->sku ?? '—' }}</div>
                @endif
            </div>

            @if ($product->stock_quantity > 0)
                <form method="POST" action="{{ route('cart.store') }}" class="product-buybox" data-add-to-cart-form>
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="qty-stepper" data-qty-stepper>
                        <button type="button" data-qty-decrement aria-label="Diminuer">−</button>
                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}">
                        <button type="button" data-qty-increment aria-label="Augmenter">+</button>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" style="flex:1" data-loading-text="Ajout...">Ajouter au panier</button>
                </form>
            @else
                <div class="alert alert-info" style="margin-top:var(--space-6)">
                    Ce produit est actuellement en rupture de stock.
                </div>
            @endif
        </div>
    </div>

    {{-- Tabs --}}
    <div class="container" style="padding-block:var(--space-10)">
        <div class="tabs__list">
            <button type="button" class="tabs__tab is-active" data-tab="description">Description</button>
            <button type="button" class="tabs__tab" data-tab="avis">Avis clients ({{ $product->reviews_count }})</button>
        </div>

        <div class="tabs__panel is-active" data-tab-panel="description">
            <div style="max-width:70ch;line-height:var(--leading-relaxed)">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>

        <div class="tabs__panel" data-tab-panel="avis">
            @auth
                @if (! $product->reviews()->where('user_id', auth()->id())->exists())
                    <form method="POST" action="{{ route('reviews.store', $product) }}" class="card" style="margin-bottom:var(--space-6);max-width:520px">
                        @csrf
                        <h3 style="font-family:var(--font-serif);font-size:var(--text-base);margin-bottom:var(--space-4)">Donner votre avis</h3>

                        <div class="field">
                            <label class="field__label">Note</label>
                            <div class="star-rating-input" style="margin-top:var(--space-2)">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" value="{{ $i }}" id="rating-{{ $i }}" required>
                                    <label for="rating-{{ $i }}">★</label>
                                @endfor
                            </div>
                            <span class="field__hint">Sélectionnez de 1 à 5 étoiles.</span>
                        </div>

                        <div class="field">
                            <label class="field__label" for="review-title">Titre <span class="text-faint">(optionnel)</span></label>
                            <input class="input" type="text" id="review-title" name="title" maxlength="150">
                        </div>

                        <div class="field">
                            <label class="field__label" for="review-comment">Commentaire</label>
                            <textarea class="textarea" id="review-comment" name="comment" rows="3" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Envoyer mon avis</button>
                    </form>
                @endif
            @else
                <p class="text-muted" style="font-size:var(--text-sm);margin-bottom:var(--space-6)">
                    <a href="{{ route('login') }}" class="text-primary">Connectez-vous</a> pour laisser un avis sur ce produit.
                </p>
            @endauth

            @forelse ($product->approvedReviews as $review)
                <div class="review-item">
                    <div class="review-item__header">
                        <span class="review-item__author">{{ $review->user->name }}</span>
                        <x-product.rating-stars :rating="$review->rating" :size="14" />
                    </div>
                    @if ($review->title)
                        <p style="font-weight:600;margin-bottom:var(--space-1)">{{ $review->title }}</p>
                    @endif
                    <p class="text-muted" style="font-size:var(--text-sm)">{{ $review->comment }}</p>
                </div>
            @empty
                <div class="empty-state">
                    <h3>Aucun avis pour l'instant</h3>
                    <p>Soyez le premier à donner votre avis sur ce produit.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Produits similaires --}}
    @if ($similarProducts->isNotEmpty())
        <section class="section" style="background:var(--color-paper-soft)">
            <div class="container">
                <h2 class="section-title">Vous aimerez aussi</h2>
                <div class="product-grid">
                    @foreach ($similarProducts as $similar)
                        <x-product.product-card :product="$similar" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
