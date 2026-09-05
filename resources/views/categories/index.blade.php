@extends('layouts.app')

@section('title', 'Catégories — Choumou3')

@section('content')

    <div class="page-header">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('home') }}">Accueil</a>
                <span>/</span>
                <span>Catégories</span>
            </nav>
            <h1>Toutes les catégories</h1>
        </div>
    </div>

    <div class="container section">
        <div class="grid grid-4">
            @foreach ($categories as $category)
                <a href="{{ route('categories.show', $category->slug) }}" class="category-card">
                    <span class="category-card__icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v18H6.5A2.5 2.5 0 0 0 4 22.5"/><path d="M4 4.5A2.5 2.5 0 0 0 6.5 7H20"/></svg>
                    </span>
                    <span class="category-card__name">{{ $category->name }}</span>
                    <span class="text-faint" style="font-size:var(--text-xs)">{{ $category->products_count }} produit(s)</span>
                </a>
            @endforeach
        </div>
    </div>

@endsection
