@extends('layouts.app')

@section('title', 'Commande confirmée — Choumou3')

@section('content')
    <div class="container section" style="max-width:640px">
        <div class="text-center" style="margin-bottom:var(--space-8)">
            <div style="width:64px;height:64px;border-radius:var(--radius-full);background:var(--color-success-tint);color:var(--color-success);display:flex;align-items:center;justify-content:center;margin-inline:auto;margin-bottom:var(--space-5);font-size:28px">
                ✓
            </div>
            <h1 style="font-family:var(--font-serif);font-size:var(--text-3xl);margin-bottom:var(--space-2)">Merci, votre commande est confirmée !</h1>
            <p class="text-muted">Un e-mail de confirmation vous a été envoyé. Vous paierez en espèces à la réception.</p>
        </div>

        <div class="card">
            <div class="flex-between" style="margin-bottom:var(--space-5)">
                <div>
                    <p class="text-faint" style="font-size:var(--text-xs)">Numéro de commande</p>
                    <p style="font-weight:700;font-size:var(--text-lg)">{{ $order->order_number }}</p>
                </div>
                <span class="badge badge-primary">{{ $order->statusLabel() }}</span>
            </div>

            @foreach ($order->items as $item)
                <div class="flex-between" style="padding-block:var(--space-2);border-bottom:1px solid var(--color-border);font-size:var(--text-sm)">
                    <span>{{ $item->book_title_snapshot }} × {{ $item->quantity }}</span>
                    <span>{{ number_format($item->subtotal, 2) }} DT</span>
                </div>
            @endforeach

            <div class="flex-between" style="padding-top:var(--space-4);font-weight:700;font-size:var(--text-lg)">
                <span>Total</span>
                <span class="text-primary">{{ number_format((float) $order->total, 2) }} DT</span>
            </div>
        </div>

        <div class="flex" style="gap:var(--space-3);margin-top:var(--space-6);justify-content:center">
            <a href="{{ route('home') }}" class="btn btn-secondary">Retour à l'accueil</a>
            @auth
                <a href="{{ route('orders.show', $order) }}" class="btn btn-primary">Suivre ma commande</a>
            @else
                <a href="{{ route('orders.track') }}" class="btn btn-primary">Suivre ma commande</a>
            @endauth
        </div>
    </div>
@endsection
