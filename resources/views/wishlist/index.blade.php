@extends('layouts.app')

@section('title', 'Ma liste de souhaits — Choumou3')

@section('content')
    <div class="page-header">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('home') }}">Accueil</a>
                <span>/</span>
                <span>Ma liste de souhaits</span>
            </nav>
            <h1>Ma liste de souhaits</h1>
        </div>
    </div>

    <div class="container section">
        @if ($books->isEmpty())
            <div class="empty-state">
                <h3>Votre liste de souhaits est vide</h3>
                <p style="margin-bottom:var(--space-6)">Ajoutez des livres en cliquant sur le cœur depuis le catalogue.</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary">Voir le catalogue</a>
            </div>
        @else
            <div class="product-grid">
                @foreach ($books as $book)
                    <x-product.product-card :book="$book" />
                @endforeach
            </div>
        @endif
    </div>
@endsection
