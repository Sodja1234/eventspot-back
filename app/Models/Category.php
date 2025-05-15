<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'title',
        'description',
        
    ];
    public function events()
    {
        return $this->belongsToMany(Event::class);
    }
}
