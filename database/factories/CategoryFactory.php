<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Sport',
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
            'Jeux',
            'Littérature',
            'Histoire'
        ];

        $category = fake()->randomElement($categories);

        return [
            'title' => $category,
            'description' => 'Événements liés à ' . $category,
        ];
    }
}