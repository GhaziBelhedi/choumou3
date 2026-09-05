<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
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
        $cart->load('items.product');
        $totals = $this->cartService->totals();

        return view('cart.index', [
            'cart' => $cart,
            'totals' => $totals,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::active()->findOrFail($data['product_id']);

        if ($product->stock_quantity < 1) {
            return $this->respond($request, false, 'Ce produit est actuellement en rupture de stock.');
        }

        $this->cartService->addItem($product, $data['quantity'] ?? 1);

        return $this->respond($request, true, "« {$product->title} » a été ajouté au panier.");
    }

    public function update(Request $request, CartItem $item): RedirectResponse|JsonResponse
    {
        $this->authorizeItem($item);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $this->cartService->updateItem($item, $data['quantity']);
        $item->refresh();

        return $this->respond($request, true, 'Panier mis à jour.', [
            'item' => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'subtotal' => number_format($item->subtotal(), 2),
            ],
        ]);
    }

    public function destroy(Request $request, CartItem $item): RedirectResponse|JsonResponse
    {
        $this->authorizeItem($item);

        $this->cartService->removeItem($item);

        return $this->respond($request, true, 'Article retiré du panier.', ['item_id' => $item->id]);
    }

    public function applyCoupon(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate(['code' => ['required', 'string']]);

        $result = $this->cartService->applyCoupon($data['code']);

        return $this->respond($request, $result['success'], $result['message']);
    }

    public function removeCoupon(Request $request): RedirectResponse|JsonResponse
    {
        $this->cartService->removeCoupon();

        return $this->respond($request, true, 'Code promo retiré.');
    }

    protected function authorizeItem(CartItem $item): void
    {
        $cart = $this->cartService->currentCart();

        abort_unless($item->cart_id === $cart->id, 403);
    }

    /**
     * Répond en JSON (requêtes AJAX) avec le panier à jour, ou redirige en HTML
     * (fallback classique sans JS).
     */
    protected function respond(Request $request, bool $success, string $message, array $extra = []): RedirectResponse|JsonResponse
    {
        if ($request->wantsJson()) {
            $cart = $this->cartService->currentCart();
            $totals = $this->cartService->totals();

            return response()->json(array_merge([
                'success' => $success,
                'message' => $message,
                'cart_count' => $cart->itemsCount(),
                'totals' => [
                    'subtotal' => number_format($totals['subtotal'], 2),
                    'discount' => number_format($totals['discount'], 2),
                    'total' => number_format($totals['subtotal'] - $totals['discount'], 2),
                ],
            ], $extra), $success ? 200 : 422);
        }

        return back()->with($success ? 'success' : 'error', $message);
    }
}
