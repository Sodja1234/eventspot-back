<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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