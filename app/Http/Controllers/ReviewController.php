<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Book $book): RedirectResponse
    {
        $user = Auth::user();

        if ($book->reviews()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Vous avez déjà donné votre avis sur ce livre.');
        }

        $data = request()->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:150'],
            'comment' => ['required', 'string', 'max:2000'],
        ], [], [
            'rating' => 'note',
            'comment' => 'commentaire',
        ]);

        $isVerifiedPurchase = OrderItem::where('book_id', $book->id)
            ->whereHas('order', fn ($q) => $q->where('user_id', $user->id)->where('status', '!=', 'cancelled'))
            ->exists();

        $book->reviews()->create([
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
