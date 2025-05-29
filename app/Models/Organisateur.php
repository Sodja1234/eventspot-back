<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisateur extends Model
{


    use HasFactory;

    protected $fillable = ['user_id', 'nom_organis'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //
}