@extends('layouts.app')

@section('title', 'Commande '.$order->order_number.' — Choumou3')

@section('content')
    <div class="page-header">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('home') }}">Accueil</a>
                <span>/</span>
                <a href="{{ route('orders.index') }}">Mes commandes</a>
                <span>/</span>
                <span>{{ $order->order_number }}</span>
            </nav>
            <h1>Commande {{ $order->order_number }}</h1>
        </div>
    </div>

    <div class="container section">
        <div class="checkout-layout">
            <div>
                <div class="card" style="margin-bottom:var(--space-5)">
                    <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-4)">Articles</h2>
                    @foreach ($order->items as $item)
                        <div class="flex-between" style="padding-block:var(--space-3);border-bottom:1px solid var(--color-border);font-size:var(--text-sm)">
                            <span>{{ $item->book_title_snapshot }} × {{ $item->quantity }}</span>
                            <span style="font-weight:600">{{ number_format((float) $item->subtotal, 2) }} DT</span>
                        </div>
                    @endforeach

                    <div class="flex-between" style="padding-top:var(--space-4);font-size:var(--text-sm)">
                        <span class="text-muted">Sous-total</span>
                        <span>{{ number_format((float) $order->subtotal, 2) }} DT</span>
                    </div>
                    @if ($order->discount_amount > 0)
                        <div class="flex-between" style="font-size:var(--text-sm);color:var(--color-success)">
                            <span>Remise</span>
                            <span>−{{ number_format((float) $order->discount_amount, 2) }} DT</span>
                        </div>
                    @endif
                    <div class="flex-between" style="font-size:var(--text-sm)">
                        <span class="text-muted">Livraison</span>
                        <span>{{ number_format((float) $order->shipping_cost, 2) }} DT</span>
                    </div>
                    <div class="flex-between" style="padding-top:var(--space-3);font-weight:700;font-size:var(--text-lg)">
                        <span>Total</span>
                        <span class="text-primary">{{ number_format((float) $order->total, 2) }} DT</span>
                    </div>
                </div>

                <div class="card">
                    <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-4)">Adresse de livraison</h2>
                    <p style="font-size:var(--text-sm);line-height:1.8">
                        {{ $order->shipping_full_name }} — {{ $order->shipping_phone }}<br>
                        {{ $order->shipping_address_line }}, {{ $order->shipping_city }}<br>
                        {{ $order->shipping_governorate }} {{ $order->shipping_postal_code }}
                    </p>
                    @if ($order->customer_notes)
                        <p class="text-faint" style="font-size:var(--text-xs);margin-top:var(--space-3)">Note : {{ $order->customer_notes }}</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-5)">Suivi de commande</h2>
                <x-order-status-timeline :order="$order" />
            </div>
        </div>
    </div>
@endsection
