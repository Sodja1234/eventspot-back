<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
}