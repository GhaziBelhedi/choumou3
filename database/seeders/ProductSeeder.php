<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $bookCategoryIds = Category::query()->whereNull('parent_id')->where('slug', '!=', 'fournitures-scolaires')->pluck('id');
        $supplyCategoryId = Category::query()->where('slug', 'fournitures-scolaires')->value('id');

        Product::factory()
            ->count(36)
            ->create()
            ->each(function (Product $product) use ($bookCategoryIds) {
                $product->categories()->attach(
                    $bookCategoryIds->random(random_int(1, 2))->all()
                );
            });

        Product::factory()
            ->supply()
            ->count(20)
            ->create()
            ->each(function (Product $product) use ($supplyCategoryId) {
                if ($supplyCategoryId) {
                    $product->categories()->attach([$supplyCategoryId]);
                }
            });
    }
}
