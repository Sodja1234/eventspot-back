<?php
// app/Models/EmailOtp.php

// app/Models/EmailOtp.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="EmailOtp",
 *     title="EmailOtp",
 *     description="Model representing an email OTP (One-Time Password) record",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Unique identifier of the OTP record",
 *         example=101
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         format="int64",
 *         description="ID of the user associated with this OTP",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="otp",
 *         type="string",
 *         description="One-time password code",
 *         example="123456"
 *     ),
 *     @OA\Property(
 *         property="expires_at",
 *         type="string",
 *         format="date-time",
 *         description="Expiration date and time of the OTP",
 *         example="2025-07-10T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the OTP was created",
 *         example="2025-07-10T10:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the OTP was last updated",
 *         example="2025-07-10T10:05:00Z"
 *     )
 * )
 */
class EmailOtp extends Model
{
    protected $fillable = ['user_id', 'otp', 'expires_at'];
    public $timestamps = true;
    protected $dates = ['expires_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}