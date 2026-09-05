@extends('admin.layouts.admin')

@section('title', 'Éditeurs')

@section('content')
    <div class="admin-page-header">
        <div><h1>Éditeurs</h1></div>
        <a href="{{ route('admin.editeurs.create') }}" class="btn btn-primary">+ Ajouter un éditeur</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Nom</th><th>Livres</th><th></th></tr></thead>
            <tbody>
                @forelse ($publishers as $publisher)
                    <tr>
                        <td style="font-weight:600">{{ $publisher->name }}</td>
                        <td>{{ $publisher->books_count }}</td>
                        <td>
                            <div class="flex" style="gap:var(--space-2)">
                                <a href="{{ route('admin.editeurs.edit', $publisher) }}" class="text-primary" style="font-size:var(--text-sm)">Modifier</a>
                                <form method="POST" action="{{ route('admin.editeurs.destroy', $publisher) }}" onsubmit="return confirm('Supprimer cet éditeur ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="font-size:var(--text-sm);color:var(--color-danger)">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-faint" style="padding:var(--space-8)">Aucun éditeur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $publishers->links('pagination.custom') }}
@endsection
