<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Interet extends Model
{
    use HasFactory;

    protected $fillable = ['nom'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}