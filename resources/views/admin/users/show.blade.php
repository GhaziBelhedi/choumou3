@extends('admin.layouts.admin')

@section('title', $user->name)

@section('content')
    <div class="admin-page-header">
        <div><h1>{{ $user->name }}</h1><p>{{ $user->email }} @if($user->phone) · {{ $user->phone }} @endif</p></div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">← Retour</a>
    </div>

    <div class="card" style="margin-bottom:var(--space-6);max-width:480px">
        <div class="flex-between">
            <div>
                <p class="text-faint" style="font-size:var(--text-xs)">Rôle actuel</p>
                <p style="font-weight:600">{{ $user->isAdmin() ? 'Administrateur' : 'Client' }}</p>
            </div>
            @if ($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" onsubmit="return confirm('Changer le rôle de cet utilisateur ?')">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm">
                        {{ $user->isAdmin() ? 'Retirer les droits admin' : 'Promouvoir administrateur' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="card">
        <h2 style="font-family:var(--font-serif);font-size:var(--text-lg);margin-bottom:var(--space-4)">Dernières commandes</h2>
        @if ($user->orders->isEmpty())
            <p class="text-faint" style="font-size:var(--text-sm)">Aucune commande.</p>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Commande</th><th>Date</th><th>Statut</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach ($user->orders as $order)
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
@endsection
