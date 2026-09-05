@extends('admin.layouts.admin')

@section('title', 'Ajouter un produit')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1>Ajouter un produit</h1>
        </div>
        <a href="{{ route('admin.produits.index') }}" class="btn btn-secondary">← Retour</a>
    </div>

    <form method="POST" action="{{ route('admin.produits.store') }}" enctype="multipart/form-data" class="card">
        @csrf
        @include('admin.products._form', ['product' => null])
        <button type="submit" class="btn btn-primary btn-lg" style="margin-top:var(--space-4)">Créer le produit</button>
    </form>
@endsection
