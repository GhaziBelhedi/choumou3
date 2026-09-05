@extends('admin.layouts.admin')

@section('title', 'Livres')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1>Livres</h1>
            <p>{{ $books->total() }} livre(s) au catalogue</p>
        </div>
        <a href="{{ route('admin.livres.create') }}" class="btn btn-primary">+ Ajouter un livre</a>
    </div>

    <form method="GET" class="admin-toolbar">
        <input type="text" name="q" class="input" placeholder="Titre, auteur, ISBN..." value="{{ request('q') }}">
        <select name="stock" class="select" onchange="this.form.submit()">
            <option value="">Tout le stock</option>
            <option value="faible" @selected(request('stock') === 'faible')>Stock faible</option>
            <option value="rupture" @selected(request('stock') === 'rupture')>Rupture</option>
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Filtrer</button>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Éditeur</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Avis</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($books as $book)
                    <tr>
                        <td><img src="{{ $book->coverUrl() }}" alt="" style="width:36px;height:50px;object-fit:cover;border-radius:var(--radius-sm)"></td>
                        <td style="font-weight:600">{{ $book->title }}</td>
                        <td>{{ $book->author }}</td>
                        <td>{{ $book->publisher->name ?? '—' }}</td>
                        <td>{{ number_format((float) $book->price, 2) }} DT</td>
                        <td>
                            @if ($book->stock_quantity == 0)
                                <span class="badge badge-danger">Rupture</span>
                            @elseif ($book->stock_quantity <= 5)
                                <span class="badge badge-warning">{{ $book->stock_quantity }}</span>
                            @else
                                {{ $book->stock_quantity }}
                            @endif
                        </td>
                        <td>{{ $book->reviews_count }}</td>
                        <td>
                            @if ($book->is_active)
                                <span class="badge badge-success">Actif</span>
                            @else
                                <span class="badge badge-neutral">Masqué</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex" style="gap:var(--space-2)">
                                <a href="{{ route('admin.livres.edit', $book) }}" class="text-primary" style="font-size:var(--text-sm)">Modifier</a>
                                <form method="POST" action="{{ route('admin.livres.destroy', $book) }}" onsubmit="return confirm('Supprimer ce livre ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="font-size:var(--text-sm);color:var(--color-danger)">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-faint" style="padding:var(--space-8)">Aucun livre.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $books->links('pagination.custom') }}
@endsection
