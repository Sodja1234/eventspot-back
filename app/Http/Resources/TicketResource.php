<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="TicketResource",
 *     type="object",
 *     title="TicketResource",
 *     description="Représentation d'un ticket d'événement",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="event_id", type="integer", example=123),
 *     @OA\Property(property="name", type="string", example="Billet VIP"),
 *     @OA\Property(property="price", type="number", format="float", example=49.99),
 *     @OA\Property(property="places", type="integer", example=100),
 *     @OA\Property(property="description", type="string", example="Accès VIP avec boissons gratuites"),
 *     @OA\Property(property="reserved_places", type="integer", example=20),
 *     @OA\Property(property="status", type="string", example="Confirmé"),
 *     @OA\Property(
 *         property="event",
 *         type="object",
 *         description="Informations sur l'événement lié au ticket",
 *         @OA\Property(property="id", type="integer", example=123),
 *         @OA\Property(property="title", type="string", example="Concert Rock"),
 *         @OA\Property(property="date_time_start", type="string", format="date-time", example="2025-07-09T20:00:00Z")
 *     )
 * )
 */
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