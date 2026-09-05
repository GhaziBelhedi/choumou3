@extends('layouts.guest')

@section('title', 'Mot de passe oublié')

@section('content')
    <h1 class="auth-card__title">Mot de passe oublié ?</h1>
    <p class="auth-card__subtitle">Indiquez votre adresse e-mail, nous vous enverrons un lien de réinitialisation.</p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="field">
            <label class="field__label" for="email">Adresse e-mail</label>
            <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="vous@exemple.com" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">Envoyer le lien</button>
    </form>

    <p class="text-center text-muted" style="margin-top:var(--space-6);font-size:var(--text-sm)">
        <a href="{{ route('login') }}" class="text-primary">&larr; Retour à la connexion</a>
    </p>
@endsection
