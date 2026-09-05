@extends('admin.layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1>Tableau de bord</h1>
            <p>Vue d'ensemble de l'activité de la librairie</p>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <p class="stat-card__label">Chiffre d'affaires (ce mois)</p>
            <p class="stat-card__value">{{ number_format((float) $revenueThisMonth, 2) }} DT</p>
        </div>
        <div class="stat-card">
            <p class="stat-card__label">Commandes (ce mois)</p>
            <p class="stat-card__value">{{ $ordersThisMonth }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-card__label">Commandes en attente</p>
            <p class="stat-card__value {{ $pendingOrders > 0 ? 'is-warning' : '' }}">{{ $pendingOrders }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-card__label">Avis à modérer</p>
            <p class="stat-card__value {{ $pendingReviews > 0 ? 'is-warning' : '' }}">{{ $pendingReviews }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-card__label">Stock faible (≤ 5)</p>
            <p class="stat-card__value {{ $lowStockBooks > 0 ? 'is-warning' : '' }}">{{ $lowStockBooks }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-card__label">Ruptures de stock</p>
            <p class="stat-card__value {{ $outOfStockBooks > 0 ? 'is-warning' : '' }}">{{ $outOfStockBooks }}</p>
        </div>
    </div>

    <div class="grid grid-2" style="grid-template-columns:1.3fr 1fr;align-items:start">
        <div class="card">
            <div class="flex-between" style="margin-bottom:var(--space-4)">
                <h2 style="font-family:var(--font-serif);font-size:var(--text-lg)">Commandes récentes</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-primary" style="font-size:var(--text-sm)">Tout voir</a>
            </div>
            @if ($recentOrders->isEmpty())
                <p class="text-faint" style="font-size:var(--text-sm)">Aucune commande pour l'instant.</p>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Commande</th><th>Date</th><th>Statut</th><th>Total</th></tr></thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td><span class="badge badge-primary">{{ $order->statusLabel() }}</span></td>
                                    <td>{{ number_format((float) $order->total, 2) }} DT</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card">
            <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-4)">Meilleures ventes</h2>
            @foreach ($topBooks as $book)
                <div class="flex-between" style="padding-block:var(--space-2);border-bottom:1px solid var(--color-border);font-size:var(--text-sm)">
                    <span>{{ $book->title }}</span>
                    <span class="text-faint">{{ $book->sales_count }} vendus</span>
                </div>
            @endforeach
        </div>
    </div>
@endsection
