<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
    'longitude'
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