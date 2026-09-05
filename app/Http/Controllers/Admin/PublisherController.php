<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\GeneratesSlugs;
use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublisherController extends Controller
{
    use GeneratesSlugs;

    public function index(): View
    {
        $publishers = Publisher::withCount('books')->orderBy('name')->paginate(20);

        return view('admin.publishers.index', compact('publishers'));
    }

    public function create(): View
    {
        return view('admin.publishers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:150']], [], ['name' => 'nom']);
        $data['slug'] = $this->generateUniqueSlug(Publisher::class, $data['name']);

        Publisher::create($data);

        return redirect()->route('admin.editeurs.index')->with('success', 'Éditeur créé.');
    }

    public function edit(Publisher $publisher): View
    {
        return view('admin.publishers.edit', compact('publisher'));
    }

    public function update(Request $request, Publisher $publisher): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:150']], [], ['name' => 'nom']);

        if ($data['name'] !== $publisher->name) {
            $data['slug'] = $this->generateUniqueSlug(Publisher::class, $data['name'], $publisher->id);
        }

        $publisher->update($data);

        return redirect()->route('admin.editeurs.index')->with('success', 'Éditeur mis à jour.');
    }

    public function destroy(Publisher $publisher): RedirectResponse
    {
        if ($publisher->books()->exists()) {
            return back()->with('error', 'Impossible de supprimer : des livres sont encore rattachés à cet éditeur.');
        }

        $publisher->delete();

        return back()->with('success', 'Éditeur supprimé.');
    }
}
