<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
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
