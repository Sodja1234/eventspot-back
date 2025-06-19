<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'name' => 'Kopp D. Franklin',
            'email' => 'kopp@odc.com',
            'password' => 'password',
            'role' => 'public'
        ]);
        User::factory(10000)->create();
    }
}