@extends('layouts.app')

@section('title', 'Mes commandes — Choumou3')

@section('content')
    <div class="page-header">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('home') }}">Accueil</a>
                <span>/</span>
                <span>Mes commandes</span>
            </nav>
            <h1>Mes commandes</h1>
        </div>
    </div>

    <div class="container section">
        @if ($orders->isEmpty())
            <div class="empty-state">
                <h3>Aucune commande pour l'instant</h3>
                <p style="margin-bottom:var(--space-6)">Vos commandes apparaîtront ici une fois passées.</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary">Découvrir le catalogue</a>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Commande</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td style="font-weight:600">{{ $order->order_number }}</td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                <td><span class="badge badge-primary">{{ $order->statusLabel() }}</span></td>
                                <td>{{ number_format((float) $order->total, 2) }} DT</td>
                                <td><a href="{{ route('orders.show', $order) }}" class="text-primary" style="font-size:var(--text-sm)">Détails</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $orders->links('pagination.custom') }}
        @endif
    </div>
@endsection
