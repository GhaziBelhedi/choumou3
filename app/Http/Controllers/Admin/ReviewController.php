<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Notifications\ReviewApproved;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status', 'pending')->toString();

        $query = Review::query()->with(['product', 'user']);

        match ($status) {
            'approved' => $query->where('is_approved', true),
            'pending' => $query->where('is_approved', false),
            default => null,
        };

        $reviews = $query->latest()->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'status'));
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => true]);
        $review->product->recalculateRatingStats();
        $review->user->notify(new ReviewApproved($review));

        return back()->with('success', 'Avis approuvé et publié.');
    }

    public function reject(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => false]);
        $review->product->recalculateRatingStats();

        return back()->with('success', 'Avis rejeté (masqué du site).');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $product = $review->product;
        $review->delete();
        $product->recalculateRatingStats();

        return back()->with('success', 'Avis supprimé.');
    }
}
