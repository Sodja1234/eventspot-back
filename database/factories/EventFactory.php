<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
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
            'title' => fake()->unique()->sentence(4),
            'description' => fake()->sentence(10),
            'cycle' => fake()->randomElement([
                'yearly','semesterly','trimesterly','monthly',
                'weekly','daily','seasonly']),
            'created_by' => fake()->numberBetween(1, 20),
            'date_time_start' => fake()->dateTime(),
            'date_time_end' => fake()->dateTime(),
            'address' => fake()->address(),
            'latitude' => fake()->latitude(-13.459, 5.386),
            'longitude' => fake()->longitude(12.039, 31.305),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Event $event) {
            $users = User::inRandomOrder()->take(rand(10, 200))->pluck('id');
            $event->favoritedByUsers()->attach($users);
        });
    }
}