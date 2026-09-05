@extends('layouts.guest')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
    <h1 class="auth-card__title">Nouveau mot de passe</h1>
    <p class="auth-card__subtitle">Choisissez un nouveau mot de passe pour votre compte.</p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="field">
            <label class="field__label" for="email">Adresse e-mail</label>
            <input class="input" type="email" id="email" name="email" value="{{ old('email', $email) }}" required autofocus>
        </div>

        <div class="field">
            <label class="field__label" for="password">Nouveau mot de passe</label>
            <input class="input" type="password" id="password" name="password" placeholder="••••••••" required>
        </div>

        <div class="field">
            <label class="field__label" for="password_confirmation">Confirmer le mot de passe</label>
            <input class="input" type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">Réinitialiser le mot de passe</button>
    </form>
@endsection
