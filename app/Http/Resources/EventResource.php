<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

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
            'address'=>$this->address,
            'latitude'=>$this->latitude,
            'longitude'=>$this->longitude,
            'categories' => CategoryResource::collection($this->categories),
            'created_by' => UserResource::make(User::find($this->created_by)),
            'date_time_start' => Carbon::parse($this->date_time_start)->translatedFormat('d F Y'),
            'date_time_end' => Carbon::parse($this->date_time_end)->translatedFormat('d F Y'),
            'tickets' => TicketResource::collection($this->ticket),
            'media' => MediaResource::make($this->medias->first()),
        ];
    }
}