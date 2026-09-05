@extends('admin.layouts.admin')

@section('title', 'Modération des avis')

@section('content')
    <div class="admin-page-header">
        <div><h1>Avis clients</h1></div>
    </div>

    <div class="tab-pills">
        <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="tab-pill {{ $status === 'pending' ? 'is-active' : '' }}">En attente</a>
        <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="tab-pill {{ $status === 'approved' ? 'is-active' : '' }}">Approuvés</a>
        <a href="{{ route('admin.reviews.index', ['status' => 'all']) }}" class="tab-pill {{ $status === 'all' ? 'is-active' : '' }}">Tous</a>
    </div>

    @if ($reviews->isEmpty())
        <div class="empty-state"><h3>Aucun avis ici</h3></div>
    @else
        @foreach ($reviews as $review)
            <div class="card" style="margin-bottom:var(--space-4)">
                <div class="flex-between" style="margin-bottom:var(--space-2)">
                    <div>
                        <a href="{{ route('products.show', $review->product->slug) }}" target="_blank" style="font-weight:600">{{ $review->product->title }}</a>
                        <p class="text-faint" style="font-size:var(--text-xs)">par {{ $review->user->name }} · {{ $review->created_at->format('d/m/Y') }} @if($review->is_verified_purchase) · <span class="text-primary">Achat vérifié</span> @endif</p>
                    </div>
                    <x-product.rating-stars :rating="$review->rating" :size="16" />
                </div>

                @if ($review->title)
                    <p style="font-weight:600;margin-bottom:var(--space-1)">{{ $review->title }}</p>
                @endif
                <p class="text-muted" style="font-size:var(--text-sm);margin-bottom:var(--space-4)">{{ $review->comment }}</p>

                <div class="flex" style="gap:var(--space-3)">
                    @if (! $review->is_approved)
                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">Approuver</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">Masquer</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Supprimer définitivement cet avis ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--color-danger)">Supprimer</button>
                    </form>
                </div>
            </div>
        @endforeach

        {{ $reviews->links('pagination.custom') }}
    @endif
@endsection
