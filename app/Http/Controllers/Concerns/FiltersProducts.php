<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait FiltersProducts
{
    /**
     * Applique les filtres (type, catégorie, langue, prix, recherche) à la requête.
     */
    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($q = $request->string('q')->trim()->toString()) {
            $query->whereFullText(['title', 'author', 'description'], $q);
        }

        if ($type = $request->string('type')->toString()) {
            $query->where('type', $type);
        }

        if ($categorySlug = $request->string('categorie')->toString()) {
            $query->whereHas('categories', fn ($q2) => $q2->where('slug', $categorySlug));
        }

        if ($language = $request->string('langue')->toString()) {
            $query->where('language', $language);
        }

        if ($request->filled('prix_min')) {
            $query->where('price', '>=', (float) $request->input('prix_min'));
        }

        if ($request->filled('prix_max')) {
            $query->where('price', '<=', (float) $request->input('prix_max'));
        }
    }

    /**
     * Applique le tri sélectionné à la requête.
     */
    protected function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'prix-asc' => $query->orderBy('price', 'asc'),
            'prix-desc' => $query->orderBy('price', 'desc'),
            'popularite' => $query->orderByDesc('average_rating')->orderByDesc('reviews_count'),
            'meilleures-ventes' => $query->orderByDesc('sales_count'),
            default => $query->orderByDesc('created_at'),
        };
    }
}
