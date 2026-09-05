<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $books = Auth::user()->wishlists()
            ->with('book')
            ->latest()
            ->get()
            ->pluck('book')
            ->filter();

        return view('wishlist.index', compact('books'));
    }

    public function toggle(Book $book): RedirectResponse
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('book_id', $book->id)->first();

        if ($wishlist) {
            $wishlist->delete();
            $message = 'Retiré de votre liste de souhaits.';
        } else {
            Wishlist::create(['user_id' => Auth::id(), 'book_id' => $book->id]);
            $message = 'Ajouté à votre liste de souhaits.';
        }

        return back()->with('success', $message);
    }
}
