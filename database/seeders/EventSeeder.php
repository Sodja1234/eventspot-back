<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Event;
use App\Models\Ticket;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::factory()
            ->count(3)
            ->has(Category::factory()->count(3))
            ->create()->each(function ($event) {
                Ticket::factory()->count(2)->create([
                    'event_id' => $event->id,
                ]);
            });
    }
}