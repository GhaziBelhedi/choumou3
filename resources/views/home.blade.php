@extends('layouts.app')

@section('title', 'Choumou3 — Librairie en ligne')

@section('content')
    <section class="section" style="padding-top:var(--space-20)">
        <div class="container" style="text-align:center;max-width:640px">
            <span class="badge badge-gold" style="margin-bottom:var(--space-4)">Livraison partout en Tunisie</span>
            <h1 style="font-family:var(--font-serif);font-size:var(--text-5xl);margin-bottom:var(--space-4)">
                Votre prochaine lecture vous attend.
            </h1>
            <p class="text-muted" style="font-size:var(--text-lg);margin-bottom:var(--space-8)">
                Des milliers de livres physiques, livrés chez vous. Paiement à la livraison, sans surprise.
            </p>
            <a href="{{ url('/livres') }}" class="btn btn-primary btn-lg">Découvrir le catalogue</a>
        </div>
    </section>

    <section class="section" style="background:var(--color-paper-soft)">
        <div class="container">
            <div class="grid grid-3">
                <div class="card text-center">
                    <h3 style="font-family:var(--font-serif);margin-bottom:var(--space-2)">🚚 Livraison rapide</h3>
                    <p class="text-muted" style="font-size:var(--text-sm)">Partout en Tunisie, frais calculés automatiquement selon votre gouvernorat.</p>
                </div>
                <div class="card text-center">
                    <h3 style="font-family:var(--font-serif);margin-bottom:var(--space-2)">💵 Paiement à la livraison</h3>
                    <p class="text-muted" style="font-size:var(--text-sm)">Payez en espèces à la réception, aucune carte requise.</p>
                </div>
                <div class="card text-center">
                    <h3 style="font-family:var(--font-serif);margin-bottom:var(--space-2)">📚 Large sélection</h3>
                    <p class="text-muted" style="font-size:var(--text-sm)">Romans, essais, jeunesse, scolaire — en français, arabe et anglais.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
