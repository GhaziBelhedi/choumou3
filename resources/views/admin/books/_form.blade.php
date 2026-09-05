@php
    $book = $book ?? null;
@endphp

<div class="form-grid form-grid-2">
    <div class="field">
        <label class="field__label" for="title">Titre</label>
        <input class="input @error('title') has-error @enderror" type="text" id="title" name="title" value="{{ old('title', $book->title ?? '') }}" required>
        @error('title') <span class="field__error">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label class="field__label" for="author">Auteur</label>
        <input class="input @error('author') has-error @enderror" type="text" id="author" name="author" value="{{ old('author', $book->author ?? '') }}" required>
        @error('author') <span class="field__error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="field">
    <label class="field__label" for="description">Description</label>
    <textarea class="textarea @error('description') has-error @enderror" id="description" name="description" rows="5" required>{{ old('description', $book->description ?? '') }}</textarea>
    @error('description') <span class="field__error">{{ $message }}</span> @enderror
</div>

<div class="form-grid form-grid-2">
    <div class="field">
        <label class="field__label" for="isbn">ISBN <span class="text-faint">(optionnel)</span></label>
        <input class="input @error('isbn') has-error @enderror" type="text" id="isbn" name="isbn" value="{{ old('isbn', $book->isbn ?? '') }}">
        @error('isbn') <span class="field__error">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label class="field__label" for="sku">SKU <span class="text-faint">(optionnel)</span></label>
        <input class="input" type="text" id="sku" name="sku" value="{{ old('sku', $book->sku ?? '') }}">
    </div>

    <div class="field">
        <label class="field__label" for="language">Langue</label>
        <select class="select" id="language" name="language" required>
            @foreach (['fr' => 'Français', 'ar' => 'Arabe', 'en' => 'Anglais'] as $value => $label)
                <option value="{{ $value }}" @selected(old('language', $book->language ?? 'fr') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label class="field__label" for="pages">Nombre de pages</label>
        <input class="input" type="number" id="pages" name="pages" min="1" value="{{ old('pages', $book->pages ?? '') }}">
    </div>

    <div class="field">
        <label class="field__label" for="publisher_id">Éditeur</label>
        <select class="select" id="publisher_id" name="publisher_id">
            <option value="">— Aucun —</option>
            @foreach ($publishers as $publisher)
                <option value="{{ $publisher->id }}" @selected(old('publisher_id', $book->publisher_id ?? null) == $publisher->id)>{{ $publisher->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label class="field__label" for="publication_date">Date de publication</label>
        <input class="input" type="date" id="publication_date" name="publication_date" value="{{ old('publication_date', optional($book?->publication_date)->format('Y-m-d')) }}">
    </div>
</div>

<div class="form-grid form-grid-2">
    <div class="field">
        <label class="field__label" for="price">Prix (DT)</label>
        <input class="input @error('price') has-error @enderror" type="number" step="0.01" min="0" id="price" name="price" value="{{ old('price', $book->price ?? '') }}" required>
        @error('price') <span class="field__error">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label class="field__label" for="compare_at_price">Prix barré (DT) <span class="text-faint">(promo, optionnel)</span></label>
        <input class="input @error('compare_at_price') has-error @enderror" type="number" step="0.01" min="0" id="compare_at_price" name="compare_at_price" value="{{ old('compare_at_price', $book->compare_at_price ?? '') }}">
        @error('compare_at_price') <span class="field__error">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label class="field__label" for="stock_quantity">Stock disponible</label>
        <input class="input @error('stock_quantity') has-error @enderror" type="number" min="0" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $book->stock_quantity ?? 0) }}" required>
        @error('stock_quantity') <span class="field__error">{{ $message }}</span> @enderror
    </div>

    <div class="field" style="justify-content:center">
        <label class="checkbox-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $book->is_active ?? true))> Actif (visible sur le site)</label>
        <label class="checkbox-row"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $book->is_featured ?? false))> Mettre en avant (coup de cœur)</label>
    </div>

    <div class="field">
        <label class="field__label" for="deal_ends_at">Deal du jour — mis en avant jusqu'au <span class="text-faint">(optionnel)</span></label>
        <input class="input @error('deal_ends_at') has-error @enderror" type="datetime-local" id="deal_ends_at" name="deal_ends_at" value="{{ old('deal_ends_at', optional($book?->deal_ends_at)->format('Y-m-d\TH:i')) }}">
        <span class="field__hint">Si renseigné et dans le futur, ce livre peut apparaître comme « Deal du jour » sur la home (le plus proche de l'échéance est affiché).</span>
        @error('deal_ends_at') <span class="field__error">{{ $message }}</span> @enderror
    </div>
</div>

<div class="field">
    <label class="field__label">Catégories</label>
    <div class="flex" style="gap:var(--space-4);flex-wrap:wrap;margin-top:var(--space-2)">
        @php $bookCategoryIds = old('categories', ($book?->categories->pluck('id')->all()) ?? []); @endphp
        @foreach ($categories as $category)
            <label class="checkbox-row">
                <input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array($category->id, $bookCategoryIds))>
                {{ $category->name }}
            </label>
        @endforeach
    </div>
</div>

<div class="form-grid form-grid-2">
    <div class="field">
        <label class="field__label">Couverture</label>
        @if (! empty($book?->cover_path))
            <img src="{{ $book->coverUrl() }}" alt="Couverture actuelle de {{ $book->title }}" style="width:80px;height:112px;object-fit:cover;border-radius:var(--radius-sm);margin-bottom:var(--space-2)">
        @endif
        <label for="cover" class="image-dropzone">
            <span class="text-muted" style="font-size:var(--text-sm)">Cliquer pour choisir une image de couverture</span>
            <input type="file" id="cover" name="cover" accept="image/*" style="display:none" data-image-input="cover-preview">
        </label>
        <div id="cover-preview" class="image-preview-grid"></div>
    </div>

    <div class="field">
        <label class="field__label">Galerie d'images</label>
        <label for="gallery" class="image-dropzone">
            <span class="text-muted" style="font-size:var(--text-sm)">Ajouter des images supplémentaires (plusieurs possibles)</span>
            <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple style="display:none" data-image-input="gallery-preview">
        </label>
        <div id="gallery-preview" class="image-preview-grid"></div>

        @if ($book && $book->images->isNotEmpty())
            <p class="field__hint" style="margin-top:var(--space-3)">Images actuelles :</p>
            <div class="image-preview-grid">
                @foreach ($book->images as $image)
                    <div style="position:relative">
                        <img src="{{ $image->url() }}" alt="Image de la galerie" loading="lazy">
                        <form method="POST" action="{{ route('admin.livres.images.destroy', $image) }}" style="position:absolute;top:2px;right:2px" onsubmit="return confirm('Supprimer cette image ?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:var(--color-white);border-radius:var(--radius-full);width:22px;height:22px;font-size:12px">✕</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
