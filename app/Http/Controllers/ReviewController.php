<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Product $product): RedirectResponse
    {
        $user = Auth::user();

        if ($product->reviews()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Vous avez déjà donné votre avis sur ce produit.');
        }

        $data = request()->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:150'],
            'comment' => ['required', 'string', 'max:2000'],
        ], [], [
            'rating' => 'note',
            'comment' => 'commentaire',
        ]);

        $isVerifiedPurchase = OrderItem::where('product_id', $product->id)
            ->whereHas('order', fn ($q) => $q->where('user_id', $user->id)->where('status', '!=', 'cancelled'))
            ->exists();

        $product->reviews()->create([
            'user_id' => $user->id,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'comment' => $data['comment'],
            'is_verified_purchase' => $isVerifiedPurchase,
            'is_approved' => false,
        ]);

        return back()->with('success', 'Merci ! Votre avis a été envoyé et sera visible après validation.');
    }
}
