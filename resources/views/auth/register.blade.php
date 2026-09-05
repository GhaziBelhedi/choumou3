@extends('layouts.guest')

@section('title', 'Créer un compte')

@section('content')
    <h1 class="auth-card__title">Créer un compte</h1>
    <p class="auth-card__subtitle">Rejoignez-nous pour suivre vos commandes et gérer votre liste de souhaits.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field">
            <label class="field__label" for="name">Nom complet</label>
            <input class="input" type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Votre nom" required autofocus>
        </div>

        <div class="field">
            <label class="field__label" for="email">Adresse e-mail</label>
            <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="vous@exemple.com" required>
        </div>

        <div class="field">
            <label class="field__label" for="phone">Téléphone <span class="text-faint">(optionnel)</span></label>
            <input class="input" type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="20 123 456">
        </div>

        <div class="field">
            <label class="field__label" for="password">Mot de passe</label>
            <input class="input" type="password" id="password" name="password" placeholder="••••••••" required>
            <span class="field__hint">8 caractères minimum.</span>
        </div>

        <div class="field">
            <label class="field__label" for="password_confirmation">Confirmer le mot de passe</label>
            <input class="input" type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">Créer mon compte</button>
    </form>

    <p class="text-center text-muted" style="margin-top:var(--space-6);font-size:var(--text-sm)">
        Déjà un compte ? <a href="{{ route('login') }}" class="text-primary" style="font-weight:600">Se connecter</a>
    </p>
@endsection
