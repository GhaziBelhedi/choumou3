@extends('layouts.app')

@section('title', 'Mon profil — Choumou3')

@section('content')
    <div class="page-header">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('home') }}">Accueil</a>
                <span>/</span>
                <span>Mon profil</span>
            </nav>
            <h1>Mon profil</h1>
        </div>
    </div>

    <div class="container section" style="max-width:640px">

        <div class="card" style="margin-bottom:var(--space-6)">
            <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-5)">Informations personnelles</h2>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="field">
                    <label class="field__label" for="name">Nom complet</label>
                    <input class="input" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="field">
                    <label class="field__label">Adresse e-mail</label>
                    <input class="input" type="email" value="{{ $user->email }}" disabled style="background:var(--color-paper-soft)">
                </div>

                <div class="field">
                    <label class="field__label" for="phone">Téléphone</label>
                    <input class="input" type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>

                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>

        <div class="card" style="margin-bottom:var(--space-6)">
            <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-5)">Mot de passe</h2>
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PUT')

                <div class="field">
                    <label class="field__label" for="current_password">Mot de passe actuel</label>
                    <input class="input" type="password" id="current_password" name="current_password" required>
                </div>

                <div class="field">
                    <label class="field__label" for="password">Nouveau mot de passe</label>
                    <input class="input" type="password" id="password" name="password" required>
                </div>

                <div class="field">
                    <label class="field__label" for="password_confirmation">Confirmer le nouveau mot de passe</label>
                    <input class="input" type="password" id="password_confirmation" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-secondary">Changer le mot de passe</button>
            </form>
        </div>

        <div class="card">
            <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-5)">Adresses enregistrées</h2>

            @forelse ($addresses as $address)
                <div class="flex-between" style="padding-block:var(--space-3);border-bottom:1px solid var(--color-border)">
                    <div style="font-size:var(--text-sm)">
                        <p style="font-weight:600">{{ $address->label ?? $address->full_name }}</p>
                        <p class="text-muted">{{ $address->address_line }}, {{ $address->city }}, {{ $address->governorate->name }}</p>
                    </div>
                    <form method="POST" action="{{ route('profile.address.destroy', $address) }}" onsubmit="return confirm('Supprimer cette adresse ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="font-size:var(--text-xs);color:var(--color-danger)">Supprimer</button>
                    </form>
                </div>
            @empty
                <p class="text-faint" style="font-size:var(--text-sm)">Aucune adresse enregistrée pour l'instant. Elles s'ajoutent automatiquement lors d'une commande si vous cochez « Enregistrer cette adresse ».</p>
            @endforelse
        </div>
    </div>
@endsection
