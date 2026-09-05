@extends('layouts.app')

@section('title', 'Notifications — Choumou3')

@section('content')
    <div class="page-header">
        <div class="container">
            <nav class="breadcrumb">
                <a href="{{ route('home') }}">Accueil</a>
                <span>/</span>
                <span>Notifications</span>
            </nav>
            <h1>Notifications</h1>
        </div>
    </div>

    <div class="container section" style="max-width:640px">
        @if ($notifications->isEmpty())
            <div class="empty-state">
                <h3>Aucune notification</h3>
                <p>Vous serez notifié ici pour le suivi de vos commandes et vos avis.</p>
            </div>
        @else
            @foreach ($notifications as $notif)
                <div class="card" style="margin-bottom:var(--space-3)">
                    @if ($notif->data['type'] === 'order_status_updated')
                        <p style="font-size:var(--text-sm)">
                            Commande <strong>{{ $notif->data['order_number'] }}</strong> :
                            statut mis à jour vers « {{ $notif->data['status_label'] }} »
                        </p>
                    @elseif ($notif->data['type'] === 'review_approved')
                        <p style="font-size:var(--text-sm)">
                            Votre avis sur « <strong>{{ $notif->data['book_title'] }}</strong> » a été publié.
                        </p>
                    @endif
                    <p class="text-faint" style="font-size:var(--text-xs);margin-top:var(--space-1)">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
            @endforeach

            {{ $notifications->links('pagination.custom') }}
        @endif
    </div>
@endsection
