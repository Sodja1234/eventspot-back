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
        return [
            'name' => fake()->sentence(2),
            'price' => fake()->numberBetween(10, 50),
            'places' => fake()->numberBetween(100,500),
            'description' => fake()->sentence(),
            'reserved_places' => fake()->numberBetween(0, 20),
        ];
    }
}