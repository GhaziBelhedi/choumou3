<form method="GET" action="{{ url()->current() }}" data-filters-form>

    <div class="filter-group">
        <label class="field__label" for="filter-q">Recherche</label>
        <input
            class="input"
            style="margin-top:var(--space-2)"
            type="search"
            id="filter-q"
            name="q"
            value="{{ request('q') }}"
            placeholder="Titre, auteur..."
        >
    </div>

    <div class="filter-group">
        <p class="filter-group__title">Type de produit</p>
        <label class="filter-option">
            <input type="radio" name="type" value="" @checked(! request('type'))>
            Tout
        </label>
        @foreach (\App\Models\Product::TYPES as $value => $label)
            <label class="filter-option">
                <input type="radio" name="type" value="{{ $value }}" @checked(request('type') === $value)>
                {{ $label }}
            </label>
        @endforeach
    </div>

    <div class="filter-group">
        <p class="filter-group__title">Catégorie</p>
        <label class="filter-option">
            <input type="radio" name="categorie" value="" @checked(! request('categorie'))>
            Toutes les catégories
        </label>
        @foreach ($categories as $category)
            <label class="filter-option">
                <input type="radio" name="categorie" value="{{ $category->slug }}" @checked(request('categorie') === $category->slug)>
                {{ $category->name }}
            </label>
        @endforeach
    </div>

    <div class="filter-group">
        <p class="filter-group__title">Langue</p>
        <label class="filter-option">
            <input type="radio" name="langue" value="" @checked(! request('langue'))>
            Toutes les langues
        </label>
        @foreach (['fr' => 'Français', 'ar' => 'Arabe', 'en' => 'Anglais'] as $value => $label)
            <label class="filter-option">
                <input type="radio" name="langue" value="{{ $value }}" @checked(request('langue') === $value)>
                {{ $label }}
            </label>
        @endforeach
    </div>

    <div class="filter-group">
        <p class="filter-group__title">Prix (DT)</p>
        <div class="filter-price-row">
            <input class="input" type="number" min="0" step="0.5" name="prix_min" value="{{ request('prix_min') }}" placeholder="Min">
            <span class="text-faint">—</span>
            <input class="input" type="number" min="0" step="0.5" name="prix_max" value="{{ request('prix_max') }}" placeholder="Max">
        </div>
    </div>

    <div style="padding-top:var(--space-5);display:flex;flex-direction:column;gap:var(--space-2)">
        <button type="submit" class="btn btn-primary btn-block">Appliquer les filtres</button>
        <a href="{{ url()->current() }}" class="btn btn-ghost btn-block" style="text-align:center">Réinitialiser</a>
    </div>
</form>
