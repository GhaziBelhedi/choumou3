<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersBooks;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use FiltersBooks;

    public function index(): View
    {
        $categories = Category::active()
            ->whereNull('parent_id')
            ->withCount(['books' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $category = Category::active()->where('slug', $slug)->firstOrFail();

        $query = Book::query()->active()
            ->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id));

        $this->applyFilters($query, $request);
        $this->applySort($query, $request->string('tri', 'nouveautes')->toString());

        $books = $query->paginate(24)->withQueryString();

        return view('books.index', [
            'books' => $books,
            'categories' => Category::active()->orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
            'currentCategory' => $category,
        ]);
    }
}
