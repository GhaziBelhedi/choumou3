@extends('layouts.guest')

@section('title', 'Mot de passe oublié')

@section('content')
    <h1 style="font-family:var(--font-serif);font-size:var(--text-2xl);margin-bottom:var(--space-3)">Mot de passe oublié</h1>
    <p class="text-muted" style="font-size:var(--text-sm);margin-bottom:var(--space-6)">
        Indiquez votre adresse e-mail, nous vous enverrons un lien de réinitialisation.
    </p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="field">
            <label class="field__label" for="email">Adresse e-mail</label>
            <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">Envoyer le lien</button>
    </form>

    <p class="text-center text-muted" style="margin-top:var(--space-6);font-size:var(--text-sm)">
        <a href="{{ route('login') }}" class="text-primary">Retour à la connexion</a>
    </p>
@endsection
