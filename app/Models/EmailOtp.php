<?php
// app/Models/EmailOtp.php

// app/Models/EmailOtp.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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