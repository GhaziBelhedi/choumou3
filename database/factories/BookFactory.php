<?php

namespace Database\Factories;

use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookFactory extends Factory
{
    public function definition(): array
    {
        $title = ucfirst(fake()->words(random_int(2, 5), true));
        $price = fake()->randomFloat(2, 12, 65);
        $onSale = fake()->boolean(25);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'author' => fake()->name(),
            'isbn' => fake()->unique()->isbn13(),
            'description' => implode("\n\n", fake()->paragraphs(3)),
            'language' => fake()->randomElement(['fr', 'ar', 'en']),
            'pages' => fake()->numberBetween(96, 512),
            'publisher_id' => Publisher::query()->inRandomOrder()->value('id'),
            'publication_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'price' => $price,
            'compare_at_price' => $onSale ? round($price * 1.25, 2) : null,
            'stock_quantity' => fake()->numberBetween(0, 40),
            'sku' => strtoupper(Str::random(8)),
            'is_featured' => fake()->boolean(15),
            'is_active' => true,
            'sales_count' => fake()->numberBetween(0, 200),
            'average_rating' => fake()->randomFloat(2, 2.5, 5),
            'reviews_count' => fake()->numberBetween(0, 40),
        ];
    }
}
