@props(['book'])

@php
    static $wishlistedIds = null;

    if (auth()->check()) {
        if ($wishlistedIds === null) {
            $wishlistedIds = auth()->user()->wishlists()->pluck('book_id')->all();
        }
        $isWishlisted = in_array($book->id, $wishlistedIds, true);
    } else {
        $isWishlisted = false;
    }
@endphp

<article class="product-card" data-book-id="{{ $book->id }}">
    <div class="product-card__thumb-wrap">
        <a href="{{ route('books.show', $book->slug) }}" class="product-card__thumb">
            <img src="{{ $book->coverUrl() }}" alt="Couverture de {{ $book->title }}" loading="lazy">
        </a>

        @if ($book->isOnSale())
            <span class="badge badge-primary product-card__badge">-{{ $book->discountPercent() }}%</span>
        @elseif ($book->is_featured)
            <span class="badge badge-gold product-card__badge">Coup de cœur</span>
        @endif

        @auth
            <form method="POST" action="{{ route('wishlist.toggle', $book) }}" class="product-card__wishlist-form" data-wishlist-form>
                @csrf
                <button
                    type="submit"
                    class="icon-btn product-card__wishlist {{ $isWishlisted ? 'is-active' : '' }}"
                    aria-label="{{ $isWishlisted ? 'Retirer de la liste de souhaits' : 'Ajouter à la liste de souhaits' }}"
                    data-wishlist-btn
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-4.5-9.5-9C.8 8.4 2.4 5 6 5c2 0 3.4 1.1 4 2.2C10.6 6.1 12 5 14 5c3.6 0 5.2 3.4 3.5 7-2.5 4.5-9.5 9-9.5 9z"/></svg>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="icon-btn product-card__wishlist" aria-label="Se connecter pour ajouter à la liste de souhaits">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-4.5-9.5-9C.8 8.4 2.4 5 6 5c2 0 3.4 1.1 4 2.2C10.6 6.1 12 5 14 5c3.6 0 5.2 3.4 3.5 7-2.5 4.5-9.5 9-9.5 9z"/></svg>
            </a>
        @endauth
    </div>

    <div class="product-card__body">
        <p class="text-faint" style="font-size:var(--text-xs);margin-bottom:var(--space-1)">{{ $book->author }}</p>
        <h3 class="product-card__title">
            <a href="{{ route('books.show', $book->slug) }}">{{ $book->title }}</a>
        </h3>

        <x-product.rating-stars :rating="$book->average_rating" :count="$book->reviews_count" />

        <div class="flex-between" style="margin-top:var(--space-3)">
            <x-product.price-tag :book="$book" size="sm" />

            @if ($book->stock_quantity > 0)
                <form method="POST" action="{{ route('cart.store') }}" data-add-to-cart-form>
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="product-card__add-btn" aria-label="Ajouter « {{ $book->title }} » au panier">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.5 3h2l2.4 12.4a2 2 0 0 0 2 1.6h8.2a2 2 0 0 0 2-1.6L21 7H6"/></svg>
                    </button>
                </form>
            @endif
        </div>
    </div>
</article>
