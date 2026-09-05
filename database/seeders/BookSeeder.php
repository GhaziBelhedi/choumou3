<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::query()->pluck('id');

        Book::factory()
            ->count(36)
            ->create()
            ->each(function (Book $book) use ($categoryIds) {
                $book->categories()->attach(
                    $categoryIds->random(random_int(1, 2))->all()
                );
            });
    }
}
