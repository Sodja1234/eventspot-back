<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *     schema="MediaResource",
 *     type="object",
 *     title="MediaResource",
 *     description="Représentation d'un média",
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="url", type="string", format="url", example="https://example.com/media/image.jpg")
 * )
 */
class MediaResource extends JsonResource
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
            'url' => $this->url,
        ];
    }
}