<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    public function index(): View
    {
        $cart = $this->cartService->currentCart();
        $cart->load('items.book');
        $totals = $this->cartService->totals();

        return view('cart.index', [
            'cart' => $cart,
            'totals' => $totals,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $book = Book::active()->findOrFail($data['book_id']);

        if ($book->stock_quantity < 1) {
            return back()->with('error', 'Ce livre est actuellement en rupture de stock.');
        }

        $this->cartService->addItem($book, $data['quantity'] ?? 1);

        return back()->with('success', 'Livre ajouté au panier.');
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $this->authorizeItem($item);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $this->cartService->updateItem($item, $data['quantity']);

        return back()->with('success', 'Panier mis à jour.');
    }

    public function destroy(CartItem $item): RedirectResponse
    {
        $this->authorizeItem($item);

        $this->cartService->removeItem($item);

        return back()->with('success', 'Article retiré du panier.');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'string']]);

        $result = $this->cartService->applyCoupon($data['code']);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function removeCoupon(): RedirectResponse
    {
        $this->cartService->removeCoupon();

        return back()->with('success', 'Code promo retiré.');
    }

    protected function authorizeItem(CartItem $item): void
    {
        $cart = $this->cartService->currentCart();

        abort_unless($item->cart_id === $cart->id, 403);
    }
}
