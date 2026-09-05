@extends('layouts.app')

@section('title', 'Récapitulatif de commande — Choumou3')

@section('content')
    <div class="container section">
        <div class="checkout-steps">
            <div class="checkout-step is-done"><span class="checkout-step__circle">✓</span> Adresse</div>
            <span class="checkout-step__sep"></span>
            <div class="checkout-step is-active"><span class="checkout-step__circle">2</span> Récapitulatif</div>
            <span class="checkout-step__sep"></span>
            <div class="checkout-step"><span class="checkout-step__circle">3</span> Confirmation</div>
        </div>

        <div class="checkout-layout">
            <div>
                <div class="card" style="margin-bottom:var(--space-5)">
                    <div class="flex-between" style="margin-bottom:var(--space-4)">
                        <h2 style="font-family:var(--font-serif);font-size:var(--text-lg)">Adresse de livraison</h2>
                        <a href="{{ route('checkout.address') }}" class="text-primary" style="font-size:var(--text-sm)">Modifier</a>
                    </div>
                    <p style="font-size:var(--text-sm);line-height:1.8">
                        {{ $address['full_name'] }} — {{ $address['phone'] }}<br>
                        {{ $address['address_line'] }}, {{ $address['city'] }}<br>
                        {{ $governorate->name }} {{ $address['postal_code'] ?? '' }}
                    </p>
                </div>

                <div class="card">
                    <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-4)">Articles ({{ $cart->items->sum('quantity') }})</h2>
                    @foreach ($cart->items as $item)
                        <div class="flex-between" style="padding-block:var(--space-3);border-bottom:1px solid var(--color-border)">
                            <div class="flex" style="gap:var(--space-3)">
                                <img src="{{ $item->book->coverUrl() }}" alt="Couverture de {{ $item->book->title }}" loading="lazy" style="width:44px;height:62px;object-fit:cover;border-radius:var(--radius-sm)">
                                <div>
                                    <p style="font-weight:600;font-size:var(--text-sm)">{{ $item->book->title }}</p>
                                    <p class="text-faint" style="font-size:var(--text-xs)">Qté : {{ $item->quantity }}</p>
                                </div>
                            </div>
                            <span style="font-weight:600;font-size:var(--text-sm)">{{ number_format($item->subtotal(), 2) }} DT</span>
                        </div>
                    @endforeach

                    <form method="POST" action="{{ route('checkout.place') }}" style="margin-top:var(--space-5)">
                        @csrf
                        <div class="field">
                            <label class="field__label" for="customer_notes">Notes pour la livraison <span class="text-faint">(optionnel)</span></label>
                            <textarea class="textarea" id="customer_notes" name="customer_notes" rows="2" placeholder="Étage, point de repère, horaires préférés..."></textarea>
                        </div>

                        <div class="alert alert-info" style="margin-top:var(--space-4)">
                            💵 Paiement à la livraison — vous payez en espèces à la réception de votre colis.
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:var(--space-2)">
                            Confirmer la commande
                        </button>
                    </form>
                </div>
            </div>

            <div class="card" style="position:sticky;top:calc(var(--header-height) + var(--space-4))">
                <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-5)">Total</h2>

                <div class="flex-between" style="margin-bottom:var(--space-2);font-size:var(--text-sm)">
                    <span class="text-muted">Sous-total</span>
                    <span>{{ number_format($totals['subtotal'], 2) }} DT</span>
                </div>

                @if ($totals['discount'] > 0)
                    <div class="flex-between" style="margin-bottom:var(--space-2);font-size:var(--text-sm);color:var(--color-success)">
                        <span>Remise ({{ $totals['coupon']->code }})</span>
                        <span>−{{ number_format($totals['discount'], 2) }} DT</span>
                    </div>
                @endif

                <div class="flex-between" style="margin-bottom:var(--space-4);font-size:var(--text-sm)">
                    <span class="text-muted">Livraison ({{ $governorate->name }})</span>
                    <span>{{ $totals['shipping'] > 0 ? number_format($totals['shipping'], 2).' DT' : 'Gratuite' }}</span>
                </div>

                <div class="flex-between" style="padding-top:var(--space-4);border-top:1px solid var(--color-border);font-weight:700;font-size:var(--text-xl)">
                    <span>Total</span>
                    <span class="text-primary">{{ number_format($totals['total'], 2) }} DT</span>
                </div>
            </div>
        </div>
    </div>
@endsection
