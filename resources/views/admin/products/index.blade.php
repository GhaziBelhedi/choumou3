@extends('admin.layouts.admin')

@section('title', 'Produits')

@section('content')
    <div class="admin-page-header">
        <div>
            <h1>Produits</h1>
            <p>{{ $products->total() }} produit(s) au catalogue</p>
        </div>
        <a href="{{ route('admin.produits.create') }}" class="btn btn-primary">+ Ajouter un produit</a>
    </div>

    <form method="GET" class="admin-toolbar">
        <input type="text" name="q" class="input" placeholder="Titre, auteur, ISBN..." value="{{ request('q') }}">
        <select name="type" class="select" onchange="this.form.submit()">
            <option value="">Tous les types</option>
            @foreach (\App\Models\Product::TYPES as $value => $label)
                <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
            @endforeach
        </select>
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
                    <th>Type</th>
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
                @forelse ($products as $product)
                    <tr>
                        <td><img src="{{ $product->coverUrl() }}" alt="Couverture de {{ $product->title }}" loading="lazy" style="width:36px;height:50px;object-fit:cover;border-radius:var(--radius-sm)"></td>
                        <td style="font-weight:600">{{ $product->title }}</td>
                        <td><span class="badge {{ $product->isBook() ? 'badge-primary' : 'badge-info' }}">{{ $product->typeLabel() }}</span></td>
                        <td>{{ $product->author ?? '—' }}</td>
                        <td>{{ $product->publisher->name ?? '—' }}</td>
                        <td>{{ number_format((float) $product->price, 2) }} DT</td>
                        <td>
                            @if ($product->stock_quantity == 0)
                                <span class="badge badge-danger">Rupture</span>
                            @elseif ($product->stock_quantity <= 5)
                                <span class="badge badge-warning">{{ $product->stock_quantity }}</span>
                            @else
                                {{ $product->stock_quantity }}
                            @endif
                        </td>
                        <td>{{ $product->reviews_count }}</td>
                        <td>
                            @if ($product->is_active)
                                <span class="badge badge-success">Actif</span>
                            @else
                                <span class="badge badge-neutral">Masqué</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex" style="gap:var(--space-2)">
                                <a href="{{ route('admin.produits.edit', $product) }}" class="text-primary" style="font-size:var(--text-sm)">Modifier</a>
                                <form method="POST" action="{{ route('admin.produits.destroy', $product) }}" onsubmit="return confirm('Supprimer ce produit ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="font-size:var(--text-sm);color:var(--color-danger)">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-faint" style="padding:var(--space-8)">Aucun produit.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $products->links('pagination.custom') }}
@endsection
