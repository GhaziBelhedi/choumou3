<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    /**
     * Les 24 gouvernorats tunisiens. Le tarif de livraison est unique pour
     * toute la Tunisie (réglable dans Paramètres), pas par gouvernorat.
     */
    public function run(): void
    {
        $governorates = [
            'Tunis', 'Ariana', 'Ben Arous', 'Manouba',
            'Nabeul', 'Zaghouan', 'Bizerte',
            'Béja', 'Jendouba', 'Kef', 'Siliana',
            'Sousse', 'Monastir', 'Mahdia',
            'Sfax', 'Kairouan', 'Kasserine', 'Sidi Bouzid',
            'Gabès', 'Médenine', 'Tataouine',
            'Gafsa', 'Tozeur', 'Kébili',
        ];

        foreach ($governorates as $name) {
            Governorate::updateOrCreate(
                ['name' => $name],
                ['is_active' => true],
            );
        }
    }
}
