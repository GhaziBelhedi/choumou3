@extends('admin.layouts.admin')

@section('title', 'Commande '.$order->order_number)

@section('content')
    <div class="admin-page-header">
        <div>
            <h1>Commande {{ $order->order_number }}</h1>
            <p>Passée le {{ $order->created_at->format('d/m/Y à H:i') }} @if($order->isGuestOrder()) — client invité @endif</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">← Retour</a>
    </div>

    <div class="checkout-layout">
        <div>
            <div class="card" style="margin-bottom:var(--space-5)">
                <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-4)">Articles</h2>
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Livre</th><th>Prix</th><th>Qté</th><th>Sous-total</th></tr></thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->book_title_snapshot }}</td>
                                    <td>{{ number_format((float) $item->unit_price, 2) }} DT</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format((float) $item->subtotal, 2) }} DT</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:var(--space-4)">
                    <div class="flex-between" style="font-size:var(--text-sm)"><span class="text-muted">Sous-total</span><span>{{ number_format((float) $order->subtotal, 2) }} DT</span></div>
                    @if ($order->discount_amount > 0)
                        <div class="flex-between" style="font-size:var(--text-sm);color:var(--color-success)"><span>Remise @if($order->coupon)({{ $order->coupon->code }})@endif</span><span>−{{ number_format((float) $order->discount_amount, 2) }} DT</span></div>
                    @endif
                    <div class="flex-between" style="font-size:var(--text-sm)"><span class="text-muted">Livraison</span><span>{{ number_format((float) $order->shipping_cost, 2) }} DT</span></div>
                    <div class="flex-between" style="font-weight:700;font-size:var(--text-lg);padding-top:var(--space-2)"><span>Total</span><span class="text-primary">{{ number_format((float) $order->total, 2) }} DT</span></div>
                </div>
            </div>

            <div class="card" style="margin-bottom:var(--space-5)">
                <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-4)">Client &amp; livraison</h2>
                <p style="font-size:var(--text-sm);line-height:1.8">
                    <strong>{{ $order->shipping_full_name }}</strong> — {{ $order->shipping_phone }}<br>
                    @if ($order->user) {{ $order->user->email }} @else {{ $order->guest_email }} @endif<br>
                    {{ $order->shipping_address_line }}, {{ $order->shipping_city }}<br>
                    {{ $order->shipping_governorate }} {{ $order->shipping_postal_code }}
                </p>
                @if ($order->customer_notes)
                    <p class="text-faint" style="font-size:var(--text-xs);margin-top:var(--space-3)">Note client : {{ $order->customer_notes }}</p>
                @endif
            </div>

            <div class="card">
                <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-4)">Mettre à jour le statut</h2>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                    @csrf
                    <div class="form-grid form-grid-2">
                        <div class="field">
                            <label class="field__label" for="status">Nouveau statut</label>
                            <select class="select" id="status" name="status" required onchange="document.getElementById('cancel-reason-field').style.display = this.value === 'cancelled' ? 'block' : 'none'">
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label class="field__label" for="note">Note interne <span class="text-faint">(optionnel)</span></label>
                            <input class="input" type="text" id="note" name="note">
                        </div>
                    </div>
                    <div class="field" id="cancel-reason-field" style="display:{{ $order->status === 'cancelled' ? 'block' : 'none' }}">
                        <label class="field__label" for="cancellation_reason">Motif d'annulation</label>
                        <input class="input" type="text" id="cancellation_reason" name="cancellation_reason" value="{{ $order->cancellation_reason }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </form>
            </div>
        </div>

        <div class="card">
            <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-5)">Historique</h2>
            <x-order-status-timeline :order="$order" />
        </div>
    </div>
@endsection
