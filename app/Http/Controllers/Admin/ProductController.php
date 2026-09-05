<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\GeneratesSlugs;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    use GeneratesSlugs;

    public function index(Request $request): View
    {
        $query = Product::query()->with('publisher')->withCount('reviews');

        if ($type = $request->string('type')->toString()) {
            $query->where('type', $type);
        }

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

        $products = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $data['slug'] = $this->generateUniqueSlug(Product::class, $data['title']);

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('products/covers', 'public');
        }

        $product = Product::create($data);
        $product->categories()->sync($request->input('categories', []));

        $this->storeGalleryImages($product, $request);

        return redirect()->route('admin.produits.index')->with('success', "« {$product->title} » a été créé.");
    }

    public function edit(Product $product): View
    {
        $product->load('categories', 'images');

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateData($request, $product->id);

        if ($data['title'] !== $product->title) {
            $data['slug'] = $this->generateUniqueSlug(Product::class, $data['title'], $product->id);
        }

        if ($request->hasFile('cover')) {
            if ($product->cover_path) {
                Storage::disk('public')->delete($product->cover_path);
            }
            $data['cover_path'] = $request->file('cover')->store('products/covers', 'public');
        }

        $product->update($data);
        $product->categories()->sync($request->input('categories', []));

        $this->storeGalleryImages($product, $request);

        return redirect()->route('admin.produits.index')->with('success', "« {$product->title} » a été mis à jour.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->cover_path) {
            Storage::disk('public')->delete($product->cover_path);
        }
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $product->delete();

        return back()->with('success', "« {$product->title} » a été supprimé.");
    }

    public function destroyImage(ProductImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Image supprimée.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateData(Request $request, ?int $ignoreProductId = null): array
    {
        $type = $request->input('type', 'livre');

        $data = $request->validate([
            'type' => ['required', 'in:livre,fourniture'],
            'title' => ['required', 'string', 'max:255'],
            'author' => [$type === 'livre' ? 'required' : 'nullable', 'nullable', 'string', 'max:150'],
            'isbn' => ['nullable', 'string', 'max:20', 'unique:products,isbn,'.($ignoreProductId ?? 'NULL').',id'],
            'description' => ['required', 'string'],
            'language' => ['nullable', 'in:fr,ar,en'],
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

    protected function storeGalleryImages(Product $product, Request $request): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        $startPosition = $product->images()->max('position') + 1;

        foreach ($request->file('gallery') as $i => $file) {
            $path = $file->store('products/gallery', 'public');
            $product->images()->create(['path' => $path, 'position' => $startPosition + $i]);
        }
    }
}
