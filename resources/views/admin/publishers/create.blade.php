@extends('admin.layouts.admin')

@section('title', 'Ajouter un éditeur')

@section('content')
    <div class="admin-page-header">
        <div><h1>Ajouter un éditeur</h1></div>
        <a href="{{ route('admin.editeurs.index') }}" class="btn btn-secondary">← Retour</a>
    </div>

    <form method="POST" action="{{ route('admin.editeurs.store') }}" class="card" style="max-width:480px">
        @csrf
        <div class="field">
            <label class="field__label" for="name">Nom</label>
            <input class="input @error('name') has-error @enderror" type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
            @error('name') <span class="field__error">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
@endsection
