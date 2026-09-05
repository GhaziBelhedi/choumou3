<?php

namespace Database\Seeders;

use App\Models\Publisher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PublisherSeeder extends Seeder
{
    public function run(): void
    {
        $publishers = [
            'Cérès Éditions',
            'Sud Éditions',
            'Éditions Nirvana',
            'Éditions Alyssa',
            'Dar El Janoub',
            'Éditions Contrastes',
        ];

        foreach ($publishers as $name) {
            Publisher::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
        }
    }
}
