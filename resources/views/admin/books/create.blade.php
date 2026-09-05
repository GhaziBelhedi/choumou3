@extends('admin.layouts.admin')

@section('title', 'Ajouter un livre')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1>Ajouter un livre</h1>
        </div>
        <a href="{{ route('admin.livres.index') }}" class="btn btn-secondary">← Retour</a>
    </div>

    <form method="POST" action="{{ route('admin.livres.store') }}" enctype="multipart/form-data" class="card">
        @csrf
        @include('admin.books._form', ['book' => null])
        <button type="submit" class="btn btn-primary btn-lg" style="margin-top:var(--space-4)">Créer le livre</button>
    </form>
@endsection
