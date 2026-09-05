@extends('admin.layouts.admin')

@section('title', 'Modifier la catégorie')

@section('content')
    <div class="admin-page-header">
        <div><h1>Modifier « {{ $category->name }} »</h1></div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">← Retour</a>
    </div>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="card" style="max-width:560px">
        @csrf
        @method('PUT')
        @include('admin.categories._form', ['category' => $category])
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
@endsection
