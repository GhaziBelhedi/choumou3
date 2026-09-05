@extends('admin.layouts.admin')

@section('title', 'Utilisateurs')

@section('content')
    <div class="admin-page-header">
        <div><h1>Utilisateurs</h1></div>
    </div>

    <form method="GET" class="admin-toolbar">
        <input type="text" name="q" class="input" placeholder="Nom ou e-mail..." value="{{ request('q') }}">
        <button type="submit" class="btn btn-secondary btn-sm">Rechercher</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Nom</th><th>E-mail</th><th>Rôle</th><th>Commandes</th><th>Inscrit le</th><th></th></tr></thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td style="font-weight:600">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($user->isAdmin())
                                <span class="badge badge-primary">Admin</span>
                            @else
                                <span class="badge badge-neutral">Client</span>
                            @endif
                        </td>
                        <td>{{ $user->orders_count }}</td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td><a href="{{ route('admin.users.show', $user) }}" class="text-primary" style="font-size:var(--text-sm)">Voir</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-faint" style="padding:var(--space-8)">Aucun utilisateur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links('pagination.custom') }}
@endsection
