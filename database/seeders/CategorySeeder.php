<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Romans',
            'Jeunesse',
            'Scolaire & Parascolaire',
            'Sciences & Essais',
            'Bandes dessinées & Mangas',
            'Développement personnel',
            'Religion & Spiritualité',
            'Livres en arabe',
            'Cuisine',
            'Histoire & Géographie',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true],
            );
        }
    }
}
