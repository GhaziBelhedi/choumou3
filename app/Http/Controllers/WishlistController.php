<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function toggle(Request $request, Book $book): RedirectResponse|JsonResponse
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('book_id', $book->id)->first();

        if ($wishlist) {
            $wishlist->delete();
            $message = 'Retiré de votre liste de souhaits.';
            $isWishlisted = false;
        } else {
            Wishlist::create(['user_id' => Auth::id(), 'book_id' => $book->id]);
            $message = 'Ajouté à votre liste de souhaits.';
            $isWishlisted = true;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_wishlisted' => $isWishlisted,
                'book_id' => $book->id,
            ]);
        }

        return back()->with('success', $message);
    }
}
