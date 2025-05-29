<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'created_by' => UserResource::make(User::find($this->created_by)),
            'date_time_start' => $this->date_time_start,
            'date_time_end' => $this->date_time_end,
        ];
    }
}