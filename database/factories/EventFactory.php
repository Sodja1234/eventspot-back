<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(3),
            'description' => fake()->sentence(6),
            'cycle' => fake()->randomElement([
                'yearly','semesterly','trimesterly','monthly',
                'weekly','daily','seasonly'
            ]),
            'created_by' => fake()->numberBetween(1, 50),
            'date_time_start' => fake()->dateTime(),
            'date_time_end' => fake()->dateTime(),
        ];
    }
}