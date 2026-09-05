@extends('admin.layouts.admin')

@section('title', 'Modifier l\'éditeur')

@section('content')
    <div class="admin-page-header">
        <div><h1>Modifier « {{ $publisher->name }} »</h1></div>
        <a href="{{ route('admin.editeurs.index') }}" class="btn btn-secondary">← Retour</a>
    </div>

    <form method="POST" action="{{ route('admin.editeurs.update', $publisher) }}" class="card" style="max-width:480px">
        @csrf
        @method('PUT')
        <div class="field">
            <label class="field__label" for="name">Nom</label>
            <input class="input @error('name') has-error @enderror" type="text" id="name" name="name" value="{{ old('name', $publisher->name) }}" required>
            @error('name') <span class="field__error">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
@endsection
