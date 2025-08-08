<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Tableau de types de billets réalistes en français
        $ticketTypes = [
            'Billet standard',
            'Billet VIP',
            'Billet premium',
            'Billet catégorie 1',
            'Billet catégorie 2',
            'Billet loge',
            'Billet balcon',
            'Billet pelouse',
            'Billet tribune',
            'Billet backstage',
            'Billet early bird',
            'Billet groupe',
            'Billet étudiant',
            'Billet senior',
            'Billet enfant'
        ];

        $ticketType = fake()->randomElement($ticketTypes);

        return [
            'name' => $ticketType,
            'price' => fake()->randomElement([10, 15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 150, 200]),
            'places' => fake()->numberBetween(50, 1000),
            'description' => 'Accès ' . strtolower($ticketType) . ' pour l\'événement',
            'reserved_places' => fake()->numberBetween(0, 50),
        ];
    }
}
