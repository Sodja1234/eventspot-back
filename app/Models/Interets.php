<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="Interest",
 *     title="Interest",
 *     description="Interest model representing a user interest",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Unique identifier of the interest",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="nom",
 *         type="string",
 *         description="Name of the interest",
 *         example="Technology"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the interest was created",
 *         example="2025-07-10T09:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the interest was last updated",
 *         example="2025-07-10T09:30:00Z"
 *     )
 * )
 */
class Interets extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    use HasFactory;

    protected $fillable = ['nom'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}