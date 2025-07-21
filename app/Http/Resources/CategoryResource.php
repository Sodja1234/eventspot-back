<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CategoryResource",
 *     type="object",
 *     title="CategoryResource",
 *     description="Représentation d'une catégorie",
 *     @OA\Property(property="id", type="integer", example=5),
 *     @OA\Property(property="title", type="string", example="Musique"),
 *     @OA\Property(property="description", type="string", example="Catégorie pour les événements musicaux")
 * )
 */
class CategoryResource extends JsonResource
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
        ];
    }
}