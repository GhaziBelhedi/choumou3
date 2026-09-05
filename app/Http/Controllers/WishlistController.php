<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
        $products = Auth::user()->wishlists()
            ->with('product')
            ->latest()
            ->get()
            ->pluck('product')
            ->filter();

        return view('wishlist.index', compact('products'));
    }

    public function toggle(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->first();

        if ($wishlist) {
            $wishlist->delete();
            $message = 'Retiré de votre liste de souhaits.';
            $isWishlisted = false;
        } else {
            Wishlist::create(['user_id' => Auth::id(), 'product_id' => $product->id]);
            $message = 'Ajouté à votre liste de souhaits.';
            $isWishlisted = true;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_wishlisted' => $isWishlisted,
                'product_id' => $product->id,
            ]);
        }

        return back()->with('success', $message);
    }
}
