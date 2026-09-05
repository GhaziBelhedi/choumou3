<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Governorate;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartService
{
    /**
     * Retourne le panier actuel (invité via session, ou utilisateur connecté)
     * sans le créer en base s'il n'existe pas encore.
     */
    public function peekCart(): ?Cart
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->first();
        }

        $token = session('cart_token');

        return $token ? Cart::where('session_id', $token)->first() : null;
    }

    /**
     * Retourne le panier actuel, en le créant si besoin.
     */
    public function currentCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        $token = session('cart_token');

        if (! $token) {
            $token = (string) Str::uuid();
            session(['cart_token' => $token]);
        }

        return Cart::firstOrCreate(['session_id' => $token]);
    }

    public function addItem(Product $product, int $quantity = 1): CartItem
    {
        $cart = $this->currentCart();

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = min($product->stock_quantity, ($item->quantity ?? 0) + $quantity);
        $item->save();

        return $item;
    }

    public function updateItem(CartItem $item, int $quantity): void
    {
        $item->update(['quantity' => min($quantity, $item->product->stock_quantity)]);
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    /**
     * Fusionne le panier invité (session) dans le panier de l'utilisateur qui vient de se connecter.
     */
    public function mergeGuestCartIntoUser(User $user): void
    {
        $token = session('cart_token');

        if (! $token) {
            return;
        }

        $guestCart = Cart::where('session_id', $token)->first();

        if (! $guestCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()->where('product_id', $guestItem->product_id)->first();

            if ($existing) {
                $existing->update([
                    'quantity' => min($existing->quantity + $guestItem->quantity, $guestItem->product->stock_quantity),
                ]);
            } else {
                $userCart->items()->create([
                    'product_id' => $guestItem->product_id,
                    'quantity' => $guestItem->quantity,
                ]);
            }
        }

        $guestCart->delete();
        session()->forget('cart_token');
    }

    public function applyCoupon(string $code): array
    {
        $coupon = Coupon::where('code', $code)->first();
        $subtotal = $this->currentCart()->subtotal();

        if (! $coupon || ! $coupon->isCurrentlyValid($subtotal, Auth::id())) {
            return ['success' => false, 'message' => "Ce code promo n'est pas valide ou plus disponible."];
        }

        session(['cart_coupon_code' => $coupon->code]);

        return ['success' => true, 'message' => 'Code promo appliqué avec succès.'];
    }

    public function removeCoupon(): void
    {
        session()->forget('cart_coupon_code');
    }

    public function getAppliedCoupon(): ?Coupon
    {
        $code = session('cart_coupon_code');

        return $code ? Coupon::where('code', $code)->first() : null;
    }

    /**
     * Calcule les totaux du panier courant : sous-total, remise, livraison, total.
     */
    public function totals(?Governorate $governorate = null): array
    {
        $cart = $this->currentCart();
        $subtotal = $cart->subtotal();

        $coupon = $this->getAppliedCoupon();
        $discount = ($coupon && $coupon->isCurrentlyValid($subtotal, Auth::id()))
            ? $coupon->calculateDiscount($subtotal)
            : 0.0;

        $freeThreshold = (float) Setting::get('free_shipping_threshold', 0);
        $flatShippingPrice = (float) Setting::get('flat_shipping_price', 0);
        $shipping = 0.0;

        // $governorate n'est plus utilisé pour le tarif (unique pour toute la Tunisie) — sa
        // présence indique simplement qu'on est à l'étape où les frais doivent être calculés.
        if ($governorate) {
            $shipping = ($freeThreshold > 0 && $subtotal >= $freeThreshold)
                ? 0.0
                : $flatShippingPrice;
        }

        $total = max($subtotal - $discount + $shipping, 0);

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'total' => $total,
            'coupon' => $coupon,
            'free_shipping_threshold' => $freeThreshold,
            'remaining_for_free_shipping' => max($freeThreshold - $subtotal, 0),
        ];
    }
}
