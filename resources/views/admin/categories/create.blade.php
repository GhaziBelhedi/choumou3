@extends('admin.layouts.admin')

@section('title', 'Ajouter une catégorie')

@section('content')
    <div class="admin-page-header">
        <div><h1>Ajouter une catégorie</h1></div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">← Retour</a>
    </div>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="card" style="max-width:560px">
        @csrf
        @include('admin.categories._form', ['category' => null])
        <button type="submit" class="btn btn-primary">Créer la catégorie</button>
    </form>
@endsection
