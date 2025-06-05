<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'id',
        'event_id',
        'url'
    ];

    public function event()
    {
        return $this->BelongsTo(Event::class);
    }
}
