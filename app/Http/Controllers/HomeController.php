<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::active()->whereNull('parent_id')->withCount('products')->orderBy('name')->get();

        $newProducts = Product::active()->latest()->limit(8)->get();

        $onSaleProducts = Product::active()
            ->whereNotNull('compare_at_price')
            ->whereColumn('compare_at_price', '>', 'price')
            ->latest()
            ->limit(8)
            ->get();

        $topRatedProducts = Product::active()
            ->where('reviews_count', '>', 0)
            ->orderByDesc('average_rating')
            ->orderByDesc('reviews_count')
            ->limit(8)
            ->get();

        // Meilleures ventes, regroupées par les premières catégories actives
        $bestsellersByCategory = $categories->take(4)->mapWithKeys(function (Category $category) {
            return [
                $category->id => [
                    'category' => $category,
                    'products' => Product::active()
                        ->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id))
                        ->orderByDesc('sales_count')
                        ->limit(8)
                        ->get(),
                ],
            ];
        })->filter(fn ($entry) => $entry['products']->isNotEmpty());

        $dealProduct = Product::onDeal()->first();

        return view('home', [
            'categories' => $categories,
            'categoryTiles' => $categories->take(4),
            'newProducts' => $newProducts,
            'onSaleProducts' => $onSaleProducts,
            'topRatedProducts' => $topRatedProducts,
            'bestsellersByCategory' => $bestsellersByCategory,
            'dealProduct' => $dealProduct,
        ]);
    }
}
