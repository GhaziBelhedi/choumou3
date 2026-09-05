@extends('layouts.app')

@section('title', 'Mon panier — Choumou3')

@section('content')

    <div class="page-header">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('home') }}">Accueil</a>
                <span>/</span>
                <span>Mon panier</span>
            </nav>
            <h1>Mon panier</h1>
        </div>
    </div>

    <div class="container section">
        @if ($cart->items->isEmpty())
            <div class="empty-state">
                <h3>Votre panier est vide</h3>
                <p style="margin-bottom:var(--space-6)">Parcourez notre catalogue pour trouver votre prochaine lecture.</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary">Voir le catalogue</a>
            </div>
        @else
            <div class="grid" style="grid-template-columns:1fr;gap:var(--space-8)">
                <div style="display:grid;grid-template-columns:1fr;gap:var(--space-8)" class="cart-layout">
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Livre</th>
                                    <th>Prix unitaire</th>
                                    <th>Quantité</th>
                                    <th>Sous-total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cart->items as $item)
                                    <tr>
                                        <td>
                                            <div class="flex" style="gap:var(--space-3)">
                                                <img src="{{ $item->book->coverUrl() }}" alt="Couverture de {{ $item->book->title }}" loading="lazy" style="width:48px;height:68px;object-fit:cover;border-radius:var(--radius-sm)">
                                                <div>
                                                    <a href="{{ route('books.show', $item->book->slug) }}" style="font-weight:600">{{ $item->book->title }}</a>
                                                    <p class="text-faint" style="font-size:var(--text-xs)">{{ $item->book->author }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format((float) $item->book->price, 2) }} DT</td>
                                        <td>
                                            <form method="POST" action="{{ route('cart.update', $item) }}">
                                                @csrf
                                                @method('PATCH')
                                                <div class="qty-stepper" data-qty-stepper data-auto-submit>
                                                    <button type="button" data-qty-decrement aria-label="Diminuer">−</button>
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->book->stock_quantity }}">
                                                    <button type="button" data-qty-increment aria-label="Augmenter">+</button>
                                                </div>
                                            </form>
                                        </td>
                                        <td style="font-weight:600">{{ number_format($item->subtotal(), 2) }} DT</td>
                                        <td>
                                            <form method="POST" action="{{ route('cart.destroy', $item) }}" onsubmit="return confirm('Retirer ce livre du panier ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="icon-btn" aria-label="Retirer" style="color:var(--color-danger)">✕</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card" style="max-width:420px;margin-left:auto;width:100%">
                        <h2 style="font-family:var(--font-serif);font-size:var(--text-xl);margin-bottom:var(--space-5)">Récapitulatif</h2>

                        <form method="POST" action="{{ route('cart.coupon.apply') }}" class="flex" style="gap:var(--space-2);margin-bottom:var(--space-5)">
                            @csrf
                            <input type="text" name="code" class="input" placeholder="Code promo" value="{{ $totals['coupon']->code ?? '' }}">
                            <button type="submit" class="btn btn-secondary">Appliquer</button>
                        </form>

                        @if ($totals['coupon'])
                            <div class="flex-between" style="margin-bottom:var(--space-3);font-size:var(--text-sm)">
                                <span class="text-muted">Coupon « {{ $totals['coupon']->code }} »</span>
                                <form method="POST" action="{{ route('cart.coupon.remove') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-primary" style="font-size:var(--text-xs)">Retirer</button>
                                </form>
                            </div>
                        @endif

                        <div class="flex-between" style="margin-bottom:var(--space-2);font-size:var(--text-sm)">
                            <span class="text-muted">Sous-total</span>
                            <span>{{ number_format($totals['subtotal'], 2) }} DT</span>
                        </div>

                        @if ($totals['discount'] > 0)
                            <div class="flex-between" style="margin-bottom:var(--space-2);font-size:var(--text-sm);color:var(--color-success)">
                                <span>Remise</span>
                                <span>−{{ number_format($totals['discount'], 2) }} DT</span>
                            </div>
                        @endif

                        <div class="flex-between" style="margin-bottom:var(--space-4);font-size:var(--text-sm)">
                            <span class="text-muted">Livraison</span>
                            <span class="text-faint">Calculée à l'étape suivante</span>
                        </div>

                        @if ($totals['remaining_for_free_shipping'] > 0 && $totals['free_shipping_threshold'] > 0)
                            <p class="badge badge-gold" style="margin-bottom:var(--space-4)">
                                Plus que {{ number_format($totals['remaining_for_free_shipping'], 2) }} DT pour la livraison gratuite
                            </p>
                        @endif

                        <div class="flex-between" style="padding-top:var(--space-4);border-top:1px solid var(--color-border);font-weight:700;font-size:var(--text-lg);margin-bottom:var(--space-6)">
                            <span>Total</span>
                            <span class="text-primary">{{ number_format($totals['subtotal'] - $totals['discount'], 2) }} DT</span>
                        </div>

                        <a href="{{ route('checkout.address') }}" class="btn btn-primary btn-block btn-lg">Passer la commande</a>
                    </div>
                </div>
            </div>
        @endif
    </div>

@endsection
