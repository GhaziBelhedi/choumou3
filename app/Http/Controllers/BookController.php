<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersBooks;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    use FiltersBooks;

    public function index(Request $request): View
    {
        $query = Book::query()->active();

        $this->applyFilters($query, $request);
        $this->applySort($query, $request->string('tri', 'nouveautes')->toString());

        $books = $query->paginate(24)->withQueryString();

        return view('books.index', [
            'books' => $books,
            'categories' => Category::active()->orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
            'currentCategory' => null,
        ]);
    }

    public function show(string $slug): View
    {
        $book = Book::active()
            ->with(['publisher', 'categories', 'images', 'approvedReviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $categoryIds = $book->categories->pluck('id');

        $similarBooks = Book::active()
            ->where('id', '!=', $book->id)
            ->when($categoryIds->isNotEmpty(), fn ($q) => $q->whereHas(
                'categories',
                fn ($q2) => $q2->whereIn('categories.id', $categoryIds)
            ))
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('books.show', [
            'book' => $book,
            'similarBooks' => $similarBooks,
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

        $books = empty($ids)
            ? collect()
            : Book::active()->whereIn('id', $ids)->get()->sortBy(fn ($book) => array_search($book->id, $ids))->values();

        return view('books.partials.recently-viewed-cards', compact('books'));
    }
}
