<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'name' => $this->name,
            'price' => $this->price,
            'places' => $this->places,
            'description' => $this->description,
            'reserved_places' => $this->reserved_places,
            'status' => $this->reserved_places > 0 ? 'Confirmé' : 'Non vendu',
            'event' => [
                'id' => $this->event->id,
                'title' => $this->event->title,
                'date_time_start' => $this->event->date_time_start,
            ],
        ];
    }
}