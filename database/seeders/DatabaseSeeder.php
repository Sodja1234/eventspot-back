<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Interets;
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
        Category::factory()->count(90)->create();
        Interets::factory()->count(90)->create();
        //User::factory()->count(11000)->create();
    }
}