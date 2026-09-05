@extends('layouts.app')

@section('title', $book->title.' — '.$book->author.' — Choumou3')
@section('meta_description', Str::limit(strip_tags($book->description), 155))

@push('scripts')
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endpush

@php
    $allImages = collect([$book->cover_path ? $book->coverUrl() : null])
        ->merge($book->images->map->url())
        ->filter()
        ->values();
    if ($allImages->isEmpty()) {
        $allImages = collect([$book->coverUrl()]);
    }
@endphp

@section('content')

    <div class="container">
        <nav class="breadcrumb" style="padding-top:var(--space-6)">
            <a href="{{ route('home') }}">Accueil</a>
            <span>/</span>
            <a href="{{ route('books.index') }}">Catalogue</a>
            @if ($book->categories->isNotEmpty())
                <span>/</span>
                <a href="{{ route('categories.show', $book->categories->first()->slug) }}">{{ $book->categories->first()->name }}</a>
            @endif
            <span>/</span>
            <span>{{ $book->title }}</span>
        </nav>
    </div>

    <div class="container product-page">

        {{-- Galerie --}}
        <div>
            <div class="product-gallery__main" data-gallery-main>
                <img src="{{ $allImages->first() }}" alt="Couverture de {{ $book->title }}" id="gallery-main-img">
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
                            <img src="{{ $src }}" alt="Image {{ $i + 1 }} de {{ $book->title }}" loading="lazy">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Infos & achat --}}
        <div>
            @if ($book->categories->isNotEmpty())
                <div class="flex" style="gap:var(--space-2);margin-bottom:var(--space-3)">
                    @foreach ($book->categories as $category)
                        <a href="{{ route('categories.show', $category->slug) }}" class="badge badge-neutral">{{ $category->name }}</a>
                    @endforeach
                </div>
            @endif

            <h1 class="product-info__title">{{ $book->title }}</h1>
            <p class="product-info__author">de <strong>{{ $book->author }}</strong></p>

            <div class="flex" style="gap:var(--space-3)">
                <x-product.rating-stars :rating="$book->average_rating" :count="$book->reviews_count" :size="18" />
                <x-product.stock-badge :quantity="$book->stock_quantity" />
            </div>

            <div style="margin-top:var(--space-5)">
                <x-product.price-tag :book="$book" size="lg" />
            </div>

            <div class="product-info__meta">
                <div><span>Auteur</span>{{ $book->author }}</div>
                <div><span>Éditeur</span>{{ $book->publisher->name ?? '—' }}</div>
                <div><span>ISBN</span>{{ $book->isbn ?? '—' }}</div>
                <div><span>Pages</span>{{ $book->pages ?? '—' }}</div>
                <div><span>Langue</span>{{ ['fr' => 'Français', 'ar' => 'Arabe', 'en' => 'Anglais'][$book->language] ?? $book->language }}</div>
                <div><span>Parution</span>{{ $book->publication_date?->format('d/m/Y') ?? '—' }}</div>
            </div>

            @if ($book->stock_quantity > 0)
                <form method="POST" action="{{ url('/panier') }}" class="product-buybox">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <div class="qty-stepper" data-qty-stepper>
                        <button type="button" data-qty-decrement aria-label="Diminuer">−</button>
                        <input type="number" name="quantity" value="1" min="1" max="{{ $book->stock_quantity }}">
                        <button type="button" data-qty-increment aria-label="Augmenter">+</button>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" style="flex:1">Ajouter au panier</button>
                </form>
            @else
                <div class="alert alert-info" style="margin-top:var(--space-6)">
                    Ce livre est actuellement en rupture de stock.
                </div>
            @endif
        </div>
    </div>

    {{-- Tabs --}}
    <div class="container" style="padding-block:var(--space-10)">
        <div class="tabs__list">
            <button type="button" class="tabs__tab is-active" data-tab="description">Description</button>
            <button type="button" class="tabs__tab" data-tab="avis">Avis clients ({{ $book->reviews_count }})</button>
        </div>

        <div class="tabs__panel is-active" data-tab-panel="description">
            <div style="max-width:70ch;line-height:var(--leading-relaxed)">
                {!! nl2br(e($book->description)) !!}
            </div>
        </div>

        <div class="tabs__panel" data-tab-panel="avis">
            @auth
                @if (! $book->reviews()->where('user_id', auth()->id())->exists())
                    <form method="POST" action="{{ route('reviews.store', $book) }}" class="card" style="margin-bottom:var(--space-6);max-width:520px">
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
                    <a href="{{ route('login') }}" class="text-primary">Connectez-vous</a> pour laisser un avis sur ce livre.
                </p>
            @endauth

            @forelse ($book->approvedReviews as $review)
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
                    <p>Soyez le premier à donner votre avis sur ce livre.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Livres similaires --}}
    @if ($similarBooks->isNotEmpty())
        <section class="section" style="background:var(--color-paper-soft)">
            <div class="container">
                <h2 class="section-title">Vous aimerez aussi</h2>
                <div class="product-grid">
                    @foreach ($similarBooks as $similar)
                        <x-product.product-card :book="$similar" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
