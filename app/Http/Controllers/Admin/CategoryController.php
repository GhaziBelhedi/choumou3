<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\GeneratesSlugs;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use GeneratesSlugs;

    public function index(): View
    {
        $categories = Category::withCount('products')->with('parent')->orderBy('name')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create', ['parents' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = $this->generateUniqueSlug(Category::class, $data['name']);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie créée.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
            'parents' => Category::where('id', '!=', $category->id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validateData($request, $category->id);

        if ($data['name'] !== $category->name) {
            $data['slug'] = $this->generateUniqueSlug(Category::class, $data['name'], $category->id);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Impossible de supprimer : des produits sont encore rattachés à cette catégorie.');
        }

        $category->delete();

        return back()->with('success', 'Catégorie supprimée.');
    }

    protected function validateData(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'parent_id' => array_filter([
                'nullable', 'exists:categories,id', $ignoreId ? 'not_in:'.$ignoreId : null,
            ]),
            'description' => ['nullable', 'string'],
        ], [], ['name' => 'nom', 'parent_id' => 'catégorie parente']);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
