@extends('layouts.app')

@section('title', 'Choumou3 — Librairie en ligne')

@section('content')
    <section class="section" style="padding-top:var(--space-16);padding-bottom:var(--space-10)">
        <div class="container" style="text-align:center;max-width:640px">
            <span class="badge badge-gold" style="margin-bottom:var(--space-4)">Livraison partout en Tunisie</span>
            <h1 style="font-family:var(--font-serif);font-size:var(--text-5xl);margin-bottom:var(--space-4)">
                Votre prochaine lecture vous attend.
            </h1>
            <p class="text-muted" style="font-size:var(--text-lg);margin-bottom:var(--space-8)">
                Des milliers de livres physiques, livrés chez vous. Paiement à la livraison, sans surprise.
            </p>
            <a href="{{ route('books.index') }}" class="btn btn-primary btn-lg">Découvrir le catalogue</a>
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <section class="container" style="padding-bottom:var(--space-12)">
            <div class="grid grid-3" style="grid-template-columns:repeat(auto-fit, minmax(150px, 1fr))">
                @foreach ($categories as $category)
                    <a href="{{ route('categories.show', $category->slug) }}" class="category-card">
                        <span class="category-card__icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v18H6.5A2.5 2.5 0 0 0 4 22.5"/><path d="M4 4.5A2.5 2.5 0 0 0 6.5 7H20"/></svg>
                        </span>
                        <span class="category-card__name" style="font-size:var(--text-sm)">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($featuredBooks->isNotEmpty())
        <section class="section" style="padding-top:0">
            <div class="container">
                <div class="flex-between" style="margin-bottom:var(--space-6)">
                    <h2 class="section-title" style="margin-bottom:0">Coups de cœur</h2>
                    <a href="{{ route('books.index') }}" class="text-primary" style="font-size:var(--text-sm);font-weight:600">Tout voir →</a>
                </div>
                <div class="scroll-row">
                    @foreach ($featuredBooks as $book)
                        <x-product.product-card :book="$book" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section" style="background:var(--color-paper-soft)">
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
                    <p class="text-muted" style="font-size:var(--text-sm)">Romans, essais, jeunesse, scolaire — en français, arabe et anglais.</p>
                </div>
            </div>
        </div>
    </section>

    @if ($newBooks->isNotEmpty())
        <section class="section">
            <div class="container">
                <div class="flex-between" style="margin-bottom:var(--space-6)">
                    <h2 class="section-title" style="margin-bottom:0">Nouveautés</h2>
                    <a href="{{ route('books.index', ['tri' => 'nouveautes']) }}" class="text-primary" style="font-size:var(--text-sm);font-weight:600">Tout voir →</a>
                </div>
                <div class="scroll-row">
                    @foreach ($newBooks as $book)
                        <x-product.product-card :book="$book" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($bestSellers->isNotEmpty())
        <section class="section" style="padding-top:0">
            <div class="container">
                <div class="flex-between" style="margin-bottom:var(--space-6)">
                    <h2 class="section-title" style="margin-bottom:0">Meilleures ventes</h2>
                    <a href="{{ route('books.index', ['tri' => 'meilleures-ventes']) }}" class="text-primary" style="font-size:var(--text-sm);font-weight:600">Tout voir →</a>
                </div>
                <div class="scroll-row">
                    @foreach ($bestSellers as $book)
                        <x-product.product-card :book="$book" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
