<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="Event",
 *     title="Event",
 *     description="Event model",
 *     @OA\Property(property="id", type="integer", format="int64", description="ID"),
 *     @OA\Property(property="title", type="string", description="Event title"),
 *     @OA\Property(property="description", type="string", description="Event description"),
 *     @OA\Property(property="cycle", type="string", nullable=true, description="Event cycle"),
 *     @OA\Property(property="created_by", type="integer", description="ID of the user who created the event"),
 *     @OA\Property(property="date_time_start", type="string", format="date-time", description="Event start date and time"),
 *     @OA\Property(property="date_time_end", type="string", format="date-time", description="Event end date and time"),
 *     @OA\Property(property="address", type="string", description="Event address"),
 *     @OA\Property(property="latitude", type="number", format="float", description="Event location latitude"),
 *     @OA\Property(property="longitude", type="number", format="float", description="Event location longitude"),
 *     @OA\Property(property="status", type="string", description="Event status (À venir, En cours, Terminé)"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp"),
 *     @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
 *     @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Category")),
 *     @OA\Property(property="medias", type="array", @OA\Items(ref="#/components/schemas/Media")),
 *     @OA\Property(property="ticket", type="array", @OA\Items(ref="#/components/schemas/Ticket"))
 * )
 */
class Event extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
    'title',
    'description',
    'cycle',
    'created_by',
    'date_time_start',
    'date_time_end',
    'address',
    'latitude',
    'longitude',
    'available',
    'remaining_seats'
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->hasMany(Ticket::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function medias()
    {
        return $this->HasMany(Media::class);
    }

     public function favoritedByUsers()
    {
    return $this->belongsToMany(User::class, 'event_user');
    }

    public function subscribeUsers()
    {
        return $this->belongsToMany(User::class, 'subscribes');
    }









    public function scopeForOrganisateur($query, $userId)
{
    return $query->where('created_by', $userId);
}

public function scopeUpcoming($query)
{
    return $query->where('date_time_start', '>', now());
}

public function scopePast($query)
{
    return $query->where('date_time_end', '<', now());
}

public function getStatusAttribute()
{
    if ($this->date_time_end < now()) {
        return 'Terminé';
    } elseif ($this->date_time_start > now()) {
        return 'À venir';
    }
    return 'En cours';
}

public function getTotalParticipantsAttribute()
{
    return $this->ticket->sum('reserved_places');
}
    public function getTotalTicketsSoldAttribute()
    {
        return $this->ticket->sum('places') - $this->ticket->sum('reserved_places');
    }
}