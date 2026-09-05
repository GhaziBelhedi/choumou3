<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            GovernorateSeeder::class,
            SettingSeeder::class,
            CategorySeeder::class,
            PublisherSeeder::class,
            ProductSeeder::class,
        ]);

        User::factory()->admin()->create([
            'name' => 'Admin Choumou3',
            'email' => 'admin@choumou3.tn',
        ]);

        User::factory()->create([
            'name' => 'Client Test',
            'email' => 'client@choumou3.tn',
        ]);
    }
}
