@extends('admin.layouts.admin')

@section('title', 'Modifier '.$product->title)

@section('content')
    <div class="admin-page-header">
        <div>
            <h1>Modifier « {{ $product->title }} »</h1>
        </div>
        <a href="{{ route('admin.produits.index') }}" class="btn btn-secondary">← Retour</a>
    </div>

    <form method="POST" action="{{ route('admin.produits.update', $product) }}" enctype="multipart/form-data" class="card">
        @csrf
        @method('PUT')
        @include('admin.products._form', ['product' => $product])
        <button type="submit" class="btn btn-primary btn-lg" style="margin-top:var(--space-4)">Enregistrer les modifications</button>
    </form>
@endsection
