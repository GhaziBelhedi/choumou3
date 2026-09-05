@extends('admin.layouts.admin')

@section('title', 'Catégories')

@section('content')
    <div class="admin-page-header">
        <div><h1>Catégories</h1></div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ Ajouter une catégorie</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Nom</th><th>Parente</th><th>Livres</th><th>Statut</th><th></th></tr></thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td style="font-weight:600">{{ $category->name }}</td>
                        <td>{{ $category->parent->name ?? '—' }}</td>
                        <td>{{ $category->books_count }}</td>
                        <td>
                            @if ($category->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-neutral">Masquée</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex" style="gap:var(--space-2)">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-primary" style="font-size:var(--text-sm)">Modifier</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Supprimer cette catégorie ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="font-size:var(--text-sm);color:var(--color-danger)">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-faint" style="padding:var(--space-8)">Aucune catégorie.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $categories->links('pagination.custom') }}
@endsection
