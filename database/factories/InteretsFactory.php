<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Interets;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Interets>
 */
class InteretsFactory extends Factory
{
    public function definition(): array
    {
        // Tableau d'intérêts réalistes en français
        $interets = [
            'Football',
            'Cinéma',
            'Musique',
            'Politique',
            'Technologie',
            'Art',
            'Mode',
            'Cuisine',
            'Voyage',
            'Éducation',
            'Santé',
            'Écologie',
            'Jeux vidéo',
            'Littérature',
            'Histoire',
            'Photographie',
            'Dance',
            'Théâtre',
            'Science',
            'Économie'
        ];

        return [
            'nom' => fake()->randomElement($interets)
        ];
    }
}
