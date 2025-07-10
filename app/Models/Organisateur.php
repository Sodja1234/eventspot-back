<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Organisateur",
 *     title="Organisateur",
 *     description="Organisateur model representing an event organizer",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Unique identifier of the organizer",
 *         example=10
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         format="int64",
 *         description="ID of the associated user",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="nom_organis",
 *         type="string",
 *         description="Name of the organizer",
 *         example="Tech Events Ltd."
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the organizer was created",
 *         example="2025-07-10T09:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the organizer was last updated",
 *         example="2025-07-10T09:30:00Z"
 *     )
 * )
 */

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