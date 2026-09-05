@extends('admin.layouts.admin')

@section('title', 'Tableau de bord')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-dashboard.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/vendor/chart.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/admin-charts.js') }}"></script>
    <script>
        window.dashboardData = {
            revenueByDay: @json($revenueByDay),
            ordersByStatus: @json($ordersByStatus),
            revenueByType: {
                livre: {{ $revenueByType['livre'] ?? 0 }},
                fourniture: {{ $revenueByType['fourniture'] ?? 0 }}
            }
        };
    </script>
@endpush

@section('content')
    <div class="admin-page-header">
        <div>
            <h1>Tableau de bord</h1>
            <p>Vue d'ensemble de l'activité de la librairie</p>
        </div>
        <div class="flex" style="gap:var(--space-2)">
            <a href="{{ route('admin.produits.create') }}" class="btn btn-primary">+ Ajouter un produit</a>
        </div>
    </div>

    {{-- ---------- Quick tools ---------- --}}
    <div class="quick-tools">
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="quick-tool">
            <span class="quick-tool__icon quick-tool__icon--red">📦</span>
            <span>
                <strong>{{ $pendingOrders }}</strong>
                <small>Commandes en attente</small>
            </span>
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="quick-tool">
            <span class="quick-tool__icon quick-tool__icon--gold">⭐</span>
            <span>
                <strong>{{ $pendingReviews }}</strong>
                <small>Avis à modérer</small>
            </span>
        </a>
        <a href="{{ route('admin.produits.index', ['stock' => 'faible']) }}" class="quick-tool">
            <span class="quick-tool__icon quick-tool__icon--teal">📉</span>
            <span>
                <strong>{{ $lowStockProducts }}</strong>
                <small>Stock faible</small>
            </span>
        </a>
        <a href="{{ route('admin.produits.index', ['stock' => 'rupture']) }}" class="quick-tool">
            <span class="quick-tool__icon quick-tool__icon--ink">🚫</span>
            <span>
                <strong>{{ $outOfStockProducts }}</strong>
                <small>Ruptures de stock</small>
            </span>
        </a>
    </div>

    {{-- ---------- Stat cards ---------- --}}
    <div class="stat-grid">
        <div class="stat-card stat-card--accent">
            <p class="stat-card__label">Chiffre d'affaires (ce mois)</p>
            <p class="stat-card__value">{{ number_format((float) $revenueThisMonth, 2) }} DT</p>
            @if ($revenueTrend !== 0)
                <span class="stat-card__trend {{ $revenueTrend > 0 ? 'is-up' : 'is-down' }}">
                    {{ $revenueTrend > 0 ? '↑' : '↓' }} {{ abs($revenueTrend) }}% vs mois dernier
                </span>
            @endif
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
    </div>

    {{-- ---------- Graphiques ---------- --}}
    <div class="chart-layout">
        <div class="card chart-card">
            <div class="flex-between" style="margin-bottom:var(--space-4)">
                <h2 class="chart-card__title">Chiffre d'affaires — 14 derniers jours</h2>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="card chart-card">
            <h2 class="chart-card__title" style="margin-bottom:var(--space-4)">Commandes par statut</h2>
            <div class="chart-container chart-container--donut">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-layout chart-layout--reverse">
        <div class="card chart-card">
            <h2 class="chart-card__title" style="margin-bottom:var(--space-4)">Livres vs Fournitures (CA)</h2>
            <div class="chart-container chart-container--donut">
                <canvas id="typeChart"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="flex-between" style="margin-bottom:var(--space-4)">
                <h2 class="chart-card__title">Commandes récentes</h2>
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
    </div>

    <div class="card">
        <h2 class="chart-card__title" style="margin-bottom:var(--space-4)">Meilleures ventes</h2>
        @foreach ($topProducts as $product)
            <div class="flex-between" style="padding-block:var(--space-2);border-bottom:1px solid var(--color-border);font-size:var(--text-sm)">
                <span class="flex" style="gap:var(--space-2)">
                    <span class="badge {{ $product->type === 'livre' ? 'badge-primary' : 'badge-info' }}" style="font-size:10px">{{ $product->type === 'livre' ? 'Livre' : 'Fourniture' }}</span>
                    {{ $product->title }}
                </span>
                <span class="text-faint">{{ $product->sales_count }} vendus</span>
            </div>
        @endforeach
    </div>
@endsection
