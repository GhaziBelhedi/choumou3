<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\GeneratesSlugs;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookImage;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BookController extends Controller
{
    use GeneratesSlugs;

    public function index(Request $request): View
    {
        $query = Book::query()->with('publisher')->withCount('reviews');

        if ($q = $request->string('q')->toString()) {
            $query->where(function ($q2) use ($q) {
                $q2->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%")
                    ->orWhere('isbn', 'like', "%{$q}%");
            });
        }

        if ($stock = $request->string('stock')->toString()) {
            match ($stock) {
                'rupture' => $query->where('stock_quantity', 0),
                'faible' => $query->whereBetween('stock_quantity', [1, 5]),
                default => null,
            };
        }

        $books = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.books.index', compact('books'));
    }

    public function create(): View
    {
        return view('admin.books.create', [
            'categories' => Category::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $data['slug'] = $this->generateUniqueSlug(Book::class, $data['title']);

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('books/covers', 'public');
        }

        $book = Book::create($data);
        $book->categories()->sync($request->input('categories', []));

        $this->storeGalleryImages($book, $request);

        return redirect()->route('admin.livres.index')->with('success', "Le livre « {$book->title} » a été créé.");
    }

    public function edit(Book $book): View
    {
        $book->load('categories', 'images');

        return view('admin.books.edit', [
            'book' => $book,
            'categories' => Category::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $this->validateData($request, $book->id);

        if ($data['title'] !== $book->title) {
            $data['slug'] = $this->generateUniqueSlug(Book::class, $data['title'], $book->id);
        }

        if ($request->hasFile('cover')) {
            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }
            $data['cover_path'] = $request->file('cover')->store('books/covers', 'public');
        }

        $book->update($data);
        $book->categories()->sync($request->input('categories', []));

        $this->storeGalleryImages($book, $request);

        return redirect()->route('admin.livres.index')->with('success', "Le livre « {$book->title} » a été mis à jour.");
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
        }
        foreach ($book->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $book->delete();

        return back()->with('success', "Le livre « {$book->title} » a été supprimé.");
    }

    public function destroyImage(BookImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Image supprimée.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateData(Request $request, ?int $ignoreBookId = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:150'],
            'isbn' => ['nullable', 'string', 'max:20', 'unique:books,isbn,'.($ignoreBookId ?? 'NULL').',id'],
            'description' => ['required', 'string'],
            'language' => ['required', 'in:fr,ar,en'],
            'pages' => ['nullable', 'integer', 'min:1'],
            'publisher_id' => ['nullable', 'exists:publishers,id'],
            'publication_date' => ['nullable', 'date'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', 'gt:price'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:60'],
            'deal_ends_at' => ['nullable', 'date', 'after:now'],
            'cover' => ['nullable', 'image', 'max:4096'],
            'gallery.*' => ['nullable', 'image', 'max:4096'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
        ], [], [
            'title' => 'titre',
            'author' => 'auteur',
            'price' => 'prix',
            'stock_quantity' => 'stock',
            'publisher_id' => 'éditeur',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);

        unset($data['cover'], $data['gallery'], $data['categories']);

        return $data;
    }

    protected function storeGalleryImages(Book $book, Request $request): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        $startPosition = $book->images()->max('position') + 1;

        foreach ($request->file('gallery') as $i => $file) {
            $path = $file->store('books/gallery', 'public');
            $book->images()->create(['path' => $path, 'position' => $startPosition + $i]);
        }
    }
}
