<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Titres réalistes (pas de Lorem Ipsum) pour un catalogue de démo crédible.
     */
    protected array $bookTitles = [
        'Les Chemins de la Liberté', "L'Ombre du Baobab", 'Le Dernier Voyage', 'Sous le Ciel de Carthage',
        'Les Secrets de la Médina', 'Le Jardin des Mots', 'Contes et Légendes de Tunisie', "L'Art de Vivre Simplement",
        'Le Silence des Vagues', "Mémoires d'un Voyageur", 'La Rose du Désert', 'Les Enfants de la Lune',
        'Petit Guide de Sagesse', "L'Héritage Oublié", "Le Fil d'Ariane", 'Histoires du Soir',
        'Le Passager Discret', 'Sur les Traces du Passé', "L'Éveil des Sens", 'Le Grand Atlas',
        "Chroniques d'une Ville", "Les Couleurs de l'Automne", "Le Voyage Intérieur", 'Petites Histoires du Maghreb',
        "L'Étoile du Sud", 'Le Cahier Rouge', 'Rêves de Papier', 'La Symphonie des Saisons',
        'Les Portes du Savoir', 'Un Été à Sidi Bou Saïd', 'La Traversée', 'Les Gardiens de la Mémoire',
    ];

    protected array $authors = [
        'Amira Ben Salah', 'Karim Trabelsi', 'Sophie Lambert', 'Mohamed Gharbi', 'Nadia Cherif',
        'Yasmine Bouazizi', 'Antoine Dupuis', 'Leïla Mansour', 'Fares Jaziri', 'Claire Moreau',
        'Sami Belhadj', 'Ines Khaldi', 'Julien Roche', 'Hana Ferjani', 'Walid Souissi',
        'Marie Fontaine', 'Rania Zaidi', 'Omar Chaabane', 'Camille Girard', 'Tarek Ammar',
    ];

    protected array $supplyNames = [
        'Cahier 100 pages grands carreaux', 'Cahier de brouillon 96 pages', 'Stylo bille bleu (lot de 4)',
        'Stylo plume classique', 'Crayon à papier HB (lot de 6)', 'Taille-crayon avec réservoir',
        'Gomme blanche', 'Trousse scolaire', 'Classeur A4 2 anneaux', 'Pochettes plastiques transparentes (x50)',
        'Règle graduée 30cm', 'Compas de précision', "Rapporteur d'angle", 'Équerre 60°',
        'Feutres de coloriage (lot de 12)', 'Crayons de couleur (lot de 24)', 'Colle en bâton',
        'Ciseaux à bouts ronds', 'Surligneurs fluo (lot de 4)', 'Calculatrice scientifique',
        'Cartable à roulettes', 'Agenda scolaire', 'Protège-cahiers (lot de 10)', 'Tableau blanc effaçable A4',
        'Bloc-notes autocollant', 'Correcteur liquide', 'Chemise cartonnée A4 (lot de 5)', 'Ramette papier A4 80g',
    ];

    public function definition(): array
    {
        $title = fake()->randomElement($this->bookTitles);
        $price = fake()->randomFloat(2, 12, 65);
        $onSale = fake()->boolean(25);

        return [
            'type' => 'livre',
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'author' => fake()->randomElement($this->authors),
            'isbn' => fake()->unique()->isbn13(),
            'description' => implode("\n\n", fake()->paragraphs(3)),
            'language' => fake()->randomElement(['fr', 'ar', 'en']),
            'pages' => fake()->numberBetween(96, 512),
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

    /**
     * État "fourniture scolaire" : pas d'auteur/ISBN/pages/langue, description courte.
     */
    public function supply(): static
    {
        return $this->state(function (array $attributes) {
            $title = fake()->randomElement($this->supplyNames);
            $price = fake()->randomFloat(2, 2, 35);
            $onSale = fake()->boolean(20);

            return [
                'type' => 'fourniture',
                'title' => $title,
                'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
                'author' => null,
                'isbn' => null,
                'description' => fake()->sentence(15),
                'language' => null,
                'pages' => null,
                'publication_date' => null,
                'price' => $price,
                'compare_at_price' => $onSale ? round($price * 1.2, 2) : null,
            ];
        });
    }
}
