<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    /**
     * Les 24 gouvernorats tunisiens avec un tarif de livraison indicatif.
     * Ajustable ensuite via le back-office admin.
     */
    public function run(): void
    {
        $grandTunis = ['Tunis', 'Ariana', 'Ben Arous', 'Manouba'];
        $nordEstSahel = ['Nabeul', 'Bizerte', 'Sousse', 'Monastir', 'Mahdia'];

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
            $price = match (true) {
                in_array($name, $grandTunis) => 7.00,
                in_array($name, $nordEstSahel) => 8.00,
                default => 9.50,
            };

            Governorate::updateOrCreate(
                ['name' => $name],
                ['shipping_price' => $price, 'is_active' => true],
            );
        }
    }
}
