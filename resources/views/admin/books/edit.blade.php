@extends('admin.layouts.admin')

@section('title', 'Modifier '.$book->title)

@section('content')
    <div class="admin-page-header">
        <div>
            <h1>Modifier « {{ $book->title }} »</h1>
        </div>
        <a href="{{ route('admin.livres.index') }}" class="btn btn-secondary">← Retour</a>
    </div>

    <form method="POST" action="{{ route('admin.livres.update', $book) }}" enctype="multipart/form-data" class="card">
        @csrf
        @method('PUT')
        @include('admin.books._form', ['book' => $book])
        <button type="submit" class="btn btn-primary btn-lg" style="margin-top:var(--space-4)">Enregistrer les modifications</button>
    </form>
@endsection
