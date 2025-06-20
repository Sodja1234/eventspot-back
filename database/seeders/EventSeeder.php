<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Event;
use App\Models\Media;
use App\Models\Ticket;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        fake()->unique(true);
        Event::factory()
            ->count(400)
            ->create()->each(function ($event) {
                Ticket::factory()->count(2)->create([
                    'event_id' => $event->id,
                ]);
                Media::factory()->count(1)->create([
                    'event_id' => $event->id,
                ]);
                $category = Category::inRandomOrder()->take(3)->pluck('id');
                $event->categories()->attach($category);
            });
    }
}