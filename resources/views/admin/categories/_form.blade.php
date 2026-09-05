@php $category = $category ?? null; @endphp

<div class="field">
    <label class="field__label" for="name">Nom</label>
    <input class="input @error('name') has-error @enderror" type="text" id="name" name="name" value="{{ old('name', $category->name ?? '') }}" required>
    @error('name') <span class="field__error">{{ $message }}</span> @enderror
</div>

<div class="field">
    <label class="field__label" for="parent_id">Catégorie parente <span class="text-faint">(optionnel)</span></label>
    <select class="select" id="parent_id" name="parent_id">
        <option value="">— Aucune (catégorie principale) —</option>
        @foreach ($parents as $parent)
            <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id ?? null) == $parent->id)>{{ $parent->name }}</option>
        @endforeach
    </select>
</div>

<div class="field">
    <label class="field__label" for="description">Description <span class="text-faint">(optionnel)</span></label>
    <textarea class="textarea" id="description" name="description" rows="3">{{ old('description', $category->description ?? '') }}</textarea>
</div>

<label class="checkbox-row" style="margin-bottom:var(--space-4)">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))>
    Active (visible sur le site)
</label>
