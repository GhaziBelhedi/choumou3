<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'type', 'title', 'slug', 'author', 'isbn', 'description', 'language', 'pages',
    'publication_date', 'price', 'compare_at_price',
    'stock_quantity', 'sku', 'cover_path', 'is_featured', 'is_active', 'deal_ends_at',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    public const TYPES = [
        'livre' => 'Livre',
        'fourniture' => 'Fourniture scolaire',
    ];

    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'deal_ends_at' => 'datetime',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
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

    public function scopeBooks($query)
    {
        return $query->where('type', 'livre');
    }

    public function scopeSupplies($query)
    {
        return $query->where('type', 'fourniture');
    }

    /**
     * Produits actuellement "en deal" (mise en avant avec compte à rebours), triés
     * par échéance la plus proche — le premier résultat est LE deal du jour affiché en home.
     */
    public function scopeOnDeal($query)
    {
        return $query->whereNotNull('deal_ends_at')->where('deal_ends_at', '>', now())->orderBy('deal_ends_at');
    }

    public function isDealActive(): bool
    {
        return $this->deal_ends_at !== null && $this->deal_ends_at->isFuture();
    }

    public function isBook(): bool
    {
        return $this->type === 'livre';
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * % de stock déjà écoulé pour la barre de progression du deal du jour,
     * basé sur des données réelles (sales_count / stock_quantity), pas fictives.
     */
    public function dealProgressPercent(): int
    {
        $total = $this->sales_count + $this->stock_quantity;

        if ($total <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->sales_count / $total) * 100));
    }

    /**
     * Les routes {product} (wishlist, avis...) résolvent le modèle par slug, pas par id,
     * pour rester cohérent avec les URLs du catalogue (/produits/{slug}).
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
     * Appelé quand un avis est approuvé/rejeté/supprimé (back-office).
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
            : asset('assets/images/product-placeholder.svg');
    }
}
