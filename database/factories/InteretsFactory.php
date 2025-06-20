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
        $usedTitles = Interets::pluck('nom')->toArray();

        $availableTitle = Category::whereNotIn('title', $usedTitles)
            ->inRandomOrder()
            ->value('title');


        if (!$availableTitle) {

            $availableTitle = fake()->unique()->word();
        }

        return [
            'nom' => $availableTitle,
        ];
    }
}