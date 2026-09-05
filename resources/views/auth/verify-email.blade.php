@extends('layouts.guest')

@section('title', 'Vérification e-mail')

@section('content')
    <h1 class="auth-card__title">Vérifiez votre e-mail 📬</h1>
    <p class="auth-card__subtitle">
        Merci de votre inscription ! Avant de continuer, merci de vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer. Si vous ne l'avez pas reçu, nous pouvons vous en renvoyer un.
    </p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary btn-block btn-lg">Renvoyer l'e-mail de vérification</button>
    </form>
@endsection
