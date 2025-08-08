<?php

namespace Database\Seeders;

use App\Models\Interets;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        fake()->unique(true);
        User::factory()->create([
            'name' => 'Kopp D. Franklin',
            'email' => 'kopp@odc.com',
            'password' => 'password',
            'role' => 'organisateur'
        ]);

        User::factory()->count(1000)->create()->each(function ($user) {
            $interets = Interets::inRandomOrder()->take(3)->pluck('id');
            $user->interets()->attach($interets);
        });
    }
}
