<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="EventResource",
 *     type="object",
 *     title="EventResource",
 *     description="Représentation d'un événement",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Concert Rock"),
 *     @OA\Property(property="description", type="string", example="Un super concert rock"),
 *     @OA\Property(property="cycle", type="string", example="Annuel"),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/CategoryResource")
 *     ),
 *     @OA\Property(
 *         property="created_by",
 *         ref="#/components/schemas/UserResource"
 *     ),
 *     @OA\Property(property="date_time_start", type="string", example="09 July 2025"),
 *     @OA\Property(property="date_time_end", type="string", example="10 July 2025"),
 *     @OA\Property(
 *         property="tickets",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/TicketResource")
 *     ),
 *     @OA\Property(
 *         property="media",
 *         ref="#/components/schemas/MediaResource"
 *     )
 * )
 */
class EventResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'cycle' => $this->cycle,
            'categories' => CategoryResource::collection($this->categories),
            'created_by' => UserResource::make(User::find($this->created_by)),
            'date_time_start' => Carbon::parse($this->date_time_start)->translatedFormat('d F Y'),
            'date_time_end' => Carbon::parse($this->date_time_end)->translatedFormat('d F Y'),
            'tickets' => TicketResource::collection($this->ticket),
            'media' => MediaResource::make($this->medias->first()),
        ];
    }
}