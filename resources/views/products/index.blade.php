@extends('layouts.app')

@section('title', $currentCategory ? $currentCategory->name.' — Choumou3' : 'Catalogue — Choumou3')

@push('scripts')
    <script src="{{ asset('assets/js/filters.js') }}"></script>
@endpush

@php
    $sortOptions = [
        'nouveautes' => 'Nouveautés',
        'popularite' => 'Popularité',
        'meilleures-ventes' => 'Meilleures ventes',
        'prix-asc' => 'Prix croissant',
        'prix-desc' => 'Prix décroissant',
    ];
    $currentSort = request('tri', 'nouveautes');
@endphp

@section('content')

    <div class="page-header">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('home') }}">Accueil</a>
                <span>/</span>
                @if ($currentCategory)
                    <a href="{{ route('products.index') }}">Catalogue</a>
                    <span>/</span>
                    <span>{{ $currentCategory->name }}</span>
                @else
                    <span>Catalogue</span>
                @endif
            </nav>
            <h1>{{ $currentCategory ? $currentCategory->name : 'Tout le catalogue' }}</h1>
        </div>
    </div>

    <div class="container catalog-layout">

        {{-- Sidebar filtres (desktop) --}}
        <aside class="filters-panel">
            @include('products.partials.filters-form')
        </aside>

        {{-- Drawer filtres (mobile) --}}
        <div class="filters-drawer" data-filters-drawer>
            <div class="filters-drawer__overlay" data-filters-overlay></div>
            <div class="filters-drawer__panel">
                <div class="flex-between" style="margin-bottom:var(--space-6)">
                    <h2 style="font-family:var(--font-serif);font-size:var(--text-xl)">Filtres</h2>
                    <button type="button" class="icon-btn" data-filters-close aria-label="Fermer">✕</button>
                </div>
                @include('products.partials.filters-form')
            </div>
        </div>

        <div>
            <div class="catalog-toolbar">
                <p class="catalog-toolbar__count">{{ $products->total() }} produit(s) trouvé(s)</p>

                <div class="flex" style="gap:var(--space-3)">
                    <button type="button" class="btn btn-secondary btn-sm filter-toggle-mobile" data-filters-toggle>
                        Filtres
                    </button>

                    <form method="GET" action="{{ url()->current() }}">
                        @foreach (request()->except(['tri', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <select name="tri" class="select" onchange="this.form.submit()" aria-label="Trier par">
                            @foreach ($sortOptions as $value => $label)
                                <option value="{{ $value }}" @selected($currentSort === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            @if (request()->anyFilled(['q', 'type', 'categorie', 'langue', 'editeur', 'prix_min', 'prix_max']))
                <div class="active-filters">
                    @if ($q = request('q'))
                        <span class="active-filter-chip">Recherche : "{{ $q }}"</span>
                    @endif
                    @if ($type = request('type'))
                        <span class="active-filter-chip">{{ \App\Models\Product::TYPES[$type] ?? $type }}</span>
                    @endif
                    @if ($cat = request('categorie'))
                        <span class="active-filter-chip">{{ $categories->firstWhere('slug', $cat)?->name ?? $cat }}</span>
                    @endif
                    @if ($lang = request('langue'))
                        <span class="active-filter-chip">{{ ['fr' => 'Français', 'ar' => 'Arabe', 'en' => 'Anglais'][$lang] ?? $lang }}</span>
                    @endif
                    @if ($pub = request('editeur'))
                        <span class="active-filter-chip">{{ $publishers->firstWhere('slug', $pub)?->name ?? $pub }}</span>
                    @endif
                    <a href="{{ $currentCategory ? route('categories.show', $currentCategory->slug) : route('products.index') }}" class="active-filter-chip" style="background:var(--color-paper-soft);color:var(--color-ink-soft)">
                        Réinitialiser ✕
                    </a>
                </div>
            @endif

            @if ($products->isEmpty())
                <div class="empty-state">
                    <h3>Aucun produit ne correspond à votre recherche</h3>
                    <p>Essayez d'élargir vos filtres ou votre recherche.</p>
                </div>
            @else
                <div class="product-grid">
                    @foreach ($products as $product)
                        <x-product.product-card :product="$product" />
                    @endforeach
                </div>

                {{ $products->links('pagination.custom') }}
            @endif
        </div>
    </div>

@endsection
