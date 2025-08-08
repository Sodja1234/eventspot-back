<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Tableau de noms français réalistes
        $firstNames = [
            'Jean', 'Marie', 'Pierre', 'Sophie', 'Michel', 'Isabelle', 'Philippe', 'Catherine', 'Laurent', 'Nathalie',
            'David', 'Sandrine', 'Thomas', 'Caroline', 'Antoine', 'Émilie', 'Julien', 'Stéphanie', 'Nicolas', 'Claire',
            'Alexandre', 'Élise', 'Guillaume', 'Camille', 'François', 'Charlotte', 'Sébastien', 'Aurélie', 'Christophe', 'Vanessa',
            'Romain', 'Julie', 'Vincent', 'Pauline', 'Olivier', 'Laetitia', 'Cédric', 'Mélanie', 'Fabien', 'Audrey',
            'Damien', 'Sylvie', 'Mathieu', 'Céline', 'Jérôme', 'Anne', 'Grégory', 'Magali', 'Benoît', 'Valérie'
        ];

        $lastNames = [
            'Martin', 'Bernard', 'Dubois', 'Durand', 'Moreau', 'Leroy', 'Simon', 'Laurent', 'Michel', 'Garcia',
            'David', 'Bertrand', 'Roux', 'Vincent', 'Fournier', 'Morel', 'Girard', 'Blanc', 'Lambert', 'Faure',
            'Rousseau', 'Fontaine', 'Chevalier', 'Lemaire', 'Perrin', 'Robin', 'Marchand', 'Dumas', 'Gaillard', 'Klein',
            'Meyer', 'Schneider', 'Muller', 'Leroux', 'Weber', 'Meyer', 'Schmitt', 'Lecomte', 'Colin', 'Poirier',
            'Bouchet', 'Lefebvre', 'Guillaume', 'Delaunay', 'Clement', 'Barbier', 'Dumont', 'Renard', 'Gauthier', 'Garnier'
        ];

        $firstName = fake()->randomElement($firstNames);
        $lastName = fake()->randomElement($lastNames);
        $fullName = $firstName . ' ' . $lastName;
        $email = strtolower($firstName) . '.' . strtolower($lastName) . '.' . fake()->randomNumber(3, true) . '@example.com';

        return [
            'name' => $fullName,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
