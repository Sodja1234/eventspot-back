<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Tableau d'URLs d'images réalistes
        $imageUrls = [
            'https://images.unsplash.com/photo-1506157786151-b8491531f063', // Stade de football
            'https://images.unsplash.com/photo-1543362906-acfc16c63ce9', // Cérémonie de remise de prix
            'https://images.unsplash.com/photo-1511578314322-379afb476865', // Festival de cinéma
            'https://images.unsplash.com/photo-1494376671877-8a0d0e5c1b2b', // Concert de musique
            'https://images.unsplash.com/photo-1506126613408-eca07ce68773', // Conférence politique
            'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4', // Événement technologique
            'https://images.unsplash.com/photo-1513364776144-60967b0f800f', // Exposition d'art
            'https://images.unsplash.com/photo-1529626444931-4d34a2f4d8a9', // Défilé de mode
            'https://images.unsplash.com/photo-1512621776951-a5729d25258f', // Festival culinaire
            'https://images.unsplash.com/photo-1492684223018-9c10946e680f', // Festival de voyage
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70', // Salon automobile
            'https://images.unsplash.com/photo-1494947661808-24a407204b1a', // Festival littéraire
            'https://images.unsplash.com/photo-1518834107812-663c628b3ce1', // Festival historique
        ];

        return [
            'url' => fake()->randomElement($imageUrls),
        ];
    }
}
