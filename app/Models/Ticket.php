<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="Ticket",
 *     title="Ticket",
 *     description="Ticket model for an event",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID"),
 *     @OA\Property(property="event_id", type="integer", format="int64", description="Associated event ID"),
 *     @OA\Property(property="name", type="string", description="Ticket type name"),
 *     @OA\Property(property="price", type="number", format="float", description="Price of the ticket"),
 *     @OA\Property(property="places", type="integer", description="Total number of available places for this ticket type"),
 *     @OA\Property(property="reserved_places", type="integer", description="Number of reserved places"),
 *     @OA\Property(property="description", type="string", nullable=true, description="Ticket description"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
 * )
 */
class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

     protected $fillable = [
        'event_id',
        'name',
        'price',
        'places',
        'description',
        'reserved_places'
    ];
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}