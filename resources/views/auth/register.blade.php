@extends('layouts.guest')

@section('title', 'Créer un compte')

@section('content')
    <h1 style="font-family:var(--font-serif);font-size:var(--text-2xl);margin-bottom:var(--space-6)">Créer un compte</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field">
            <label class="field__label" for="name">Nom complet</label>
            <input class="input" type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
        </div>

        <div class="field">
            <label class="field__label" for="email">Adresse e-mail</label>
            <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div class="field">
            <label class="field__label" for="phone">Téléphone <span class="text-faint">(optionnel)</span></label>
            <input class="input" type="tel" id="phone" name="phone" value="{{ old('phone') }}">
        </div>

        <div class="field">
            <label class="field__label" for="password">Mot de passe</label>
            <input class="input" type="password" id="password" name="password" required>
            <span class="field__hint">8 caractères minimum.</span>
        </div>

        <div class="field">
            <label class="field__label" for="password_confirmation">Confirmer le mot de passe</label>
            <input class="input" type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">Créer mon compte</button>
    </form>

    <p class="text-center text-muted" style="margin-top:var(--space-6);font-size:var(--text-sm)">
        Déjà un compte ? <a href="{{ route('login') }}" class="text-primary">Se connecter</a>
    </p>
@endsection
