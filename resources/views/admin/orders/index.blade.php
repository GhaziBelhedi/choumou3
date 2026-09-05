@extends('admin.layouts.admin')

@section('title', 'Commandes')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1>Commandes</h1>
            <p>{{ $orders->total() }} commande(s)</p>
        </div>
    </div>

    <form method="GET" class="admin-toolbar">
        <input type="text" name="q" class="input" placeholder="N° commande, nom, téléphone..." value="{{ request('q') }}">
        <select name="status" class="select" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Commande</th><th>Client</th><th>Date</th><th>Articles</th><th>Total</th><th>Statut</th><th></th></tr></thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td style="font-weight:600">{{ $order->order_number }}</td>
                        <td>{{ $order->shipping_full_name }} @if($order->isGuestOrder())<span class="badge badge-neutral" style="margin-left:4px">Invité</span>@endif</td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $order->items_count }}</td>
                        <td>{{ number_format((float) $order->total, 2) }} DT</td>
                        <td><span class="badge badge-primary">{{ $order->statusLabel() }}</span></td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="text-primary" style="font-size:var(--text-sm)">Voir</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-faint" style="padding:var(--space-8)">Aucune commande.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $orders->links('pagination.custom') }}
@endsection
