<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersProducts;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    use FiltersProducts;

    public function index(Request $request): View
    {
        $query = Product::query()->active();

        $this->applyFilters($query, $request);
        $this->applySort($query, $request->string('tri', 'nouveautes')->toString());

        $products = $query->paginate(24)->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::active()->orderBy('name')->get(),
            'currentCategory' => null,
        ]);
    }

    /**
     * Alias pratique /livres — catalogue pré-filtré sur les livres.
     */
    public function books(Request $request): View
    {
        $request->merge(['type' => 'livre']);

        return $this->index($request);
    }

    /**
     * Alias pratique /fournitures-scolaires — catalogue pré-filtré sur les fournitures.
     */
    public function supplies(Request $request): View
    {
        $request->merge(['type' => 'fourniture']);

        return $this->index($request);
    }

    public function show(string $slug): View
    {
        $product = Product::active()
            ->with(['categories', 'images', 'approvedReviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $categoryIds = $product->categories->pluck('id');

        $similarProducts = Product::active()
            ->where('id', '!=', $product->id)
            ->where('type', $product->type)
            ->when($categoryIds->isNotEmpty(), fn ($q) => $q->whereHas(
                'categories',
                fn ($q2) => $q2->whereIn('categories.id', $categoryIds)
            ))
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('products.show', [
            'product' => $product,
            'similarProducts' => $similarProducts,
        ]);
    }

    /**
     * Endpoint AJAX pour la section "Consultés récemment" de la home — les IDs
     * viennent du localStorage du navigateur (tracking client, pas de session serveur).
     */
    public function recentlyViewed(Request $request): View
    {
        $ids = array_filter(array_map('intval', explode(',', (string) $request->query('ids'))));
        $ids = array_slice($ids, 0, 8);

        $products = empty($ids)
            ? collect()
            : Product::active()->whereIn('id', $ids)->get()->sortBy(fn ($p) => array_search($p->id, $ids))->values();

        return view('products.partials.recently-viewed-cards', compact('products'));
    }
}
