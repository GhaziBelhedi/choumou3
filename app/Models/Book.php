<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title', 'slug', 'author', 'isbn', 'description', 'language', 'pages',
    'publisher_id', 'publication_date', 'price', 'compare_at_price',
    'stock_quantity', 'sku', 'cover_path', 'is_featured', 'is_active',
])]
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'book_category');
    }

    public function images(): HasMany
    {
        return $this->hasMany(BookImage::class)->orderBy('position');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true)->latest();
    }

    public function wishlistedBy(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Les routes {book} (wishlist, avis...) résolvent le modèle par slug, pas par id,
     * pour rester cohérent avec les URLs du catalogue (/livres/{slug}).
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isOnSale(): bool
    {
        return ! is_null($this->compare_at_price) && (float) $this->compare_at_price > (float) $this->price;
    }

    public function discountPercent(): ?int
    {
        if (! $this->isOnSale()) {
            return null;
        }

        return (int) round((1 - ($this->price / $this->compare_at_price)) * 100);
    }

    /**
     * Recalcule average_rating / reviews_count à partir des avis approuvés.
     * Appelé quand un avis est approuvé/rejeté/supprimé (back-office, Phase 5).
     */
    public function recalculateRatingStats(): void
    {
        $stats = $this->approvedReviews()->selectRaw('AVG(rating) as avg_rating, COUNT(*) as cnt')->first();

        // average_rating/reviews_count sont volontairement absents du Fillable (ce sont des
        // statistiques calculées, pas des champs éditables par formulaire) — forceFill() les
        // met à jour sans les exposer au mass-assignment classique.
        $this->forceFill([
            'average_rating' => round((float) ($stats->avg_rating ?? 0), 2),
            'reviews_count' => (int) ($stats->cnt ?? 0),
        ])->save();
    }

    public function coverUrl(): string
    {
        return $this->cover_path
            ? asset('storage/'.$this->cover_path)
            : asset('assets/images/book-placeholder.svg');
    }
}
