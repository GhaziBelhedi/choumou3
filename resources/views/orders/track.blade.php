@extends('layouts.app')

@section('title', 'Suivi de commande — Choumou3')

@section('content')
    <div class="page-header">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('home') }}">Accueil</a>
                <span>/</span>
                <span>Suivi de commande</span>
            </nav>
            <h1>Suivi de commande</h1>
        </div>
    </div>

    <div class="container section" style="max-width:640px">
        <div class="card" style="margin-bottom:var(--space-6)">
            <p class="text-muted" style="font-size:var(--text-sm);margin-bottom:var(--space-5)">
                Entrez votre numéro de commande et le numéro de téléphone utilisé lors de la commande.
            </p>

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('orders.track.submit') }}">
                @csrf
                <div class="field">
                    <label class="field__label" for="order_number">Numéro de commande</label>
                    <input class="input" type="text" id="order_number" name="order_number" placeholder="CH-2026-000123" value="{{ old('order_number') }}" required>
                </div>
                <div class="field">
                    <label class="field__label" for="phone">Téléphone</label>
                    <input class="input" type="tel" id="phone" name="phone" value="{{ old('phone') }}" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">Suivre ma commande</button>
            </form>
        </div>

        @if ($order)
            <div class="card">
                <div class="flex-between" style="margin-bottom:var(--space-5)">
                    <div>
                        <p class="text-faint" style="font-size:var(--text-xs)">Commande</p>
                        <p style="font-weight:700">{{ $order->order_number }}</p>
                    </div>
                    <span class="badge badge-primary">{{ $order->statusLabel() }}</span>
                </div>

                <x-order-status-timeline :order="$order" />
            </div>
        @endif
    </div>
@endsection
