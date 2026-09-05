@extends('layouts.guest')

@section('title', 'Connexion')

@section('content')
    <h1 style="font-family:var(--font-serif);font-size:var(--text-2xl);margin-bottom:var(--space-6)">Connexion</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label class="field__label" for="email">Adresse e-mail</label>
            <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="field">
            <label class="field__label" for="password">Mot de passe</label>
            <input class="input" type="password" id="password" name="password" required>
        </div>

        <div class="flex-between" style="margin-bottom:var(--space-6)">
            <label class="checkbox-row">
                <input type="checkbox" name="remember">
                Se souvenir de moi
            </label>
            <a href="{{ route('password.request') }}" style="font-size:var(--text-sm)" class="text-primary">Mot de passe oublié ?</a>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">Se connecter</button>
    </form>

    <p class="text-center text-muted" style="margin-top:var(--space-6);font-size:var(--text-sm)">
        Pas encore de compte ? <a href="{{ route('register') }}" class="text-primary">Créer un compte</a>
    </p>
@endsection
