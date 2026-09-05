<?php

namespace App\Http\Controllers;

use App\Models\CouponUsage;
use App\Models\Governorate;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    public function showAddress(): View|RedirectResponse
    {
        $cart = $this->cartService->currentCart();

        if ($cart->items()->doesntExist()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        return view('checkout.address', [
            'governorates' => Governorate::active()->orderBy('name')->get(),
            'savedAddresses' => Auth::check() ? Auth::user()->addresses()->with('governorate')->get() : collect(),
            'old' => session('checkout.address', []),
        ]);
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'governorate_id' => ['required', 'exists:governorates,id'],
            'city' => ['required', 'string', 'max:100'],
            'address_line' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'guest_email' => [Auth::guest() ? 'required' : 'nullable', 'nullable', 'email', 'max:191'],
        ], [], [
            'full_name' => 'nom complet',
            'phone' => 'téléphone',
            'governorate_id' => 'gouvernorat',
            'city' => 'ville',
            'address_line' => 'adresse',
            'guest_email' => 'adresse e-mail',
        ]);

        session(['checkout.address' => $data]);

        if (Auth::check() && $request->boolean('save_address')) {
            Auth::user()->addresses()->create($data);
        }

        return redirect()->route('checkout.review');
    }

    public function showReview(): View|RedirectResponse
    {
        $address = session('checkout.address');

        if (! $address) {
            return redirect()->route('checkout.address');
        }

        $cart = $this->cartService->currentCart();

        if ($cart->items()->doesntExist()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $cart->load('items.book');
        $governorate = Governorate::findOrFail($address['governorate_id']);
        $totals = $this->cartService->totals($governorate);

        return view('checkout.review', [
            'address' => $address,
            'cart' => $cart,
            'totals' => $totals,
            'governorate' => $governorate,
        ]);
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $address = session('checkout.address');
        abort_unless($address, 400, "L'adresse de livraison est manquante.");

        $cart = $this->cartService->currentCart();
        $cart->load('items.book');
        abort_if($cart->items->isEmpty(), 400, 'Votre panier est vide.');

        $request->validate(['customer_notes' => ['nullable', 'string', 'max:500']]);

        foreach ($cart->items as $item) {
            if ($item->quantity > $item->book->stock_quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "Stock insuffisant pour \"{$item->book->title}\" — merci d'ajuster la quantité.");
            }
        }

        $governorate = Governorate::findOrFail($address['governorate_id']);
        $totals = $this->cartService->totals($governorate);

        $order = DB::transaction(function () use ($cart, $address, $totals, $request, $governorate) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'guest_email' => Auth::guest() ? ($address['guest_email'] ?? null) : null,
                'status' => 'pending',
                'subtotal' => $totals['subtotal'],
                'discount_amount' => $totals['discount'],
                'shipping_cost' => $totals['shipping'],
                'total' => $totals['total'],
                'coupon_id' => $totals['coupon']?->id,
                'shipping_full_name' => $address['full_name'],
                'shipping_phone' => $address['phone'],
                'shipping_governorate' => $governorate->name,
                'shipping_city' => $address['city'],
                'shipping_address_line' => $address['address_line'],
                'shipping_postal_code' => $address['postal_code'] ?? null,
                'customer_notes' => $request->input('customer_notes'),
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'book_id' => $item->book_id,
                    'book_title_snapshot' => $item->book->title,
                    'book_isbn_snapshot' => $item->book->isbn,
                    'unit_price' => $item->book->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->book->price * $item->quantity,
                ]);

                $item->book->decrement('stock_quantity', $item->quantity);
                $item->book->increment('sales_count', $item->quantity);
            }

            $order->statusHistory()->create([
                'status' => 'pending',
                'note' => 'Commande passée par le client.',
            ]);

            if ($totals['coupon']) {
                CouponUsage::create([
                    'coupon_id' => $totals['coupon']->id,
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                ]);
                $totals['coupon']->increment('used_count');
            }

            $cart->items()->delete();

            return $order;
        });

        session()->forget(['checkout.address', 'cart_coupon_code']);
        session(['last_order_number' => $order->order_number]);

        return redirect()->route('checkout.confirmation', $order->order_number);
    }

    public function showConfirmation(string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)->with('items')->firstOrFail();

        if ($order->user_id) {
            abort_unless(Auth::check() && $order->user_id === Auth::id(), 403);
        } else {
            abort_unless(session('last_order_number') === $orderNumber, 403);
        }

        return view('checkout.confirmation', compact('order'));
    }
}
