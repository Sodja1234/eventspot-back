<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="InteretsResource",
 *     type="object",
 *     title="InteretsResource",
 *     description="Représentation d'un intérêt",
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="nom", type="string", example="Musique")
 * )
 */
class InteretsResource extends JsonResource
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
            'nom' => $this->nom,
        ];
    }
}